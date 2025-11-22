<?php

/**
 * Modelo de Cobranza
 * 
 * app/Models/Cobranza.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con la cobranza
 * de cuotas de contratos vehiculares. Proporciona estadísticas ejecutivas
 * de cartera, gestión de notificaciones preventivas por SMS, seguimiento
 * de cuotas próximas a vencer y vencidas, actualización de datos de
 * contacto, y generación de reportes PDF de notificación y recojo vehicular.
 * Implementa sistema de caché JSON para optimizar consultas frecuentes de
 * cuotas vencidas con actualización automática cada hora.
 */
namespace App\Models;

use App\Core\Database;
use Error;
use PDO;
use PDOException;

require_once __DIR__ . '/../Helpers/ApiSms.php';

/**
 * Clase Cobranza
 * 
 * Modelo para la gestión integral de cobranza de contratos vehiculares.
 * Proporciona métodos para monitorear cartera (próximos a vencer, vencidos),
 * enviar notificaciones preventivas vía SMS usando API externa, actualizar
 * datos de contacto de clientes, generar reportes de gestión de cobranza,
 * y mantener caché JSON de cuotas vencidas con actualización automática
 * horaria. Integra múltiples procedimientos almacenados con manejo correcto
 * de cursores y utiliza helper ApiSms para comunicación con clientes.
 */
class Cobranza
{

    /**
     * Instancia de conexión a la base de datos
     * @var PDO
     */
    private PDO $db;

    /**
     * Instancia del helper de API SMS
     * @var 
     */
    private $apiSms;

    /**
     * Constructor del modelo
     * 
     * Inicializa la conexión a la base de datos mediante el patrón Singleton
     * y carga el helper ApiSms para envío de notificaciones
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->apiSms = new \ApiSms();
    }

    /**
     * Obtiene estadísticas ejecutivas de cobranza
     * 
     * Ejecuta un procedimiento almacenado que retorna indicadores clave
     * de gestión de cobranza: total de deudores activos, contratos con
     * cuotas próximas a vencer (3 días), contratos con cuotas vencidas,
     * monto total por cobrar, y total de cuotas ya pagadas.
     * 
     * @return array Array asociativo con indicadores clave:
     *               - total_deudores: int (Total de clientes con contratos activos)
     *               - por_vencer_3dias: int (Contratos con cuotas que vencen en 3 días)
     *               - contratos_con_vencidos: int (Contratos con al menos una cuota vencida)
     *               - total_por_cobrar: float (Suma de saldos pendientes de pago)
     *               - total_cuotas_pagadas: int (Cantidad de cuotas pagadas en el sistema)
     *               Retorna valores en 0 en caso de error
     */
    public function getEstadisticasContratos(): array
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

    /**
     * Obtiene tarjetas de cobranza para vista de resumen
     * 
     * Ejecuta un procedimiento almacenado "sp_get_tarjetas_cobranza" que retorna una lista de
     * contratos que requieren atención inmediata: cuotas próximas a
     * vencer (3 días) o cuotas ya vencidas. Cada "tarjeta" incluye
     * información completa del contrato: cliente, vehículo, cuota,
     * fecha de vencimiento, y estado.
     *
     * @return array Array de tarjetas de cobranza. Cada elemento contiene:
     *               - idcontrato: int (ID del contrato)
     *               - cliente: string (Nombre completo del cliente)
     *               - telefono: string (Teléfono de contacto)
     *               - vehiculo: string (Descripción del vehículo)
     *               - monto_cuota: decimal (Valor de la cuota)
     *               - fecha_vencimiento: string (Fecha de vencimiento)
     *               - dias_vencimiento: int (Días para vencer o vencidos)
     *               - estado: string (Próximo a vencer / Vencido)
     *               Retorna array vacío si no hay contratos pendientes o hay error
     */
    public function getTarjetasCobranza(): array
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

