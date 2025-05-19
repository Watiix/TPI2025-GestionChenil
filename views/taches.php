<div class="container mt-5">
    <h2 class="mb-4">Mes tâches du jour</h2>

    <?php if (empty($taches)): ?>
        <div class="alert alert-info">Aucune tâche assignée pour aujourd’hui.</div>
    <?php else: ?>
        <div class="d-flex flex-column gap-3">
            <?php foreach ($taches as $tache): ?>
                <?php if ($tache['IdEmploye'] == $_SESSION['user']['IdUtilisateur']): ?>
                    <div class="card shadow-sm border-start" style="border-left: 6px solid rgb(55, 118, 173);">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1"><?= htmlspecialchars($tache['Titre']) ?></h5>
                                <p class="mb-2"><?= htmlspecialchars($tache['Description']) ?></p>
                                <small class="text-muted">
                                    Animal : <?= htmlspecialchars($tache['NomAnimal'] ?? 'Non défini') ?>
                                </small>
                            </div>

                            <div class="text-end">
                                <?php if ((int)$tache['Etat'] === 1): ?>
                                    <span class="badge bg-success">Terminée</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark mb-2">En cours</span>
                                    <form action="/tache-valider/<?= $tache['IdTache'] ?>" method="get">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            Marquer comme validée
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
