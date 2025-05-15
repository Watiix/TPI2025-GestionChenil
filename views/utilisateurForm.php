<?php $isEdit = isset($utilisateurs['IdUtilisateur']); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un utilisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>  
<main class="bg-white py-5 pt-5 mt-5"> 
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg p-4 rounded-4 border-0" style="min-height: 700px;">
                    
                    <div class="text-center mt-3 mb-4">
                        <h2 class="mt-3"><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un utilisateur</h2>
                        <p class="text-muted">Complétez les informations de l'utilisateur</p>
                    </div>

                    <form action="<?= $isEdit ? '/utilisateur-update/' . $utilisateurs['IdUtilisateur'] : '/utilisateur-add' ?>" method="post">

                        <div class="mb-3">
                            <label for="Nom" class="form-lab    el">Nom</label>
                            <input type="text" class="form-control" id="Nom" name="Nom" required
                                   value="<?= htmlspecialchars($utilisateurs['Nom'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="Prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="Prenom" name="Prenom" required
                                   value="<?= htmlspecialchars($utilisateurs['Prenom'] ?? '') ?>">
                        </div>
                         
                        <div class="mb-3">
                            <label for="Pseudo" class="form-label">Pseudo</label>
                            <input type="text" class="form-control" id="Pseudo" name="Pseudo" required
                                   value="<?= htmlspecialchars($utilisateurs['Pseudo'] ?? '') ?>">
                        </div>
                        <?php if (!$isEdit): ?>
                            <div class="mb-3">
                                <label for="MotDePasse" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="MotDePasse" name="MotDePasse" required>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="Email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="Email" name="Email" required
                                   value="<?= htmlspecialchars($utilisateurs['Email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="DateNaissance" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="DateNaissance" name="DateNaissance" required
                                   value="<?= htmlspecialchars($utilisateurs['DateNaissance'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="Statut" class="form-label">Statut</label>
                            <select class="form-select" id="Statut" name="Statut" required>
                                <option value="" disabled <?= !isset($utilisateurs['Statut']) ? 'selected' : '' ?>>-- Sélectionner --</option>
                                <option value="1" <?= isset($utilisateurs['Statut']) && $utilisateurs['Statut'] == 1 ? 'selected' : '' ?>>Client</option>
                                <option value="2" <?= isset($utilisateurs['Statut']) && $utilisateurs['Statut'] == 2 ? 'selected' : '' ?>>Employé</option>
                                <option value="3" <?= isset($utilisateurs['Statut']) && $utilisateurs['Statut'] == 3 ? 'selected' : '' ?>>Administrateur</option>
                            </select>
                        </div>

                        <?php if (!empty($_SESSION['form_error'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($_SESSION['form_error']) ?>
                            </div>
                            <?php unset($_SESSION['form_error']); ?>
                        <?php endif; ?>

                        <div class="d-grid mt-3">
                            <button type="submit" class="btn" style="background-color: rgb(55, 118, 173); color: white;">
                                <?= $isEdit ? 'Mettre à jour' : 'Ajouter l\'utilisateur' ?>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/utilisateurs">← Retour à la liste</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
