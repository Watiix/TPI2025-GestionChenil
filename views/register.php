<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte - Les Amis Fidèles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<main class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg p-4 rounded-4 border-0" style="min-height: 800px;">
                    
                    <div class="text-center mt-3 mb-4">
                        <img src="/img/logo.png" alt="Les Amis Fidèles"
                             class="img-fluid rounded-circle mx-auto d-block"
                             style="max-width: 200px;">
                        <h2 class="mt-3">Créer un compte</h2>
                        <p class="text-muted">Remplissez les informations pour vous inscrire</p>
                    </div>

                    <form action="/register-post" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom</label>
                                <input type="text" class="form-control" name="name" id="name" required
                                       value="<?= htmlspecialchars($data['name'] ?? '') ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="firstname" class="form-label">Prénom</label>
                                <input type="text" class="form-control" name="firstname" id="firstname" required
                                       value="<?= htmlspecialchars($data['firstname'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pseudo" class="form-label">Pseudo</label>
                            <input type="text" class="form-control" name="pseudo" id="pseudo" required
                                   value="<?= htmlspecialchars($data['pseudo'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="birthdate" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" name="birthdate" id="birthdate" required
                                   value="<?= htmlspecialchars($data['birthdate'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" name="email" id="email" required
                                   value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                        </div>

                        <?php if (!empty($_SESSION['form_error'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($_SESSION['form_error']) ?>
                            </div>
                            <?php unset($_SESSION['form_error']); ?>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['form_succes'])): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($_SESSION['form_succes']) ?>
                            </div>
                            <?php unset($_SESSION['form_succes']); ?>
                        <?php endif; ?>

                        <div class="d-grid mt-3">
                            <button type="submit" class="btn" style="background-color: rgb(55, 118, 173); color: white;">
                                Créer un compte
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/login">Déjà un compte ? <strong>Se connecter</strong></a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
