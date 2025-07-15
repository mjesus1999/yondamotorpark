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
    // Se asociara los vehiculos a la tabla DET_ORDEN_COMPRA y asi mismo el idorden se tomara el id recuperado del primer registro de la vista OC

    public function createVehiculoForOC($params = []) : int {

        try {
            $query = "CALL sp_registrarVehiculoDesdeOC(:idmodelo,:idcombsutible,:idlogistica,:idlocal,:version,:condicion,:color,:chasis,:placa,:placarotativa:,:seriemotor,:)";
            return 1;
        } catch(PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }



}