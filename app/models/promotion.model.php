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
            r.libelle AS referentiel,
            COUNT(a.id_apprenant) AS nombre_apprenants
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
            p.id_promotion, p.nom, p.date_debut, p.date_fin, p.statut, r.libelle
        ORDER BY 
            p.date_debut DESC
    ";
    
    try {
        // Exécution de la requête avec fetchAll forcé
        $result = $executeselect($sql, true, $params);
        
        // Vérification et conversion du résultat
        if (!is_array($result)) {
            error_log("Resultat inattendu: " . gettype($result) . " pour la requête: " . $sql);
            return [];
        }
        
        return $result;
        
    } catch (PDOException $e) {
        // Journalisation détaillée de l'erreur
        error_log("Erreur PDO dans findAllPromotion: " . $e->getMessage());
        error_log("Requête SQL: " . $sql);
        error_log("Paramètres: " . print_r($params, true));
        
        return [];
    } catch (Exception $e) {
        // Capture des autres exceptions non-PDO
        error_log("Erreur générique dans findAllPromotion: " . $e->getMessage());
        return [];
    }
}