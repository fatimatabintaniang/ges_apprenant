<?php
    $connectToDatabase = function() {
        $servername = "localhost";
        $username = "postgres";
        $port = "8000";
        $password = "niang@4693";
        $dbname = "ges_apprenant";
    
        try {
            $conn = new PDO("pgsql:host=$servername;port=$port;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    };
    
    $isEmpty = function($name, &$errors) {
        if (empty($_POST[$name])) {
            $errors[$name] = ucfirst($name) . " est obligatoire.";
        }
    };
    
    $dd = function() {
        echo "<pre>";
        print_r(func_get_args());
        echo "</pre>";
        die;
    };
    
    $executeselect = function($sql, $isALL = false, $params = []) use ($connectToDatabase) {
        $pdo = $connectToDatabase();
        $stmt = $pdo->prepare($sql);
        
        // Bind des paramètres de manière sécurisée
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue(is_int($key) ? $key + 1 : $key, $value, $paramType);
            }
        }
        
        try {
            $stmt->execute();
            
            // Toujours retourner un tableau
            $result = $isALL ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Gestion des cas vides
            if ($isALL) {
                return $result ?: []; // Retourne un tableau vide si false/null
            }
            return $result ?: []; // Pour fetch(), retourne un tableau vide si false/null
            
        } catch (PDOException $e) {
            error_log("Erreur SQL: " . $e->getMessage() . " - Requête: " . $sql);
            return $isALL ? [] : false;
        }
    };
    $getDashboardStat = function() use ($connectToDatabase) {
        $db = $connectToDatabase();
    
        $stats = [];
    
        $query = $db->query("SELECT COUNT(*) as total FROM apprenant");
        $stats["total_apprenant"] = $query->fetch()["total"];
    
        $query = $db->query("SELECT COUNT(*) as total FROM referentiel WHERE archived=FALSE");
        $stats["total_referentiel"] = $query->fetch()["total"];
    
        $query = $db->query("SELECT COUNT(*) as total FROM promotion WHERE statut='Actif'");
        $stats["total_promotionActive"] = $query->fetch()["total"];
    
        $query = $db->query("SELECT COUNT(*) as total FROM promotion ");
        $stats["total_promotion"] = $query->fetch()["total"];
    
        return $stats;
    };

    $execute = function($sql, $params = [], $pdo = null) use ($connectToDatabase) {
    // Gestion de la connexion PDO
    $shouldCloseConnection = false;
    if ($pdo === null) {
        $pdo = $connectToDatabase();
        $shouldCloseConnection = true;
    }

    $stmt = $pdo->prepare($sql);
    
    try {
        // Binding des paramètres
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                // Support à la fois pour les clés numériques et nommées
                $paramName = is_int($key) ? $key + 1 : $key;
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($paramName, $value, $paramType);
            }
        }
        
        $stmt->execute();
        
        // Retourne le lastInsertId pour les INSERT, sinon true
        return $stmt->rowCount() > 0 ? $pdo->lastInsertId() : true;
        
    } catch (PDOException $e) {
        error_log("Erreur SQL (execute): " . $e->getMessage() . " - Requête: " . $sql);
        return false;
    } finally {
        // Fermeture de la connexion si nous l'avons créée ici
        if ($shouldCloseConnection) {
            $pdo = null;
        }
    }
};

function getMimeTypeFromBinary($binaryData): string
{
     if (is_resource($binaryData)) {
        $binaryData = stream_get_contents($binaryData);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_buffer($finfo, $binaryData);
    finfo_close($finfo);
    return $mimeType;
}

$global = compact('connectToDatabase',   'isEmpty' , 'dd', 'executeselect', 'execute','getDashboardStat');

