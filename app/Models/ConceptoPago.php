<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class ConceptoPago
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getConceptos(): array
    {
        $query = "SELECT idconcepto,concepto FROM conceptospago;";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
