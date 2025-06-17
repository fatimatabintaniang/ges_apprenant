<?php
//======================Fonction qui recupere la liste des referentiels=================================




function findAllReferentiel($search = '')
{
    global $executeselect;

    $sql = "SELECT r.id_referentiel, r.libelle, r.image, r.description, r.session, r.capacite 
            FROM referentiel r
            WHERE r.archived = FALSE";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND r.libelle LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY r.libelle ASC";

    try {
        $referentiels = $executeselect($sql, true, $params) ?: [];

        // Important : décoder les images BYTEA
        foreach ($referentiels as &$ref) {
            if (!empty($ref['image'])) {
                // Si ton driver PDO est PostgreSQL, il faut "déséchapper" les données BYTEA
                $ref['image'] = stream_get_contents($ref['image']); // Pour éviter des erreurs si 'image' est une ressource
            }
        }

        return $referentiels;

    } catch (PDOException $e) {
        error_log("Erreur PDO: " . $e->getMessage());
        return [];
    }
}


//========================================Ajout referentiel=========================================
// la fonction addReferentiels
function addReferentiels($libelle, $description, $imageData, $capacite, $session) {
    global $execute, $executeselect;
    

    // Vérification de l'existence du libellé
    $checkSql = "SELECT id_referentiel FROM referentiel WHERE libelle = :libelle";
    $existing = $executeselect($checkSql, false, [':libelle' => $libelle]);

    if ($existing) {
        throw new Exception("Un référentiel avec ce libellé existe déjà");
    }

    // Modification de la requête pour utiliser bytea
    $sql = "INSERT INTO referentiel (libelle, description, image, capacite, session) 
            VALUES (:libelle, :description, :image, :capacite, :session)";

    $params = [
        ':libelle' => $libelle,
        ':description' => $description,
        ':image' => $imageData,
        ':capacite' => (int)$capacite,
        ':session' => $session
    ];

    return $execute($sql, $params);
}

// la fonction updateReferentiel
function updateReferentiel($id, $libelle, $description, $imageData, $capacite, $session) {
    global $execute;
    
    $sql = "UPDATE referentiel 
            SET libelle = :libelle, 
                description = :description, 
                capacite = :capacite, 
                session = :session";
    
    $params = [
        ':libelle' => $libelle,
        ':description' => $description,
        ':capacite' => $capacite,
        ':session' => $session,
        ':id' => $id
    ];
    
    // Ajouter l'image seulement si elle est fournie
    if ($imageData !== null) {
        $sql .= ", image = :image";
        $params[':image'] = $imageData;
    }
    
    $sql .= " WHERE id_referentiel = :id";
    
    return $execute($sql, $params) !== false;
}

// Ajouter une fonction pour récupérer l'image
// function getReferentielImage($id) {
//     global $executeselect;
//     $sql = "SELECT image FROM referentiel WHERE id_referentiel = :id";
//     $result = $executeselect($sql, false, [':id' => $id]);
//     return $result ? $result['image'] : null;
// }

//====================fonction pour archiver un referentiel=====================

function archiveReferentiel($id) {
    global $execute;
    $sql = "UPDATE referentiel SET archived = TRUE WHERE id_referentiel = :id";
    return $execute($sql, [':id' => $id]);
}
