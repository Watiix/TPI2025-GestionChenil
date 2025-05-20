<?php

declare(strict_types=1);

namespace Lucancstr\GestionChenil\Models;

use Lucancstr\GestionChenil\Models\Databases;
use PDO;

class Reservation
{
    // protected $map = [
    //     ''
    // ];

    public ?int $idReservation = null;

    public ?date $DateDebut = null;

    public ?date $DateFin = null;

    public ?floatval $PrixJour = null;

    public ?string $BesoinParticulier = null;

    public ?int $Etat = null;

    public ?int $IdProprietaire = null;

    public ?int $IdAdministrateur = null;

    public ?int $IdAnimal = null;


    public static function getAllReservation()
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT * FROM RESERVATIONS ORDER BY (Etat = 2) ASC, DateDebut ASC");
        return $stmt->fetchAll();
    }

    public static function getAllUserReservation($IdProprietaire)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM RESERVATIONS WHERE IdProprietaire = :IdProprietaire ORDER BY FIELD(Etat, 1, 0, 2), DateDebut ASC");
        $stmt->bindParam(':IdProprietaire', $IdProprietaire, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    public static function validateReservation($idReservation, $IdAdministrateur)
    {   
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("UPDATE RESERVATIONS SET IdAdministrateur = :IdAdministrateur, Etat = 1 WHERE IdReservation = :idReservation");

        $stmt->bindParam(':IdAdministrateur', $IdAdministrateur);
        $stmt->bindParam(':idReservation', $idReservation);
        $stmt->execute();
    }

    public static function refusedReservation($idReservation, $IdAdministrateur)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("UPDATE RESERVATIONS SET IdAdministrateur = :IdAdministrateur, Etat = 2 WHERE IdReservation = :idReservation");

        $stmt->bindParam(':IdAdministrateur', $IdAdministrateur);
        $stmt->bindParam(':idReservation', $idReservation);
        $stmt->execute();
    }

    public static function deleteReservation($idReservation)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("DELETE FROM RESERVATIONS WHERE IdReservation = :idReservation");

        $stmt->execute(['idReservation' => $idReservation]);
        $stmt->execute();
    }

    public static function getAllAnimalProprio()
    {
        $pdo = Database::connection();

        $sql = "SELECT a.IdAnimal, a.NomAnimal, a.Race, a.IdProprietaire, u.Nom AS NomProprio, u.Prenom AS PrenomProprio
            FROM ANIMAUX a
            INNER JOIN UTILISATEURS u ON a.IdProprietaire = u.IdUtilisateur
            ORDER BY u.Nom ASC, a.NomAnimal ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getAllAnimalProprioByUser($idUser)
    {
        $pdo = Database::connection();

        $sql = "SELECT IdAnimal, NomAnimal, Race, IdProprietaire
            FROM ANIMAUX
            WHERE IdProprietaire = :idUser
            ORDER BY NomAnimal ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function createReservation($DateDebut, $DateFin, $PrixJour, $BesoinParticulier, $IdProprietaire, $IdAnimal)
    {
        $pdo = Database::connection();
    
        $stmt = $pdo->prepare("INSERT INTO RESERVATIONS (DateDebut, DateFin, PrixJour, BesoinParticulier, IdProprietaire, IdAnimal, Etat) VALUES (:DateDebut, :DateFin, :PrixJour, :BesoinParticulier, :IdProprietaire, :IdAnimal, 0)");
    
        $stmt->bindParam(':DateDebut', $DateDebut);
        $stmt->bindParam(':DateFin', $DateFin);
        $stmt->bindParam(':PrixJour', $PrixJour);
        $stmt->bindParam(':BesoinParticulier', $BesoinParticulier);
        $stmt->bindParam(':IdProprietaire', $IdProprietaire);
        $stmt->bindParam(':IdAnimal', $IdAnimal);
    
        $stmt->execute();
    }

    public static function getReservationbyId($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM RESERVATIONS WHERE IdReservation = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updateReservation($DateDebut, $DateFin, $PrixJour, $BesoinParticulier,$IdProprietaire, $IdAnimal, $idReservation)
    {
        $pdo = Database::connection();
    
        $stmt = $pdo->prepare("UPDATE RESERVATIONS SET DateDebut = :DateDebut, DateFin = :DateFin, PrixJour = :PrixJour, BesoinParticulier = :BesoinParticulier, IdProprietaire = :IdProprietaire, IdAnimal = :IdAnimal WHERE IdReservation = :idReservation");

        $stmt->bindParam(':DateDebut', $DateDebut);
        $stmt->bindParam(':DateFin', $DateFin);
        $stmt->bindParam(':PrixJour', $PrixJour);
        $stmt->bindParam(':BesoinParticulier', $BesoinParticulier);
        $stmt->bindParam(':IdProprietaire', $IdProprietaire);
        $stmt->bindParam(':IdAnimal', $IdAnimal);
        $stmt->bindParam(':idReservation', $idReservation);
    
        $stmt->execute();
    }   

    public static function getReservationsByAnimalId($IdAnimal)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM RESERVATIONS WHERE Etat = 1 AND IdAnimal = :idanimal ORDER BY DateDebut ASC");
        $stmt->bindParam(':idanimal', $IdAnimal, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}