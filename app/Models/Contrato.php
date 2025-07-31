<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Contrato
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    


}