<?php
/**
 * @file config/database.php
 * Configuration des paramètres de connexion DB pour l'application Covoiturage Inter-Sites.
 */

// IMPORTANT : Les identifiants ne doivent jamais être en dur dans un projet réel. 
return [
    'host' => 'localhost',
    'dbname' => 'agence_covoiturage', 
    'user' => 'root', 
    'password' => '', // Mot de passe root (À sécuriser en production !)
];