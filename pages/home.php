<?php
require_once __DIR__ . '/../components/header.php';
?>
<?php
/**
 * @file pages/home.php
 * Page d'accueil : Liste des trajets disponibles et affichage modale des détails.
 */

// Initialisation du contrôleur (doit être fait dans index.php ou passé)
$dashboardController = new DashboardController($pdo); 

// Récupération des données à afficher
$trips = $dashboardController->listAvailableTrips();
?>

<div class="container-fluid">
    <h2 class="mb-4 text-primary">Trajets Covoiturage Proposés</h2>

    <?php if (empty($trips)): ?>
        <div class="alert alert-info" role="alert">
            Aucun trajet planifié disponible actuellement. N'hésitez pas à créer le vôtre !
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Liste des trajets (Tableau) -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Départ</th>
                                    <th>Date Départ</th>
                                    <th>Arrivée</th>
                                    <th>Date Arrivée</th>
                                    <th>Places Disponibles</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="trip-list">
                                <?php foreach ($trips as $trip): ?>
                                <tr data-trip-id="<?= htmlspecialchars($trip['id']) ?>">
                                    <td class="col-2"><?= htmlspecialchars($trip['agence_depart']) ?></td>
                                    <td class="col-2"><?= date('d/m', strtotime($trip['date_depart'])) ?></td>
                                    <td class="col-2"><?= htmlspecialchars($trip['agence_arrivee']) ?></td>
                                    <td class="col-2"><?= date('d/m', strtotime($trip['date_arrivee'])) ?></td>
                                    <td class="col-2 fw-bold text-success"><?= $trip['places_disponibles'] ?> / <?= $trip['total_places'] ?></td>
                                    <td class="col-2">
                                        <button type="button" class="btn btn-sm btn-info view-details" data-toggle="modal" data-target="#detailModal" data-trip-id="<?= htmlspecialchars($trip['id']) ?>">Détails</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite pour les infos utilisateur / CTA (Optionnel) -->
            <div class="col-lg-4">
                <div class="card shadow sticky-top" style="top: 20px;">
                    <div class="card-body text-center p-3">
                        <h4 class="mb-3">Bienvenue !</h4>
                        <?php if ($role == 'ROLE_USER'): ?>
                            <p>Vous êtes connecté. Vous pouvez <a href="/create/trip" class="text-primary fw-bold">proposer un trajet</a> pour organiser vos déplacements.</p>
                        <?php else: ?>
                            <p>Accès réservé aux Administrateurs.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODALE DE DÉTAILS DU TRAJET (Pour l'affichage complémentaire) -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">Détails du Trajet <span class="badge bg-warning text-dark">#<span id="displayTripId"></span></span></h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body row">
                  <div class="col-md-6 border-end pe-3">
                      <h5 class="mb-3">Informations de Base</h5>
                      <p><strong class="me-2">Départ :</strong> <span id="detailAgencyDepart"></span></p>
                      <p><strong class="me-2">Arrivée :</strong> <span id="detailAgencyArrivee"></span></p>
                      <p><strong class="me-2">Date/Heure Départ :</strong> <span id="detailDateDepart"></span></p>
                      <p><strong class="me-2">Date/Heure Arrivée :</strong> <span id="detailDateArrivee"></span></p>
                  </div>
                  <div class="col-md-6">
                      <h5 class="mb-3">Capacité et Contact</h5>
                      <hr>
                      <p><strong class="me-2">Places Disponibles :</strong> <span id="detailPlacesDisponibles" class="fs-4 fw-bold text-success"></span> / <span id="detailTotalPlaces"></span></p>
                      <p><strong class="me-2">Personne à contacter :</strong> <a href="mailto:<?php echo htmlspecialchars($trip['personne_contact']) ?>"><?= htmlspecialchars($trip['personne_contact']) ?></a></p>

                      <!-- Bloc des informations de l'auteur (Visuel) -->
                      <h6 class="mt-4">Auteur du Trajet</h6>
                       <div class="card p-3 bg-light">
                           <p><strong class="me-2">Identité :</strong> <span id="detailAuthorName"></span> (<span id="detailAuthorEmail"></span>)</p>
                           <p><strong class="me-2">Téléphone :</strong> <span id="detailAuthorPhone"></span></p>
                       </div>

                      <!-- Boutons d'action pour l'auteur (dépend de la vérification des IDs) -->
                      <div class="mt-4">
                          <?php if ($_SESSION['user_id'] == $trip['auteur_id']): ?>
                              <button type="button" class="btn btn-warning me-2">Modifier le trajet</button>
                              <button type="button" class="btn btn-danger">Supprimer le trajet</button>
                          <?php else: ?>
                            <button type="button" class="btn btn-secondary disabled">Modification/Suppression réservée au créateur</button>
                         <?php endif; ?>
                      </div>
                  </div>
              </div>
            </div>
        </div>

    <?php endif; ?>


    <!-- Script JavaScript pour remplir la modale avec les données du clic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            const tripList = document.querySelector('#trip-list');

            if (tripList) {
                tripList.querySelectorAll('.view-details').forEach(button => {
                    button.addEventListener('click', function() {
                        const tripId = this.dataset.tripId;
                        
                        // Récupérer les données du row parent
                        const row = this.closest('tr');

                        if (!row) return; // Sécurité

                        document.getElementById('displayTripId').textContent = tripId;
                        document.getElementById('detailAgencyDepart').textContent = row.cells[0].textContent.trim();
                        document.getElementById('detailDateDepart').textContent = row.cells[1].textContent.trim();
                        document.getElementById('detailAgencyArrivee').textContent = row.cells[2].textContent.trim();
                        document.getElementById('detailDateArrivee').textContent = row.cells[3].textContent.trim();
                        document.getElementById('detailPlacesDisponibles').textContent = row.cells[4].textContent.split('/')[0].trim();
                        document.getElementById('detailTotalPlaces').textContent = row.cells[4].textContent.split('/')[1].trim();

                        // Simuler le remplissage des infos auteur/contact (Nous devons récupérer les données réelles du DB)
                        // Dans un environnement réel, on appellerait AJAX ici pour charger $trip['auteur_id'] etc.
                        const authorId = <?= json_encode($pdo->query("SELECT * FROM Trajets WHERE id = " . intval($tripId)) ?->fetch()['auteur_id'] ?? 1); ?>;
                        
                        if (authorId) {
                             document.getElementById('detailAuthorName').textContent = "Nom Auteur DB"; // Placeholder réel à faire via AJAX
                             document.getElementById('detailAuthorEmail').textContent = "auteur@agence.fr";
                             document.getElementById('detailAuthorPhone').textContent = "0123456789";
                        }


                        modal.show();
                    });
                });
            }
        });
    </script>
</div>
<?php
require_once __DIR__ . '/../components/footer.php';
?>