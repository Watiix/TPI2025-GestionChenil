<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil employé</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container text-center mt-5 pt-5">
        <h1 class="display-4 mb-4">Bonjour <?= htmlspecialchars($_SESSION['user']['Prenom']) ?></h1>
        <p class="lead mb-5">Va effectuer tes tâches quotidiennes.</p>

        <a href="/taches" class="btn btn-lg text-white" style="background-color: rgb(55, 118, 173);">
            Accéder à mes tâches
        </a>
    </div>

</body>
</html>
