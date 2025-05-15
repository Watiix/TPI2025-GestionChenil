<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">

        <h1 class="mb-4">Tableau de bord</h1>

        <?php if (!empty($_SESSION['form_succes'])): ?>
                <div class="alert alert-success mt-3">
                    <?= htmlspecialchars($_SESSION['form_succes']) ?>
                </div>
                <?php unset($_SESSION['form_succes']); ?>
            <?php endif; ?>

        <!-- Utilisateurs avec IdValide = 0 -->
        <div class="card mb-4">
            <div class="card-header text-white" style="background-color: rgb(55, 118, 173); color: white;">
                Utilisateurs à valider
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
                            <?php if ((int)$user['Valide'] === 0): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['Nom']) ?></td>
                                    <td><?= htmlspecialchars($user['Prenom']) ?></td>
                                    <td><?= htmlspecialchars($user['Email']) ?></td>
                                    <td><?= htmlspecialchars($user['Pseudo']) ?></td>
                                    <td><?= htmlspecialchars($user['DateNaissance']) ?></td>
                                    <td>
                                        <?php
                                            switch ((int)$user['Statut']) {
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
                                                    echo '<span class="badge bg-secondary">Inconnu</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="/utilisateur-accepted/<?= $user['IdUtilisateur'] ?>" class="btn btn-sm btn-outline-success">Accepter</a>
                                            <a href="/utilisateur-refused/<?= $user['IdUtilisateur'] ?>"  class="btn btn-sm btn-outline-danger" onclick="return confirm('Refuser cet utilisateur ?')">Refuser</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (!empty($_SESSION['form_succes'])): ?>
                <div class="alert alert-success mt-3">
                    <?= htmlspecialchars($_SESSION['form_succes']) ?>
                </div>
                <?php unset($_SESSION['form_succes']); ?>
            <?php endif; ?>

        <!-- Réservations non validées (IdEtat = 1) -->
        <div class="card mb-4">
            <div class="card-header text-white"  style="background-color: rgb(55, 118, 173); color: white;">
                Réservations non validées
            </div>
            <div class="card-body">
                    <table class="table table-bordered table-hover align-middle">
                    <thead class="table">
                    <tr>
                        <th>ID</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Prix/Jour</th>
                        <th>Besoins particuliers</th>
                        <th>Propriétaire</th>
                        <th>Animal</th>
                        <th>État</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                            <?php if ((int)$res['Etat'] === 0): ?>
                        <tr>
                            <td><?= htmlspecialchars($res['IdReservation']) ?></td>
                            <td><?= htmlspecialchars($res['DateDebut']) ?></td>
                            <td><?= htmlspecialchars($res['DateFin']) ?></td>
                            <td><?= htmlspecialchars($res['PrixJour']) ?> CHF</td>
                            <td><?= htmlspecialchars($res['BesoinParticulier']) ?></td>
                            <td><?= htmlspecialchars($res['IdProprietaire']) ?></td>
                            <td><?= htmlspecialchars($res['IdAnimal']) ?></td>
                            <td>
                                <?php
                                    switch ($res['Etat']) {
                                        case 0:
                                            echo '<span class="badge bg-warning">En attente</span>';
                                            break;
                                        case 1:
                                            echo '<span class="badge bg-success">Validée</span>';
                                            break;
                                        case 2:
                                            echo '<span class="badge bg-danger">Refusée</span>';
                                            break;
                                        default:
                                            echo '<span class="badge bg-dark">Inconnu</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                    <div class="d-flex gap-2">
                                        <a href="/reservation-accepted/<?= $res['IdReservation'] ?>" class="btn btn-sm btn-outline-success">Valider</a>
                                        <a href="/reservation-refused/<?= $res['IdReservation'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Refuser cette réservation ?')">Refuser</a>
                                    </div>
                            </td>   
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>  
    </div>
</body>
</html>
