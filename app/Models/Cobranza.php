<?php
// app/Models/Cobranza.php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;
/* use ApiSms; */

require_once __DIR__ . '/../Helpers/ApiSms.php';

class Cobranza
{
    private PDO $db;
    private $apiSms;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->apiSms = new \ApiSms();
    }

    /* MOSTRAR ESTADISTICAS DE LOS CONTRATOS */
    public function getEstadisticasContratos()
    {
        $query = "CALL sp_get_estadisticas_cobranza()";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            // Obtener las estadísticas (primer resultado)
            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'total_deudores' => 0,
                'por_vencer_3dias' => 0,
                'contratos_con_vencidos' => 0,
                'total_por_cobrar' => 0,
                'total_cuotas_pagadas' => 0
            ];

            $stmt->closeCursor();

            return $estadisticas;

        } catch (PDOException $error) {
            error_log("Error en getEstadisticasContratos: " . $error->getMessage());
            return [
                'total_deudores' => 0,
                'por_vencer_3dias' => 0,
                'contratos_con_vencidos' => 0,
                'total_por_cobrar' => 0,
                'total_cuotas_pagadas' => 0
            ];
        }
    }

    /* MOSTRAR TARJETAS DE COBRANZA / RESUMEN
     * (contratos próximos a vencer o vencidos)
     */
    public function getTarjetasCobranza()
    {
        $query = "CALL sp_get_tarjetas_cobranza()";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            // Obtener todas las tarjetas
            $tarjetas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $tarjetas ?: [];

        } catch (PDOException $error) {
            error_log("Error en getTarjetasCobranza: " . $error->getMessage());
            return [];
        }
    }

    /* PARA ENVIAR MENSAJES JSON */
    private function sendJSON($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /* PARA MOSTRAR INFORMACION DE CLIENTE EN EL MODAL DE DETALLES */

    public function getInfoCliente($idContrato)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_get_info_cliente_cobranza(?)");
            $stmt->execute([$idContrato]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->sendJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (PDOException $e) {
            $this->sendJSON([
                'success' => false,
                'message' => 'Error al obtener información del cliente: ' . $e->getMessage()
            ]);
        }
    }

    /* OBTIENE DETALLE DEL CONTRATO */
    public function getDetalleContrato($idContrato)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_get_detalle_contrato_cobranza(?)");
            $stmt->execute([$idContrato]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->sendJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (PDOException $e) {
            $this->sendJSON([
                'success' => false,
                'message' => 'Error al obtener detalle del contrato: ' . $e->getMessage()
            ]);
        }
    }

    /* OBTIENE CRONOGRAMA DE PAGOS */
    public function getCronogramaPagos($idContrato)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_get_cronograma_pagos_cobranza(?)");
            $stmt->execute([$idContrato]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (PDOException $e) {
            $this->sendJSON([
                'success' => false,
                'message' => 'Error al obtener cronograma: ' . $e->getMessage()
            ]);
        }
    }

    /* OBTIENE EL HISTORIAL DE PAGOS */
    public function getHistorialPagos($idContrato, $limite = 5)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_get_historial_pagos_cobranza(?, ?)");
            $stmt->execute([$idContrato, $limite]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (PDOException $e) {
            $this->sendJSON([
                'success' => false,
                'message' => 'Error al obtener historial: ' . $e->getMessage()
            ]);
        }
    }

    public function getClientesNotificar()
    {
        $query = "CALL sp_get_cuotas_proximas_vencer()";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $results;
        } catch (PDOException $error) {
            error_log("Error en getClientesNotificar: " . $error->getMessage());
            return [];
        }
    }

    /* MUESTRA CUOTAS VENCIDOS */
    public function getCuotasVencidas()
    {
        $query = "CALL sp_get_cuotas_vencidas()";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $error) {
            error_log("Error en getVencidas: " . $error->getMessage());
            return [];
        }

    }

    /* API PARA MANDAR NOTIFICACION (SMS) */
    public function enviarSmsNotificacion($idContrato, $telefono, $nombreCliente, $montoCuota, $fechaVencimiento): array
    {
        try {
            // Formatear el monto con comas y 2 decimales
            $montoFormateado = 'S/. ' . number_format((float) $montoCuota, 2, '.', ',');

            $mensaje = "Estimado(a) {$nombreCliente}, le recordamos que su cuota de {$montoFormateado} vence el {$fechaVencimiento}, le agradece Motorpark";

            /* $mensaje = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $mensaje); */

            $resultado = $this->apiSms->sendMessage($telefono, $mensaje);

            return [
                'success' => $resultado,
                'message' => $resultado ? 'SMS enviado con éxito' : 'Error al enviar SMS'
            ];
        } catch (PDOException $e) {
            error_log("Error al enviar SMS: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al enviar: ' . $e->getMessage()
            ];
        }
    }

    /* public function enviarSmsNotificacion($idContrato, $telefono, $nombreCliente, $montoCuota, $fechaVencimiento): array
    {

        try {
            $mensaje = "Estimado(a) {$nombreCliente}, le recordamos que su cuota de {$montoCuota} vence el {$fechaVencimiento} :D";

            $resultado = $this->apiSms->sendMessage($telefono, $mensaje);

            return [
                'success' => $resultado,
                'message' => $resultado ? 'Sms enviado con exito' : 'Error al enviar Sms'
            ];
        } catch (PDOException $e) {
            error_log("Error al enviar sms: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al enviar: ' . $e->getMessage()
            ];
        }
    } */

    public function actualizarTelefonoCliente($idContrato, $telefonoActual, $telefonoNuevo): array
    {
        try {
            $stmt = $this->db->prepare("CALL sp_actualizar_telefono_cliente(?, ?, ?)");
            $stmt->execute([$idContrato, $telefonoNuevo, $telefonoActual]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($resultado && $resultado['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => $resultado['message'],
                    'telefono_nuevo' => $resultado['telefono_nuevo']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $resultado['message'] ?? 'Error desconocido al actualizar'
                ];
            }

        } catch (PDOException $e) {
            error_log("Error al actualizar teléfono: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }


    // AVANCE DE LOS REPORTES
    public function getReporteNotificacion($idContrato)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_get_datos_reporte_notificacion_pdf(?)");
            $stmt->execute([$idContrato]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $data;
        } catch (PDOException $e) {
            error_log("Error en getReporteNotificacion: " . $e->getMessage());
            return null;
        }
    }

    public function getReporteRecojoVehicular($idContrato)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_get_datos_reporte_recojo_vehicular_pdf(?);");
            $stmt->execute([$idContrato]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $data;
        } catch (PDOException $e) {
            error_log("Error en getReporteRecojoVehicular: " . $e->getMessage());
            return null;
        }
    }

    /**
     * CREAR JSON DE LOS VENCIDOS
     */

    public function actualizarJsonVencidos(): bool
    {
        try {
            $query = "CALL sp_get_cuotas_vencidas()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $stmt->closeCursor();

            // Preparar estructura JSON
            $jsonData = [
                'query' => 'vencidos',
                'date' => date('Y-m-d H:i:s'),
                'total_registros' => count($datos),
                'data' => $datos
            ];

            // Crear directorio si no existe
            $dirPath = __DIR__ . '/../../storage/jsonvencidos';
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0755, true);
            }

            $filePath = $dirPath . '/vencidos.json';

            // Guardar JSON
            $result = file_put_contents(
                $filePath,
                json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            return $result !== false;

        } catch (PDOException $error) {
            error_log("Error en actualizarJsonVencidos: " . $error->getMessage());
            return false;
        }
    }

    public function leerJsonVencidos(): array
    {
        $filePath = __DIR__ . '/../../storage/jsonvencidos/vencidos.json';

        if (!file_exists($filePath)) {
            // Si no existe, crear uno nuevo
            $this->actualizarJsonVencidos();
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (!$data) {
            return [
                'query' => 'vencidos',
                'date' => date('Y-m-d H:i:s'),
                'total_registros' => 0,
                'data' => []
            ];
        }

        return $data;
    }

    public function necesitaActualizacion(): bool
    {
        $filePath = __DIR__ . '/../../storage/jsonvencidos/vencidos.json';

        if (!file_exists($filePath)) {
            return true;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['date'])) {
            return true;
        }

        // Verificar si tiene más de 1 hora
        $fechaJson = strtotime($data['date']);
        $horaActual = time();
        $diferencia = ($horaActual - $fechaJson) / 3600; // convertir a horas

        return $diferencia >= 1;
    }

    public function getDetalleVencidas($idContrato)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_get_detalle_cuotas_vencidas(?)");
            $stmt->execute([$idContrato]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (PDOException $e) {
            $this->sendJSON([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ]);
        }
    }


}
