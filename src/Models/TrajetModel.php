<?php
// src/Models/TrajetModel.php

namespace App\Models;

use PDO;

class TrajetModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getTrajetsDisponibles(): array
    {
        $sql = "
            SELECT 
                t.id_trajet, 
                t.date_heure_depart, 
                t.date_heure_arrivee, 
                t.nb_places_dispo,
                ad.nom_ville AS ville_depart,
                aa.nom_ville AS ville_arrivee
            FROM trajet t
            INNER JOIN agence ad ON t.id_agence_depart = ad.id_agence
            INNER JOIN agence aa ON t.id_agence_arrivee = aa.id_agence
            WHERE t.nb_places_dispo > 0 
            ORDER BY t.date_heure_depart ASC
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}