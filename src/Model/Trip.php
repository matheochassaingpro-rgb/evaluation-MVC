<?php
/**
 * @file src/Model/Trip.php
 * Classe représentant un Trajet planifié.
 */
class Trip {
    protected $id;
    protected $agency_depart_id;
    protected $agency_arrivee_id;
    protected $date_depart; 
    protected $date_arrivee; 
    protected $total_places;
    protected $places_disponibles;
    protected $personne_contact;
    protected $auteur_id;

    public function __construct($id, $departId, $arriveeId, $dateDepart, $dateArrivee, $totalPlaces, $placesDisponibles, $contact, $authorId) {
        $this->id = $id;
        $this->agency_depart_id = $departId;
        $this->agency_arrivee_id = $arriveeId;
        $this->date_depart = $dateDepart;
        $this->date_arrivee = $dateArrivee;
        $this->total_places = $totalPlaces;
        $this->places_disponibles = $placesDisponibles;
        $this->personne_contact = $contact;
        $this->auteur_id = $authorId;
    }

    // Getters essentiels pour la réutilisation du code.
    public function getId(): ?int { return $this->id; }
    public function getAgencyDepartId(): ?int { return $this->agency_depart_id; }
    public function getAgencyArriveeId(): ?int { return $this->agency_arrivee_id; }
}