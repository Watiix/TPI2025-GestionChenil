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

    public static function generateTodayTasksIfNotExists()
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM TACHES WHERE DateCreation = CURDATE()");
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {

            $descriptions = [
                "Alimentation",
                "Promenade / activité",
                "Nettoyage enclo / cage",
                "Alimentation",
                "Promenade / activité",
                "Nettoyage enclo / cage"
            ];

            $stmt = $pdo->prepare("INSERT INTO TACHES (Description, Etat, DateCreation) VALUES (:desc, '0', CURDATE())");

            foreach ($descriptions as $desc) {
                $stmt->bindParam(':desc', $desc);
                $stmt->execute();
            }
        }
    }

    public static function getTodayUnassigned()
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("SELECT * FROM TACHES WHERE Etat = 0 AND DateCreation = CURDATE()");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function assignToEmployeeAndAnimal($idTache, $idEmploye, $idAnimal)
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare("UPDATE TACHES SET IdEmploye = :employe, IdAnimal = :animal WHERE IdTache = :tache");
    
        $stmt->bindParam(':employe', $idEmploye, PDO::PARAM_INT);
        $stmt->bindParam(':animal', $idAnimal, PDO::PARAM_INT);
        $stmt->bindParam(':tache', $idTache, PDO::PARAM_INT);
    
        $stmt->execute();
    }
}