<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;


class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($params = []):int
    {
        $query = "INSERT INTO clientes(idpersona, idempresa, idcolregistra, idcolactualiza, tipocliente)
              VALUES(:idpersona, :idempresa, :idcolregistra, :idcolactualiza, :tipocliente)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idpersona' => $params['idpersona'] ,
                ':idempresa' => $params['idempresa'],
                ':idcolregistra' => $params['idcolregistra'],
                ':idcolactualiza' => $params['idcolactualiza'],
                ':tipocliente' => $params['tipocliente'],
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }


     public function disabled($idcliente = -1):int {
        try {
            $query = "UPDATE clientes SET estado = 'INACT' WHERE idcliente = :id ";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':id' => $idcliente));
            
            return (int) $stmt->rowCount();


        } catch(PDOException $error) {
            error_log($error->getMessage());
            return - 1;
        }
    }


}