    /**
     * Envía respuesta JSON y termina ejecución
     * 
     * Método privado helper que establece el header Content-Type como
     * application/json, serializa los datos y termina la ejecución.
     * Utilizado por métodos que responden directamente a peticiones AJAX.
     *
     * @param array $data Datos a serializar en JSON
     * @return void Envía respuesta y termina con exit
     */
    private function sendJSON($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Obtiene información del cliente para modal de detalles
     * 
     * Ejecuta procedimiento almacenado "sp_get_info_cliente_cobranza" que retorna datos personales
     * del cliente asociado al contrato: nombre completo, documento
     * de identidad, dirección, teléfonos de contacto, correo electrónico
     * y estado del cliente.
     *
     * @param int $idContrato ID del contrato del cliente
     * @return void Envía respuesta JSON y termina ejecución
     */
    public function getInfoCliente($idContrato): void
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

    /**
     * Obtiene detalle completo del contrato
     * 
     * Ejecuta procedimiento almacenado "sp_get_detalle_contrato_cobranza" que retorna información detallada
     * del contrato: número de contrato, fecha de firma, monto total,
     * plazo en meses, cuota mensual, inicial pagada, saldo pendiente,
     * número de cuotas pagadas y vencidas, próximo vencimiento, y estado
     * general del contrato.
     *
     * @param int $idContrato ID del contrato a consultar
     * @return void Envía respuesta JSON y termina ejecución
     */
    public function getDetalleContrato($idContrato): void
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

    /**
     * Obtiene cronograma de pagos del contrato
     * 
     * Ejecuta procedimiento almacenado "sp_get_cronograma_pagos_cobranza" que retorna el cronograma completo
     * de cuotas del contrato: número de cuota, fecha de vencimiento,
     * monto de la cuota, estado (pendiente/pagada/vencida), fecha de pago
     * efectivo, y días de mora si aplica. Permite visualizar el historial
     * y proyección de pagos del contrato.
     *
     * @param int $idContrato ID del contrato a consultar
     * @return void Envía respuesta JSON y termina ejecución
     */
    public function getCronogramaPagos($idContrato): void
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

    /**
     * Obtiene historial de pagos realizados
     * 
     * Ejecuta procedimiento almacenado "sp_get_historial_pagos_cobranza" que retorna el historial de los
     * últimos N pagos efectuados del contrato: número de cuota, fecha
     * de pago, monto pagado, método de pago, usuario que registró, y
     * número de recibo/comprobante. Útil para auditoría y verificación
     * de pagos recientes.
     *
     * @param int $idContrato ID del contrato a consultar
     * @param int $limite Cantidad máxima de registros a retornar
     * @return void Envía respuesta JSON y termina ejecución
     */
    public function getHistorialPagos($idContrato, $limite = 5): void
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

    /**
     * Obtiene clientes con cuotas próximas a vencer
     * 
     * Ejecuta procedimiento almacenado "sp_get_cuotas_proximas_vencer" que retorna lista de contratos
     * con cuotas que vencen en los próximos 3 días. Incluye datos del
     * cliente (nombre, teléfono), datos del contrato, monto de la cuota,
     * fecha de vencimiento y días restantes. Utilizado para sistema de
     * notificaciones preventivas.
     *
     * @return array Array de clientes a notificar. Cada elemento contiene:
     *               - idcontrato: int
     *               - cliente: string
     *               - telefono: string
     *               - monto_cuota: decimal
     *               - fecha_vencimiento: date
     *               - dias_restantes: int
     *               Retorna array vacío si no hay clientes o error
     */
    public function getClientesNotificar(): array
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

    /**
     * Obtiene listado de cuotas vencidas
     * 
     * Ejecuta procedimiento almacenado "sp_get_cuotas_vencidas" que retorna todos los contratos
     * con cuotas vencidas. Incluye datos del cliente, vehículo, número
     * de cuotas vencidas, monto total adeudado, días de mora, y fecha
     * de la cuota más antigua vencida. Base para gestión de cartera
     * morosa y reportes de cobranza.
     *
     * @return array Array de cuotas vencidas. Cada elemento contiene:
     *               - idcontrato: int
     *               - cliente: string
     *               - telefono: string
     *               - vehiculo: string
     *               - cuotas_vencidas: int
     *               - monto_total_vencido: decimal
     *               - dias_mora_max: int
     *               - fecha_vencimiento_antigua: date
     *               Retorna array vacío si no hay cuotas vencidas o error
     */
    public function getCuotasVencidas(): array
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

    /**
     * Envía notificación SMS al cliente
     * 
     * Genera y envía mensaje SMS personalizado al cliente recordando
     * el vencimiento próximo de su cuota. Formatea el monto en soles
     * con formato peruano (S/. X,XXX.XX) y construye mensaje con
     * nombre del cliente, monto y fecha de vencimiento. Utiliza helper
     * ApiSms para comunicación con proveedor de mensajería.
     *
     * @param int $idContrato ID del contrato
     * @param string $telefono Número telefónico del destinatario
     * @param string $nombreCliente Nombre completo del cliente
     * @param float $montoCuota Monto de la cuota a pagar
     * @param string $fechaVencimiento Fecha de vencimiento
     * @return array {message: string, success: bool}
     */
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

    /**
     * Actualiza teléfono del cliente
     * 
     * Ejecuta procedimiento almacenado "sp_actualizar_telefono_cliente" que actualiza el número telefónico
     * del cliente asociado al contrato. Valida que el teléfono actual
     * coincida con el registrado antes de actualizar (medida de seguridad).
     * Retorna el nuevo teléfono confirmado tras la actualización exitosa.
     *
     * @param int $idContrato ID del contrato del cliente
     * @param string $telefonoActual Teléfono actual registrado
     * @param string $telefonoNuevo Nuevo número telefónico a registrar
     * @return array {message: mixed, success: bool, telefono_nuevo: mixed|array{message: mixed, success: bool}|array{message: string, success: bool}}
     */
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

    /**
     * Obtiene datos para reporte de notificación PDF
     * 
     * Ejecuta procedimiento almacenado "sp_get_datos_reporte_notificacion_pdf" que retorna información completa
     * necesaria para generar el documento PDF de notificación de cobranza.
     * Incluye datos del cliente, contrato, vehículo, detalle de cuotas
     * vencidas, y condiciones de pago. Utilizado por generador de PDFs
     * para crear documento oficial de notificación.
     *
     * @param int $idContrato ID del contrato a reportar
     */
    public function getReporteNotificacion($idContrato): ?array
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

    /**
     * Obtiene datos para reporte de recojo vehicular PDF
     * 
     * Ejecuta procedimiento almacenado "sp_get_datos_reporte_recojo_vehicular_pdf" que retorna información completa
     * necesaria para generar el documento PDF de notificación de recojo
     * vehicular por mora grave. Incluye datos del cliente, contrato,
     * vehículo, historial de cuotas vencidas, monto total adeudado, y
     * fundamentos legales. Utilizado en casos de incumplimiento severo
     * del contrato de financiamiento.
     *
     * @param int $idContrato ID del contrato en mora
     */
    public function getReporteRecojoVehicular($idContrato): ?array
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
     * Actualiza archivo JSON de cuotas vencidas
     * 
     * Ejecuta procedimiento almacenado para obtener cuotas vencidas actuales,
     * genera estructura JSON con metadatos (fecha/hora, total de registros),
     * y guarda archivo en storage/jsonvencidos/vencidos.json. Crea directorio
     * si no existe.
     *
     * @return bool true si el archivo se creó/actualizó correctamente, false si hubo error
     */
    public function actualizarJsonVencidos(): bool
    {
        try {
            $query = "CALL sp_get_cuotas_vencidas()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            error_log('Datos obtenidos: ' . print_r($datos, true));
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

    /**
     * Lee archivo JSON de cuotas vencidas
     * 
     * Lee el archivo JSON cacheado de cuotas vencidas desde
     * storage/jsonvencidos/vencidos.json. Si el archivo no existe,
     * lo crea automáticamente ejecutando actualizarJsonVencidos().
     * Si el archivo existe pero está corrupto o vacío, retorna
     * estructura por defecto con arrays vacíos.
     *
     * @return array Array con estructura JSON completa:
     *               - query: string (identificador "vencidos")
     *               - date: string (fecha/hora última actualización)
     *               - total_registros: int (cantidad de registros)
     *               - data: array (array de cuotas vencidas)
     */
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

    /**
     * Verifica si el caché JSON necesita actualización
     * 
     * Comprueba si el archivo JSON de cuotas vencidas requiere
     * actualización basándose en tres criterios:
     * 1. Si el archivo no existe
     * 2. Si el archivo está corrupto o sin metadata de fecha
     * 3. Si han pasado más de 1 hora desde la última actualización
     *
     * @return bool true si necesita actualización, false si el caché es reciente
     */
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

    /**
     * Obtiene detalle de cuotas vencidas de un contrato
     * 
     * Ejecuta procedimiento almacenado que retorna el detalle de todas
     * las cuotas vencidas de un contrato específico: número de cuota,
     * fecha de vencimiento original, días de mora, monto de la cuota,
     * intereses moratorios generados, y monto total a pagar (capital +
     * intereses). Útil para mostrar desglose detallado en modal de
     * gestión de cobranza.
     *
     * @param int $idContrato ID del contrato a consultar
     * @return void Envía respuesta JSON y termina ejecución
     */
    public function getDetalleVencidas($idContrato): void
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
