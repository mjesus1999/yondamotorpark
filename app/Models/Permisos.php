<?php
// app/Models/Permisos.php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class Permisos
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getPermisosByCargo(int $idCargo): array
    {
        $stmt = $this->db->prepare("
            SELECT modulo
            FROM accesos
            WHERE idcargo = :idCargo
            AND permiso = 'S'
        ");
        $stmt->execute([':idCargo' => $idCargo]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}