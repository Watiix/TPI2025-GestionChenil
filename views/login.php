<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Les Amis Fidèles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<main class="bg-white d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow-lg p-4 rounded-4 border-0 d-flex flex-column justify-content-between" style="min-height: 730px;">
                
                <div class="flex-grow-1">
                    <div class="text-center mt-4 mb-5">
                        <img src="/img/logo.png" alt="Les Amis Fidèles"
                             class="img-fluid rounded-circle mx-auto d-block"
                                style="max-width: 230px;">
                        <h2 class="mt-3">Connexion</h2>
                        <p class="text-muted">Accédez à votre compte pour gérer vos animaux et réservations</p>
                    </div>

                        <form action="/login-post" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse Email</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger p-2">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['form_succes'])): ?>
                            <div class="alert alert-success p-2">
                                <?= htmlspecialchars($_SESSION['form_succes']) ?>
                            </div>
                            <?php unset($_SESSION['form_succes']); ?>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['form_error'])): ?>
                            <div class="alert alert-danger p-2">
                                <?= htmlspecialchars($_SESSION['form_error']) ?>
                            </div>
                            <?php unset($_SESSION['form_error']); ?>
                        <?php endif; ?>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn" style="background-color: rgb(55, 118, 173); color: white;">Se connecter</button>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="/register">Pas encore de compte ? <strong>Créer un compte</strong></a>
                </div>

            </div>
        </div>
    </div>
</div>


                        </main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
