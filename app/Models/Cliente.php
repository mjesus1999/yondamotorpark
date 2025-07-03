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
            $cmd = $this->db->prepare($query);
            $cmd->execute([
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


}