<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

class Egreso
{

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    
}
