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

    /**
     * getAllReservation
     *
     * Récupère toutes les réservations, triées par état (en attente en premier) puis par date de début.
     *
     * @return array Liste des réservations
     */

    public static function getAllReservation()
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT * FROM RESERVATIONS ORDER BY (Etat = 2) ASC, DateDebut ASC");
        return $stmt->fetchAll();
    }

    /**
     * getAllUserReservation
     *
     * Récupère toutes les réservations d’un propriétaire donné, triées par état (validé, en attente, refusé) puis par date.
     *
     * @param int $IdProprietaire ID du propriétaire
     * @return array Liste des réservations du propriétaire
     */

    public static function getAllUserReservation($IdProprietaire)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM RESERVATIONS WHERE IdProprietaire = :IdProprietaire ORDER BY FIELD(Etat, 1, 0, 2), DateDebut ASC");
        $stmt->bindParam(':IdProprietaire', $IdProprietaire, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * getReservationbyId
     *
     * Récupère une réservation selon son ID.
     *
     * @param int $id ID de la réservation
     * @return array|null Données de la réservation ou null si non trouvée
     */

    public static function getReservationbyId($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM RESERVATIONS WHERE IdReservation = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * getReservationsByAnimalId
     *
     * Récupère toutes les réservations validées (Etat = 1) pour un animal donné, triées par date de début.
     *
     * @param int $IdAnimal ID de l'animal
     * @return array Liste des réservations validées pour cet animal
     */

    public static function getReservationsByAnimalId($IdAnimal)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM RESERVATIONS WHERE Etat = 1 AND IdAnimal = :idanimal ORDER BY DateDebut ASC");
        $stmt->bindParam(':idanimal', $IdAnimal, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    /**
     * validateReservation
     *
     * Valide une réservation en mettant à jour son état et l’ID de l’administrateur qui l’a validée.
     *
     * @param int $idReservation ID de la réservation
     * @param int $IdAdministrateur ID de l’administrateur
     * @return void
     */

    public static function validateReservation($idReservation, $IdAdministrateur)
    {   
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("UPDATE RESERVATIONS SET IdAdministrateur = :IdAdministrateur, Etat = 1 WHERE IdReservation = :idReservation");

        $stmt->bindParam(':IdAdministrateur', $IdAdministrateur);
        $stmt->bindParam(':idReservation', $idReservation);
        $stmt->execute();
    }

    /**
     * refusedReservation
     *
     * Refuse une réservation en mettant à jour son état et l’ID de l’administrateur qui l’a refusée.
     *
     * @param int $idReservation ID de la réservation
     * @param int $IdAdministrateur ID de l’administrateur
     * @return void
     */

    public static function refusedReservation($idReservation, $IdAdministrateur)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("UPDATE RESERVATIONS SET IdAdministrateur = :IdAdministrateur, Etat = 2 WHERE IdReservation = :idReservation");

        $stmt->bindParam(':IdAdministrateur', $IdAdministrateur);
        $stmt->bindParam(':idReservation', $idReservation);
        $stmt->execute();
    }

    /**
     * deleteReservation
     *
     * Supprime une réservation de la base de données selon son ID.
     *
     * @param int $idReservation ID de la réservation à supprimer
     * @return void
     */

    public static function deleteReservation($idReservation)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("DELETE FROM RESERVATIONS WHERE IdReservation = :idReservation");

        $stmt->execute(['idReservation' => $idReservation]);
        $stmt->execute();
    }

    /**
     * getAllAnimalProprio
     *
     * Récupère tous les animaux avec le nom et prénom de leur propriétaire, triés par nom du proprio puis nom de l’animal.
     *
     * @return array Liste des animaux avec leurs propriétaires
     */

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

    /**
     * getAllAnimalProprioByUser
     *
     * Récupère tous les animaux appartenant à un utilisateur spécifique, triés par nom.
     *
     * @param int $idUser ID du propriétaire
     * @return array Liste des animaux du propriétaire
     */

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

    /**
     * createReservation
     *
     * Crée une nouvelle réservation avec l'état par défaut à 0 (en attente).
     *
     * @param string $DateDebut
     * @param string $DateFin
     * @param float $PrixJour
     * @param string $BesoinParticulier
     * @param int $IdProprietaire
     * @param int $IdAnimal
     * @return void
     */

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

    /**
     * updateReservation
     *
     * Met à jour une réservation existante avec les nouvelles données fournies.
     *
     * @param string $DateDebut
     * @param string $DateFin
     * @param float $PrixJour
     * @param string $BesoinParticulier
     * @param int $IdProprietaire
     * @param int $IdAnimal
     * @param int $idReservation
     * @return void
     */
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

    /**
     * updateReservationIfUpdateAnimal
     *
     * Met à jour le propriétaire d'une réservation lors on modifie le propriétaire d'un animal
     *
     * @param int $IdAnimal
     * @param int $id_utilisateur
     */
    public static function updateReservationIfUpdateAnimal($IdAnimal, $id_utilisateur)
    {
        $pdo = Database::connection();
    
        $stmt = $pdo->prepare("UPDATE RESERVATIONS SET IdProprietaire = :IdProprietaire WHERE IdAnimal = :IdAnimal ");

        $stmt->bindParam(':IdAnimal', $IdAnimal);
        $stmt->bindParam(':IdProprietaire', $id_utilisateur);
    
        $stmt->execute();
    }
}