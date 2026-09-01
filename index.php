<?php
/**
 * @file index.php - Point d'entrée principal (Bootstrap)
 */

session_start();

// 1. Configuration et connexion à la DB
require_once 'config/database.php';
require_once 'src/Utils/Database.php'; 

// 2. Inclusion manuelle des contrôleurs pour éviter les erreurs "Class not found"
require_once 'controllers/AuthController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/TripController.php';

try {
    $pdo = \Database::getInstance();
} catch (Exception $e) {
    die("Erreur Critique : Impossible de se connecter à la base de données. " . htmlspecialchars($e->getMessage()));
}

/**
 * Fonction de redirection pour simplifier le routage.
 */
function redirect($path, $params = []) {
    if (is_array($params)) {
        $_GET = array_merge($_GET, array_filter($params)); 
    }
    header("Location: " . $path);
    exit;
}

// --- Logique de routage principale ---
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($request_uri === '/users/login') {
    require 'controllers/AuthController.php'; 
} elseif ($request_uri === '/logout') {
    $auth = new AuthController($pdo);
    $auth->logout();
} else {
    // Détermination du contenu basé sur la URI et méthode HTTP
    if (preg_match('#^/dashboard/(agences|trajets)$#', $request_uri, $matches)) {
        $targetPage = $matches[1]; 
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            redirect('/users/login?error=1'); // Redirection si non-admin
        }

        // Le contrôleur est initialisé ici, mais la page est incluse pour le rendu.
        $controller = new DashboardController($pdo); 
        require "pages/dashboard_{$targetPage}.php";
        
    } elseif ($request_uri === '/create/trip') {
         if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROLE_USER') {
            redirect('/users/login?error=1'); // Redirection si non-utilisateur
        }
        require 'pages/create_trip.php';
        
    } elseif (preg_match('#^/dashboard/agences/(create|delete)$#', $request_uri, $matches)) {
         if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            redirect('/users/login?error=1'); 
        }
        $userController = new UserController($pdo);

        // --- Gestion des requêtes POST (Actions) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $matches[1] === 'create') {
            $nomAgence = trim($_POST['agency_name']);
            
            // CORRECTION: Appel de la vraie méthode (sans lui repasser $pdo)
            $result = $userController->createAgency($nomAgence);

            header("Location: /dashboard/agences?result_message=" . urlencode($result['message'] ?? 'Opération effectuée.'));
            exit;
        }
        // Gestion POST pour la suppression d'agence
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $matches[1] === 'delete') {
             $agencyId = $_POST['agency_id'] ?? null;
             
             // CORRECTION: Appel de la vraie méthode
             $result = $userController->deleteAgency($agencyId);

            header("Location: /dashboard/agences?result_message=" . urlencode($result['message']));
            exit;
        } else {
            // Affichage de la page de listing par défaut si GET request
            require 'pages/dashboard_agences.php'; 
        }

    } elseif (preg_match('#^/admin/(users|trajets)/.*$#', $request_uri, $matches)) {
         if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROLE_ADMIN') {
            redirect('/users/login?error=1'); 
        }
        
        $userController = new UserController($pdo);

        // Cas de suppression d'utilisateur (Exemple: /admin/users/delete/5)
         if (preg_match('#^.*users/delete/(\d+)$#', $request_uri, $matches)) {
             $userId = $matches[1];
             require 'pages/dashboard_users.php'; 
        } else if (preg_match('#.*trajets/delete/(\d+)$#', $request_uri, $matches)) {
            require 'pages/dashboard_trajets.php'; 
        } else {
             include 'components/header.php'; 
             echo "<h1 class='text-primary'>Tableau de bord Admin Général</h1>";
             require 'components/footer.php';
         }

    } else {
        // Page par défaut (Accueil)
        require 'pages/home.php'; 
    }
}