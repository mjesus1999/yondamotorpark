<?php
//app/Controller/Cotizacion.php

namespace App\Models;

use App\Core\Database;
use PDO;

class Cotizacion
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getRequisitos(): array
    {
        $stmt = $this->pdo->prepare("SELECT idrequisito, requisito FROM requisitos ORDER BY requisito");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}