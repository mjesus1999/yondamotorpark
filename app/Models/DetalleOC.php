<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

class DetalleOC
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($params = []): int
    {
        $query = "INSERT INTO detordencompra(idordencompra,idvehiculo,preciocompra) VALUES(:idordencompra,:idvehiculo,:preciocompra)";
        try {

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(

                ':idordencompra' => $params['idordencompra'],
                ':idvehiculo' => $params['idvehiculo'],
                ':preciocompra' => $params['preciocompra']
            ));
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}
