<?php

require_once "../app/bootstrap/bootstrap.php";

$page = $_REQUEST["page"];
switch ($page) {
    case "listePromotion":
        showPromotionList();
        break;

        default:
        header("Location: " . WEBROOB . "?controllers=promotion&page=listePromotion");     
        break;

    }
