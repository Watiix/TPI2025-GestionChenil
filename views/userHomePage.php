<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil utilisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .full-height {
            min-height: 100vh;
        }
        body {
            background-image: url('/img/chien.jpg'); /* adapte ce chemin */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        article{
            margin-top: 100px;
        }
    </style>
</head>
<body>  

<article class="container d-flex justify-content-center pt-5 mt-5" style="min-height: 70vh;">
    <div class="text-center">
        <!-- Image -->
        <img src="/img/logoBlanc.png" alt="Bienvenue" class="mb-4" style="max-width: 200px;">

        <!-- Titre -->
        <h1 class="display-4 fw-bold mb-4" style="color: white;">
            Bienvenue sur Les Amis Fidèles
        </h1>

        <!-- Texte -->
        <p class="lead mb-4" style="color: white;">
            Commencez par enregistrer vos animaux pour accéder à nos services personnalisés.
        </p>

        <!-- Bouton -->
        <a href="/animaux" class="btn btn-lg text-white" style="background-color: rgb(55, 118, 173);">
            Enregistrer mes animaux
        </a>

        <div class="d-flex justify-content-center mt-4 gap-4 text-white">
    <div>
        <i class="bi bi-heart-fill fs-3"></i><br>
        Soins personnalisés
    </div>
    <div>
        <i class="bi bi-geo-alt-fill fs-3"></i><br>
        Promenades quotidiennes
    </div>
    <div>
        <i class="bi bi-shield-lock-fill fs-3"></i><br>
        Sécurité garantie
    </div>
</div>

    </div>
</article>


</body>
</html>
