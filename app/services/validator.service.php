<?php
//=================================fonction pour la validations==========================================
function validateData($data, $entityType)
{
    global $executeselect;
    $errors = [];

    // Validation commune ou spécifique selon l'entité
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

            if (empty(trim($data['image']))) {
                $errors['image'] = "L'image est obligatoire";
            }

            if (empty(trim($data['capacite']))) {
                $errors['capacite'] = "La capacité est obligatoire";
            } elseif (!is_numeric($data['capacite']) || (int)$data['capacite'] < 0) {
                $errors['capacite'] = "La capacité doit être un nombre positif";
            }

            if (empty(trim($data['session']))) {
                $errors['session'] = "La session est obligatoire";
            }
            break;

        default:
            throw new Exception("Type d'entité non reconnu pour la validation");
    }

    return $errors;
}
