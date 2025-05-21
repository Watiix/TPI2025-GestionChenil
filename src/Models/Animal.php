<?php

declare(strict_types=1);

namespace Lucancstr\GestionChenil\Models;

use Lucancstr\GestionChenil\Models\Databases;
use PDO;

class Animal
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
     * getAllWithProprietaire
     *
     * Récupère tous les animaux avec les infos de leur propriétaire (nom et prénom) via une jointure.
     *
     * @return array Liste des animaux avec données du propriétaire
     */
    public static function getAllWithProprietaire()
    {
        $pdo = Database::connection();
        $stmt = $pdo->query("SELECT ANIMAUX.*, UTILISATEURS.Nom AS NomProprietaire, UTILISATEURS.Prenom AS PrenomProprietaire 
            FROM ANIMAUX 
            LEFT JOIN UTILISATEURS ON ANIMAUX.IdProprietaire = UTILISATEURS.IdUtilisateur");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * getAll
     *
     * Récupère tous les animaux de la base de données sans jointure.
     *
     * @return array Liste de tous les animaux
     */

    public static function getAll()
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM ANIMAUX");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * getAnimalByIdAnimal
     *
     * Récupère un animal selon son ID.
     *
     * @param int $id ID de l'animal
     * @return array|null Données de l'animal ou null si non trouvé
     */

    public static function getAnimalByIdAnimal($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT IdAnimal, NomAnimal, Race, Age, Sexe, Poids, Taille, Alimentation, IdProprietaire FROM ANIMAUX WHERE IdAnimal = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * getIdProprietaireByIdAnimal
     *
     * Récupère l'ID du propriétaire d'un animal donné.
     *
     * @param int $id ID de l'animal
     * @return int|null ID du propriétaire ou null si non trouvé
     */

    public static function getIdProprietaireByIdAnimal($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT IdProprietaire FROM ANIMAUX WHERE IdAnimal = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    /**
     * addAnimal
     *
     * Ajoute un nouvel animal à la base de données avec toutes ses informations et son propriétaire.
     *
     * @param string $NomAnimal
     * @param string $Race
     * @param int $Age
     * @param string $Sexe
     * @param string $Poids
     * @param string $Taille
     * @param string $Alimentation
     * @param int $IdProprietaire
     * @return void
     */

    public static function addAnimal($NomAnimal, $Race, $Age, $Sexe, $Poids, $Taille, $Alimentation, $IdProprietaire)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("INSERT INTO ANIMAUX 
            (NomAnimal, Race, Age, Sexe, Poids, Taille, Alimentation, IdProprietaire)
            VALUES (:NomAnimal, :Race, :Age, :Sexe, :Poids, :Taille, :Alimentation, :IdProprietaire)");
    
        $stmt->bindParam(':NomAnimal', $NomAnimal);
        $stmt->bindParam(':Race', $Race);
        $stmt->bindParam(':Age', $Age, PDO::PARAM_INT);
        $stmt->bindParam(':Sexe', $Sexe);
        $stmt->bindParam(':Poids', $Poids);
        $stmt->bindParam(':Taille', $Taille);
        $stmt->bindParam(':Alimentation', $Alimentation);
        $stmt->bindParam(':IdProprietaire', $IdProprietaire, PDO::PARAM_INT);
    
        $stmt->execute();
    }

    /**
     * deleteById
     *
     * Supprime un animal de la base de données selon son ID.
     *
     * @param int $id ID de l'animal à supprimer
     * @return void
     */

    public static function deleteById($id)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("DELETE FROM ANIMAUX WHERE IdAnimal = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * updateAnimal
     *
     * Met à jour les informations d’un animal existant dans la base de données.
     *
     * @param string $NomAnimal
     * @param string $Race
     * @param int $Age
     * @param string $Sexe
     * @param string $Poids
     * @param string $Taille
     * @param string $Alimentation
     * @param int $IdProprietaire
     * @param int $IdAnimal
     * @return void
     */

    public static function updateAnimal($NomAnimal, $Race, $Age, $Sexe, $Poids, $Taille, $Alimentation, $IdProprietaire, $IdAnimal)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("UPDATE ANIMAUX SET NomAnimal = :NomAnimal, Race = :Race, Age = :Age, Sexe = :Sexe, Poids = :Poids, Taille = :Taille, Alimentation = :Alimentation, IdProprietaire = :IdProprietaire WHERE IdAnimal = :IdAnimal");

        $stmt->bindParam(':NomAnimal', $NomAnimal);
        $stmt->bindParam(':Race', $Race);
        $stmt->bindParam(':Age', $Age, PDO::PARAM_INT);
        $stmt->bindParam(':Sexe', $Sexe);
        $stmt->bindParam(':Poids', $Poids);
        $stmt->bindParam(':Taille', $Taille);
        $stmt->bindParam(':Alimentation', $Alimentation);
        $stmt->bindParam(':IdProprietaire', $IdProprietaire, PDO::PARAM_INT);
        $stmt->bindParam(':IdAnimal', $IdAnimal, PDO::PARAM_INT);

        $stmt->execute();
    }
}
