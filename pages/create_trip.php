<?php
/**
 * @file pages/create_trip.php
 * Page de création de trajet pour l'utilisateur connecté.
 */

// Initialisation du contrôleur (doit être fait dans index.php)
$tripController = new TripController($pdo); 
?>

<div class="container-fluid">
    <h2 class="mb-4 text-primary">Créer un Nouveau Trajet</h2>
    <p class="alert alert-info" role="alert">Vous êtes connecté en tant que <?= htmlspecialchars($_SESSION['prenom']) ?> <?= htmlspecialchars($_SESSION['nom']) ?>.</p>

    <?php 
    // Gestion des messages de résultat (success/error)
    $message = $_GET['result_message'] ?? null;
    if ($message) {
        if (strpos($message, 'succès') !== false || strpos($message, 'créé') !== false) {
            echo "<div class='alert alert-success' role='alert'>{$message}</div>";
        } else {
             echo "<div class='alert alert-danger' role='alert'>{$message}</div>";
        }
    }
    ?>

    <form method="POST" action="/create/trip">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
        <!-- Informations de l'utilisateur (Non modifiables, affichées en lecture seule) -->
        <div class="card p-3 mb-4 bg-light">
            <h6 class="mb-2">Vos Informations (Lecture Seule)</h6>
            <p><strong class="me-3">Nom :</strong> <?= htmlspecialchars($_SESSION['nom']) ?> (<?= htmlspecialchars($_SESSION['user_id']) ?>)</p>
            <p><strong class="me-3">Prénom :</strong> <?= htmlspecialchars($_SESSION['prenom']) ?></p>
            <p><strong class="me-3">Email :</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
            <p><strong class="me-3">Téléphone :</strong> <em>(Données RH à récupérer de la DB)</em></p>
        </div>

        <div class="row mb-4 g-3">
            <!-- AGENCE DE DÉPART (Sélection) -->
            <div class="col-md-6">
                <label for="agence_depart_id" class="form-label">Agence de départ <span class="text-danger">*</span></label>
                <select class="form-select" id="agence_depart_id" name="agence_depart_id" required>
                    <option value="">-- Sélectionner l'Agence --</option>
                    <?php 
                        // Correction: Récupérer la liste des agences depuis la DB (simulé ici)
                        $agencies = [];
                        $stmt = $pdo->query("SELECT id, nom FROM Agences ORDER BY nom");
                        while ($agency = $stmt->fetch()) {
                            echo "<option value='{$agency['id']}'>{$agency['nom']}</option>";
                        }
                    ?>
                </select>
            </div>

            <!-- AGENCE D'ARRIVÉE (Sélection) -->
            <div class="col-md-6">
                <label for="agence_arrivee_id" class="form-label">Agence d'arrivée <span class="text-danger">*</span></label>
                <select class="form-select" id="agence_arrivee_id" name="agence_arrivee_id" required>
                    <option value="">-- Sélectionner l'Agence --</option>
                    <?php 
                        // Correction: Récupérer la liste des agences depuis la DB (simulé ici)
                        $agencies = [];
                        $stmt = $pdo->query("SELECT id, nom FROM Agences ORDER BY nom");
                        while ($agency = $stmt->fetch()) {
                            echo "<option value='{$agency['id']}'>{$agency['nom']}</option>";
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="row mb-4 g-3">
            <!-- DATE DE DÉPART -->
            <div class="col-md-6">
                <label for="date_depart" class="form-label">Date et Heure de Départ <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="date_depart" name="date_depart" required min="<?= date('Y-m-d\TH:i', strtotime('+1 day'))) ?>">
            </div>

            <!-- DATE D'ARRIVÉE -->
            <div class="col-md-6">
                <label for="date_arrivee" class="form-label">Date et Heure d'Arrivée <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="date_arrivee" name="date_arrivee" required min="<?= date('Y-m-d\TH:i', strtotime('+1 day'))) ?>">
            </div>
        </div>

        <!-- NOUVEAUX CHAMPS OBLIGATOIRES -->
         <div class="row mb-4 g-3">
            <div class="col-md-6">
                <label for="total_places" class="form-label">Nombre total de places (Capacité) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="total_places" name="total_places" required min="1" value="10">
            </div>

            <div class="col-md-6">
                <label for="personne_contact" class="form-label">Contact sur place <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="personne_contact" name="personne_contact" required placeholder="Ex: Contact XXXXXX">
            </div>
        </div>


        <button type="submit" class="btn btn-primary w-100 mt-3">Proposer le Trajet</button>
    </form>

    <!-- Script JS pour améliorer l'UX (préremplissage de dates futures, validation client) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateDepart = document.getElementById('date_depart');
            const dateArrivee = document.getElementById('date_arrivee');

            // Initialisation des dates pour le test (empêcher les dates passées)
            let today = new Date();
            today.setDate(today.getDate() + 1); // Minimum demain
            let defaultDateLocal = today.toISOString().slice(0, 16);
            dateDepart.setAttribute('min', defaultDateLocal);

            // Écouteur de changement sur la date de départ pour forcer une date d'arrivée postérieure
            dateDepart.addEventListener('change', function() {
                let departTime = new Date(this.value).getTime();
                if (departTime) {
                    const minArriveeTime = departTime + (2 * 60 * 60 * 1000); // Départ + 2 heures en ms
                    document.getElementById('date_arrivee').setAttribute('min', new Date(minArriveeTime).toISOString().slice(0, 16));
                }
            });
        });
    </script>
</div>