<?php

namespace App\Models;

use App\Core\Database;
use FFI\CData;
use PDO;
use PDOException;

class Caja
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllContratosDatos(): ?array
    {
        $query = "CALL sp_getAll_contratos_caja()";
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

    public function getCronogramaByIdContrato(int $id): array
    {
        $query = "CALL  sp_get_cronogramas_by_idcontrato(:idcontrato)";

        try {

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idcontrato' => $id));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }
}
