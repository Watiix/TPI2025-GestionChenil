<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Animal;
use Lucancstr\GestionChenil\Models\Utilisateur;
use Lucancstr\GestionChenil\Models\Reservation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;


class ReservationController extends BaseController {

    public function getReservation(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $user = $_SESSION['user'];
    
        if ($user['Statut'] == 3) {
            $reservations = Reservation::getAllReservation();
        } elseif ($user['Statut'] == 1) {
            $reservations = Reservation::getAllUserReservation($user['IdUtilisateur']);
        }else{
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $this->view->render($response, 'reservations.php', ['reservations' => $reservations]);
    }

    public function acceptReservation(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $idUserAdmin = $_SESSION['user']['IdUtilisateur'];
        $idReservation = $args['id'];

        Reservation::validateReservation($idReservation, $idUserAdmin);
        $_SESSION['form_succes'] = "Réservation validée avec succès.";

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function refusedReservation(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $idUserAdmin = $_SESSION['user']['IdUtilisateur'];
        $idReservation = $args['id'];

        Reservation::refusedReservation($idReservation, $idUserAdmin);
        $_SESSION['form_succes'] = "Réservation refusée avec succès.";

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function deleteReservation(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $idReservation = $args['id'];

        Reservation::deleteReservation($idReservation);
        $_SESSION['form_succes'] = "Supprimé avec succès.";

        return $response->withHeader('Location', '/reservations')->withStatus(302);
    }

    public function showReservationForm(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $idUser = $_SESSION['user']['IdUtilisateur'];

        if ($_SESSION['user']['Statut'] == 3)
        {
            $allAnimalProprio = Reservation::getAllAnimalProprio();

            return $this->view->render($response, 'reservationForm.php', [
                'allAnimalProprio' => $allAnimalProprio
            ]);
        }

        $animalProprio = Reservation::getAllAnimalProprioByUser($idUser);

        return $this->view->render($response, 'reservationForm.php', [
            'animalProprio' => $animalProprio
        ]);
    }

    public function addReservation(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        // Reset les messages
        unset($_SESSION['form_error']);
        unset($_SESSION['form_succes']);

        $post = $request->getParsedBody();

        // Nettoyage / Filtrage des champs
        $DateDebut = trim($post['DateDebut']);
        $DateFin = trim($post['DateFin']);
        $PrixJour = trim($post['PrixJour']);
        $BesoinParticulier = trim($post['BesoinParticulier']);
        $IdAnimal = trim($post['IdAnimal']);

        $reservations = [
            'DateDebut' => $DateDebut,
            'DateFin' => $DateFin,
            'PrixJour' => $PrixJour,
            'BesoinParticulier' => $BesoinParticulier
        ];

        if (
            empty($DateDebut) || empty($DateFin) || empty($PrixJour) || empty($BesoinParticulier)
        ) {
            $_SESSION['form_error'] = "Tous les champs doivent être remplis.";
        }

        // Validation de la date
        try {
            Utilisateur::validateDate($DateDebut, 'Y-m-d');
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Date invalide.";
        }

        // Validation de la date
        try {
            Utilisateur::validateDate($DateFin, 'Y-m-d');
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Date invalide.";
        } 

        // Si pas d'erreur, on ajoute
        if (!isset($_SESSION['form_error'])) {
            try {
                $IdProprietaire = Animal::getIdProprietaireByIdAnimal($IdAnimal);
                Reservation::createReservation($DateDebut, $DateFin, $PrixJour, $BesoinParticulier,$IdProprietaire, $IdAnimal);
                $_SESSION['form_succes'] = "Reservation ajoutée avec succès.";
    
                return $response->withHeader('Location', '/reservations')->withStatus(302);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        $idUser = $_SESSION['user']['IdUtilisateur'];

        $allAnimalProprio = Reservation::getAllAnimalProprio();
        $animalProprio = Reservation::getAllAnimalProprioByUser($idUser);

        return $this->view->render($response->withStatus(302), 'reservationForm.php', ['reservations' => $reservations, 'animalProprio' => $animalProprio, 'allAnimalProprio' => $allAnimalProprio]);
    }

    public function showEditForm(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $id = $args['id'];

        $reservations = Reservation::getReservationbyId($id);

        $idUser = $_SESSION['user']['IdUtilisateur'];

        $allAnimalProprio = Reservation::getAllAnimalProprio();
        $animalProprio = Reservation::getAllAnimalProprioByUser($idUser);

        return $this->view->render($response->withStatus(302), 'reservationForm.php', ['reservations' => $reservations, 'animalProprio' => $animalProprio, 'allAnimalProprio' => $allAnimalProprio]);
    }

    public function editReservation(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $idReservation = $args['id'];

        // Reset les messages
        unset($_SESSION['form_error']);
        unset($_SESSION['form_succes']);

        $post = $request->getParsedBody();

        // Nettoyage / Filtrage des champs
        $DateDebut = trim($post['DateDebut']);
        $DateFin = trim($post['DateFin']);
        $PrixJour = trim($post['PrixJour']);
        $BesoinParticulier = trim($post['BesoinParticulier']);
        $IdAnimal = trim($post['IdAnimal']);

        $reservations = [
            'DateDebut' => $DateDebut,
            'DateFin' => $DateFin,
            'PrixJour' => $PrixJour,
            'BesoinParticulier' => $BesoinParticulier
        ];

        if (
            empty($DateDebut) || empty($DateFin) || empty($PrixJour) || empty($BesoinParticulier)
        ) {
            $_SESSION['form_error'] = "Tous les champs doivent être remplis.";
        }

        // Validation de la date
        try {
            Utilisateur::validateDate($DateDebut, 'Y-m-d');
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Date invalide.";
        }

        // Validation de la date
        try {
            Utilisateur::validateDate($DateFin, 'Y-m-d');
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Date invalide.";
        } 

        // Si pas d'erreur, on ajoute
        if (!isset($_SESSION['form_error'])) {
            try {
                $IdProprietaire = Animal::getIdProprietaireByIdAnimal($IdAnimal);
                Reservation::updateReservation($DateDebut, $DateFin, $PrixJour, $BesoinParticulier,$IdProprietaire, $IdAnimal, $idReservation);
                $_SESSION['form_succes'] = "Reservation modifiée avec succès.";
    
                return $response->withHeader('Location', '/reservations')->withStatus(302);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        $idUser = $_SESSION['user']['IdUtilisateur'];

        $allAnimalProprio = Reservation::getAllAnimalProprio();
        $animalProprio = Reservation::getAllAnimalProprioByUser($idUser);

        return $this->view->render($response->withStatus(302), 'reservationForm.php', ['reservations' => $reservations, 'animalProprio' => $animalProprio, 'allAnimalProprio' => $allAnimalProprio]);
    }
}