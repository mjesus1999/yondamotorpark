<?php
// app/Models/Vehiculo.php
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

    /**
     * Trae los vehículos disponibles filtrando por marca, tipo, modelo y año
     * @param int $idmarca
     * @param int $idtipovehiculo
     * @param int $idmodelo
     * @param string $anio  (p.ej. '2025')
     * @return array
     */
    public function getByFilters(int $idmarca, int $idtipovehiculo, int $idmodelo, string $anio): array
    {
        $sql = "
          SELECT 
            v.idvehiculo, md.modelo, md.anio, v.version, v.condicion,
            c.combustible, v.color, v.precioventa
          FROM vehiculos v
          JOIN modelos md 
            ON md.idmodelo = v.idmodelo
          JOIN tipovehiculos tv 
            ON tv.idtipovehiculo = md.idtipovehiculo
          JOIN marcas mr 
            ON mr.idmarca = md.idmarca
          JOIN combustible c 
            ON c.idcombustible = v.idcombustible
          WHERE mr.idmarca = :idmarca
            AND tv.idtipovehiculo = :idtipo
            AND md.idmodelo = :idmodelo
            AND md.anio = :anio
            AND v.disponibilidad = 'libre'
          ORDER BY md.modelo, v.version;
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->bindParam(':idtipo', $idtipovehiculo, PDO::PARAM_INT);
            $stmt->bindParam(':idmodelo', $idmodelo, PDO::PARAM_INT);
            $stmt->bindParam(':anio', $anio, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
