<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Concesionario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Obtiene todos los Concesionarios de la DB:
    public function getAll(): ?array
    {
        $query = 'SELECT idconcesionario, ruc, razonsocial, nombrecomercial FROM concesionarios ORDER BY creado DESC';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());

            return [];
        }
    }

    // Obtener los concesionarios por RUC EN LA DB
    public function getConcesionarioByRUC($ruc = ''): ?array
    {
        $query = 'SELECT idconcesionario, ruc, razonsocial, nombrecomercial FROM concesionarios WHERE ruc = ?';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($ruc));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    // Crear el concesionario.
    public function create($params = []): int
    {
        $query = 'INSERT INTO concesionarios (ruc, razonsocial, nombrecomercial) VALUES (:ruc,:razonsocial,:nombrecomercial)';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(
                array(
                    ':ruc' => $params['ruc'],
                    ':razonsocial' => $params['razonsocial'],
                    ':nombrecomercial' => $params['nombrecomercial']
                )
            );
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    public function update($params): int
    {
        try {
            $query = 'UPDATE concesionarios SET nombrecomercial = :nombrecomercial, modificado = NOW() WHERE idconcesionario = :idconcesionario';
            $stmt = $this->db->prepare($query);
            $stmt->execute(
                array(
                    ':nombrecomercial' => $params['nombrecomercial'],
                    ':idconcesionario' => $params['idconcesionario']
                )
            );
            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    // Obtener las ordenes de compra si es que se le ha hecho alguna compra - Devuelve 0(Se puede eliminar) - Devuelvre(1) NO!!
    public function getOC($idconcesionario = -1): int
    {
        try {
            $stmt = $this->db->prepare('call spu_concesionarios_obtener_oc(?,@registros)');
            $stmt->execute(
                array($idconcesionario)
            );
            $response = $this->db->query('SELECT @registros AS registros')->fetch(PDO::FETCH_ASSOC);
            return (int) $response['registros'];
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    public function delete($idconcesionario = -1): int
    {
        try {
            $stmt = $this->db->prepare('call spu_concesionarios_eliminar_todo(?)');
            $stmt->execute(array($idconcesionario));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}
        