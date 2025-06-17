<?php
require_once "../app/models/model.php";


//===============================Fonction pour recuperer la liste des promotions================================
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
            p.image,
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
    
    // Finalisation de la requête avec tri personnalisé
    $sql .= "
        GROUP BY 
            p.id_promotion, p.nom, p.date_debut, p.date_fin, p.statut, p.image
        ORDER BY 
            CASE WHEN p.statut = 'Actif' THEN 0 ELSE 1 END, -- Les promotions actives en premier
            p.date_debut DESC -- Puis tri par date décroissante
    ";
    
    try {
        $promotions = $executeselect($sql, true, $params) ?: [];

        // Important : décoder les images BYTEA
        foreach ($promotions as &$ref) {
            if (!empty($ref['image'])) {
                $ref['image'] = stream_get_contents($ref['image']);
            }
        }

        return $promotions;
        
    } catch (PDOException $e) {
        error_log("Erreur PDO dans findAllPromotion: " . $e->getMessage());
        error_log("Requête SQL: " . $sql);
        return [];
    }
}

//==================================================Ajout promotion=============================================
function addPromotionWithReferentiels($nom, $date_debut, $date_fin, $statut='Inactif', $referentiels, $imageData) {
    global $execute, $executeselect;

    // Vérifier s'il y a déjà une promotion active si on essaie d'en créer une active
    if ($statut === 'Actif') {
        $activePromotion = $executeselect(
            "SELECT id_promotion FROM promotion WHERE statut = 'Actif' LIMIT 1",
            false
        );
        
        if ($activePromotion) {
            throw new Exception("Une promotion est déjà active. Vous ne pouvez avoir qu'une seule promotion active à la fois.");
        }
    }

    // 1. Vérifier si le nom existe déjà
    $checkSql = "SELECT id_promotion FROM promotion WHERE nom = :nom";
    $existing = $executeselect($checkSql, false, [':nom' => $nom]);
    
    if ($existing) {
        throw new Exception("Une promotion avec ce nom existe déjà");
    }

    // 2. Ajouter la promotion
    $sql = "INSERT INTO promotion (nom, date_debut, date_fin, statut,image) 
            VALUES (:nom, :date_debut, :date_fin, :statut,:image) RETURNING id_promotion";
    
    $params = [
        ':nom' => $nom,
        ':date_debut' => $date_debut,
        ':date_fin' => $date_fin,
        ':statut' => $statut,
        ':image' => $imageData
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
            error_log("Échec de l'ajout du référentiel $referentielId à la promotion $promoId");
        }
    }

    return $promoId;
}

//======================================fonction pour recuperer la liste des referentiels=======================
function findAllReferentiels() {
    global $executeselect;
    
    $sql = "SELECT id_referentiel, libelle FROM referentiel WHERE archived = FALSE ORDER BY libelle";
    return $executeselect($sql, true);
}

//========================fonction pour modifier le statut===========================================
function updatePromotionStatus($id_promotion, $new_status) {
    global $execute;
    
    // Validation des données
    if (!in_array($new_status, ['Actif', 'Inactif'])) {
        error_log("Statut invalide: $new_status");
        return false;
    }
    
    try {
        $sql = "UPDATE promotion SET statut = :statut WHERE id_promotion = :id";
        $params = [
            ':statut' => $new_status,
            ':id' => $id_promotion
        ];
        
        return $execute($sql, $params);
        
    } catch (PDOException $e) {
        error_log("Erreur lors de la modification du statut: " . $e->getMessage());
        return false;
    }
} 