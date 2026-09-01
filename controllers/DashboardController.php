<?php
/**
 * @file controllers/DashboardController.php
 * Gère la logique métier pour le tableau de bord (Admin et Accueil).
 */

class DashboardController {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les trajets disponibles pour la page d'accueil (places > 0, futurs).
     * @return array Liste des trajets.
     */
    public function listAvailableTrips(): array {
        $sql = "SELECT t.*, a_dep.nom AS agence_depart, a_arr.nom AS agence_arrivee FROM Trajets t
                JOIN Agences a_dep ON t.agence_depart_id = a_dep.id
                JOIN Agences a_arr ON t.agence_arrivee_id = a_arr.id
                WHERE t.places_disponibles > 0 AND t.date_depart >= NOW() ORDER BY date_depart ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère TOUS les trajets (utilisé par l'Admin Dashboard).
     * @return array Liste de tous les trajets.
     */
    public function listAllTrips(): array {
        $sql = "SELECT t.*, a_dep.nom AS agence_depart, a_arr.nom AS agence_arrivee FROM Trajets t
                JOIN Agences a_dep ON t.agence_depart_id = a_dep.id
                JOIN Agences a_arr ON t.agence_arrivee_id = a_arr.id
                ORDER BY t.date_depart ASC DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }


    /**
     * Récupère les détails complets d'un trajet spécifique.
     * @param int $tripId L'ID du trajet.
     * @return array|false Détails ou false si non trouvé.
     */
    public function getTripDetails(int $tripId) {
        $stmt = $this->pdo->prepare("SELECT * FROM Trajets WHERE id = ?");
        $stmt->execute([$tripId]);
        return $stmt->fetch(); 
    }
}