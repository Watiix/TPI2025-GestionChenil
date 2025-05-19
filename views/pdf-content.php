<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport PDF - Les Amis Fidèles</title>
</head>
<body>

    <h1 class="text-center mb-4">Les Amis Fidèles</h1>

    <div class="stat-box">
        <h2>Statistiques globales</h2>
        <ul class="mb-0">
            <li><strong>Utilisateurs :</strong> <?= $nbUtilisateurs ?></li>
            <li><strong>Animaux :</strong> <?= $nbAnimaux ?></li>
            <li><strong>Réservations :</strong> <?= $nbReservations ?></li>
        </ul>
    </div>
    
    <h2>Animaux par utilisateur</h2>

    <?php foreach ($utilisateurs as $user): ?>
        <div class="user-block">
            <h4><?= htmlspecialchars($user['Prenom'] . ' ' . $user['Nom']) ?> 
                <small class="text-muted">(<?= htmlspecialchars($user['Email']) ?>)</small>
            </h4>

            <?php if (!empty($user['animaux'])): ?>
                <?php foreach ($user['animaux'] as $animal): ?>
                    <div class="animal-card">
                        <strong>Nom :</strong> <?= htmlspecialchars($animal['NomAnimal']) ?><br>
                        <strong>Race :</strong> <?= htmlspecialchars($animal['Race']) ?><br>
                        <strong>Âge :</strong> <?= htmlspecialchars($animal['Age']) ?> ans<br>
                        <strong>Sexe :</strong> <?= htmlspecialchars($animal['Sexe']) ?><br>
                        <strong>Poids :</strong> <?= htmlspecialchars($animal['Poids']) ?> kg<br>
                        <strong>Taille :</strong> <?= htmlspecialchars($animal['Taille']) ?> cm<br>
                        <strong>Alimentation :</strong> <?= htmlspecialchars($animal['Alimentation']) ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Aucun animal enregistré.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

</body>
</html>
