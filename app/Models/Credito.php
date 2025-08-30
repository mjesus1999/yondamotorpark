<?php
// app/Models/Credito.php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Credito
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getEstadisticasMorosos(): array
    {
        $query = "CALL sp_get_estadisticas_morosos()";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [
                'total_morosos' => 0,
                'deuda_total' => 0,
                'seguimientos_hoy' => 0,
                'dias_promedio' => 0
            ];
        } catch (PDOException $error) {
            error_log("Error en estadísticas morosos: " . $error->getMessage());
            return [
                'total_morosos' => 0,
                'deuda_total' => 0,
                'seguimientos_hoy' => 0,
                'dias_promedio' => 0
            ];
        }
    }

    public function getMorososClasificados(): array
    {
        $query = "CALL sp_get_morosos_clasificados()";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->clasificarMorosos($results);
        } catch (PDOException $error) {
            error_log("Error en morosos clasificados: " . $error->getMessage());
            return [
                '5-dias' => [],
                '2-semanas' => [],
                '1-mes' => []
            ];
        }
    }

    private function clasificarMorosos(array $morosos): array
    {
        $clasificados = [
            '5-dias' => [],
            '2-semanas' => [],
            '1-mes' => []
        ];

        foreach ($morosos as $moroso) {
            $diasAtraso = (int) $moroso['dias_atraso'];

            if ($diasAtraso <= 7) {
                $clasificados['5-dias'][] = $moroso;
            } elseif ($diasAtraso <= 30) {
                $clasificados['2-semanas'][] = $moroso;
            } else {
                $clasificados['1-mes'][] = $moroso;
            }
        }

        return $clasificados;
    }

    public function registrarSeguimiento(array $data): int
    {
        $query = "CALL sp_registrar_seguimiento_moroso(:idcontrato, :tipo, :observaciones, :evidencia, :fecha_seguimiento, :usuario_registro)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idcontrato' => $data['idcontrato'],
                ':tipo' => $data['tipo'],
                ':observaciones' => $data['observaciones'],
                ':evidencia' => $data['evidencia'],
                ':fecha_seguimiento' => $data['fecha_seguimiento'],
                ':usuario_registro' => $data['usuario_registro']
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ($result['last_insert_id'] ?? 0);
        } catch (PDOException $error) {
            error_log("Error al registrar seguimiento: " . $error->getMessage());
            return 0;
        }
    }

    public function getHistorialSeguimientos(int $idContrato): array
    {
        try {
            $query = "CALL sp_get_historial_seguimientos(:idcontrato)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idcontrato' => $idContrato]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor(); // muy importante con SPs
            return $results ?: [];
        } catch (PDOException $e) {
            error_log("Error getHistorialSeguimientos: " . $e->getMessage());
            return [];
        }
    }



    /* public function getHistorialSeguimientos(int $idContrato): array
    {
        $query = "CALL sp_get_historial_seguimientos(:idcontrato)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idcontrato' => $idContrato]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Error en historial seguimientos: " . $error->getMessage());
            return [];
        }
    } */

    public function getClienteByContrato(int $idContrato): ?array
    {
        $query = "SELECT 
                    con.idcontrato,
                    CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
                    p.nrodoc,
                    p.celular,
                    cot.valorcuota,
                    con.estado
                  FROM contratos con
                  JOIN cotizaciones cot ON con.idcotizacion = cot.idcotizacion
                  JOIN clientes cli ON cot.idcliente = cli.idcliente
                  JOIN personas p ON cli.idpersona = p.idpersona
                  WHERE con.idcontrato = :idcontrato";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idcontrato' => $idContrato]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Error al obtener cliente: " . $error->getMessage());
            return null;
        }
    }
}