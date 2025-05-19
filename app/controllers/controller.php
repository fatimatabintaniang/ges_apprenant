<?php
function RenderView(string $views, array $data = [], string $layout) {
        ob_start();
        extract($data);
        require_once "../app/views/$views.html.php";
        $content = ob_get_clean();
        require_once "../app/views/layout/$layout.php";
    };

    function ControlePage(string $controllers) {
        // Autoriser l'accès aux pages de login sans redirection
        if ($controllers === 'login') {
            return;
        }
        
        // Rediriger vers le login si pas de session
        if (!isset($_SESSION['utilisateur'])) {
            header("Location: " . WEBROOB . "?controllers=login&page=login");
            exit();
        }
        
        // Empêcher l'accès au login si déjà connecté
        if (isset($_SESSION['utilisateur']) && $controllers === 'login') {
            header("Location: " . WEBROOB . "?controllers=promotion&page=listePromotion");
            exit();
        }
    };

    