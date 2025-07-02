<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Local
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        try {
            $query = "
            SELECT 
                l.*, 
                d.distrito, 
                p.provincia, 
                dp.departamento
            FROM locales l
            INNER JOIN distritos d ON l.iddistrito = d.iddistrito
            INNER JOIN provincias p ON d.idprovincia = p.idprovincia
            INNER JOIN departamentos dp ON p.iddepartamento = dp.iddepartamento
        ";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }



    public function create($params = []): int
    {
        $query = "INSERT INTO locales(tienda, iddistrito, idmotorpark, principal, responsable, correo, direccion, telefono, latitud, longitud) 
                    VALUES (:tienda,:iddistrito,:idmotorpark,:principal,:responsable,:correo,:direccion,:telefono,:lattidud,:longitud)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(
                array(
                    ':tienda' => $params['tienda'],
                    ':iddistrito' => $params['iddistrito'],
                    ':idmotorpark' => $params['idmotorpark'],
                    ':principal' => $params['principal'],
                    ':responsable'  => $params['responsable'],
                    ':correo' => $params['correo'], // Puede ser null
                    ':direccion' => $params['direccion'], // Puede ser null
                    ':telefono' => $params['telefono'], // Puede ser null
                    ':latitud' => $params['latitud'], // Puede ser null
                    ':longitud' => $params['longitud'] // Puede ser null
                )
            );
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}

// $local = new Local();

// var_dump($local->getAll());
