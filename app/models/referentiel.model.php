<?php
//======================Fonction qui recupere la liste des referentiels=================================
function findAllReferentiel($search = '')
{
    global $executeselect;

    $sql = "SELECT r.id_referentiel, r.libelle,r.image,r.description,r.session,r.capacite FROM referentiel r
     WHERE r.archived = FALSE";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND r.libelle LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY r.libelle ASC";

    try {
        return $executeselect($sql, true, $params) ?: [];
    } catch (PDOException $e) {
        error_log("Erreur PDO: " . $e->getMessage());
        return [];
    }
}

//========================================Ajout referentiel=========================================
function addReferentiels($libelle, $description, $image, $capacite, $session)
{
    global $execute, $executeselect;

    // 1. Vérification de l'existence du libellé
    $checkSql = "SELECT id_referentiel FROM referentiel WHERE libelle = :libelle";
    $existing = $executeselect($checkSql, false, [':libelle' => $libelle]);

    if ($existing) {
        throw new Exception("Un référentiel avec ce libellé existe déjà");
    }

    // 2. inserer un referentiel
    $sql = "INSERT INTO referentiel (libelle, description, image, capacite, session) 
            VALUES (:libelle, :description, :image, :capacite, :session)";

    $params = [
        ':libelle' => $libelle,
        ':description' => $description,
        ':image' => $image,
        ':capacite' => (int)$capacite,
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

//====================fonction pour modifier un referentiel=====================

function updateReferentiel($id, $libelle, $description, $image, $capacite, $session)
{
    global $execute;
    $sql = "UPDATE referentiel 
            SET libelle = :libelle, 
                description = :description, 
                image = :image, 
                capacite = :capacite, 
                session = :session 
            WHERE id_referentiel = :id";
    $params = [
        ':libelle' => $libelle,
        ':description' => $description,
        ':image' => $image,
        ':capacite' => $capacite,
        ':session' => $session,
        ':id' => $id
    ];
    $result = $execute($sql, $params);
    return $result !== false;
}

//====================fonction pour archiver un referentiel=====================

function archiveReferentiel($id) {
    global $execute;
    $sql = "UPDATE referentiel SET archived = TRUE WHERE id_referentiel = :id";
    return $execute($sql, [':id' => $id]);
}
