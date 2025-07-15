<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class TipoVehiculo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }




    public function getTipoVehiculoByMarca(int $idmarca): array
    {
        $query = "
                SELECT
                DISTINCT(TV.tipovehiculo), MD.idtipovehiculo 
                FROM modelos MD
                INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
                WHERE MD.idmarca = ?
                ORDER BY TV. tipovehiculo;
                ";
        try {
            $query = $this->db->prepare($query);
            $query->execute([$idmarca]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e);
            return [];
        }
    }
}
