<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des utilisateurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Gestion des utilisateurs</h2>
        <a href="/utilisateur-showForm" class="btn btn-primary" style="background-color: rgb(55, 118, 173); color: white;">Ajouter un utilisateur</a>
    </div>

    <?php if (!empty($_SESSION['form_succes'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['form_succes']) ?>
        </div>
        <?php unset($_SESSION['form_succes']); ?>
    <?php endif; ?>

    <?php if (empty($utilisateurs)): ?>
        <div class="alert alert-warning">Aucun utilisateur trouvé.</div>
    <?php else: ?>
        <div class="card mb-4">
            <div class="card-header text-white" style="background-color: rgb(55, 118, 173);">
                Liste des utilisateurs validés
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Pseudo</th>
                            <th>Date de naissance</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $user): ?>
                            <?php if ((int)$user['Valide'] !== 0): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['Nom']) ?></td>
                                <td><?= htmlspecialchars($user['Prenom']) ?></td>
                                <td><?= htmlspecialchars($user['Email']) ?></td>
                                <td><?= htmlspecialchars($user['Pseudo']) ?></td>
                                <td><?= htmlspecialchars($user['DateNaissance']) ?></td>
                                <td>
                                    <?php
                                        switch ((int)$user['Statut']) {
                                            case 0:
                                                echo '<span class="badge bg-secondary">En attente</span>';
                                                break;
                                            case 1:
                                                echo '<span class="badge bg-success">Client</span>';
                                                break;
                                            case 2:
                                                echo '<span class="badge bg-info text-dark">Employé</span>';
                                                break;
                                            case 3:
                                                echo '<span class="badge bg-warning text-dark">Admin</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-dark">Inconnu</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="/utilisateur-edit/<?= $user['IdUtilisateur'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                                        <a href="/utilisateur-delete/<?= $user['IdUtilisateur'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
