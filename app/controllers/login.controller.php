<?php
require_once "../app/models/login.model.php";
require_once "../app/controllers/controller.php";

if (isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"] ?? "login";
    $errors = [];
    $resetErrors = [];
    $showModal = isset($_GET['showModal']);

    if ($page == "login") {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            // Afficher la page de connexion avec ou sans le modal
            $data = [
                'showModal' => $showModal,
                'errors' => $errors,
                'resetErrors' => $resetErrors
            ];
            RenderView("security/login", $data, "security.layout");
        } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Traitement du formulaire de connexion
            $email = trim($_POST["email"] ?? '');
            $mot_de_passe = trim($_POST["mot_de_passe"] ?? '');

            // Validation
            if (empty($email)) {
                $errors["email"] = "L'email est obligatoire.";
            }
            if (empty($mot_de_passe)) {
                $errors["mot_de_passe"] = "Le mot de passe est obligatoire.";
            }

            if (empty($errors)) {
                $user = $findUserConnect($email, $mot_de_passe);

                if ($user) {
                    $_SESSION["utilisateur"] = $user;
                    
                    // Redirection selon le rôle
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

            // Si erreurs, réafficher le formulaire avec les erreurs
            RenderView("security/login", ['errors' => $errors], "security.layout");
            exit;
        }
    } elseif ($page == "resetPassword") {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Traitement du formulaire de réinitialisation
            $email = trim($_POST["email"] ?? '');
            $newPassword = trim($_POST["newPassword"] ?? '');
            $confirmPassword = trim($_POST["confirmPassword"] ?? '');

            // Validation
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
                // Vérifier que l'email existe
                $userExists = $findUserByEmail($email);
                
                if ($userExists) {
                    // Mettre à jour le mot de passe
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

            // Si erreurs, réafficher le formulaire avec les erreurs
            RenderView("security/login", [
                'resetErrors' => $resetErrors,
                'showModal' => true,
                'email' => $email
            ], "security.layout");
            exit;
        }
    } elseif ($page == "deconnexion") {
        // Déconnexion
        session_unset();
        session_destroy();
        header("Location:".WEBROOB."?controllers=login&page=login");
        exit;
    }
}

// Par défaut, afficher la page de connexion
RenderView("security/login", [], "security.layout");