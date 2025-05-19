<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Génération de rapport PDF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
        rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg p-4 border-0 rounded-4">
                <div class="text-center mb-4">
                    <h2 class="mb-3">Rapport PDF</h2>
                    <p class="text-muted">Cliquez sur le bouton ci-dessous pour générer automatiquement un rapport PDF à partir des données disponibles.</p>
                </div>

                <form action="/generer-pdf" method="post">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" style="background-color: rgb(55, 118, 173);">
                            Générer le rapport PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
