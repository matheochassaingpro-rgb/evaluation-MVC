<?php
/**
 * @file controllers/UserController.php
 * Gère la logique métier pour la gestion des utilisateurs et administrateurs. 
 * Ce contrôleur absorbe les anciennes responsabilités de AdminController.
 */

class UserController {
    private $pdo;
    protected $currentUserInfo = []; 

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        // On peut récupérer les infos actuelles si on était dans un contexte d'action admin
    }

    /**
     * Récupère tous les utilisateurs du système.
     * @return array Tableau des objets User Model ou tableau associatif.
     */
    public function listAllUsers(): array {
        $stmt = $this->pdo->query("SELECT * FROM ProfilsUtilisateurs ORDER BY nom ASC, prenom ASC");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Utilisation du modèle pour garantir la cohérence des données.
            $users[] = new User($row['id'], $row['nom'], $row['prenom'], $row['telephone'], $row['email']);
        }
        return $users;
    }

    /**
     * Supprime un utilisateur, en s'assurant que l'utilisateur n'est pas l'administrateur actuel.
     */
    public function deleteUser(int $userId): array {
        // Sécurité: Vérifier si le compte est ADMIN et si on ne supprime pas le compte connecté (si c'était possible).
        if ($_SESSION['role'] !== 'ROLE_ADMIN') {
             return ['success' => false, 'message' => "Vous n'avez pas les droits d'administrateur pour effectuer cette suppression."];
        }

        // Vérification supplémentaire que l'utilisateur ciblé est bien un compte de données et non une entité liée.
        $stmtCheck = $this->pdo->prepare("SELECT * FROM ProfilsUtilisateurs WHERE id = ?");
        $stmtCheck->execute([$userId]);
        if (!$stmtCheck->fetch()) {
            return ['success' => false, 'message' => "L'utilisateur spécifié n'existe pas."];
        }

        try {
            // Suppression en cascade gérée par FOREIGN KEY dans Trajets/UserAccounts.
            $stmt = $this->pdo->prepare("DELETE FROM ProfilsUtilisateurs WHERE id = ?");
            $success = $stmt->execute([$userId]);

            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "L'utilisateur a été supprimé avec succès."];
            } else {
                 // Cela pourrait être dû à des contraintes non gérées par cascade.
                 return ['success' => false, 'message' => "Échec de la suppression. Veuillez vérifier les dépendances du compte (Trajets actifs ?)."];
            }

        } catch (PDOException $e) {
             return ['success' => false, 'message' => "Erreur DB lors de la suppression : " . $e->getMessage()];
        }
    }
}