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


    public function getTipoClienteById(int $id): ?string
    {
        try {
            $query = "SELECT tipocliente FROM clientes WHERE idcliente = :idcliente";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idcliente' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['tipocliente'] : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }


    public function create($params = []): int
    {
        $query = "INSERT INTO clientes(idpersona, idempresa, idcolregistra, idcolactualiza, tipocliente)
              VALUES(:idpersona, :idempresa, :idcolregistra, :idcolactualiza, :tipocliente)";
        try {
            $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idpersona' => $params['idpersona'],
                ':idempresa' => $params['idempresa'],
                ':idcolregistra' => $idUsuario,
                ':idcolactualiza' => $params['idcolactualiza'],
                ':tipocliente' => $params['tipocliente'],
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }


    public function disabled($idcliente = -1): int
    {
        try {
            $query = "UPDATE clientes SET estado = 'INACT' WHERE idcliente = :id ";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':id' => $idcliente));

            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}
