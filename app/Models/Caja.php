<?php

namespace App\Models;

use App\Core\Database;
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

    public function getReporteIngresosHoy(): array
    {
        $query = "CALL spu_caja_reporte_completo_hoy()";
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

    public function getReporteByFecha($fechaInicio, $fechaFin)
    {
        $query = "CALL ObtenerReportePagosPorFechas(:fechainicio, :fechafin)";

        try {

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fechainicio', $fechaInicio);
            $stmt->bindParam(':fechafin', $fechaFin);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultados;
        } catch (PDOException $error) {

            error_log("Error al llamar al procedimiento almacenado: " . $error->getMessage());
            return [];
        }
    }
}
