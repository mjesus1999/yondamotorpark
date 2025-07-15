<?php
//app/Controllers/Vehiculo.php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class Vehiculo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getDisponibles(int $idmarca, int $idtipovehiculo, string $modelo, string $anio): array
    {
        $sql = "
          SELECT
            v.idvehiculo,
            m.modelo,
            m.anio,
            v.version,
            v.color,
            v.placa
          FROM vehiculos v
          JOIN modelos m ON v.idmodelo = m.idmodelo
          WHERE m.idmarca = :idmarca
            AND m.idtipovehiculo = :idtipovehiculo
            AND m.modelo = :modelo
            AND m.anio = :anio
            AND v.disponibilidad = 'libre'
          ORDER BY v.version, v.placa
        ";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->bindParam(':idtipovehiculo', $idtipovehiculo, PDO::PARAM_INT);
            $stmt->bindParam(':modelo', $modelo, PDO::PARAM_STR);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

}