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
        Tache::generateTodayTasksIfNotExists();

        $taches = Tache::getTodayUnassigned();
        $employes = Utilisateur::getEmployes();
        $animaux = Animal::getAll();    

        $totalTaches = count($taches);
        $totalEmployes = count($employes);
        $totalAnimaux = count($animaux);

        if ($totalTaches > 0 && $totalEmployes > 0 && $totalAnimaux > 0) {
            $indexEmploye = 0;
            $indexAnimal = 0;

            foreach ($taches as $tache) {
                $employe = $employes[$indexEmploye % $totalEmployes];
                $animal = $animaux[$indexAnimal % $totalAnimaux];
                
                Tache::assignToEmployeeAndAnimal($tache['IdTache'],$employe['IdUtilisateur'],$animal['IdAnimal']);
                $indexEmploye++;
                $indexAnimal++;
            }

            return $this->view->render($response, 'taches.php');   
        }
    }   
}