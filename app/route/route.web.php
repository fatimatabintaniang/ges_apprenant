<?php
session_start();
define ("WEBROOB","http://fatima.niang.ecole221.sn:8001");
require_once "../app/models/model.php";



function run (){
    $controllers=[
        "promotion"=>"../app/controllers/promotion.controller.php",
        "login"=>"../app/controllers/login.controller.php"
    ];
    
    if (isset($_GET["controllers"])) {
        $controller=$_GET["controllers"];
        if(array_key_exists($controller,$controllers)){
            
                require_once $controllers[$controller];
    
        }else{
            echo("Controler inexistant");
        }
    }else { 
            require_once "../app/controllers/login.controller.php";
            exit;
        }
    
}
