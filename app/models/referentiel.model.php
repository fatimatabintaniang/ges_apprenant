<?php
//======================Fonction qui recupere la liste des referentiels=================================
function findAllReferentiel($search = '') { 
    global $executeselect;
    
    $sql = "SELECT r.id_referentiel, r.libelle,r.image,r.description,r.session,r.capacite FROM referentiel r";
    $params = [];
    
    if (!empty($search)) {
        $sql .= " WHERE r.libelle LIKE :search";
        $params[':search'] = '%'.$search.'%';
    }
    
    $sql .= " ORDER BY r.libelle ASC";
    
    try {
        return $executeselect($sql, true, $params) ?: [];
    } catch (PDOException $e) {
        error_log("Erreur PDO: " . $e->getMessage());
        return [];
    }
}

//========================================Ajout promotion=========================================
function addReferentiels($libelle, $description, $image, $capacite, $session) {
    global $execute, $executeselect;

    // 1. Vérification de l'existence du libellé
    $checkSql = "SELECT id_referentiel FROM referentiel WHERE libelle = :libelle";
    $existing = $executeselect($checkSql, false, [':libelle' => $libelle]);
    
    if ($existing) {
        throw new Exception("Un référentiel avec ce libellé existe déjà");
    }

    // 2. Debug avancé
    $sql = "INSERT INTO referentiel (libelle, description, image, capacite, session) 
            VALUES (:libelle, :description, :image, :capacite, :session)";
    
    $params = [
        ':libelle' => $libelle,
        ':description' => $description,
        ':image' => $image,
        ':capacite' => (int)$capacite, // Conversion explicite en integer
        ':session' => $session
    ];

    // 3. Exécution avec gestion d'erreur améliorée
    $result = $execute($sql, $params);
    
    if (!$result) {
        // Récupération de l'erreur PDO directement
        global $connectToDatabase;
        $pdo = $connectToDatabase();
        $errorInfo = $pdo->errorInfo();
        throw new Exception("Erreur SQL: " . ($errorInfo[2] ?? "Inconnue"));
    }
    
    return $result;
}


//===================Fonstion pour la validation du formulaire=================================
function validateReferentielData($data) {
    global $executeselect;
    $errors = [];
    
    // Validation du nom
    if (empty(trim($data['libelle']))) {
        $errors['libelle'] = "Le libelle  est obligatoire";
    } else {
        $sql = "SELECT COUNT(*) as count FROM referentiel WHERE libelle = :libelle";
        $count = $executeselect($sql, false, [':libelle' => $data['libelle']]);
        if ($count && $count['count'] > 0) {
            $errors['libelle'] = "Ce libelle de referentiel existe déjà";
        }
    }
    
    // Validation description
    if (empty(trim($data['description']))) {
        $errors['description'] = "la description est obligatoire";
    }

    if (empty(trim($data['image']))) {
        $errors['image'] = "l'image est obligatoire";
    }

     if (empty(trim($data['capacite'])) ) {
        $errors['capacite'] = "la capacite est obligatoire";
    }

     if (empty(trim($data['session'])) ) {
        $errors['session'] = "la session est obligatoire";
    }
    
    return $errors;
}

//====================fonction pour modifier un referentiel=====================
 
function updateReferentiel($id_referentiel, $libelle, $description, $image, $capacite, $session) {
    global $execute, $executeselect;

    // 1. Vérifier que le référentiel existe
    $checkSql = "SELECT id_referentiel FROM referentiel WHERE id_referentiel = :id";
    $existing = $executeselect($checkSql, false, [':id' => $id_referentiel]);
    
    if (!$existing) {
        throw new Exception("Le référentiel à modifier n'existe pas");
    }

    // 2. Vérifier si le nouveau libellé existe déjà (pour un autre référentiel)
    $checkLibelleSql = "SELECT id_referentiel FROM referentiel 
                       WHERE libelle = :libelle AND id_referentiel != :id";
    $libelleExists = $executeselect($checkLibelleSql, false, [
        ':libelle' => $libelle,
        ':id' => $id_referentiel
    ]);
    
    if ($libelleExists) {
        throw new Exception("Un autre référentiel avec ce libellé existe déjà");
    }

    // 3. Préparation de la requête de mise à jour
    $sql = "UPDATE referentiel SET 
            libelle = :libelle,
            description = :description,
            image = :image,
            capacite = :capacite,
            session = :session,
            updated_at = CURRENT_TIMESTAMP
            WHERE id_referentiel = :id";
    
    $params = [
        ':libelle' => $libelle,
        ':description' => $description,
        ':image' => $image,
        ':capacite' => (int)$capacite,
        ':session' => $session,
        ':id' => $id_referentiel
    ];

    // 4. Exécution de la mise à jour
    $result = $execute($sql, $params);
    
    if (!$result) {
        global $connectToDatabase;
        $pdo = $connectToDatabase();
        $errorInfo = $pdo->errorInfo();
        throw new Exception("Erreur SQL lors de la modification: " . ($errorInfo[2] ?? "Inconnue"));
    }
    
    return $result;
}