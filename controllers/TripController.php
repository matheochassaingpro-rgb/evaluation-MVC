<?php
/**
 * @file controllers/TripController.php
 * Gère la logique de création, modification et validation des trajets utilisateur.
 */

class TripController {
    private $pdo;
    protected $currentUserInfo = []; 

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        // Initialisation (dépendance à la session globale pour le contexte de l'utilisateur connecté)
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'ROLE_USER') {
            $this->currentUserInfo = [
                'id' => $_SESSION['user_id'],
                'nom' => $_SESSION['nom'],
                'prenom' => $_SESSION['prenom'],
            ];
        } else {
             $this->currentUserInfo = null;
        }
    }

    /**
     * Valide les données d'un trajet proposé par un utilisateur.
     * @param array $data Les données soumises du formulaire.
     * @return array|bool Tableau des IDs et dates validées ou false en cas d'erreur.
     */
    private function validateTripData(array $data) {
        if (empty($data['agence_depart_id']) || empty($data['agence_arrivee_id']) || 
            empty($data['date_depart']) || empty($data['date_arrivee'])) {
            return false; // Champs manquants
        }

        // Contrôle de cohérence: Départ et Arrivée différents
        if ($data['agence_depart_id'] == $data['agence_arrivee_id']) {
            return ['error' => 'Les agences de départ et d\'arrivée doivent être différentes.'];
        }

        // Contrôle de cohérence: Arrivée après Départ
        try {
            $dateDepart = new DateTime($data['date_depart']);
            $dateArrivee = new DateTime($data['date_arrivee']);
            if ($dateDepart >= $dateArrivee) {
                return ['error' => 'La date/heure d\'arrivée doit être strictement postérieure à la date/heure de départ.'];
            }
        } catch (Exception $e) {
            return ['error' => 'Format de date invalide fourni.'];
        }

        // On retourne les IDs et les dates formatées pour l'insertion, incluant les champs supplémentaires requis
        return [
            'depart_id' => (int)$data['agence_depart_id'],
            'arrivee_id' => (int)$data['agence_arrivee_id'],
            'date_depart' => $data['date_depart'], 
            'date_arrivee' => $data['date_arrivee'],
            'total_places' => filter_var($data['total_places'], FILTER_VALIDATE_INT) ?: 1,
            'personne_contact' => trim($data['personne_contact'])
        ];
    }

    /**
     * Crée un nouveau trajet en base de données.
     */
    public function createTrip(array $formData) {
        if (!$this->currentUserInfo) return ['success' => false, 'message' => "Session utilisateur invalide."];

        $validationResult = $this->validateTripData($formData);

        if (is_array($validationResult) && isset($validationResult['error'])) {
            return ['success' => false, 'message' => $validationResult['error']];
        } elseif ($validationResult === false) {
             return ['success' => false, 'message' => 'Veuillez remplir tous les champs obligatoires.'];
        }

        try {
            $this->pdo->beginTransaction();

            // CORRECTION : Utilisation des valeurs dynamiques de $formData au lieu de hardcoding (10 et 'Contact générique').
            // Règle métier: places_disponibles = total_places.
            $stmt = $this->pdo->prepare("INSERT INTO Trajets (agence_depart_id, agence_arrivee_id, date_depart, date_arrivee, total_places, places_disponibles, personne_contact, auteur_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $validationResult['depart_id'], 
                $validationResult['arrivee_id'], 
                $validationResult['date_depart'], 
                $validationResult['date_arrivee'], 
                $validationResult['total_places'], 
                $validationResult['total_places'], // CORRECTION: places_disponibles = total_places
                $validationResult['personne_contact'], 
                $this->currentUserInfo['id']
            ]);
            $tripId = $this->pdo->lastInsertId();

            $this->pdo->commit();
            return ['success' => true, 'message' => "Le trajet a été créé avec succès !"];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => "Erreur de base de données : " . $e->getMessage()];
        }
    }

    /**
     * Modifie les informations d'un trajet par son auteur.
     */
    public function updateTrip(int $tripId, array $formData) {
        if (!$this->currentUserInfo) return ['success' => false, 'message' => "Session utilisateur invalide."];

        $validationResult = $this->validateTripData($formData);
         if (!is_array($validationResult)) {
             return ['success' => false, 'message' => "Erreur de validation des dates/agences."];
         }

        try {
            // On vérifie d'abord que l'utilisateur est bien l'auteur avant toute modification (Sécurité)
            $stmtCheck = $this->pdo->prepare("SELECT * FROM Trajets WHERE id = ? AND auteur_id = ?");
            $stmtCheck->execute([$tripId, $this->currentUserInfo['id']]);
            $tripData = $stmtCheck->fetch();

            if (!$tripData) {
                return ['success' => false, 'message' => "Vous n'êtes pas autorisé à modifier ce trajet ou il n'existe pas."];
            }


            $this->pdo->beginTransaction();

            $updateStmt = $this->pdo->prepare("UPDATE Trajets SET agence_depart_id = ?, agence_arrivee_id = ?, date_depart = ?, date_arrivee = ? WHERE id = ? AND auteur_id = ?");
            $success = $updateStmt->execute([
                $validationResult['depart_id'], 
                $validationResult['arrivee_id'], 
                $validationResult['date_depart'], 
                $validationResult['date_arrivee'], 
                $tripId, // Le WHERE est sur l'ID ET le propriétaire (sécurité)
                $this->currentUserInfo['id']
            ]);

            if ($success) {
                $this->pdo->commit();
                return ['success' => true, 'message' => "Trajet modifié avec succès."];
            } else {
                 $this->pdo->rollBack();
                 return ['success' => false, 'message' => "Échec de la mise à jour due à une erreur DB."];
            }

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => "Erreur critique lors de la modification : " . $e->getMessage()];
        }
    }


    /**
     * Supprime un trajet appartenant à l'utilisateur.
     */
    public function deleteOwnTrip(int $tripId) {
         if (!$this->currentUserInfo) return ['success' => false, 'message' => "Session utilisateur invalide."];

        $stmt = $this->pdo->prepare("DELETE FROM Trajets WHERE id = ? AND auteur_id = ?");
        $result = $stmt->execute([$tripId, $this->currentUserInfo['id']]);
        
        if ($result && $stmt->rowCount() > 0) {
            return ['success' => true, 'message' => "Trajet supprimé avec succès."];
        } else {
            return ['success' => false, 'message' => "Vous n'êtes pas autorisé à supprimer ce trajet ou il n'existe pas."];
        }
    }
}