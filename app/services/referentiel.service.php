<?php
require_once "../app/bootstrap/bootstrap.php";

function showReferentielList() {
    $search = $_GET['search'] ?? '';
    
    RenderView("referentiel/listeReferentiel", [
        'referentiels' => findAllReferentiel($search)
    ], "base.layout");
}

function getReferentielToEdit($id) {
    global $executeselect;
    $sql = "SELECT * FROM referentiel WHERE id_referentiel = :id";
    return $executeselect($sql, false, [':id' => $id]);
}

function addReferentielHandler() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ?controllers=referentiel&page=listeReferentiel");
        exit;
    }

    // Préparation des données pour validation
    $data = array_merge($_POST, ['image_file' => $_FILES['image'] ?? null]);

    $errors = validateData($data, 'referentiel');
    
    if (empty($errors)) {
        try {
          
                $escapedImage=null;
            // Vérifie si un fichier a bien été envoyé
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    
                    
                    $imageData = file_get_contents($_FILES['image']['tmp_name']);

                    
        
                  $conn = pg_connect("host=localhost dbname=ges_apprenant user=postgres password=niang@4693 port=8000");
                  
                    $escapedImage = pg_escape_bytea($conn,$imageData);


                } else {
                    echo "Aucun fichier n'a été téléchargé ou une erreur est survenue.";
                }

                //   var_dump( $escapedImage);
                //   die;
            // Ajout du référentiel
            $success = addReferentiels(
                trim($_POST['libelle']),
                trim($_POST['description']),
                $escapedImage,
                (int)$_POST['capacite'],
                trim($_POST['session'])
            );

            if ($success) {
                $_SESSION['success'] = "Référentiel ajouté avec succès";
                header("Location: ?controllers=referentiel&page=listeReferentiel&success=1");
                exit;
            }
        } catch (Exception $e) {
            $errors['global'] = $e->getMessage();
            error_log("Erreur addReferentiel: " . $e->getMessage());
        }
    }

    // Gestion des erreurs
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: ?controllers=referentiel&page=listeReferentiel&showModal=1");
    exit;
}

function updateReferentielHandler() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['referentiel_id'])) {
        header("Location: ?controllers=referentiel&page=listeReferentiel");
        exit;
    }

    $referentiel_id = (int)$_POST['referentiel_id'];
    $data = array_merge($_POST, ['image_file' => $_FILES['image'] ?? null]);
    $errors = validateData($data, 'referentiel');

    if (empty($errors)) {
        try {
            // Gestion de l'image
 $imageData = null;            
            // Cas 1: On veut supprimer l'image existante
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        $imageData = null;
    } 
    // Cas 2: Nouvelle image fournie
    elseif (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
        if ($imageInfo !== false) {
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
        } else {
            throw new Exception("Le fichier uploadé n'est pas une image valide");
        }
    }

            // Mise à jour du référentiel
            $success = updateReferentiel(
                $referentiel_id,
                trim($_POST['libelle']),
                trim($_POST['description']),
                $imageData,
                (int)$_POST['capacite'],
                trim($_POST['session'])
            );

            if ($success) {
                $_SESSION['success'] = "Référentiel mis à jour avec succès";
                header("Location: ?controllers=referentiel&page=listeReferentiel&success=1");
                exit;
            }
        } catch (Exception $e) {
            $errors['global'] = $e->getMessage();
            error_log("Erreur updateReferentiel: " . $e->getMessage());
        }
    }

    // Gestion des erreurs
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: ?controllers=referentiel&page=listeReferentiel&action=edit&referentiel_id=$referentiel_id&showModal=1");
    exit;
}

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