<?php
// app/Models/TipoVehiculo

namespace App\Models;
use App\Core\Database;
use PDO;

class TipoVehiculo
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT idtipovehiculo, tipovehiculo
            FROM tipovehiculos
            ORDER BY tipovehiculo
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}