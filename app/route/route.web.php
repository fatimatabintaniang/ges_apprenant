<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define ("WEBROOB","http://fatima.niang.ecole221.sn:8001");
require_once "../app/models/model.php";
require_once "../app/controllers/controller.php";

function run (){
    $controllers=[
        "promotion"=>"../app/controllers/promotion.controller.php",
        "referentiel"=>"../app/controllers/referentiel.controller.php",
        "apprenant"=>"../app/controllers/apprenant.controller.php",
        "login"=>"../app/controllers/login.controller.php"
    ];
    
    $controller = $_GET["controllers"] ?? "login";
        if(array_key_exists($controller,$controllers)){
                ControlePage(  $controller);
                require_once $controllers[$controller];
           
        }else{
            echo("Controler inexistant");
        }
}



