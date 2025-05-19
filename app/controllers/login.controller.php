<?php
require_once "../app/bootstrap/bootstrap.php";
global $executeselect, $execute;
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");
// Traitement de la déconnexion 
if (isset($_REQUEST["page"]) && $_REQUEST["page"] == "deconnexion") {
    // Nettoyage complet de la session
    $_SESSION = array();
    
    // Destruction du cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    session_regenerate_id(true);
    
    // Redirection vers la page de login avec en-têtes anti-cache
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Location: ".WEBROOB."?controllers=login&page=login");
    exit;
}

// Vérification de la session après le traitement de déconnexion
if (isset($_SESSION['utilisateur'])) {
    if (!isset($_REQUEST["page"]) || $_REQUEST["page"] == "login") {
        header("Location: " . WEBROOB . "?controllers=promotion&page=listePromotion");
        exit;
    }
}

// Traitement des autres pages
if (isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"] ?? "login";
    $errors = [];
    $resetErrors = [];
    $showModal = isset($_GET['showModal']);

    if ($page == "login") {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $data = [
                'showModal' => $showModal,
                'errors' => $errors,
                'resetErrors' => $resetErrors
            ];
            RenderView("security/login", $data, "security.layout");
        } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = trim($_POST["email"] ?? '');
            $mot_de_passe = trim($_POST["mot_de_passe"] ?? '');

            if (empty($email)) $errors["email"] = "L'email est obligatoire.";
            if (empty($mot_de_passe)) $errors["mot_de_passe"] = "Le mot de passe est obligatoire.";

            if (empty($errors)) {
                $user = $findUserConnect($email, $mot_de_passe);

                if ($user) {
                    $_SESSION["utilisateur"] = $user;
                    session_regenerate_id(true);
                    
                    switch ($user['role']) {
                        case 'Admin':
                            header("Location: " . WEBROOB . "?controllers=promotion&page=listePromotion");
                            break;
                        case 'Vigile':
                            // Redirection pour Vigile
                            break;
                        case 'Apprenant':
                            // Redirection pour Apprenant
                            break;
                        default:
                            header("Location: " . WEBROOB . "?controllers=dashboard&page=dashboard");
                            break;
                    }
                    exit;
                } else {
                    $errors["email"] = "Email ou mot de passe incorrect.";
                }
            }

            RenderView("security/login", ['errors' => $errors], "security.layout");
            exit;
        }
    } elseif ($page == "resetPassword") {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = trim($_POST["email"] ?? '');
            $newPassword = trim($_POST["newPassword"] ?? '');
            $confirmPassword = trim($_POST["confirmPassword"] ?? '');

            if (empty($email)) {
                $resetErrors['email'] = "L'email est obligatoire.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $resetErrors['email'] = "L'email n'est pas valide.";
            }

            if (empty($newPassword)) {
                $resetErrors['newPassword'] = "Le nouveau mot de passe est obligatoire.";
            } elseif (strlen($newPassword) < 8) {
                $resetErrors['newPassword'] = "Le mot de passe doit contenir au moins 8 caractères.";
            }

            if (empty($confirmPassword)) {
                $resetErrors['confirmPassword'] = "La confirmation du mot de passe est obligatoire.";
            } elseif ($newPassword !== $confirmPassword) {
                $resetErrors['confirmPassword'] = "Les mots de passe ne correspondent pas.";
            }

            if (empty($resetErrors)) {
                $userExists = $findUserByEmail($email);
                
                if ($userExists) {
                    $updateSuccess = $updateUserPassword($email, $newPassword);
                    
                    if ($updateSuccess) {
                        $_SESSION['success_message'] = "Votre mot de passe a été réinitialisé avec succès.";
                        header("Location: " . WEBROOB . "?controllers=login&page=login");
                        exit;
                    } else {
                        $resetErrors['general'] = "Une erreur s'est produite lors de la mise à jour du mot de passe.";
                    }
                } else {
                    $resetErrors['email'] = "Aucun compte trouvé avec cet email.";
                }
            }

            RenderView("security/login", [
                'resetErrors' => $resetErrors,
                'showModal' => true,
                'email' => $email
            ], "security.layout");
            exit;
        }
    }
}

// Par défaut, afficher la page de connexion
RenderView("security/login", [], "security.layout");