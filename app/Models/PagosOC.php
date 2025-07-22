<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class PagosOC
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    public function  create($params = []) : int {

        $query = "INSERT INTO pagosOC(idorden,idlogistica,amortizacion,comprobante) VALUES(:idorden,:idlogistica,:amortizacion,:comprobante)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idorden' => $params['idorden'],
                ':idlogistica' => $params['idlogistica'],
                ':amortizacion' => $params['amortizacion'],
                ':comprobante' => $params['comprobante']

            ));

            return (int) $this->db->lastInsertId();

    

        }catch(PDOException $error) {
            error_log($error);
            return - 1;
        }
        
    }








}

// $orden = new OrdenCompra();

// var_dump($orden->getAll());
