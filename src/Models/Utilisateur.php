<?php

declare(strict_types=1);

namespace Lucancstr\GestionChenil\Models;

use Lucancstr\GestionChenil\Models\Databases;
use PDO;

class Utilisateur
{
    // protected $map = [
    //     ''
    // ];

    public ?int $idUser = null;

    public ?string $firstname = null;

    public ?string $name = null;

    public ?string $email = null;

    public ?string $password = null;

    public ?date $birthdate = null;

    /**
     * validatePassword
     *
     * Vérifie si le mot de passe est assez long, commence par une majuscule et contient un caractère spécial.
     *
     * @param string $password
     * @return void
     */

    public static function validatePassword(string $password): void
    {
        if (strlen($password) < 5) {
            throw new \Exception("Le mot de passe doit contenir au moins 5 caractères.");
        }

        // Vérifie si le premier caractère est une majuscule
        if (!ctype_upper($password[0])) {
            throw new \Exception("Le mot de passe doit commencer par une majuscule.");
        }

        if (!preg_match('/[\W]/', $password)) { // \W = tout ce qui n'est pas a-zA-Z0-9_
            throw new \Exception("Le mot de passe doit contenir au moins un caractère spécial.");
        }
    }

    /**
     * validateDate
     *
     * Vérifie si une date respecte bien le format donné.
     *
     * @param string $date
     * @param string $format
     * @return void
     */

    public static function validateDate(string $date, string $format): void
    {
        $d = \DateTime::createFromFormat($format, $date);

        // Si la création échoue ou si la date après formatage ne correspond pas à l'entrée -> KO
        if (!$d || $d->format($format) !== $date) {
            throw new \Exception("La date '$date' n'est pas valide. Format attendu : $format");
        }
    }

    /**
     * validateBirthdate
     *
     * Vérifie si la date de naissance est bien au bon format et qu'elle n'est pas dans le futur.
     *
     * @param string $date La date à vérifier
     * @param string $format Le format attendu (ex: 'Y-m-d')
     * @return void
     * @throws \Exception Si la date est invalide ou supérieure à la date du jour
     */

    public static function validateBirthdate(string $date, string $format): void
    {
        $d = \DateTime::createFromFormat($format, $date);

        // Si la création échoue ou si la date après formatage ne correspond pas à l'entrée -> KO
        if (!$d || $d->format($format) !== $date) {
            throw new \Exception("La date '$date' n'est pas valide. Format attendu : $format");
        }

         // Comparer avec la date du jour
        $today = new \DateTime(); // maintenant
        if ($d > $today) {
            throw new \Exception("La date '$date' ne peut pas être dans le futur.");
        }
    }
    
    /**
     * emailAlreadyExist
     *
     * Vérifie si un email est déjà utilisé dans la base.
     *
     * @param string $email
     * @return bool true si l'email existe, false sinon
     */

    public static function emailAlreadyExist($email) :bool{
        $pdo = Database::connection();

        $stmt = $pdo->prepare("SELECT * FROM UTILISATEURS WHERE Email = :Email");
        $stmt->bindParam(':Email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user){
            return true;
        }

        return false;     
    }
    
    /**
     * createAccount
     *
     * Crée un nouveau compte utilisateur avec hash du mot de passe et statut par défaut à 1 (non validé).
     *
     * @param string $name
     * @param string $firstname
     * @param string $pseudo
     * @param string $password
     * @param string $email
     * @param string $birthdate
     * @return void
     * @throws \Exception En cas d'erreur lors de l'insertion en base
     */

    public static function createAccount($name, $firstname, $pseudo, $password, $email, $birthdate) {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
            $pdo = Database::connection();
            $stmt = $pdo->prepare("
                INSERT INTO UTILISATEURS (Nom, Prenom, Pseudo, MotDePasse, Email, DateNaissance, Statut)
                VALUES (:name, :firstname, :pseudo, :password, :email, :birthdate, 1)
            ");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->bindParam(':password', $passwordHash); // ici tu utilises bien le hash
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->execute();
        } catch (\PDOException $e) {
            // Propage l'exception au contrôleur
            throw new \Exception("Erreur lors de la création du compte : " . $e->getMessage());
        }
    }

