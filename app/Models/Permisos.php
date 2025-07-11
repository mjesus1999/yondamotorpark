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

    public function setPermiso(int $idCargo, string $modulo, bool $permisos): bool
    {
        $stmt = $this->db->prepare("
        INSERT INTO accesos (idcargo, modulo, permisos)
        VALUES (:idcargo, :modulo, :permiso)
        ON DUPLICATE KEY UPDATE permiso = :permiso
        ");
        $p = $permisos ? 'S' : 'N';
        $stmt->bindParam(':idcargo', $idCargo, PDO::PARAM_INT);
        $stmt->bindParam(':modulo', $modulo, PDO::PARAM_STR);
        $stmt->bindParam(':permisos', $p, PDO::PARAM_STR);
        return $stmt->execute();
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