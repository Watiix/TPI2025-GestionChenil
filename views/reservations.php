<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des réservations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Gestion des réservations</h2>
        <a href="/reservation-showForm" class="btn btn-primary" style="background-color: rgb(55, 118, 173); color: white;">Ajouter une réservation  </a>
    </div>

    <?php if (!empty($_SESSION['form_succes'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['form_succes']) ?></div>
        <?php unset($_SESSION['form_succes']); ?>
    <?php endif; ?>

    <?php if (empty($reservations) ): ?>
        <div class="alert alert-warning">Aucune réservation trouvée.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead style="background-color: rgb(55, 118, 173); color: white;">
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

                        <?php // REGLER UN PROBLEME ICI ?>
                        <?php if ((int)$res['Etat'] !== 0 || $_SESSION['user']['Statut'] === 1): ?>
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
                                <?php 
                                $isAdmin = $_SESSION['user']['Statut'] == 3;
                                $etat = $res['Etat'];
                                     if ($isAdmin && $etat === 1): ?>
                                    <div class="d-flex gap-2">
                                        <a href="/reservation-edit/<?= $res['IdReservation'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                                        <a href="/reservation-delete/<?= $res['IdReservation'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette réservation ?')">Supprimer</a>
                                    </div>

                                    <?php elseif ($isAdmin && $etat === 2): ?>
                                    <div class="d-flex gap-2">
                                        <a href="/reservation-delete/<?= $res['IdReservation'] ?>" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Supprimer cette réservation ?')">Supprimer</a>
                                    </div>
                            
                                <?php elseif (!$isAdmin && $etat === 0): ?>
                                    <span class="text-muted fst-italic">Aucune action disponible</span>
                            
                                <?php elseif (!$isAdmin && $etat === 1): ?>
                                    <div class="d-flex gap-2">
                                        <a href="/reservation-edit/<?= $res['IdReservation'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                                        <a href="/reservation-delete/<?= $res['IdReservation'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette réservation ?')">Supprimer</a>
                                    </div>

                                    
                                    <?php elseif (!$isAdmin && $etat === 2): ?>
                                    <div class="d-flex gap-2">
                                    <span class="text-muted fst-italic">Aucune action disponible</span>
                                    </div>
                                <?php endif; ?>
                            </td>   
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
