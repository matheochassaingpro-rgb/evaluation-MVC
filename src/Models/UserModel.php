<?php
// src/Models/UserModel.php

namespace App\Models;

use PDO;

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getUserByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM utilisateur WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetch();
    }
}