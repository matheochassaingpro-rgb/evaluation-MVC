<?php
/**
 * @file pages/dashboard_trajets.php
 * Page de gestion des Trajets pour l'Administrateur.
 */

// Assurez-vous que DashboardController est chargé et utilisable ici, ou mieux : 
$adminTripController = new AdminController($pdo); // Utilisation du contrôleur admin général
?>

<div class="container-fluid">
    <h2 class="mb-4 text-primary">Gestion des Trajets (Admin)</h2>

    <div class="alert alert-warning" role="alert">
        Attention : La suppression d'un trajet est définitive. Assurez-vous de disposer des informations correctes avant de procéder.
    </div>

    <!-- Liste des trajets -->
    <div class="card shadow p-4">
        <h5 class="mb-3">Liste Complète des Trajets</h5>
        <table class="table table-bordered table-striped table-hover">
            <thead class="bg-dark text-white">
                <tr>
                    <th>ID</th>
                    <th>Départ (Agence)</th>
                    <th>Arrivée (Agence)</th>
                    <th>Date Départ</th>
                    <th>Places Disponibles</th>
                    <th>Auteur ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Utilisation du contrôleur admin pour récupérer la liste complète et paginée (omise ici par concision)
                $trips = $adminTripController->listAllTrips(); // Supposons que cette méthode existe ou est appelée.
                foreach ($trips as $trip): ?>
                    <tr data-id="<?= htmlspecialchars($trip['id']) ?>">
                        <td><?= htmlspecialchars($trip['id']) ?></td>
                        <td><?= htmlspecialchars($trip['agence_depart']) ?></td>
                        <td><?= htmlspecialchars($trip['agence_arrivee']) ?></td>
                        <td><?= date('d/m', strtotime($trip['date_depart'])) ?></td>
                        <td class="fw-bold text-success"><?= $trip['places_disponibles'] ?> / <?= $trip['total_places'] ?></td>
                        <td><?= htmlspecialchars($trip['auteur_id']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#detailModal" data-trip-id="<?= htmlspecialchars($trip['id']) ?>">Voir Détails</button>
                            <form method="POST" action="/admin/trajets/delete/<?= htmlspecialchars($trip['id']) ?>" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce trajet ?');">
                                <input type="hidden" name="_csrf" value="<?= $_SESSION['csrf_token'] ?? 'dummy' ?>"> 
                                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- MODALE DE DÉTAILS DU TRAJET (Réutilisation de la structure) -->
     <div class="modal fade" id="detailModalAdmin" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
          <!-- ... Contenu identique à home.php mais plus riche en données administratives ... -->
      </div>
</div>

<?php 
/**
 * Note: La méthode listAllTrips doit être ajoutée au DashboardController ou un nouveau TripAdminController.
 */
?>