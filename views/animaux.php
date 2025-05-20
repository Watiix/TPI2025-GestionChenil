<?php 
use Lucancstr\GestionChenil\Models\Tache; 
use Lucancstr\GestionChenil\Models\Reservation;?>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Liste des animaux</h2>
        <?php if ($_SESSION['user']['Statut'] !== 2){?>
        <a href="/animal-form" class="btn btn-success" style="background-color: rgb(55, 118, 173); color: white;">Ajouter un animal</a>
        <?php } ?>
    </div>

    <!-- BARRE DE RECHERCHE -->
    <form action="/animaux" method="GET" class="mb-4">
        <div class="row g-3 align-items-end">

            <?php if ($_SESSION['user']['Statut'] == 3): ?>
                <div class="col-md-3">
                    <label for="id" class="form-label">Propriétaire</label>
                    <select name="id" id="id" class="form-select">
                        <option value="0">-- Tous --</option>
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                            <option value="<?= $utilisateur['IdUtilisateur'] ?>"
                                <?= isset($_GET['id']) && $_GET['id'] == $utilisateur['IdUtilisateur'] ? 'selected' : '' ?>>
                                <?= $utilisateur['Nom'] . ' ' . $utilisateur['Prenom'] . ' - ' . $utilisateur['Pseudo'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100" style="background-color: rgb(55, 118, 173); color: white;">Search</button>
            </div>
            <?php endif; ?>
        </div>
    </form>

    <!-- MESSAGE DE SUCCÈS -->
    <?php if (!empty($_SESSION['form_succes'])): ?>
        <div class="alert alert-success mt-2"><?= htmlspecialchars($_SESSION['form_succes']) ?></div>
        <?php unset($_SESSION['form_succes']); ?>
    <?php endif; ?>

    <!-- AUCUN ANIMAL -->
    <?php $utilisateur = $_SESSION['user'] ?? null; ?>
    <?php if (empty($animaux)): ?>
        <div class="alert alert-warning">Aucun animal trouvé.</div>
    <?php else: ?>
        <!-- LISTE DES ANIMAUX -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <?php foreach ($animaux as $animal): ?>
                <div class="col">
                    <div class="card shadow-sm border rounded-4 h-100">
                        <div class="card-body p-3 position-relative">
                            <?php if ($_SESSION['user']['Statut'] !== 2){?>
                            <a href="/animal-edit/<?= $animal['IdAnimal'] ?>" class="position-absolute top-0 end-0 m-2 text-primary" title="Modifier">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </a>
                            <?php } ?>
                            <h5 class="card-title"><?= htmlspecialchars($animal['NomAnimal'] ?? 'N/A') ?></h5>
                            <p class="card-text mb-1"><strong>Race :</strong> <?= htmlspecialchars($animal['Race'] ?? 'N/A') ?></p>
                            <p class="card-text mb-1"><strong>Âge :</strong> <?= htmlspecialchars($animal['Age'] ?? 'N/A') ?> ans</p>
                            <p class="card-text mb-1"><strong>Sexe :</strong> <?= htmlspecialchars($animal['Sexe'] ?? 'N/A') ?></p>
                            <p class="card-text mb-1"><strong>Poids :</strong> <?= htmlspecialchars($animal['Poids'] ?? 'N/A') ?> kg</p>
                            <p class="card-text mb-1"><strong>Taille :</strong> <?= htmlspecialchars($animal['Taille'] ?? 'N/A') ?> cm</p>
                            <p class="card-text mb-1"><strong>Alimentation :</strong> <?= htmlspecialchars($animal['Alimentation'] ?? 'N/A') ?></p>
                            <?php if ($utilisateur && $utilisateur['IdUtilisateur'] != $animal['IdProprietaire']): ?>
                                <hr class="my-2">
                                <p class="card-text"><strong>Propriétaire :</strong>
                                    <?= htmlspecialchars($animal['NomProprietaire'] ?? '') . ' ' . htmlspecialchars($animal['PrenomProprietaire'] ?? '') ?>
                                </p>
                            <?php endif; ?>
                            <?php 
                            $reservations = Reservation::getReservationsByAnimalId($animal['IdAnimal']); ?>
                            <hr class="my-2">
                            <p class="card-text"><strong>Réservations :</strong></p>
                            <ul class="list-unstyled mb-0 ps-3">
                                        <li class="mb-1">
                                        <?php foreach ($reservations as $reservation): ?>
                                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                            <?= htmlspecialchars($reservation['DateDebut'] ?? '') . ' - ' . htmlspecialchars($reservation['DateFin'] ?? '') ?>
                                            <?php endforeach; ?>
                                        </li>
                                </ul>

                            <?php $taches = Tache::getTachesByAnimalId($animal['IdAnimal']); 
                            if(isset($taches)):?>
                                <hr class="my-2">
                                <p class="card-text"><strong>Tâches :</strong></p>
                                <ul class="list-unstyled mb-0 ps-3">
                                    <?php foreach ($taches as $tache): ?>
                                        <li class="mb-1">
                                            <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                            <?= htmlspecialchars($tache['Titre'] ?? '') ?>
                                            <small class="text-muted">– <?= htmlspecialchars($tache['Date'] ?? '') ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif ?>
                        </div>
                        <?php if ($_SESSION['user']['Statut'] !== 2){?>
                        <div class="card-footer bg-white text-center border-top-0">
                            <a href="/animal-delete/<?= $animal['IdAnimal'] ?>" class="btn btn-danger w-100" onclick="return confirm('Supprimer cet animal ?')">
                                Supprimer
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
