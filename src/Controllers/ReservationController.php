<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Animal;
use Lucancstr\GestionChenil\Models\Utilisateur;
use Lucancstr\GestionChenil\Models\Reservation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;


class ReservationController extends BaseController {

    /**
     * getReservation
     *
     * Récupère et affiche les réservations selon le rôle de l'utilisateur (admin/employé : toutes, proprio : les siennes).
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function getReservation(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $user = $_SESSION['user'];
    
        if ($user['Statut'] == 3 || $user['Statut'] == 2) {
            $reservations = Reservation::getAllReservation();
        } elseif ($user['Statut'] == 1) {
            $reservations = Reservation::getAllUserReservation($user['IdUtilisateur']);
        }else{
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $this->view->render($response, 'reservations.php', ['reservations' => $reservations]);
    }

    /**
     * acceptReservation
     *
     * Valide une réservation (admin uniquement) et enregistre l'ID de l'admin validateur.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
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

    /**
     * refusedReservation
     *
     * Refuse une réservation (admin uniquement) et enregistre l'ID de l'admin qui l'a refusée.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
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

    /**
     * deleteReservation
     *
     * Supprime une réservation selon son ID, sans restriction de rôle.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function deleteReservation(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $idReservation = $args['id'];

        Reservation::deleteReservation($idReservation);
        $_SESSION['form_succes'] = "Supprimé avec succès.";

        return $response->withHeader('Location', '/reservations')->withStatus(302);
    }

    /**
     * showReservationForm
     *
     * Affiche le formulaire de réservation. Charge tous les animaux si admin, sinon seulement ceux du proprio connecté.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
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

     /**
     * addReservation
     *
     * Ajoute une nouvelle réservation après vérification des champs et validation des dates.
     * Récupère l'ID du propriétaire lié à l'animal pour l'enregistrement.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
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

    /**
     * showEditForm
     *
     * Affiche le formulaire de modification d’une réservation avec les données préremplies et les animaux disponibles.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function showEditForm(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $id = $args['id'];

        $reservations = Reservation::getReservationbyId($id);

        $idUser = $_SESSION['user']['IdUtilisateur'];

        $allAnimalProprio = Reservation::getAllAnimalProprio();
        $animalProprio = Reservation::getAllAnimalProprioByUser($idUser);

        return $this->view->render($response->withStatus(302), 'reservationForm.php', ['reservations' => $reservations, 'animalProprio' => $animalProprio, 'allAnimalProprio' => $allAnimalProprio]);
    }

     /**
     * editReservation
     *
     * Modifie une réservation existante après validation des champs et des dates.
     * Met à jour l’animal et le propriétaire liés à la réservation.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
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