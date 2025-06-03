<?php
require_once "../app/bootstrap/bootstrap.php";

function showApprenantList()
{

    $filter = $_GET['statusFilter'] ?? 'all';
    $search = $_GET['search'] ?? '';
    $referentielFilter = $_GET['referentielFilter'] ?? 'all';
    $showModal = isset($_GET['showModal']);

    $referentiels = findAllReferentiel();


    RenderView("apprenant/listeApprenant", [
        'apprenants' => findAllApprenant($filter, $search, $referentielFilter),
        'referentiels' => $referentiels
    ], "base.layout");
}

function toggleApprenantStatus()
{
    $id = $_GET['id'] ?? null;
    $currentStatus = $_GET['current_status'] ?? null;
    $redirect = $_GET['redirect'] ?? '?controllers=apprenant&page=listeApprenant';

    if ($id && $currentStatus) {
        // Déterminer le nouveau statut
        $newStatus = ($currentStatus === 'actif') ? 'remplace' : 'actif';

        // Appel de la fonction du modèle
        $success = updateApprenantStatus($id, $newStatus);

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

function addApprenatHandler()
{

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $tuteurData = [
            'nom' => $_POST['nom_tuteur'] ?? '',
            'prenom' => $_POST['prenom_tuteur'] ?? '',
            'telephone' => $_POST['tuteur_telephone'] ?? '',
            'adresse' => $_POST['tuteur_adresse'] ?? '',
            'lieu_parente' => $_POST['tuteur_lien'] ?? ''
        ];

        $data = array_merge($_POST, [
            'image_file' => $_FILES['image'] ?? null,
            'tuteurs' => [$tuteurData]
        ]);
        $errors = validateData($data, 'apprenant');

        if (empty($errors)) {

            $escapedImage = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {


                $imageData = file_get_contents($_FILES['image']['tmp_name']);



                $conn = pg_connect("host=localhost dbname=ges_apprenant user=postgres password=niang@4693 port=8000");

                $escapedImage = pg_escape_bytea($conn, $imageData);
            } else {
                echo "Aucun fichier n'a été téléchargé ou une erreur est survenue.";
            }
            $success = addApprenantWithTuteur(
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['date_naissance'],
                $_POST['lieu_naissance'],
                $_POST['adresse'],
                $_POST['telephone'],
                $_POST['email'],
                $escapedImage,
                $_POST['tuteurs'] ?? []


            );

            if ($success) {
                $redirect = $_POST['redirect'] ?? '?controllers=apprenant&page=listeApprenant';
                header("Location: $redirect&success=1");
                exit;
            } else {
                $errors['global'] = "Une erreur est survenue lors de l'ajout";
            }
        }

        // En cas d'erreur
        RenderView("apprenant/listeApprenant", [
            'apprenants' => findAllApprenant($_GET['statusFilter'] ?? 'all', $_GET['search'] ?? ''),
            'errors' => $errors,
            'old' => $_POST,
            'showModal' => true
        ], "base.layout");
        exit;
    }
}
