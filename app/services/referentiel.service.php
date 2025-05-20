<?php
require_once "../app/models/model.php";
require_once "../app/controllers/controller.php";
function showReferentielList() {
    $search = $_GET['search'] ?? '';
    
    RenderView("referentiel/listeReferentiel", [
        'referentiels' => findAllReferentiel($search)
    ], "base.layout");
}
function addReferentielHandler() {
    session_start();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = validateReferentielData($_POST);
        
        if (empty($errors)) {
            // Tentative d'ajout
            if (addReferentiels($_POST['libelle'], $_POST['description'], $_POST['image'], $_POST['capacite'], $_POST['session'])) {
                header("Location: ?controllers=referentiel&page=listeReferentiel&success=1");
                exit;
            } else {
                $errors['global'] = "Erreur lors de l'ajout";
            }
        }
        
        // Stockage des erreurs et anciennes valeurs
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;
        header("Location: ?controllers=referentiel&page=listeReferentiel&showModal=1");
        exit;
    }
    
    // Si pas POST, redirection
    header("Location: ?controllers=referentiel&page=listeReferentiel");
    exit;
}
function getReferentielToEdit($id) {
    global $executeselect;
    $sql = "SELECT * FROM referentiel WHERE id_referentiel = :id";
    return $executeselect($sql, false, [':id' => $id]);
}

function updateReferentielHandler() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ?controllers=referentiel&page=listeReferentiel");
        exit;
    }

    $errors = validateReferentielData($_POST);
    $referentiel_id = $_POST['referentiel_id'] ?? null;

    if (empty($errors) && $referentiel_id) {
        try {
            $success = updateReferentiel(
                $referentiel_id,
                $_POST['libelle'],
                $_POST['description'],
                $_POST['image'],
                $_POST['capacite'],
                $_POST['session']
            );
            
            if ($success) {
                header("Location: ?controllers=referentiel&page=listeReferentiel&search=".urlencode($_GET['search'] ?? '')."&success=1");
                exit;
            }
        } catch (Exception $e) {
            $errors['global'] = $e->getMessage();
        }
    }

    // En cas d'erreur, réafficher le formulaire
    RenderView("referentiel/listeReferentiel", [
        'referentiels' => findAllReferentiel($_GET['search'] ?? ''),
        'referentielToEdit' => $_POST, // Pour pré-remplir le formulaire avec les données soumises
        'errors' => $errors,
        'showModal' => true,
        'action' => 'edit'
    ], "base.layout");
}