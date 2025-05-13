<?php
require_once "../app/models/model.php";
require_once "../app/controllers/controller.php";
function showPromotionList() {
    global $getDashboardStat;
    
    // Récupérer les paramètres depuis l'URL
    $filter = $_GET['statusFilter'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    // Passer les paramètres à la fonction du modèle
    $promotions = findAllPromotion($filter, $search); 
    
    $stats = $getDashboardStat();
    
    RenderView("promotion/listePromotion", [
        'promotions' => $promotions,
        'stats' => $stats,
        'total' => $getDashboardStat()["total_apprenant"],
        'total_referentiel' => $getDashboardStat()["total_referentiel"],
        'total_promotionActive' => $getDashboardStat()["total_promotionActive"],
        'total_promotion' => $getDashboardStat()["total_promotion"]
    ], "base.layout");
}