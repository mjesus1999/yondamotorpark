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

    /* OBTIENE RESUMEN FINANCIERO */
    /* public function getResumenFinanciero($idContrato)
    {
        try {
            $stmt = $this->db->prepare("CALL sp_get_resumen_financiero_cobranza(?)");
            $stmt->execute([$idContrato]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->sendJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (PDOException $e) {
            $this->sendJSON([
                'success' => false,
                'message' => 'Error al obtener resumen financiero: ' . $e->getMessage()
            ]);
        }
    } */

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
            $mensaje = "Estimad@ {$nombreCliente}, le recordamos que su cuota de {$montoCuota} vence el {$fechaVencimiento} :D";

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
    }


}