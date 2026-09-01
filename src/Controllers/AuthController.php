<?php
// src/Controllers/AuthController.php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController
{
    public function showLogin(): void
    {
        require_once __DIR__ . '/../Views/login.php';
    }

    public function processLogin(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($email);

        // On vérifie que l'utilisateur existe ET que le mot de passe correspond au hachage (remplace 'mot_de_passe' par ta colonne)
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            
            // On stocke l'utilisateur en session (sans le mot de passe pour des raisons de sécurité)
            unset($user['mot_de_passe']);
            $_SESSION['user'] = $user;
            
            // Redirection vers la page d'accueil
            header('Location: /');
            exit;
        } else {
            // Affichage basique en cas d'erreur pour tester
            echo "Email ou mot de passe incorrect. <a href='/connexion'>Réessayer</a>";
        }
    }
}