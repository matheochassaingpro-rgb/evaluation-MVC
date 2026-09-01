<?php ob_start(); ?>

<h1 class="mb-4">Trajets proposés</h1>
<div class="row">
    <?php if (!empty($trajets)): ?>
        <?php foreach ($trajets as $trajet): ?>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <?= htmlspecialchars($trajet['ville_depart']) ?> &rarr; <?= htmlspecialchars($trajet['ville_arrivee']) ?>
                        </h5>
                        <p class="card-text">
                            <strong>Départ :</strong> <?= htmlspecialchars($trajet['date_heure_depart']) ?><br>
                            <strong>Places :</strong> <span class="badge bg-secondary"><?= htmlspecialchars($trajet['nb_places_dispo']) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Aucun trajet disponible.</div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>