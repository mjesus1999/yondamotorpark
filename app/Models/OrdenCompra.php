<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class OrdenCompra
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): ?array
    {

        $query = "
            SELECT 
                oc.serie,
                oc.emision,
                oc.moneda,
                con.razonsocial
            FROM ordenescompra oc 
            JOIN tiendas t ON oc.idtienda = t.idtienda
            JOIN concesionarios con ON t.idconcesionario = con.idconcesionario;
                
        ";
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
}

// $orden = new OrdenCompra();

// var_dump($orden->getAll());
