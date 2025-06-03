<?php
//=================================fonction pour la validations=========================
function validateData($data, $entityType)
{
    global $executeselect;
    $errors = [];

    //================= Validation spécifique selon l'entité============================
    switch ($entityType) {
        case 'promotion':
            // Validation du nom
            if (empty($data['nom'])) {
                $errors['nom'] = "Le nom de la promotion est obligatoire";
            } else {
                $sql = "SELECT COUNT(*) as count FROM promotion WHERE nom = :nom";
                $params = [':nom' => $data['nom']];

                // Ajout de la condition d'exclusion en mode édition
                if (!empty($data['promotion_id'])) {
                    $sql .= " AND id_promotion != :current_id";
                    $params[':current_id'] = $data['promotion_id'];
                }

                $count = $executeselect($sql, false, $params);
                if ($count && $count['count'] > 0) {
                    $errors['nom'] = "Ce nom de promotion existe déjà";
                }
            }

            // Validation de l'image (seulement pour l'ajout ou si nouvelle image fournie)
            if (empty($data['promotion_id']) && empty($data['image_file']['tmp_name'])) {
                $errors['image'] = "L'image est obligatoire";
            } elseif (!empty($data['image_file']['tmp_name'])) {
                // Vérifier que c'est bien une image
                $allowed = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($data['image_file']['type'], $allowed)) {
                    $errors['image'] = "Type de fichier non autorisé (seulement JPG, PNG, GIF)";
                } elseif ($data['image_file']['size'] > 2000000) { // 2MB max
                    $errors['image'] = "L'image est trop volumineuse (max 2MB)";
                }
            }

            // Validation des dates
            if (empty($data['date_debut']) || !strtotime($data['date_debut'])) {
                $errors['date_debut'] = "Date de début invalide";
            }

            if (empty($data['date_fin']) || !strtotime($data['date_fin'])) {
                $errors['date_fin'] = "Date de fin invalide";
            } elseif (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
                $errors['date_fin'] = "La date de fin doit être postérieure à la date de début";
            }

            // Validation des référentiels
            if (empty($data['referentiels'])) {
                $errors['referentiels'] = "Sélectionner au moins un référentiel";
            }
            break;

        case 'referentiel':
            // Validation du libellé
            if (empty(trim($data['libelle']))) {
                $errors['libelle'] = "Le libelle est obligatoire";
            } else {
                $sql = "SELECT COUNT(*) as count FROM referentiel WHERE libelle = :libelle";
                $params = [':libelle' => $data['libelle']];

                // Ajout de la condition d'exclusion en mode édition
                if (!empty($data['referentiel_id'])) {
                    $sql .= " AND id_referentiel != :current_id";
                    $params[':current_id'] = $data['referentiel_id'];
                }

                $count = $executeselect($sql, false, $params);
                if ($count && $count['count'] > 0) {
                    $errors['libelle'] = "Ce libelle de referentiel existe déjà";
                }
            }

            // Validation description
            if (empty(trim($data['description']))) {
                $errors['description'] = "La description est obligatoire";
            }

            // Validation de l'image (seulement pour l'ajout ou si nouvelle image fournie)
            if (empty($data['referentiel_id']) && empty($data['image_file']['tmp_name'])) {
                $errors['image'] = "L'image est obligatoire";
            } elseif (!empty($data['image_file']['tmp_name'])) {
                // Vérifier que c'est bien une image
                $allowed = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($data['image_file']['type'], $allowed)) {
                    $errors['image'] = "Type de fichier non autorisé (seulement JPG, PNG, GIF)";
                } elseif ($data['image_file']['size'] > 2000000) { // 2MB max
                    $errors['image'] = "L'image est trop volumineuse (max 2MB)";
                }
            }

            //validation capacite

            if (empty(trim($data['capacite']))) {
                $errors['capacite'] = "La capacité est obligatoire";
            } elseif (!is_numeric($data['capacite']) || (int)$data['capacite'] < 0) {
                $errors['capacite'] = "La capacité doit être un nombre positif";
            }

            if (empty(trim($data['session']))) {
                $errors['session'] = "La session est obligatoire";
            }
            break;

        case 'apprenant':
            // Validation des champs obligatoires
            if (empty(trim($data['nom']))) {
                $errors['nom'] = "Le nom de l'apprenant est obligatoire";
            }

            if (empty(trim($data['prenom']))) {
                $errors['prenom'] = "Le prénom de l'apprenant est obligatoire";
            }

            // Validation email
            if (empty(trim($data['email']))) {
                $errors['email'] = "L'email est obligatoire";
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Format d'email invalide";
            } else {
                // Vérification unicité email
                $sql = "SELECT COUNT(*) as count FROM apprenant WHERE email = :email";
                $params = [':email' => $data['email']];

                if (!empty($data['apprenant_id'])) {
                    $sql .= " AND id_apprenant != :current_id";
                    $params[':current_id'] = $data['apprenant_id'];
                }

                $count = $executeselect($sql, false, $params);
                if ($count && $count['count'] > 0) {
                    $errors['email'] = "Cet email est déjà utilisé par un autre apprenant";
                }
            }

            // Validation téléphone
            if (empty(trim($data['telephone']))) {
                $errors['telephone'] = "Le telephone est obligatoire";
            }

            // Validation date de naissance
            if (empty($data['date_naissance'])) {
                $errors['date_naissance'] = "La date de naissance est obligatoire";
            } elseif (!strtotime($data['date_naissance'])) {
                $errors['date_naissance'] = "Date de naissance invalide";
            } elseif (strtotime($data['date_naissance']) > strtotime('-16 years')) {
                $errors['date_naissance'] = "L'apprenant doit avoir au moins 16 ans";
            }

             // Validation adresse
            if (empty(trim($data['lieu_naissance']))) {
                $errors['lieu_naissance'] = "Le lieu de naissance est obligatoire";
            }

            // Validation adresse
            if (empty(trim($data['adresse']))) {
                $errors['adresse'] = "L'adresse est obligatoire";
            }

            // Validation image (si fournie)
            if (!empty($data['image_file']['tmp_name'])) {
                $allowed = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($data['image_file']['type'], $allowed)) {
                    $errors['image'] = "Type de fichier non autorisé (seulement JPG, PNG, GIF)";
                } elseif ($data['image_file']['size'] > 2000000) {
                    $errors['image'] = "L'image est trop volumineuse (max 2MB)";
                }
            } elseif (empty($data['apprenant_id'])) {
                $errors['image'] = "L'image est obligatoire pour un nouvel apprenant";
            }

            // Validation tuteur(s)
              if (empty(trim($data['nom_tuteur']))) {
                $errors['nom_tuteur'] = "Le nom du tuteur est obligatoire";
            }

              if (empty(trim($data['prenom_tuteur']))) {
                $errors['prenom_tuteur'] = "Le prenom du tuteur est obligatoire";
            }

              if (empty(trim($data['telephone_tuteur']))) {
                $errors['telephone_tuteur'] = "Le telephone du tuteur est obligatoire";
            }
              if (empty(trim($data['lien']))) {
                $errors['lien'] = "Le lien du tuteur est obligatoire";
            }

              if (empty(trim($data['adresse_tuteur']))) {
                $errors['adresse_tuteur'] = "L'adresse' du tuteur est obligatoire";
            }
        
            break;

        default:
            throw new Exception("Type d'entité non reconnu pour la validation");
    }

    return $errors;
}
