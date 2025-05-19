<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Animal;
use Lucancstr\GestionChenil\Models\Utilisateur;
use Lucancstr\GestionChenil\Models\Reservation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;


class RapportController extends BaseController {

    public function showRapport(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $utilisateurs = Utilisateur::getAllWithAnimaux();


        return $this->view->render($response, 'rapport.php');
    }

    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        
    }
}