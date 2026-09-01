<?php
/**
 * @file pages/dashboard_agences.php
 * Gère l'affichage et les actions CRUD des Agences par un administrateur.
 */

$message = $_GET['result_message'] ?? null;
$action = isset($_GET['view']) ? $_GET['view'] : 'list'; // list, create, delete
?>

<div class="container-fluid">
    <h2 class="mb-4 text-primary">Gestion des Agences</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?= (strpos($message, 'succès') !== false || strpos($message, 'effectué') !== false) ? 'success' : 'danger'; ?>" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- SECTION CRÉATION AGENCE -->
    <div class="card shadow mb-5 p-4 <?= ($action == 'create') ? '' : 'd-none' ?>" id="agency-creation-section">
        <h3>Créer une nouvelle Agence</h3>
        <form method="POST" action="/dashboard/agences/create" id="createAgencyForm">
            <div class="mb-3">
                <label for="agency_name" class="form-label">Nom de l'Agence <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="agency_name" name="agency_name" required maxlength="100">
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer l'Agence</button>
        </form>
    </div>

    <!-- SECTION LISTING AGENCES -->
    <div class="card shadow mb-5 p-4">
        <h3>Liste des Agences</h3>
        <?php 
            // Requête pour la liste complète (Utilisée à la fois par le formulaire et le listing)
            $stmt = $pdo->query("SELECT * FROM Agences ORDER BY nom ASC");
            $agencies = $stmt->fetchAll();
        ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom de l'Agence</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agencies as $agency): ?>
                <tr id="agency-row-<?= htmlspecialchars($agency['id']) ?>">
                    <td><?= htmlspecialchars($agency['id']) ?></td>
                    <td><?= htmlspecialchars($agency['nom']) ?></td>
                    <td>
                        <!-- Bouton pour l'action (supprimer/modifier) -->
                        <?php if ($_SESSION['role'] === 'ROLE_ADMIN'): ?>
                            <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="<?= htmlspecialchars($agency['id']) ?>">Modifier</button>
                            <!-- Form de suppression (POST obligatoire) -->
                            <form method="POST" action="/dashboard/agences/delete" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer l\'agence <?= htmlspecialchars($agency['nom']) ?> ?');">
                                <input type="hidden" name="agency_id" value="<?= htmlspecialchars($agency['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">Accès Admin Requis</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Script JS pour basculer les vues d'administration -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createSection = document.getElementById('agency-creation-section');
            // Si nous ne sommes pas sur la page de création, cacher la section créaion
            if (window.location.pathname !== '/dashboard/agences/create') {
                createSection.classList.add('d-none');
            } else {
                 createSection.classList.remove('d-none');
            }
        });
    </script>
</div>