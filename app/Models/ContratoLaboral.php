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
        $sql = "INSERT INTO contratoslaborales
                  (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
                VALUES
                  (:idpersona, :idcargo, :fechainicio, :fechafin, :tipocontrato)";
        try {
            $stmt = $this->db->prepare($sql);
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
}
