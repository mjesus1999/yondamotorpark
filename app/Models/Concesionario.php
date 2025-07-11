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
        $query = "SELECT idconcesionario, ruc, razonsocial, nombrecomercial FROM concesionarios ORDER BY creado DESC";
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
        $query = "SELECT idconcesionario, ruc, razonsocial, nombrecomercial FROM concesionarios WHERE ruc = ?";
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


    public function create($params = []): int
    {
        $query = "INSERT INTO concesionarios (ruc, razonsocial, nombrecomercial) VALUES (:ruc,:razonsocial,:nombrecomercial)";
        try {
            $cmd = $this->db->prepare($query);
            $cmd->execute(
                array(
                    ':ruc' => $params['ruc'],
                    ':razonsocial' => $params['razonsocial'],
                    ':nombrecomercial' =>  $params['nombrecomercial']
                )
            );
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}
