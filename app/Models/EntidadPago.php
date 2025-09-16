<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class EntidadPago
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    
    public function getAllEntidadesPago(): array
    {
        $query = 'SELECT identidadpago, entidad FROM entidadespago ORDER BY entidad ASC;';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log('Error en getEntidadesPago PagosOC: ' . $error->getMessage());
            return [];
        }
    }
}

// $orden = new OrdenCompra();

// var_dump($orden->getAll());
