<?php $isEdit = isset($reservations['IdReservation']); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une réservation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<main class="bg-white py-5 mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg p-4 rounded-4 border-0">

                    <div class="text-center mb-4">
                    <h2 class="mt-3"><?= $isEdit ? 'Modifier' : 'Ajouter' ?> une réservation</h2>
                        <p class="text-muted">Complétez les détails de la réservation</p>
                    </div>

                    <form action="<?= $isEdit ? '/reservation-update/' . $reservations['IdReservation'] : '/reservation-add' ?>" method="post">
                        <div class="mb-3">
                            <label for="DateDebut" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="DateDebut" name="DateDebut" required
                                   value="<?= htmlspecialchars($reservations['DateDebut'] ?? '') ?>">
                        </div>  

                        <div class="mb-3">
                            <label for="DateFin" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="DateFin" name="DateFin" required
                                   value="<?= htmlspecialchars($reservations['DateFin'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="PrixJour" class="form-label">Prix par jour (CHF)</label>
                            <input type="number" step="1" min="0" class="form-control" id="PrixJour" name="PrixJour" required
                                   value="<?= htmlspecialchars($reservations['PrixJour'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="BesoinParticulier" class="form-label">Besoins particuliers</label>
                            <textarea class="form-control" id="BesoinParticulier" name="BesoinParticulier" rows="3"><?= htmlspecialchars($reservations['BesoinParticulier'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="IdAnimal" class="form-label">Animal</label>
                            <select class="form-select" id="IdAnimal" name="IdAnimal" required>
                                <option value="">-- Sélectionner un animal --</option>

                                <?php if ($_SESSION['user']['Statut'] == 3 && isset($allAnimalProprio)): ?>
                                    <?php foreach ($allAnimalProprio as $animal): ?>
                                        <option value="<?= $animal['IdAnimal'] ?>"
                                            <?= isset($reservations['IdAnimal']) && $reservations['IdAnimal'] == $animal['IdAnimal'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($animal['NomAnimal']) ?>
                                            (<?= htmlspecialchars($animal['Race']) ?>)
                                            - <?= htmlspecialchars($animal['PrenomProprio'] . ' ' . $animal['NomProprio']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php elseif (isset($animalProprio)): ?>
                                    <?php foreach ($animalProprio as $animal): ?>
                                        <option value="<?= $animal['IdAnimal'] ?>"
                                            <?= isset($reservations['IdAnimal']) && $reservations['IdAnimal'] == $animal['IdAnimal'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($animal['NomAnimal']) ?>
                                            (<?= htmlspecialchars($animal['Race']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                            <?= $isEdit ? 'Mettre à jour' : 'Ajouter' ?>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="/reservations">&larr; Retour à la liste</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
