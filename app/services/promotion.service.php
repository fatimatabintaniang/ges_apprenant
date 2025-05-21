<?php
require_once "../app/bootstrap/bootstrap.php";

function showPromotionList() {
    global $getDashboardStat;
    
    $filter = $_GET['statusFilter'] ?? 'all';
    $search = $_GET['search'] ?? '';
    $showModal = isset($_GET['showModal']);
    
    RenderView("promotion/listePromotion", [
        'promotions' => findAllPromotion($filter, $search),
        'stats' => $getDashboardStat(),
        'total' => $getDashboardStat()["total_apprenant"],
        'total_referentiel' => $getDashboardStat()["total_referentiel"],
        'total_promotionActive' => $getDashboardStat()["total_promotionActive"],
        'total_promotion' => $getDashboardStat()["total_promotion"],
        'showModal' => $showModal
    ], "base.layout");
}

function addPromotionHandler() {
    global $getDashboardStat;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = validateData($_POST, 'promotion');
        
        if (empty($errors)) {
            $success = addPromotionWithReferentiels(
                $_POST['nom'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['statut'],
                $_POST['referentiels'] ?? []
            );
            
            if ($success) {
                $redirect = $_POST['redirect'] ?? '?controllers=promotion&page=listePromotion';
                header("Location: $redirect&success=1");
                exit;
            } else {
                $errors['global'] = "Une erreur est survenue lors de l'ajout";
            }
        }
        
        // En cas d'erreur
        RenderView("promotion/listePromotion", [
            'promotions' => findAllPromotion($_GET['statusFilter'] ?? 'all', $_GET['search'] ?? ''),
            'stats' => $getDashboardStat(),
            'total' => $getDashboardStat()["total_apprenant"],
            'total_referentiel' => $getDashboardStat()["total_referentiel"],
            'total_promotionActive' => $getDashboardStat()["total_promotionActive"],
            'total_promotion' => $getDashboardStat()["total_promotion"],
            'errors' => $errors,
            'old' => $_POST,
            'showModal' => true
        ], "base.layout");
        exit;
    }
}

function togglePromotionStatus() {
    $id = $_GET['id'] ?? null;
    $currentStatus = $_GET['current_status'] ?? null;
    $redirect = $_GET['redirect'] ?? '?controllers=promotion&page=listePromotion';
    
    if ($id && $currentStatus) {
        // Déterminer le nouveau statut
        $newStatus = ($currentStatus === 'Actif') ? 'Inactif' : 'Actif';
        
        // Appel de la fonction du modèle
        $success = updatePromotionStatus($id, $newStatus);
        
        if ($success) {
            header("Location: $redirect&status_toggled=1");
        } else {
            header("Location: $redirect&error=1");
        }
        exit;
    }
    
    header("Location: $redirect");
    exit;
}
