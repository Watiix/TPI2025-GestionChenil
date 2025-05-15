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

    public static function validateDate(string $date, string $format): void
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

    public static function getUserbyId($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT IdUtilisateur, Nom, Prenom, Pseudo, Email, MotDePasse, DateNaissance, Statut, Valide FROM UTILISATEURS WHERE IdUtilisateur = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll()
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT IdUtilisateur, Nom, Prenom, Pseudo, Email, DateNaissance, Statut, Valide FROM UTILISATEURS ORDER BY Valide ASC, Statut DESC");
        return $stmt->fetchAll();
    }

    public static function validateUser($id)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("UPDATE UTILISATEURS SET Valide = 1 WHERE IdUtilisateur = :id");

        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    
    public static function refusedUser($id)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("DELETE FROM UTILISATEURS WHERE IdUtilisateur = :id");

        $stmt->execute(['id' => $id]);
        $stmt->execute();
    }

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
}