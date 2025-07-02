<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Motorpark
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }







public function getMotorPark()
    {
        try {
            $query = "SELECT idmotorpark, nombrecomercial FROM motorpark";
            $cmd = $this->db->prepare($query);
            $cmd->execute();
            $results = $cmd->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

}