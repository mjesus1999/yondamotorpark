<?php

namespace App\Models;

use App\Core\Database;
use PDO;

use PDOException;

class FichaSolicitud
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    


}