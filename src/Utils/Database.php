<?php
/**
 * @file src/Utils/Database.php
 * Gestionnaire unique de connexion PDO.
 */
class Database {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            $config_path = __DIR__ . '/../../config/database.php'; 
            $config = require $config_path;

            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instance = new PDO($dsn, $config['user'], $config['password'], $options);
            } catch (PDOException $e) {
                throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}