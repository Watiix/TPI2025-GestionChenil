<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Animal;
use Lucancstr\GestionChenil\Models\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;


class AnimalController extends BaseController {

    /**
     * getAnimaux
     *
     * Récupère les animaux à afficher selon le rôle du user (admin, employé ou proprio).
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function getAnimaux(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $id = $_GET['id'] ?? null;
        $user = $_SESSION['user'];
        
        if ($user['Statut'] !== 1) {
            if(isset($id))
                if($id == 0)
                    $animaux = Animal::getAllWithProprietaire();
                else
                    $animaux = Animal::getAnimalByIdAnimal($id);
            else
                $animaux = Animal::getAllWithProprietaire();

            $utilisateurs = Utilisateur::getAcceptedUser();

            return $this->view->render($response, 'animaux.php', ['animaux' => $animaux, 'utilisateurs' => $utilisateurs]);
        } else {
            $animaux = Utilisateur::getAnimauxByUserId($user['IdUtilisateur']);
        }


        return $this->view->render($response, 'animaux.php', ['animaux' => $animaux]);
    }

    /**
     * showAnimalFormPage
     *
     * Affiche le formulaire d’ajout d’un animal. Si admin, charge aussi la liste des utilisateurs.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function showAnimalFormPage(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $isAdmin = ($_SESSION['user']['Statut'] ?? null) === 3;

        if ($isAdmin) {
            $utilisateurs = Utilisateur::getAcceptedUser();

            return $this->view->render($response, 'animalForm.php', [
                'isAdmin' => $isAdmin,
                'utilisateurs' => $utilisateurs
            ]);
        }
        
        return $this->view->render($response, 'animalForm.php', [
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * addAnimal
     *
     * Ajoute un nouvel animal après vérif des champs. Associe l’animal à l’utilisateur (admin ou proprio).
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function addAnimal(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        // reset les session
        unset($_SESSION['form_error']);
        unset($_SESSION['form_succes']);

        $user = $_SESSION['user'];
        $post = $request->getParsedBody();

        // Filtrage/Nettoyage
        $NomAnimal = trim($post['NomAnimal']);
        $Race = trim($post['Race']);
        $Age = trim($post['Age']);
        $Sexe = trim($post['Sexe']);
        $Poids = trim($post['Poids']);
        $Taille = trim($post['Taille']);
        $Alimentation = trim($post['Alimentation']);

        // Champs vides
        if (empty($NomAnimal) || empty($Race) || empty($Age) 
            || empty($Sexe) || empty($Poids) || empty($Taille) || empty($Alimentation)) {
            $_SESSION['form_error'] = "Tous les champs doivent être remplis.";
        }

        if($user['Statut'] === 3)
        {
            $id_utilisateur = $post['IdProprietaire'];
        }
        else
        {
            $id_utilisateur = $user['IdUtilisateur'];
        }

        if (!isset($_SESSION['form_error'])) {
            Animal::addAnimal($NomAnimal,$Race, $Age, $Sexe, $Poids, $Taille, $Alimentation, $id_utilisateur);
            $_SESSION['form_succes'] = "Animal ajouté avec succès.";   
        }

        return $response->withHeader('Location', '/animaux')->withStatus(302);
    }

    /**
     * showEditForm
     *
     * Affiche le formulaire pré-rempli pour modifier un animal. Si admin, charge aussi les utilisateurs.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function showEditForm(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $id = $args['id'];
        $animaux = Animal::getAnimalByIdAnimal($id);
        $isAdmin = $_SESSION['user']['Statut'] === 3;
    
        // Optionnel : récupère les utilisateurs pour l’admin
        if ($isAdmin) {
            $utilisateurs = Utilisateur::getAcceptedUser();

            return $this->view->render($response, 'animalForm.php', [
                'isAdmin' => $isAdmin,
                'utilisateurs' => $utilisateurs,
                'animaux' => $animaux
            ]);
        }

        return $this->view->render($response, 'animalForm.php', [
            'isAdmin' => $isAdmin,
            'animaux' => $animaux
        ]);
    }

    /**
     * deleteAnimal
     *
     * Supprime un animal selon son ID. Stocke un message de succès ou d'erreur en session.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function deleteAnimal(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $id = $args['id'];

        try {
            Animal::deleteById($id);
            $_SESSION['form_succes'] = "Animal supprimé avec succès.";
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Erreur lors de la suppression.";
        }
    
        return $response->withHeader('Location', '/animaux')->withStatus(302);
    }
    
    /**
     * updateAnimal
     *
     * Modifie un animal existant avec les nouvelles infos du formulaire. Vérifie les champs et le rôle du user.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function updateAnimal(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        // reset les session
        unset($_SESSION['form_error']);
        unset($_SESSION['form_succes']);

        $IdAnimal = $args['id'];

        $user = $_SESSION['user'];
        $post = $request->getParsedBody();

        // Filtrage/Nettoyage
        $NomAnimal = trim($post['NomAnimal']);
        $Race = trim($post['Race']);
        $Age = trim($post['Age']);
        $Sexe = trim($post['Sexe']);
        $Poids = trim($post['Poids']);
        $Taille = trim($post['Taille']);
        $Alimentation = trim($post['Alimentation']);

        // Champs vides
        if (empty($NomAnimal) || empty($Race) || empty($Age) 
            || empty($Sexe) || empty($Poids) || empty($Taille) || empty($Alimentation)) {
            $_SESSION['form_error'] = "Tous les champs doivent être remplis.";
        }

        if($user['Statut'] === 3)
        {
            $id_utilisateur = $post['IdProprietaire'];
        }
        else
        {
            $id_utilisateur = $user['IdUtilisateur'];
        }

        if (!isset($_SESSION['form_error'])) {
            Animal::updateAnimal($NomAnimal,$Race, $Age, $Sexe, $Poids, $Taille, $Alimentation, $id_utilisateur, $IdAnimal);
            $_SESSION['form_succes'] = "Animal modifie avec succès.";   
        }

        return $response->withHeader('Location', '/animaux')->withStatus(302);
    }
}