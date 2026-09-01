<?php
/**
 * @file controllers/AuthController.php
 * Gère la logique de connexion et déconnexion des utilisateurs.
 */

class AuthController {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Traite le formulaire de connexion. Renvoie un tableau contenant 'error' ou les données de redirection/success.
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['status' => 'redirect', 'path' => '/users/login?message=method']; 
        }

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            return ['error' => true, 'code' => 2, 'message' => 'Veuillez remplir tous les champs.'];
        }

        try {
            // Récupération sécurisée du profil et des informations de connexion
            $stmt = $this->pdo->prepare("SELECT p.*, u.* FROM ProfilsUtilisateurs p JOIN UserAccounts u ON p.id = u.user_id WHERE p.email = ?");
            $stmt->execute([$email]);
            $userData = $stmt->fetch();

            if (!$userData) {
                return ['error' => true, 'code' => 3, 'message' => 'Aucun compte trouvé avec cette adresse e-mail.'];
            }

            // Vérification du mot de passe (Le hash doit être généré lors de l'inscription/réinitialisation)
            if (password_verify($password, $userData['password_hash'])) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['nom'] = $userData['nom'];
                $_SESSION['prenom'] = $userData['prenom'];
                $_SESSION['email'] = $userData['email'];
                $_SESSION['role'] = $userData['role'];

                if ($userData['role'] === 'ROLE_ADMIN') {
                    header("Location: /dashboard/agences"); exit;
                } else {
                    header("Location: /"); exit;
                }
            } else {
                return ['error' => true, 'code' => 4, 'message' => 'Identifiants incorrects.'];
            }
        } catch (PDOException $e) {
            // Gestion des erreurs DB pour le logging interne, pas affichées à l'utilisateur.
            return ['error' => true, 'code' => 5, 'message' => "Erreur serveur lors de la connexion."];
        }
    }

    /**
     * Gère la déconnexion.
     */
    public function logout() {
        session_unset();
        session_destroy();
    }
}