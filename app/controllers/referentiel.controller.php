<?php

require_once "../app/bootstrap/bootstrap.php";

$page = $_REQUEST["page"];
switch ($page) {
    case "listeReferentiel":
        $data = [
            'referentiels' => findAllReferentiel($_GET['search'] ?? ''),
            'errors' => $_SESSION['errors'] ?? [], // Récupère les erreurs de session
            'old' => $_SESSION['old'] ?? [], // Récupère les anciennes valeurs
        ];

        if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $data['referentielToEdit'] = getReferentielToEdit($_GET['referentiel_id']);
        }

        if (isset($_GET['showModal'])) {
            $data['showModal'] = true;
        }

        RenderView("referentiel/listeReferentiel", $data, "base.layout");
        break;
    case "addReferentiel":
        addReferentielHandler();
        break;

    case "updateReferentiel":
        updateReferentielHandler();
        break;

    default:
        header("Location: " . WEBROOB . "?controllers=referentiel&page=listeReferentiel");
        exit;
}
