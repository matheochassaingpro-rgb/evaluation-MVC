<?php
// src/Controllers/HomeController.php

namespace App\Controllers;

use App\Models\TrajetModel;

class HomeController
{
    public function index(): void
    {
        $trajetModel = new TrajetModel();
        $trajets = $trajetModel->getTrajetsDisponibles();
        
        // Appel de la vue
        require_once __DIR__ . '/../Views/home.php';
    }
}