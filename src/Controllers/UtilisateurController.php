<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Animal;
use Lucancstr\GestionChenil\Models\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;


class UtilisateurController extends BaseController {

    /**
     * getUsers
     *
     * Affiche la liste des utilisateurs avec leurs animaux, uniquement accessible par un admin.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function getUsers(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $utilisateurs = Utilisateur::getAllWithAnimaux(); // Récupère tous les utilisateurs

        return $this->view->render($response, 'utilisateurs.php', [
            'utilisateurs' => $utilisateurs
        ]);
    }

    /**
     * acceptUser
     *
     * Valide un utilisateur (admin only) en changeant son statut dans la base.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function acceptUser(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $idUtilisateur = $args['id'];

        Utilisateur::validateUser($idUtilisateur);
        $_SESSION['form_succes'] = "Utilisateur validé avec succès.";

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    /**
     * refusedUser
     *
     * Refuse (supprime) un utilisateur en attente. Accessible uniquement par un admin.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function refusedUser(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $idUtilisateur = $args['id'];

        Utilisateur::refusedUser($idUtilisateur);
        $_SESSION['form_succes'] = "Supprimé avec succès.";

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    /**
     * deleteUser
     *
     * Supprime un utilisateur existant (admin uniquement) via son ID.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function deleteUser(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }
        
        $idUtilisateur = $args['id'];

        Utilisateur::refusedUser($idUtilisateur);
        $_SESSION['form_succes'] = "Supprimé avec succès.";

        return $response->withHeader('Location', '/utilisateurs')->withStatus(302);
    }

    /**
     * showUserForm
     *
     * Affiche le formulaire d’ajout d’un utilisateur (réservé à l’admin).
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */

    public function showUserForm(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $this->view->render($response, 'utilisateurForm.php');
    }

    /**
     * addUtilisateur
     *
     * Ajoute un nouvel utilisateur depuis le formulaire admin après vérification des champs et validation.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function addUtilisateur(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Reset les messages
        unset($_SESSION['form_error']);
        unset($_SESSION['form_succes']);

        $post = $request->getParsedBody();

        // Nettoyage / Filtrage des champs
        $Nom = trim($post['Nom']);
        $Prenom = trim($post['Prenom']);
        $Pseudo = trim($post['Pseudo']);
        $MotDePasse = trim($post['MotDePasse']);
        $Email = trim($post['Email']);
        $DateNaissance = trim($post['DateNaissance']);
        $Statut = trim($post['Statut']);

        $utilisateurs = [
            'Nom' => $Nom,
            'Prenom' => $Prenom,
            'Pseudo' => $Pseudo,
            'Email' => $Email,
            'DateNaissance' => $DateNaissance,
            'Statut' => $Statut
        ];

        // Vérification email valide
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['form_error'] = "L'email est invalide.";
        }

        if (
            empty($Nom) || empty($Prenom) || empty($Pseudo) || empty($MotDePasse)
            || empty($Email) || empty($DateNaissance) || empty($Statut)
        ) {
            $_SESSION['form_error'] = "Tous les champs doivent être remplis.";
        }

        // Email déjà utilisé
        if (Utilisateur::emailAlreadyExist($Email)) {
            $_SESSION['form_error'] = "Cet email est deja utilisé.";
        }

        // Validation de la date
        try {
            Utilisateur::validateBirthdate($DateNaissance, 'Y-m-d');
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Date invalide.";
        }

        try {
            Utilisateur::validatePassword($MotDePasse);
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Le mot de passe doit contenir au min. 5 lettres, une majuscule et un caractère spécial.";
        }   

        // Si pas d'erreur, on ajoute
        if (!isset($_SESSION['form_error'])) {
            try {
                Utilisateur::addUtilisateur($Nom, $Prenom, $Pseudo, $MotDePasse, $Email, $DateNaissance, $Statut);
                $_SESSION['form_succes'] = "Utilisateur ajouté avec succès.";
    
                return $response->withHeader('Location', '/utilisateurs')->withStatus(302);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return $this->view->render($response->withStatus(302), 'utilisateurForm.php', ['utilisateurs' => $utilisateurs]);
    }

    /**
     * showEditForm
     *
     * Affiche le formulaire de modification d’un utilisateur (admin uniquement) avec ses infos pré-remplies.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function showEditForm(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $id = $args['id'];
        $utilisateurs = Utilisateur::getUserbyId($id);

        return $this->view->render($response, 'utilisateurForm.php', [
            'utilisateurs' => $utilisateurs
        ]);
    }

     /**
     * editUser
     *
     * Modifie les infos d’un utilisateur existant après vérification des champs (admin uniquement).
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function editUser(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

         // Reset les messages
        unset($_SESSION['form_error']);
        unset($_SESSION['form_succes']);

        $idUtilisateur = $args['id'];

        $post = $request->getParsedBody();

        // Nettoyage / Filtrage des champs
        $Nom = trim($post['Nom']);
        $Prenom = trim($post['Prenom']);
        $Pseudo = trim($post['Pseudo']);
        $Email = trim($post['Email']);
        $DateNaissance = trim($post['DateNaissance']);
        $Statut = trim($post['Statut']);

        $utilisateurs = [
            'IdUtilisateur' => $idUtilisateur,
            'Nom' => $Nom,
            'Prenom' => $Prenom,
            'Pseudo' => $Pseudo,
            'Email' => $Email,
            'DateNaissance' => $DateNaissance,
            'Statut' => $Statut
        ];

        // Vérification email valide
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['form_error'] = "L'email est invalide.";
        }

        // Validation de la date
        try {
            Utilisateur::validateBirthdate($DateNaissance, 'Y-m-d');
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Date invalide.";
        }

        if (
            empty($Nom) || empty($Prenom) || empty($Pseudo)
            || empty($Email) || empty($DateNaissance) || empty($Statut)
        ) {
            $_SESSION['form_error'] = "Tous les champs doivent être remplis.";
        } 

        // Si pas d'erreur, on ajoute
        if (!isset($_SESSION['form_error'])) {
            try {
                Utilisateur::updateUtilisateur($Nom, $Prenom, $Pseudo, $Email, $DateNaissance, $Statut, $idUtilisateur);
                $_SESSION['form_succes'] = "Utilisateur modifié avec succès.";
    
                return $response->withHeader('Location', '/utilisateurs')->withStatus(302);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        return $this->view->render($response->withStatus(302), 'utilisateurForm.php', ['utilisateurs' => $utilisateurs]);
    }
}