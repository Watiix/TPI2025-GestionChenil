<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Animal;
use Lucancstr\GestionChenil\Models\Utilisateur;
use Lucancstr\GestionChenil\Models\Tache;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;


class TacheController extends BaseController {

    /**
     * getTaches
     *
     * Récupère les tâches du jour. Si aucune tâche n'existe, les génère et les assigne automatiquement aux employés.
     * Affiche la liste des tâches dans la vue.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function getTaches(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {   
        $allTaches = Tache::getToday();
        $employes = Utilisateur::getEmployes();

        if ($allTaches == null) {
            $animaux = Animal::getAll();  
            Tache::generateTodayTasksIfNotExists($animaux);

            $taches = Tache::getTodayUnassigned();

            $totalEmployes = count($employes);
            $totalTaches = count($taches);
            $index = 0;
            foreach ($taches as $tache) {
                // rnd 
                $employe = $employes[$index];
                Tache::assignToEmployee($tache['IdTache'], $employe['IdUtilisateur']);
                $index++;
                
                if($index == $totalEmployes){
                    $index = 0;
                }
            }
        }

        if($_SESSION['user']['IdUtilisateur'] == 3)
        {
            return $this->view->render($response, 'taches.php', [
                'taches' => $allTaches,
            ]);
        }

        return $this->view->render($response, 'taches.php', [
            'taches' => $allTaches,
        ]);
    }  
    
    /**
     * validateTache
     *
     * Valide une tâche (employé uniquement) selon son ID.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function validateTache(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 2){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $idTache = $args['id'];

        Tache::validateTache($idTache);
        $_SESSION['form_succes'] = "Tache validée avec succès.";

        return $response->withHeader('Location', '/taches')->withStatus(302);
    }
}