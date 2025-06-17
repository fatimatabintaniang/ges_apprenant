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
    global $getDashboardStat ,$executeselect;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         $activePromotion = $executeselect(
            "SELECT id_promotion FROM promotion WHERE statut = 'Actif' LIMIT 1",
            false
        );
        
        // Si une promotion est déjà active et qu'on essaie d'en créer une nouvelle active
        if ($activePromotion && ($_POST['statut'] ?? '') === 'Actif') {
            $errors['statut'] = "Une promotion est déjà active. Vous ne pouvez avoir qu'une seule promotion active à la fois.";
            $_POST['statut'] = 'Inactif'; // Forcer Inactif
        } else {
            // Par défaut Inactif
            $_POST['statut'] = $_POST['statut'] ?? 'Inactif';
        }
        $data = array_merge($_POST, ['image_file' => $_FILES['image'] ?? null]);
        $errors = validateData($data, 'promotion');
        
        if (empty($errors)) {

            $escapedImage=null;
             if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    
                    
                    $imageData = file_get_contents($_FILES['image']['tmp_name']);

                    
        
                  $conn = pg_connect("host=localhost dbname=ges_apprenant user=postgres password=niang@4693 port=8000");
                  
                    $escapedImage = pg_escape_bytea($conn,$imageData);


                } else {
                    echo "Aucun fichier n'a été téléchargé ou une erreur est survenue.";
                }
            $success = addPromotionWithReferentiels(
                $_POST['nom'],
                $_POST['date_debut'],
                $_POST['date_fin'],
                $_POST['statut'],
                $_POST['referentiels'] ?? [],
                $escapedImage

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
        
        // Si on veut activer cette promotion
        if ($newStatus === 'Actif') {
            // Désactiver toutes les autres promotions actives
            global $execute;
            $execute("UPDATE promotion SET statut = 'Inactif' WHERE statut = 'Actif' AND id_promotion != :id", 
                    [':id' => $id]);
        }
        
        // Mettre à jour le statut de la promotion courante
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
