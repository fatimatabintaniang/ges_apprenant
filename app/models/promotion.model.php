<?php
require_once "../app/models/model.php";
function findAllPromotion($filter = 'all', $search = '') {
    global $executeselect;
    
    // Construction de la requête SQL de base
    $sql = "
        SELECT 
            p.id_promotion,
            p.nom AS promotion,
            p.date_debut,
            p.date_fin,
            p.statut,
            STRING_AGG(r.libelle, ', ') AS referentiel,
            COUNT(DISTINCT a.id_apprenant) AS nombre_apprenants
        FROM 
            promotion p
        LEFT JOIN 
            promoref pr ON p.id_promotion = pr.id_promotion
        LEFT JOIN 
            referentiel r ON pr.id_referentiel = r.id_referentiel
        LEFT JOIN 
            apprenant a ON p.id_promotion = a.id_promotion
    ";
    
    // Préparation des conditions de filtre
    $conditions = [];
    $params = [];
    
    // Filtre par statut
    if ($filter === 'active') {
        $conditions[] = "p.statut = 'Actif'";
    } elseif ($filter === 'inactive') {
        $conditions[] = "p.statut = 'Inactif'";
    }
    
    // Filtre par recherche
    if (!empty($search)) {
        $conditions[] = "p.nom LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    
    // Combinaison des conditions
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    // Finalisation de la requête
    $sql .= "
        GROUP BY 
            p.id_promotion, p.nom, p.date_debut, p.date_fin, p.statut
        ORDER BY 
            p.date_debut DESC
    ";
    
    try {
        $result = $executeselect($sql, true, $params);
        return is_array($result) ? $result : [];
        
    } catch (PDOException $e) {
        error_log("Erreur PDO dans findAllPromotion: " . $e->getMessage());
        error_log("Requête SQL: " . $sql);
        return [];
    }
}

//========================================Ajout promotion=========================================
function addPromotionWithReferentiels($nom, $date_debut, $date_fin, $statut, $referentiels) {
    global $execute, $executeselect;

    // 1. Vérifier si le nom existe déjà
    $checkSql = "SELECT id_promotion FROM promotion WHERE nom = :nom";
    $existing = $executeselect($checkSql, false, [':nom' => $nom]);
    
    if ($existing) {
        throw new Exception("Une promotion avec ce nom existe déjà");
    }

    // 2. Ajouter la promotion
    $sql = "INSERT INTO promotion (nom, date_debut, date_fin, statut) 
            VALUES (:nom, :date_debut, :date_fin, :statut) RETURNING id_promotion";
    
    $params = [
        ':nom' => $nom,
        ':date_debut' => $date_debut,
        ':date_fin' => $date_fin,
        ':statut' => $statut
    ];
    
    $result = $executeselect($sql, false, $params);
    
    if (!$result) {
        throw new Exception("Échec de l'ajout de la promotion");
    }
    
    $promoId = $result['id_promotion'];

    // 3. Ajouter les associations avec les référentiels
    foreach ($referentiels as $referentielId) {
        $sql = "INSERT INTO promoref (id_promotion, id_referentiel) 
                VALUES (:id_promotion, :id_referentiel)";
        
        $success = $execute($sql, [
            ':id_promotion' => $promoId,
            ':id_referentiel' => $referentielId
        ]);
        
        if (!$success) {
            // On continue quand même mais on log l'erreur
            error_log("Échec de l'ajout du référentiel $referentielId à la promotion $promoId");
            // Vous pouvez choisir de return false ici si vous voulez arrêter complètement
        }
    }

    return $promoId;
}
function findAllReferentiels() {
    global $executeselect;
    
    $sql = "SELECT id_referentiel, libelle FROM referentiel ORDER BY libelle";
    return $executeselect($sql, true);
}

function validatePromotionData($data) {
    global $executeselect;
    $errors = [];
    
    // Validation du nom
    if (empty($data['nom'])) {
        $errors['nom'] = "Le nom de la promotion est obligatoire";
    } else {
        $sql = "SELECT COUNT(*) as count FROM promotion WHERE nom = :nom";
        $count = $executeselect($sql, false, [':nom' => $data['nom']]);
        if ($count && $count['count'] > 0) {
            $errors['nom'] = "Ce nom de promotion existe déjà";
        }
    }
    
    // Validation des dates
    if (empty($data['date_debut']) || !strtotime($data['date_debut'])) {
        $errors['date_debut'] = "Date de début invalide";
    }
    
    if (empty($data['date_fin']) || !strtotime($data['date_fin'])) {
        $errors['date_fin'] = "Date de fin invalide";
    } elseif (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
        $errors['date_fin'] = "La date de fin doit être postérieure à la date de début";
    }

    // Validation des référentiels (corrigé le nom du champ)
    if (empty($data['referentiels'])) {
        $errors['referentiels'] = "Sélectionner au moins un référentiel";
    }
    
    return $errors;
}