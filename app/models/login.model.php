<?php
require_once "../app/models/model.php";
$findUserConnect = function($email, $mot_de_passe) { 
    global $executeselect;
    $sql = "
        SELECT *
        FROM utilisateur u
        WHERE u.email = :email AND u.mot_de_passe = :mot_de_passe
    ";
    
    $params = [
        'email' => $email,
        'mot_de_passe' => $mot_de_passe
    ];
    
    try {
        return $executeselect($sql, false, $params);
    } catch (PDOException $e) {
        echo "Erreur lors de la connexion : " . $e->getMessage();
        return null;
    }
};

$findUserByEmail = function($email) {
    global $executeselect;
    $sql = "SELECT * FROM utilisateur WHERE email = :email";
    $params = ['email' => $email];
    
    try {
        return $executeselect($sql, false, $params);
    } catch (PDOException $e) {
        error_log("Erreur lors de la recherche de l'utilisateur : " . $e->getMessage());
        return null;
    }
};

$updateUserPassword = function($email, $newPassword) {
    global $execute;
    $sql = "UPDATE utilisateur SET mot_de_passe = :mot_de_passe WHERE email = :email";
    $params = [
        'email' => $email,
        'mot_de_passe' => $newPassword 
    ];
    
    try {
        return $execute($sql, $params);
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour du mot de passe : " . $e->getMessage());
        return false;
    }
};