    /**
     * login
     *
     * Vérifie si l'email existe et si le mot de passe est correct. Retourne l'utilisateur si ok.
     *
     * @param string $email
     * @param string $password
     * @return array Infos de l'utilisateur connecté
     * @throws \Exception Si l'email n'existe pas ou si le mot de passe est incorrect
     */
    public static function login($email, $password) {

        $pdo = Database::connection();

        $stmt = $pdo->prepare("SELECT * FROM UTILISATEURS WHERE Email = :Email");
        $stmt->bindParam(':Email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['MotDePasse'])) {
                return $user;
            } else {
                throw new \Exception("Mot de passe incorrect !");
            }
        } else {
            // Email n'existe pas
            throw new \Exception("Email inexistant !");
        }
    }

    /**
     * getUserbyId
     *
     * Récupère les infos d’un utilisateur selon son ID.
     *
     * @param int $id ID de l'utilisateur
     * @return array|null Données de l'utilisateur ou null si non trouvé
     */

    public static function getUserbyId($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT IdUtilisateur, Nom, Prenom, Pseudo, Email, MotDePasse, DateNaissance, Statut, Valide FROM UTILISATEURS WHERE IdUtilisateur = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * getAll
     *
     * Récupère tous les utilisateurs triés par validation (non validés d'abord) puis par statut (admin > employé > proprio).
     *
     * @return array Liste des utilisateurs
     */

    public static function getAll()
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT IdUtilisateur, Nom, Prenom, Pseudo, Email, DateNaissance, Statut, Valide FROM UTILISATEURS ORDER BY Valide ASC, Statut DESC");
        return $stmt->fetchAll();
    }

    /**
     * getAcceptedUser
     *
     * Récupère tous les utilisateurs validés (Valide = 1), triés par validation puis statut.
     *
     * @return array Liste des utilisateurs validés
     */

    public static function getAcceptedUser()
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT IdUtilisateur, Nom, Prenom, Pseudo, Email, DateNaissance, Statut, Valide FROM UTILISATEURS WHERE Valide = 1 ORDER BY Valide ASC, Statut DESC");
        return $stmt->fetchAll();
    }

    /**
     * getEmployes
     *
     * Récupère tous les utilisateurs ayant le statut employé (Statut = 2).
     *
     * @return array Liste des employés
     */
    public static function getEmployes()
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT * FROM UTILISATEURS WHERE Statut = 2");
        return $stmt->fetchAll();
    }

    /**
     * validateUser
     *
     * Valide un utilisateur en mettant à jour son champ `Valide` à 1.
     *
     * @param int $id ID de l'utilisateur à valider
     * @return void
     */
    public static function validateUser($id)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("UPDATE UTILISATEURS SET Valide = 1 WHERE IdUtilisateur = :id");

        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    
    /**
     * refusedUser
     *
     * Supprime un utilisateur de la base selon son ID (cas d'un refus).
     *
     * @param int $id ID de l'utilisateur à supprimer
     * @return void
     */
    public static function refusedUser($id)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("DELETE FROM UTILISATEURS WHERE IdUtilisateur = :id");

        $stmt->execute(['id' => $id]);
        $stmt->execute();
    }

    /**
     * addUtilisateur
     *
     * Ajoute un nouvel utilisateur en base avec un mot de passe hashé.
     *
     * @param string $Nom
     * @param string $Prenom
     * @param string $Pseudo
     * @param string $MotDePasse
     * @param string $Email
     * @param string $DateNaissance
     * @param int $Statut
     * @return void
     */
    public static function addUtilisateur($Nom, $Prenom, $Pseudo, $MotDePasse, $Email, $DateNaissance, $Statut)
    {
        $passwordHash = password_hash($MotDePasse, PASSWORD_DEFAULT);

        $pdo = Database::connection();
    
        $stmt = $pdo->prepare("INSERT INTO UTILISATEURS (Nom, Prenom, Pseudo, MotDePasse, Email, DateNaissance, Statut) VALUES (:Nom, :Prenom, :Pseudo, :MotDePasse, :Email, :DateNaissance, :Statut)");
    
        $stmt->bindParam(':Nom', $Nom);
        $stmt->bindParam(':Prenom', $Prenom);
        $stmt->bindParam(':Pseudo', $Pseudo);
        $stmt->bindParam(':MotDePasse', $passwordHash,);
        $stmt->bindParam(':Email', $Email);
        $stmt->bindParam(':DateNaissance', $DateNaissance);
        $stmt->bindParam(':Statut', $Statut);
    
        $stmt->execute();
    }
    
    /**
     * updateUtilisateur
     *
     * Met à jour les infos d’un utilisateur existant dans la base.
     *
     * @param string $Nom
     * @param string $Prenom
     * @param string $Pseudo
     * @param string $Email
     * @param string $DateNaissance
     * @param int $Statut
     * @param int $idUtilisateur
     * @return void
     */
    public static function updateUtilisateur($Nom, $Prenom, $Pseudo, $Email, $DateNaissance, $Statut, $idUtilisateur)
    {
        $pdo = Database::connection();
    
        $stmt = $pdo->prepare("UPDATE UTILISATEURS SET Nom = :Nom, Prenom = :Prenom, Pseudo = :Pseudo, Email = :Email, DateNaissance = :DateNaissance, Statut = :Statut WHERE IdUtilisateur = :idUtilisateur");

    
        $stmt->bindParam(':Nom', $Nom);
        $stmt->bindParam(':Prenom', $Prenom);
        $stmt->bindParam(':Pseudo', $Pseudo);
        $stmt->bindParam(':Email', $Email);
        $stmt->bindParam(':DateNaissance', $DateNaissance);
        $stmt->bindParam(':Statut', $Statut);
        $stmt->bindParam(':idUtilisateur', $idUtilisateur);
    
        $stmt->execute();
    }

    /**
     * getAnimauxByUserId
     *
     * Récupère tous les animaux appartenant à un utilisateur donné.
     *
     * @param int $id ID du propriétaire
     * @return array Liste des animaux de l'utilisateur
     */

    public static function getAnimauxByUserId($id)
    {
        $pdo = Database::connection();
    
        $stmt = $pdo->prepare("SELECT * FROM ANIMAUX WHERE IdProprietaire = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * getAllWithAnimaux
     *
     * Récupère tous les utilisateurs validés avec leurs animaux associés.
     *
     * @return array Liste des utilisateurs avec une clé 'animaux' contenant leurs animaux
     */
    public static function getAllWithAnimaux()
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM UTILISATEURS WHERE Valide = 1 ORDER BY Valide ASC, Statut DESC");
        $stmt->execute();
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($utilisateurs as &$user) {
            $user['animaux'] = self::getAnimauxByUserId($user['IdUtilisateur']);
        }
        
        return $utilisateurs;
    }

    /**
     * InsertRapport
     *
     * Insère un nouveau rapport avec son contenu, la date du jour et l'ID de l'administrateur.
     *
     * @param string $contenu Contenu du rapport
     * @param int $id ID de l'administrateur
     * @return void
     */
    public static function InsertRapport($contenu, $id)
    {
        $pdo = Database::connection();
    
        $stmt = $pdo->prepare("INSERT INTO RAPPORT (Contenu, DateGeneration, IdAdministrateur) VALUES (:contenu , CURDATE(), :idAdministrateur)");
    
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':idAdministrateur', $id);
    
        $stmt->execute();
    }
}