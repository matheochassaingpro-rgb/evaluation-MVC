<?php
/**
 * @file pages/login.php
 * Affichage du formulaire de connexion utilisateur.
 */

// Les variables globales ($pdo, $is_logged_in, etc.) doivent être disponibles par le routeur qui inclut cette page.

?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow p-4 mt-5">
            <h2 class="text-center mb-4 text-primary">Connexion à l'Intranet Covoiturage</h2>
            
            <?php 
            // Gestion des messages d'erreurs/succès (Simplifié pour ce contexte)
            $message = $_GET['message'] ?? null;
            $error_code = $_GET['error'] ?? null;

            if ($message == 'logout') {
                 echo "<div class='alert alert-success' role='alert'>Vous avez été déconnecté avec succès.</div>";
            } elseif ($error_code) {
                $messages = [
                    1 => "L'accès à cette page est restreint.",
                    2 => "Veuillez remplir tous les champs email et mot de passe.",
                    3 => "Aucun compte trouvé avec cette adresse e-mail.",
                    4 => "Identifiants incorrects. Vérifiez votre e-mail et mot de passe.",
                    5 => "Erreur serveur lors de la connexion. Veuillez réessayer plus tard."
                ];
                $error_message = $messages[$error_code] ?? "Une erreur est survenue.";
                echo "<div class='alert alert-danger' role='alert'>{$error_message}</div>";
            }
            ?>

            <form method="POST" action="/users/login">
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se Connecter</button>
            </form>
        </div>
    </div>
</div>
<?php 
/** 
 * Nettoyé : Suppression des commentaires inutiles et simplification de la gestion des messages.
 */
?>