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


function addApprenantWithTuteur(
    $nom, $prenom, $date_naissance, $lieu_naissance, $adresse, $telephone, 
    $email, $mot_de_passe, $nom_tuteur, $prenom_tuteur, $telephone_tuteur, 
    $adresse_tuteur, $lien_parente, $imageData, $id_referentiel, $id_promotion, $matricule,$statut='actif'
) {
    global $connectToDatabase;

    if (!$id_promotion) {
        throw new Exception("Aucune promotion active n'est disponible");
    }

    $pdo = $connectToDatabase();

    try {
        // Démarrer la transaction
        $pdo->beginTransaction();

        // 1. Insertion du tuteur
        $sqlTuteur = "INSERT INTO tuteur (nom_tuteur, prenom_tuteur, telephone_tuteur, adresse_tuteur, lien_parente) 
                      VALUES (:nom, :prenom, :telephone, :adresse, :lien)";
        $stmtTuteur = $pdo->prepare($sqlTuteur);
        $stmtTuteur->execute([
            ':nom' => $nom_tuteur,
            ':prenom' => $prenom_tuteur,
            ':telephone' => $telephone_tuteur,
            ':adresse' => $adresse_tuteur,
            ':lien' => $lien_parente
        ]);
        $id_tuteur = $pdo->lastInsertId();

        // 2. Insertion de l'utilisateur
        $sqlUser = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) 
                    VALUES (:nom, :prenom, :email, :mot_de_passe, 'Apprenant')";
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':mot_de_passe' =>$mot_de_passe
        ]);
        $id_utilisateur = $pdo->lastInsertId();

        // 3. Insertion de l'apprenant
        $sqlApprenant = "INSERT INTO apprenant (id_utilisateur, id_tuteur, date_de_naissance, lieu_de_naissance, 
                         adresse, telephone, image, id_referentiel, id_promotion, matricule,statut) 
                         VALUES (:id_utilisateur, :id_tuteur, :date_naissance, :lieu_naissance, 
                         :adresse, :telephone, :image, :id_referentiel, :id_promotion, :matricule,:statut)";
        $stmtApprenant = $pdo->prepare($sqlApprenant);
        $stmtApprenant->execute([
            ':id_utilisateur' => $id_utilisateur,
            ':id_tuteur' => $id_tuteur,
            ':date_naissance' => $date_naissance,
            ':lieu_naissance' => $lieu_naissance,
            ':adresse' => $adresse,
            ':telephone' => $telephone,
            ':image' => $imageData,
            ':id_referentiel' => $id_referentiel,
            ':id_promotion' => $id_promotion,
            ':matricule' => $matricule,
            ':statut'=>$statut
        ]);

        // Valider la transaction
        $pdo->commit();
        return true;

    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        error_log("Erreur lors de l'ajout de l'apprenant avec tuteur : " . $e->getMessage());
        return false;
    }
}


function getActivePromotion() {
    global $executeselect;
    
    $sql = "SELECT id_promotion, nom AS promotion FROM promotion WHERE statut = 'Actif' LIMIT 1";
    $result = $executeselect($sql, false);
    
    return $result ? $result : null;
}



