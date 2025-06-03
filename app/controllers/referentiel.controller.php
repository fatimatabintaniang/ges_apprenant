<?php

require_once "../app/bootstrap/bootstrap.php";

$page = $_REQUEST["page"];
switch ($page) {
    case "listeReferentiel":
        $data = [
        'referentiels' => findAllReferentiel($_GET['search'] ?? ''),
        'errors' => $_SESSION['errors'] ?? [],
        'old' => $_SESSION['old'] ?? [],
        'showConfirmModal' => isset($_GET['ask_confirm']) && isset($_GET['referentiel_id']),
        'confirmReferentielId' => $_GET['referentiel_id'] ?? null,
        'search' => $_GET['search'] ?? ''
        ];

        // Nettoyez les erreurs après utilisation
        unset($_SESSION['errors'], $_SESSION['old']);

        if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $data['referentielToEdit'] = !empty($data['old']) ? $data['old'] : getReferentielToEdit($_GET['referentiel_id']);
        }

        RenderView("referentiel/listeReferentiel", $data, "base.layout");
        break;
    case "addReferentiel":
        addReferentielHandler();
        break;

    case "updateReferentiel":
        updateReferentielHandler();
        break;
case "archiveReferentiel":
        archiveReferentielHandler();
        break;

     

    default:
        header("Location: " . WEBROOB . "?controllers=referentiel&page=listeReferentiel");
        exit;
}
