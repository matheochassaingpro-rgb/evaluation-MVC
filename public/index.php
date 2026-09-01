<?php
// public/index.php

session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Buki\Router\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;

$router = new Router();

// ==========================================
// ROUTES DE L'APPLICATION
// ==========================================

// --- Page d'accueil (Liste des trajets) ---
$router->get('/', function() {
    $controller = new HomeController();
    $controller->index();
});

// --- Authentification ---
// 1. Affichage du formulaire de connexion
$router->get('/connexion', function() {
    $controller = new AuthController();
    $controller->showLogin();
});

// 2. Traitement du formulaire à la soumission
$router->post('/connexion', function() {
    $controller = new AuthController();
    $controller->processLogin();
});

// ==========================================
// EXECUTION DU ROUTEUR
// ==========================================
$router->run();