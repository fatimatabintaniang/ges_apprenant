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

function genererMatriculeIncremental()
{
    $prefix = 'E221-';

    // Connexion à la base (tu peux réutiliser ton système de connexion)
    $conn = pg_connect("host=localhost dbname=ges_apprenant user=postgres password=niang@4693 port=8000");

    // Récupérer le dernier matricule enregistré
    $result = pg_query($conn, "SELECT matricule FROM apprenant ORDER BY id_apprenant DESC LIMIT 1");

    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $dernierMatricule = $row['matricule'];

        // Extraire le numéro et l’incrémenter
        $numero = (int)substr($dernierMatricule, strlen($prefix));
        $nouveauNumero = str_pad($numero + 1, 4, '0', STR_PAD_LEFT);
    } else {
        // Premier matricule
        $nouveauNumero = '0001';
    }

    return $prefix . $nouveauNumero;
}


function addApprenatHandler() {
    // Récupérer la promotion active
    $activePromotion = getActivePromotion();
     
    global $dd;
   
    if (!$activePromotion) {
        // Gérer le cas où aucune promotion n'est active
        $errors['global'] = "Aucune promotion active n'est disponible. Veuillez activer une promotion avant d'ajouter un apprenant.";
        RenderView("apprenant/listeApprenant", [
            'apprenants' => findAllApprenant($_GET['statusFilter'] ?? 'all', $_GET['search'] ?? ''),
            'promotions' => findAllPromotion(),
            'referentiels' => findAllReferentiel(),
            'errors' => $errors,
            'showModal' => true
        ], "base.layout");
        exit;
    }

    // Chargement initial des données nécessaires
    $baseData = [
        'apprenants' => findAllApprenant($_GET['statusFilter'] ?? 'all', $_GET['search'] ?? ''),
        'promotions' => findAllPromotion(),
        'referentiels' => findAllReferentiel(),
        'activePromotion' => $activePromotion, 
        'old' => [],
        'errors' => [],
        'showModal' => isset($_GET['showModal']) || (isset($_POST['_method']) && $_POST['_method'] === 'POST')
    ];

    // Traitement de la soumission du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = array_merge($_POST, [
            'image_file' => $_FILES['image'] ?? null
        ]);

        // Validation des données
        $errors = validateData($data, 'apprenant');

        if (empty($errors)) {
            // Gestion de l'image
            $escapedImage = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageData = file_get_contents($_FILES['image']['tmp_name']);
                $conn = pg_connect("host=localhost dbname=ges_apprenant user=postgres password=niang@4693 port=8000");
                $escapedImage = pg_escape_bytea($conn, $imageData);
            }

            // Génération du matricule
            $matricule = genererMatriculeIncremental();

    $success = addApprenantWithTuteur(
    $_POST['nom'],
    $_POST['prenom'],
    $_POST['date_naissance'],
    $_POST['lieu_naissance'],
    $_POST['adresse'],
    $_POST['telephone'],
    $_POST['email'],
    $_POST['mot_de_passe'],
    $_POST['nom_tuteur'],
    $_POST['prenom_tuteur'],
    $_POST['telephone_tuteur'],
    $_POST['adresse_tuteur'],
    $_POST['lien_parente'],
    $escapedImage,
    $_POST['id_referentiel'],
    $activePromotion['id_promotion'],
    $matricule
);
         
            if ($success) {
                
                header("Location: " . ($_POST['redirect'] ?? '?controllers=apprenant&page=listeApprenant') . "&success=1");
                exit;
            } else {
                $errors['global'] = "Une erreur est survenue lors de l'ajout";
            }
        }

        // Préparation des données en cas d'erreur
        $baseData['errors'] = $errors;
        $baseData['old'] = $_POST;
        $baseData['showModal'] = true;
    }

    RenderView("apprenant/listeApprenant", $baseData, "base.layout");
    exit;
}
