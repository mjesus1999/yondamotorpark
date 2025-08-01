<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

class Vehiculo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Se creará los vehículos para una orden de compra - Se mandará
    
    public function createVehiculoOC($params = []): int
    {

        $query = "CALL sp_vehiculo_OC_registrar(:idmodelo,:idcombustible,:version,:condicion,:color,:chasis,:placa,:placarotativa,:seriemotor)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idmodelo' => $params['idmodelo'],
                ':idcombustible' => $params['idcombustible'],
                ':version' => $params['version'],
                ':condicion' => $params['condicion'],
                ':color' => $params['color'],
                ':chasis' => $params['chasis'],
                ':placa' => $params['placa'],
                ':placarotativa' => $params['placarotativa'],
                ':seriemotor' => $params['seriemotor']

            ));

            $idVehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return (int)$idVehiculo['last_id'];

        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}
