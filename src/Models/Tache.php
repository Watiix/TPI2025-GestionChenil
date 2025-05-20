<?php

declare(strict_types=1);

namespace Lucancstr\GestionChenil\Models;

use Lucancstr\GestionChenil\Models\Databases;
use PDO;

class Tache
{
    // protected $map = [
    //     ''
    // ];

    public ?int $IdTache = null;

    public ?string $Description = null;

    public ?int $Etat = null;

    public ?date $DateCreation = null;

    public ?int $IdAnimal = null;

    public ?int $IdEmploye = null;

    public static function generateTodayTasksIfNotExists($animaux)
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM TACHES WHERE Date = CURDATE()");
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {

            $titres = [
                "Alimentation",
                "Promenade / activité",
                "Nettoyage enclo / cage"
            ];

            $descriptions = [
                "Donner la portion quotidienne de croquettes.",
                "Sortie de 20 minutes à l’extérieur avec surveillance.",
                "Nettoyer et désinfecter l’espace de l’animal."
            ];

            $stmt = $pdo->prepare("INSERT INTO TACHES (Titre, Description, Etat, Date, IdAnimal) VALUES (:titre, :desc, '0', CURDATE(), :idanimal)");
            foreach ($animaux as $animal) {
                for ($i = 0; $i < count($titres); $i++) {
                    $stmt->bindParam(':titre', $titres[$i]);
                    $stmt->bindParam(':desc', $descriptions[$i]);
                    $stmt->bindParam(':idanimal', $animal['IdAnimal']);
                    $stmt->execute();
                }
            }
        }
    }

    public static function getTodayUnassigned()
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM TACHES WHERE Etat = 0 AND Date = CURDATE()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function assignToEmployee($idTache, $idEmploye)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("UPDATE TACHES SET IdEmploye = :employe WHERE IdTache = :tache");
    
        $stmt->bindParam(':employe', $idEmploye, PDO::PARAM_INT);
        $stmt->bindParam(':tache', $idTache, PDO::PARAM_INT);
    
        $stmt->execute();
    }

    public static function getToday()
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("
            SELECT 
                t.*,
                a.IdAnimal,
                a.NomAnimal,
                u.Nom AS NomEmploye,
                u.Prenom AS PrenomEmploye
            FROM TACHES t
            LEFT JOIN ANIMAUX a ON t.IdAnimal = a.IdAnimal
            LEFT JOIN UTILISATEURS u ON t.IdEmploye = u.IdUtilisateur
            WHERE DATE(t.Date) = CURDATE() ORDER BY t.IdEmploye ASC
        ");
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public static function validateTache($idTache)
    {
        $pdo = Database::connection();
        
        $stmt = $pdo->prepare("UPDATE TACHES SET Etat = 1 WHERE IdTache = :idTache");

        $stmt->bindParam(':idTache', $idTache); 
        $stmt->execute();
    }

    public static function getTachesByAnimalId($idAnimal)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM TACHES WHERE Etat = 1 AND IdAnimal = :idanimal");
        $stmt->bindParam(':idanimal', $idAnimal);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}   