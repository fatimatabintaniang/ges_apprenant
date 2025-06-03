<?php
require_once "../app/models/model.php";

function findAllApprenant($filter = 'all', $search = '', $referentielFilter = 'all') {
    global $executeselect;
    
    $sql = "
        SELECT 
            t.id_tuteur,
            a.id_apprenant,
            u.id_utilisateur,
            CONCAT(u.nom, ' ', u.prenom) AS nom_complet,
            p.id_promotion,
            a.matricule,
            a.statut,
            a.adresse,
            a.telephone,
            a.image,
            r.libelle AS referentiel,
            a.date_de_naissance,
            a.lieu_de_naissance
        FROM 
            apprenant a
        LEFT JOIN 
            utilisateur u ON a.id_utilisateur = u.id_utilisateur
        LEFT JOIN 
            referentiel r ON r.id_referentiel = a.id_referentiel
        LEFT JOIN 
            promotion p ON p.id_promotion = a.id_promotion
         LEFT JOIN 
            tuteur t ON t.id_tuteur = a.id_tuteur    
    ";
    
    // Préparation des conditions de filtre
    $conditions = [];
    $params = [];
    
    // Filtre par statut
    if ($filter === 'actif') {
        $conditions[] = "a.statut = 'actif'";
    } elseif ($filter === 'remplace') {
        $conditions[] = "a.statut = 'remplace'";
    }

    //Filtre par referentiel
    if ($referentielFilter !== 'all') {
        $conditions[] = "a.id_referentiel = :referentiel";
        $params[':referentiel'] = $referentielFilter;
    }
    
    // Filtre par recherche
    if (!empty($search)) {
        $conditions[] = "a.matricule LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    
    // Combinaison des conditions
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
 
    
    try {
        $result = $executeselect($sql, true, $params);
        return is_array($result) ? $result : [];
        
    } catch (PDOException $e) {
        error_log("Erreur PDO dans findAllApprenant: " . $e->getMessage());
        error_log("Requête SQL: " . $sql);
        return [];
    }
}

function updateApprenantStatus($id_apprenant, $new_status) {
    global $execute;
    
    // Validation des données
    if (!in_array($new_status, ['actif', 'remplace'])) {
        error_log("Statut invalide: $new_status");
        return false;
    }
    
    try {
        $sql = "UPDATE apprenant SET statut = :statut WHERE id_apprenant = :id";
        $params = [
            ':statut' => $new_status,
            ':id' => $id_apprenant
        ];
        
        return $execute($sql, $params);
        
    } catch (PDOException $e) {
        error_log("Erreur lors de la modification du statut: " . $e->getMessage());
        return false;
    }
}

//==================================================Ajout promotion=============================================
function addApprenantWithTuteur($nom, $prenom, $date_naissance, $lieu_naissance, $adresse, $telephone,$matricule, $email, $imageData, $tuteur) {
    global $execute, $executeselect;

    // 1. Vérifier si l'email existe déjà
    $checkSql = "SELECT id_apprenant FROM apprenant WHERE email = :email";
    $existing = $executeselect($checkSql, false, [':email' => $email]);
    
    if ($existing) {
        throw new Exception("Un apprenant avec cet email existe déjà");
    }

    // 2. Ajouter l'apprenant
    $sqlApprenant = "INSERT INTO apprenant (nom, prenom, date_naissance, lieu_naissance, adresse, telephone,matricule, email, image) 
                    VALUES (:nom, :prenom, :date_naissance, :lieu_naissance, :adresse, :telephone,:matricule, :email, :image) 
                    RETURNING id_apprenant";
    
    $params = [
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':date_naissance' => $date_naissance,
        ':lieu_naissance' => $lieu_naissance,
        ':adresse' => $adresse,
        ':telephone' => $telephone,
        ':matricule' => $matricule,
        ':email' => $email,
        ':image' => $imageData
    ];
    
    $result = $executeselect($sqlApprenant, false, $params);
    $apprenantId = $result['id_apprenant'];

    // 3. Ajouter l'utilisateur
    $sqlUser = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) 
               VALUES (:nom, :prenom, :email, :mot_de_passe, 'Apprenant') 
               RETURNING id_utilisateur";
    
    $userResult = $executeselect($sqlUser, false, [
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':mot_de_passe' => $mot_de_passe
    ]);
    $userId = $userResult['id_utilisateur'];

    // 4. Lier l'utilisateur à l'apprenant
    $execute("UPDATE apprenant SET id_utilisateur = :userId WHERE id_apprenant = :apprenantId", [
        ':userId' => $userId,
        ':apprenantId' => $apprenantId
    ]);

    // 5. Vérifier si le tuteur existe déjà
    $checkTuteurSql = "SELECT id_tuteur FROM tuteur WHERE telephone_tuteur = :telephone_tuteur";
    $existingTuteur = $executeselect($checkTuteurSql, false, [':telephone_tuteur' => $tuteur['telephone_tuteur']]);
    
    if ($existingTuteur) {
        // Le tuteur existe déjà, on lie simplement l'apprenant
        $tuteurId = $existingTuteur['id_tuteur'];
        $execute("UPDATE apprenant SET id_tuteur = :tuteurId WHERE id_apprenant = :apprenantId", [
            ':tuteurId' => $tuteurId,
            ':apprenantId' => $apprenantId
        ]);
    } else {
        // Le tuteur n'existe pas, on le crée et on lie l'apprenant
        $sqlTuteur = "INSERT INTO tuteur (nom_tuteur, prenom_tuteur, telephone_tuteur, adresse_tuteur, lieu_parente) 
                     VALUES (:nom_tuteur, :prenom_tuteur, :telephone_tuteur, :adresse_tuteur, :lieu_parente)
                     RETURNING id_tuteur";
        
        $tuteurResult = $executeselect($sqlTuteur, false, [
            ':nom_tuteur' => $tuteur['nom_tuteur'],
            ':prenom_tuteur' => $tuteur['prenom_tuteur'],
            ':telephone_tuteur' => $tuteur['telephone_tuteur'],
            ':adresse_tuteur' => $tuteur['adresse_tuteur'],
            ':lieu_parente' => $tuteur['lieu_parente']
        ]);
        $tuteurId = $tuteurResult['id_tuteur'];
        
        $execute("UPDATE apprenant SET id_tuteur = :tuteurId WHERE id_apprenant = :apprenantId", [
            ':tuteurId' => $tuteurId,
            ':apprenantId' => $apprenantId
        ]);
    }

    return $apprenantId;
}

