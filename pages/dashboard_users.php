<?php
/**
 * @file pages/dashboard_users.php
 * Page de gestion des Utilisateurs (Admin Only).
 */

// Assurez-vous que le contrôleur UserController est chargé et utilisable ici, ou mieux : 
// injecter une instance dans la vue si on veut garder le découplage.
$userController = new UserController($pdo);
$users = $userController->listUsers();
?>

<div class="container-fluid">
    <h2 class="mb-4 text-primary">Gestion des Utilisateurs (Admin)</h2>
    <p class="alert alert-warning" role="alert">Attention : La suppression d'un utilisateur est irréversible et peut affecter les données de trajets.</p>

    <div class="card shadow p-4">
        <h5 class="mb-3">Liste des Employés</h5>
        <table class="table table-striped table-hover">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-danger" 
                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce compte ? Ceci est irréversible.')) { document.getElementById('deleteUserForm_<?= $user['id'] ?>').submit(); }">Supprimer</button>
                        <!-- Formulaire caché pour la soumission sécurisée -->
                        <form id="deleteUserForm_<?= $user['id'] ?>" method="POST" action="/admin/users/delete/<?= htmlspecialchars($user['id']) ?>">
                            <input type="hidden" name="_csrf" value="<?= $_SESSION['csrf_token'] ?? 'dummy' ?>"> 
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>