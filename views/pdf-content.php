<?php
use Lucancstr\GestionChenil\Models\Reservation; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        h1, h2 { color: #3776ad; }
        .block { margin-bottom: 20px; }
        ul { padding-left: 15px; }
    </style>
</head>
<body>
    <h1>Les Amis Fidèles - Rapport PDF</h1>

    <p><strong>Utilisateurs :</strong> <?= $nbUsers ?></p>
    <p><strong>Animaux :</strong> <?= $nbAnimaux ?></p>
    <p><strong>Réservations :</strong> <?= $nbReservations ?></p>

    <hr>

    <?php foreach ($utilisateurs as $user): ?>
    <div class="block mb-4">
        <h2><?= htmlspecialchars($user['Prenom']) . ' ' . htmlspecialchars($user['Nom']) ?></h2>
        <p>Email : <?= htmlspecialchars($user['Email']) ?></p>
        <p>Pseudo : <?= htmlspecialchars($user['Pseudo']) ?></p>
        
        <?php if (!empty($user['animaux'])): ?>
            <ul>
                <?php foreach ($user['animaux'] as $animal): ?>
                    <li>
                        <strong><?= htmlspecialchars($animal['NomAnimal']) ?></strong> –
                        <?= htmlspecialchars($animal['Race']) ?> –
                        <?= htmlspecialchars($animal['Age']) ?> ans
                        
                        <?php $reservations = Reservation::getReservationsByAnimalId($animal['IdAnimal']);       
                        if (!empty($reservations)): ?>
                            <ul>
                                <?php foreach ($reservations as $res): ?>
                                    <li>
                                        Réservation : <?= htmlspecialchars($res['DateDebut']) ?> → <?= htmlspecialchars($res['DateFin']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p><em>Aucune réservation</em></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun animal</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

</body>
</html>
