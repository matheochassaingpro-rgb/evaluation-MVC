<?php
// src/Models/Database.php

namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                // Utilisation de 127.0.0.1 comme lors de ton setup réussi
                self::$instance = new PDO(
                    "mysql:host=127.0.0.1;dbname=covoiturage_entreprise;charset=utf8mb4",
                    "root",
                    "Matheowarior123!", // Laisse vide si tu n'as pas de mot de passe
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                die("Erreur PDO : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}