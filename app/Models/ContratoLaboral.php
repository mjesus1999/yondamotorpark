<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class ContratoLaboral
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(int $idPersona, int $idCargo, string $fechaInicio, ?string $fechaFin, string $tipoContrato): int
    {
        $query = "INSERT INTO contratoslaborales
                  (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
                VALUES
                  (:idpersona, :idcargo, :fechainicio, :fechafin, :tipocontrato)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idpersona' => $idPersona,
                ':idcargo' => $idCargo,
                ':fechainicio' => $fechaInicio,
                ':fechafin' => $fechaFin ?: null,
                ':tipocontrato' => $tipoContrato,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return -1;
        }
    }
    public function delete(int $idcontrato): int
    {
        $query = "DELETE FROM contratoslaborales WHERE idcontratolaboral = :id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $idcontrato]);
            return $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error);
            return -1;
        }
    }
}
