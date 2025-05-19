<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Animal;
use Lucancstr\GestionChenil\Models\Utilisateur;
use Lucancstr\GestionChenil\Models\Tache;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;


class TacheController extends BaseController {

    public function getTaches(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {   
        $allTaches = Tache::getToday();

        if ($allTaches == null) {
            $animaux = Animal::getAll();  
            Tache::generateTodayTasksIfNotExists($animaux);

            $taches = Tache::getTodayUnassigned();
            $employes = Utilisateur::getEmployes();

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

        return $this->view->render($response, 'taches.php', [
            'taches' => $allTaches,
        ]);
    }  
    
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