<?php

require_once "../app/bootstrap/bootstrap.php";

$page = $_REQUEST["page"];

switch ($page) {
    case "listeApprenant":
        showApprenantList();
        break;

         case "addApprenant":
        addApprenatHandler();
        break;

         case "toggleStatus":
        toggleApprenantStatus();
        break;

        default:
        header("Location: " . WEBROOB . "?controllers=apprenant&page=listeApprenant");     
        break;

    }
