<?php
// app/Models/Cobranza.php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Cobranza
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene estadísticas generales de cobranza
     * @return array Estadísticas de cobranza
     */
    public function getEstadisticasCobranza(): array
    {
        $query = "CALL sp_get_estadisticas_cobranza()";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [
                'total_cuentas_por_cobrar' => 0,
                'monto_total_por_cobrar' => 0,
                'cobranzas_exitosas_hoy' => 0,
                'efectividad_cobranza' => 0,
                'promedio_dias_cobranza' => 0
            ];
        } catch (PDOException $error) {
            error_log("Error en estadísticas de cobranza: " . $error->getMessage());
            return [
                'total_cuentas_por_cobrar' => 0,
                'monto_total_por_cobrar' => 0,
                'cobranzas_exitosas_hoy' => 0,
                'efectividad_cobranza' => 0,
                'promedio_dias_cobranza' => 0
            ];
        }
    }

    /**
     * Obtiene las cuentas por cobrar clasificadas por prioridad
     * @return array Cuentas clasificadas
     */
    public function getCuentasPorCobrarClasificadas(): array
    {
        $query = "CALL sp_get_cuentas_por_cobrar_clasificadas()";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->clasificarCuentasPorPrioridad($results);
        } catch (PDOException $error) {
            error_log("Error en cuentas por cobrar clasificadas: " . $error->getMessage());
            return [
                'alta_prioridad' => [],
                'media_prioridad' => [],
                'baja_prioridad' => []
            ];
        }
    }

    /**
     * Clasifica las cuentas por cobrar según criterios de prioridad
     * @param array $cuentas Cuentas por cobrar
     * @return array Cuentas clasificadas por prioridad
     */
    private function clasificarCuentasPorPrioridad(array $cuentas): array
    {
        $clasificadas = [
            'alta_prioridad' => [],
            'media_prioridad' => [],
            'baja_prioridad' => []
        ];

        foreach ($cuentas as $cuenta) {
            $diasVencido = (int) $cuenta['dias_vencido'];
            $montoDeuda = (float) $cuenta['monto_deuda'];

            // Criterios de clasificación según literatura de gestión de cobranza
            // (Brachfield, 2009; Ross et al., 2016)
            if ($diasVencido > 60 || $montoDeuda > 5000) {
                $clasificadas['alta_prioridad'][] = $cuenta;
            } elseif ($diasVencido > 30 || $montoDeuda > 2000) {
                $clasificadas['media_prioridad'][] = $cuenta;
            } else {
                $clasificadas['baja_prioridad'][] = $cuenta;
            }
        }

        return $clasificadas;
    }

    /**
     * Registra una acción de cobranza
     * @param array $data Datos de la acción de cobranza
     * @return int ID de la acción registrada
     */
    public function registrarAccionCobranza(array $data): int
    {
        $query = "CALL sp_registrar_accion_cobranza(:idcontrato, :tipo_accion, :canal_comunicacion, :resultado, :observaciones, :fecha_contacto, :fecha_proxima_accion, :monto_comprometido, :usuario_registro)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idcontrato' => $data['idcontrato'],
                ':tipo_accion' => $data['tipo_accion'],
                ':canal_comunicacion' => $data['canal_comunicacion'],
                ':resultado' => $data['resultado'],
                ':observaciones' => $data['observaciones'],
                ':fecha_contacto' => $data['fecha_contacto'],
                ':fecha_proxima_accion' => $data['fecha_proxima_accion'],
                ':monto_comprometido' => $data['monto_comprometido'],
                ':usuario_registro' => $data['usuario_registro']
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) ($result['last_insert_id'] ?? 0);
        } catch (PDOException $error) {
            error_log("Error al registrar acción de cobranza: " . $error->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene el historial de acciones de cobranza de un contrato
     * @param int $idContrato ID del contrato
     * @return array Historial de acciones
     */
    public function getHistorialAccionesCobranza(int $idContrato): array
    {
        try {
            $query = "CALL sp_get_historial_acciones_cobranza(:idcontrato)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idcontrato' => $idContrato]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $results ?: [];
        } catch (PDOException $e) {
            error_log("Error getHistorialAccionesCobranza: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las cuentas programadas para cobranza del día
     * @return array Cuentas programadas
     */
    public function getCuentasProgramadasHoy(): array
    {
        try {
            $query = "CALL sp_get_cuentas_programadas_hoy()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $results ?: [];
        } catch (PDOException $e) {
            error_log("Error getCuentasProgramadasHoy: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene métricas de efectividad de cobranza
     * @param string $fechaInicio Fecha inicio del período
     * @param string $fechaFin Fecha fin del período
     * @return array Métricas de efectividad
     */
    public function getMetricasEfectividad(string $fechaInicio, string $fechaFin): array
    {
        try {
            $query = "CALL sp_get_metricas_efectividad_cobranza(:fecha_inicio, :fecha_fin)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':fecha_inicio' => $fechaInicio,
                ':fecha_fin' => $fechaFin
            ]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $results ?: [];
        } catch (PDOException $e) {
            error_log("Error getMetricasEfectividad: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualiza el estado de una cuenta por cobrar
     * @param int $idContrato ID del contrato
     * @param string $nuevoEstado Nuevo estado de la cuenta
     * @return bool Resultado de la operación
     */
    public function actualizarEstadoCuenta(int $idContrato, string $nuevoEstado): bool
    {
        try {
            $query = "CALL sp_actualizar_estado_cuenta_cobranza(:idcontrato, :nuevo_estado)";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':idcontrato' => $idContrato,
                ':nuevo_estado' => $nuevoEstado
            ]);
            return $result;
        } catch (PDOException $e) {
            error_log("Error actualizarEstadoCuenta: " . $e->getMessage());
            return false;
        }
    }
}