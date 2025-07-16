<?php
//app/Controllers/FormatoCotizacion.php

namespace App\Models;

use App\Core\Database;
use PDO;

class FormatoCotizacion
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $query = "
            SELECT 
                idformato, 
                tipocotizacion
            FROM formatocotizacion
            ORDER BY fechainicio DESC;
        ";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
