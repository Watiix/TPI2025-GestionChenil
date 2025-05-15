<?php $isEdit = isset($animaux); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un animal - Les Amis Fidèles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>  
<main class="bg-white py-5 pt-5 mt-4"> 
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg p-4 rounded-4 border-0" style="min-height: 800px;">
                    
                    <div class="text-center mt-3 mb-4">
                        <h2 class="mt-3"><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un animal</h2>
                        <p class="text-muted">Complétez les informations de l'animal</p>
                    </div>

                    <form action="<?= $isEdit ? '/animal-update/' . $animaux['IdAnimal'] : '/animal-post' ?>" method="post">
                        <div class="mb-3">
                            <label for="NomAnimal" class="form-label">Nom de l'animal</label>
                            <input type="text" class="form-control" name="NomAnimal" id="NomAnimal" required
                                   value="<?= htmlspecialchars($animaux['NomAnimal'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="Race" class="form-label">Race</label>
                            <input type="text" class="form-control" name="Race" id="Race" required
                                   value="<?= htmlspecialchars($animaux['Race'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="Age" class="form-label">Âge</label>
                            <input type="number" class="form-control" name="Age" id="Age" min="0" required
                                   value="<?= htmlspecialchars($animaux['Age'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="Sexe" class="form-label">Sexe</label>
                            <select class="form-select" name="Sexe" id="Sexe" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="Mâle" <?= isset($animaux['Sexe']) && $animaux['Sexe'] === 'Mâle' ? 'selected' : '' ?>>Mâle</option>
                                <option value="Femelle" <?= isset($animaux['Sexe']) && $animaux['Sexe'] === 'Femelle' ? 'selected' : '' ?>>Femelle</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="Poids" class="form-label">Poids (kg)</label>
                            <input type="number" class="form-control" name="Poids" id="Poids" step="0.1" min="0" required
                                   value="<?= htmlspecialchars($animaux['Poids'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="Taille" class="form-label">Taille (cm)</label>
                            <input type="number" class="form-control" name="Taille" id="Taille" step="0.1" min="0" required
                                   value="<?= htmlspecialchars($animaux['Taille'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="Alimentation" class="form-label">Alimentation</label>
                            <input type="text" class="form-control" name="Alimentation" id="Alimentation" required
                                   value="<?= htmlspecialchars($animaux['Alimentation'] ?? '') ?>">
                        </div>

                        <?php if ($isAdmin): ?>
                            <div class="mb-3">
                                <label for="IdProprietaire" class="form-label">Propriétaire</label>
                                <select class="form-select" name="IdProprietaire" id="IdProprietaire" required>
                                    <option value="">-- Sélectionner un utilisateur --</option>
                                    <?php foreach ($utilisateurs as $user): ?>
                                        <option value="<?= htmlspecialchars($user['IdUtilisateur']) ?>"
                                            <?= isset($animaux['IdProprietaire']) && $animaux['IdProprietaire'] == $user['IdUtilisateur'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['Prenom'] . ' ' . $user['Nom']) ?> (<?= htmlspecialchars($user['Email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['form_error'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($_SESSION['form_error']) ?>
                            </div>
                            <?php unset($_SESSION['form_error']); ?>
                        <?php endif; ?>
                        
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn" style="background-color: rgb(55, 118, 173); color: white;">
                                <?= $isEdit ? 'Mettre à jour' : 'Ajouter l\'animal' ?>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/animaux">← Retour à la liste</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
