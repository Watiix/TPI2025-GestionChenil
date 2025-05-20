<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Utilisateur;
use Lucancstr\GestionChenil\Models\Reservation;
use Lucancstr\GestionChenil\Models\Tache;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class HomeController extends BaseController {

    /**
     * showHomePage
     *
     * Affiche la page d’accueil selon le rôle de l’utilisateur connecté (proprio, employé ou admin).
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function showHomePage(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if (!isset($_SESSION['user'])) {
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        if($_SESSION['user']['Statut'] == 1)
        {   
            return $this->view->render($response, 'userHomePage.php');
        }
        elseif($_SESSION['user']['Statut'] == 2)
        {
            $taches = Tache::getToday();

            return $this->view->render($response, 'employeHomePage.php', [
                'taches' => $taches
            ]);
        }
        else
        {
            $utilisateurs = Utilisateur::getAll();
            $reservations = Reservation::getAllReservation();
            $taches = Tache::getAllTaches();

            

            return $this->view->render($response, 'adminHomePage.php', [
                'utilisateurs' => $utilisateurs,
                'reservations' => $reservations,
                'taches' => $taches
            ]);
        }
    }

    /**
     * showLoginPage
     *
     * Affiche la page de connexion sans layout.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function showLoginPage(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {   
        return $this->renderWithoutLayout($response, 'login.php');
    }

    /**
     * showRegisterPage
     *
     * Affiche la page d'inscription sans layout.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */

    public function showRegisterPage(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {   
        return $this->renderWithoutLayout($response, 'register.php');
    }
}