<?php
// app/Models/TipoVehiculo

namespace App\Models;
use App\Core\Database;
use PDO;
use Exception;

class TipoVehiculo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare("
            SELECT idtipovehiculo, tipovehiculo
            FROM tipovehiculos
            ORDER BY tipovehiculo
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTipoVehiculoByMarca(int $idmarca): array
    {
        $sql = "
            SELECT
            DISTINCT(TV.tipovehiculo), MD.idtipovehiculo 
            FROM modelos MD
            INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
            WHERE MD.idmarca = ?
            ORDER BY TV. tipovehiculo;
            ";

        try {
            $query = $this->db->prepare($sql);
            $query->execute([$idmarca]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e);
            return [];
        }
    }

}