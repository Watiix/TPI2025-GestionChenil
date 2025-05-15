<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Utilisateur;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;


class AuthController extends BaseController {

    public function createAccount(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        // reset les session
        unset($_SESSION['form_error']);
        unset($_SESSION['form_succes']);

        $postData = $request->getParsedBody();

        // Filtrage/Nettoyage
        $name = trim($postData['name']);
        $firstname = trim($postData['firstname']);
        $pseudo = trim($postData['pseudo']);
        $birthdate = trim($postData['birthdate']);
        $email = trim($postData['email']);
        $password = trim($postData['password']); // On garde brut pour ne pas casser les caractères spéciaux
        $confirmPassword = trim($postData['confirm_password']);
    
        $data = [
            'name' => $name,
            'firstname' => $firstname,
            'pseudo' => $pseudo,
            'birthdate' => $birthdate,
            'email' => $email
        ];

        // Vérification email valide
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['form_error'] = "L'email est invalide.";
        }

        // Validation de la date
        try {
            Utilisateur::validateDate($birthdate, 'Y-m-d');
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Date invalide.";
        }

        // Champs vides
        if (empty($firstname) || empty($name) || empty($email) 
            || empty($password) || empty($birthdate)) {
            $_SESSION['form_error'] = "Tous les champs doivent être remplis.";
        }

        // Email déjà utilisé
        if (Utilisateur::emailAlreadyExist($email)) {
            $_SESSION['form_error'] = "Cet email est deja utilisé.";
        }

        // Passeword ne correspondent pas
        if ($password !== $confirmPassword) {
            $_SESSION['form_error'] = "Les mots de passe ne correspondent pas.";
        }

        // Validation du mot de passe
        try {
            Utilisateur::validatePassword($password);
        } catch (\Exception $e) {
            $_SESSION['form_error'] = "Le mot de passe doit contenir au min. 5 lettres, une majuscule et un caractère spécial.";
        }    

        if (!isset($_SESSION['form_error'])) {
            try {
                Utilisateur::createAccount($name, $firstname, $pseudo, $password, $email, $birthdate);
                $_SESSION['form_succes'] = "Compte crée avec succès.";
                    return $this->renderWithoutLayout($response->withStatus(302), 'login.php');
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        return $this->renderWithoutLayout($response->withStatus(302), 'register.php', ['data' => $data]);
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        // reset la session
        unset($_SESSION['form_error']);
        unset($_SESSION['form_succes']);

        $postData = $request->getParsedBody();

        // Filtrage/Nettoyage
        $email = trim($postData['email']);
        $password = trim($postData['password']); // Pas de htmlspecialchars ici pour éviter d'affecter les caractères spéciaux des mdp

        $data = [
            'email' => $email
        ];

        if (empty($email) || empty($password)) {
            $_SESSION['form_error'] = "Tous les champs doivent être remplis.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['form_error'] = "L'email est invalide.";
        }

        if (!isset($_SESSION['form_error'])) {
            try {
                $user = Utilisateur::login($email, $password);
                $userId = $user['IdUtilisateur'];
                $userData = Utilisateur::getUserbyId($userId);
                $_SESSION['user'] = $userData;

                if($_SESSION['user']['Valide'] === 0)
                {
                    $_SESSION['form_error'] = "Votre compte n'est pas valider par un adminitrateur";
                }
                else
                {
                    return $response
                    ->withHeader('Location', '/')
                    ->withStatus(302);
                }
            } catch (\Exception $e) {
                $_SESSION['form_error'] = "Erreur de login.";
            }
        }

        return $this->renderWithoutLayout($response->withStatus(302),'login.php', ['data' => $data]);
    
    }
    
    public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface 
    {
        session_destroy();
        unset($_SESSION);
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}