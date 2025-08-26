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
            WHERE idcargo = :idCargo AND permisos = 1
        ");
        $stmt->execute([':idCargo' => $idCargo]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /* public function tienePermiso(int $idCargo, string $modulo): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM permisos 
            WHERE idcargo = :idCargo AND moduloapp = :modulo
        ");
        $stmt->execute([
            ':idCargo' => $idCargo,
            ':modulo' => $modulo
        ]);
        return $stmt->fetchColumn() > 0;
    } */
}