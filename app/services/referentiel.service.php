<?php
require_once "../app/bootstrap/bootstrap.php";

function showReferentielList() {
    $search = $_GET['search'] ?? '';
    
    RenderView("referentiel/listeReferentiel", [
        'referentiels' => findAllReferentiel($search)
    ], "base.layout");
}
function addReferentielHandler() {
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              $errors = validateData($_POST, 'referentiel');

        
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

    // Validation
     $errors = validateData($_POST, 'referentiel');
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
                header("Location: ?controllers=referentiel&page=listeReferentiel&success=1");
                exit;
            }
        } catch (Exception $e) {
            $errors['global'] = $e->getMessage();
        }
    }

    // Stockage en session comme pour l'ajout
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: ?controllers=referentiel&page=listeReferentiel&action=edit&referentiel_id=$referentiel_id&showModal=1");
    exit;
}

// Dans votre contrôleur référentiel
function archiveReferentielHandler() {
    // Vérifier si l'ID est présent
    if (!isset($_GET['referentiel_id'])) {
        $_SESSION['error'] = "ID du référentiel manquant";
        header("Location: ?controllers=referentiel&page=listeReferentiel");
        exit;
    }

    $id = $_GET['referentiel_id'];
    $search = $_GET['search'] ?? '';

    // Appeler la fonction d'archivage du modèle
    $success = archiveReferentiel($id);

    if ($success) {
        $_SESSION['success'] = "Référentiel archivé avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de l'archivage du référentiel";
    }

    // Rediriger vers la liste avec le paramètre de recherche
    header("Location: ?controllers=referentiel&page=listeReferentiel&search=$search");
    exit;
}