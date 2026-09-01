<?php
/**
 * @file components/header.php
 * Header commun pour les pages. Doit être inclus après l'initialisation de la PDO et des sessions.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Covoiturage Inter-Sites</title>
    <!-- Inclusion Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styles spécifiques pour le projet */
        body { padding-top: 20px; }
        .navbar-brand-covoiturage { font-weight: bold; color: #007bff !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow mb-4">
    <div class="container-fluid">
        <a class="navbar-brand navbar-brand-covoiturage" href="/">Covoit.</a>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Bienvenue, <?= htmlspecialchars(ucfirst($_SESSION["prenom"] ?? '')) ?> <?= htmlspecialchars(ucfirst($_SESSION["nom"] ?? '')) ?> (<?= ($_SESSION['role'] ?? '') === 'ROLE_ADMIN' ? 'Admin' : 'Utilisateur' ?>)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-Item" href="/logout">Déconnexion</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/users/login">Connexion</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container">