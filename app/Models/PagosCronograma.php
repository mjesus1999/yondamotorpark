<?php

namespace App\Models;

use App\Core\Database;
use FFI\CData;
use PDO;
use PDOException;

class PagosCronograma
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function add(): int {
       $query = "";
        try {


        } catch(PDOException $error) {
             error_log($error->getMessage());
              return - 1;
        }
    }

}