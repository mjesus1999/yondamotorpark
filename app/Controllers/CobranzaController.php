<?php

/**
 * Controlador de Cobranza
 * 
 * app/Controllers/CobranzaController.php
 * 
 * Gestiona todas las operaciones de cobranza de contratos vehiculares:
 * dashboard de estadísticas, notificaciones preventivas por SMS, gestión
 * de cartera vencida, actualización de datos de contacto, y generación
 * de reportes PDF (notificación de mora y recojo vehicular). Implementa
 * sistema de caché JSON con actualización automática para optimizar
 * consultas de cuotas vencidas. Todas las operaciones requieren
 * autenticación y retornan respuestas en formato JSON.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cobranza;
use App\Models\Usuario;
use Exception;

/**
 * Clase CobranzaController
 * 
 * Controlador principal para el módulo de cobranza. Maneja todas las
 * peticiones relacionadas con gestión de cartera: visualización de
 * estadísticas ejecutivas, tarjetas de contratos pendientes, envío de
 * notificaciones SMS preventivas, actualización de teléfonos, consulta
 * de cronogramas y detalles de contratos, y generación de reportes PDF.
 * Implementa caché JSON para cuotas vencidas con verificación y
 * actualización automática horaria.
 * 
 */
class CobranzaController extends Controller
{
    /**
     * Instancia del modelo de Cobranza
     * @var Cobranza
     */
    private Cobranza $cobranzaModel;
    /**
     * Instancia del modelo de Usuario
     * @var Usuario
     */
    private Usuario $usuarioModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa las instancias de los modelos Cobranza y Usuario
     * necesarios para todas las operaciones del controlador.
     */
    public function __construct()
    {
        $this->cobranzaModel = new Cobranza();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Vista principal del módulo de cobranza
     * 
     * Renderiza la vista principal (dashboard) del módulo de cobranza.
     * Requiere autenticación. La vista muestra estadísticas ejecutivas,
     * tarjetas de contratos pendientes (próximos a vencer y vencidos),
     * y opciones para gestión de cartera.
     * 
     * @return void Renderiza vista cobranza.index
     */
    public function index(): void
    {
        $this->authRequired();
        $this->view('cobranza.index');
    }

    /**
     * Obtiene estadísticas ejecutivas de cobranza
     * 
     * Endpoint API que retorna indicadores clave de gestión: total de
     * deudores activos, contratos próximos a vencer (3 días), contratos
     * con cuotas vencidas, monto total por cobrar, y cuotas pagadas.
     * 
     * @return void Envía respuesta JSON con estadísticas o error
     */
    public function getEstadisticas(): void
    {
        $this->authRequired();

        header('Content-Type: application/json');

        try {
            $data = $this->cobranzaModel->getEstadisticasContratos();
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ]);
        }
    }

    /**
     * Obtiene tarjetas de contratos para gestión de cobranza
     * 
     * Endpoint API que retorna lista de contratos que requieren atención:
     * cuotas próximas a vencer (3 días) o vencidas. Cada tarjeta incluye
     * datos del cliente, vehículo, monto, fecha de vencimiento y estado.
     * Requiere autenticación.
     *
     * @return void Envía respuesta JSON con array de tarjetas o error
     */
    public function getTarjetas(): void
    {
        $this->authRequired();

        header('Content-Type: application/json');

        try {
            $tarjetas = $this->cobranzaModel->getTarjetasCobranza();
            echo json_encode([
                'success' => true,
                'data' => $tarjetas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener tarjetas'
            ]);
        }
    }

    /**
     * Obtiene información personal del cliente
     * 
     * Endpoint API que retorna datos personales del cliente asociado al
     * contrato: nombre completo, documento de identidad, dirección,
     * teléfonos, correo electrónico y estado. Usado en modales de
     * detalle de contratos.
     *
     * @param int $idContrato ID del contrato del cliente a consultar
     * @return void Envía respuesta JSON desde el modelo o error
     */
    public function getInfoCliente($idContrato): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getInfoCliente($idContrato);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener información del cliente'
            ]);
        }
    }

    /**
     * Obtiene detalle completo del contrato
     * 
     * Endpoint API que retorna información detallada del contrato: número,
     * fecha de firma, monto total, plazo, cuota mensual, inicial pagada,
     * saldo pendiente, cuotas pagadas/vencidas, próximo vencimiento, y
     * estado. Usado en modales de gestión de cobranza.
     *
     * @param int $idContrato ID del contrato a consultar
     * @return void Envía respuesta JSON desde el modelo o error
     */
    public function getDetalleContrato($idContrato): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getDetalleContrato($idContrato);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalle del contrato'
            ]);
        }
    }

    /**
     * Obtiene cronograma de pagos del contrato
     * 
     * Endpoint API que retorna el cronograma completo de cuotas: número,
     * fecha de vencimiento, monto, estado (pendiente/pagada/vencida),
     * fecha de pago real, y días de mora si aplica. Permite visualizar
     * historial y proyección de pagos.
     *
     * @param int $idContrato ID del contrato a consultar
     * @return void Envía respuesta JSON desde el modelo o error
     */
    public function getCronogramaPagos($idContrato): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getCronogramaPagos($idContrato);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener cronograma de pagos'
            ]);
        }
    }

    /**
     * Obtiene historial de pagos realizados
     * 
     * Endpoint API que retorna los últimos N pagos efectuados del contrato:
     * número de cuota, fecha de pago, monto, método de pago, usuario que
     * registró, y número de recibo. Útil para auditoría y verificación.
     *
     * @param int $idContrato ID del contrato a consultar
     * @param string $limite Cantidad máxima de registros a retornar
     * @return void Envía respuesta JSON desde el modelo o error
     */
    public function getHistorialPagos($idContrato, $limite = 5): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getHistorialPagos($idContrato, $limite);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener historial de pagos'
            ]);
        }
    }

    /**
     * Obtiene lista de clientes para notificaciones preventivas
     * 
     * Endpoint API que retorna contratos con cuotas que vencen en los
     * próximos 3 días. Incluye datos del cliente, teléfono, monto de
     * cuota, fecha de vencimiento y días restantes. Usado por módulo
     * de notificaciones preventivas para envío masivo de SMS.
     *
     * @return void Envía respuesta JSON con array de clientes o error
     */
    public function getClientesNotificar(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $clientes = $this->cobranzaModel->getClientesNotificar();
            echo json_encode([
                'success' => true,
                'data' => $clientes
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene cuotas vencidas desde caché JSON
     * 
     * Endpoint API que retorna lista de contratos con cuotas vencidas
     * desde archivo JSON cacheado. Incluye metadatos: fecha de última
     * actualización y total de registros. Optimiza rendimiento al evitar
     * consultas repetitivas a base de datos.
     *
     * @return void Envía respuesta JSON con datos cacheados o error
     */
    public function getVencidos(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        try {
            // Leer desde JSON
            $jsonData = $this->cobranzaModel->leerJsonVencidos();

            echo json_encode([
                'success' => true,
                'data' => $jsonData['data'],
                'fecha_datos' => $jsonData['date'],
                'total_registros' => $jsonData['total_registros']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener vencidos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Vista de notificaciones preventivas
     * 
     * Renderiza la vista del módulo de notificaciones preventivas.
     * Muestra lista de clientes con cuotas próximas a vencer (3 días)
     * y permite envío individual o masivo de SMS de recordatorio.
     *
     * @return void Renderiza vista cobranza.indexNotificar
     */
    public function indexNotificar(): void
    {
        $this->authRequired();
        $this->view('cobranza.indexNotificar');
    }

    /**
     * Vista de cuotas vencidas
     * 
     * Renderiza la vista del módulo de gestión de cartera vencida.
     * Obtiene datos de cuotas vencidas desde base de datos (no caché)
     * y los pasa a la vista. Muestra tabla con clientes morosos,
     * vehículos, cuotas vencidas, monto adeudado y días de mora
     *
     * @return void Renderiza vista cobranza.indexVencidos con datos
     */
    public function indexVencidos(): void
    {
        $this->authRequired();

        $vencidos = $this->cobranzaModel->getCuotasVencidas();
        $this->view('cobranza.indexVencidos', ['vencidos' => $vencidos]);
    }

    /**
     * Vista de reporte de notificación de atraso
     * 
     * Renderiza la vista para generar PDF de notificación formal de
     * atraso de pagos. Valida que el ID del contrato sea numérico y
     * lo pasa a la vista. El PDF incluye datos del cliente, contrato,
     * cuotas vencidas, y firma del colaborador.
     *
     * @param int $id ID del contrato a reportar
     * @return void Renderiza vista de generación de PDF o error
     */
    public function reporteCobranzaAtrasado($id): void
    {
        $this->authRequired();

        // Validar que el ID sea válido
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            die('ID de contrato inválido');
        }

        // Pasar el idcontrato a la vista
        $this->view('cobranza/reports.notificacion_reporte_atraso_mes', [
            'idcontrato' => (int) $id
        ]);
    }

    /**
     * Vista de reporte de recojo vehicular
     * 
     * Renderiza la vista para generar PDF de notificación formal de
     * recojo vehicular por mora grave. Valida que el ID del contrato
     * sea numérico y lo pasa a la vista. El PDF incluye datos del
     * cliente, vehículo, historial de mora, fundamentos legales, y
     * advertencia de recuperación.
     *
     * @param int $id ID del contrato en mora grave
     * @return void Renderiza vista de generación de PDF o error
     */
    public function reporteRecojoVehicular($id)
    {
        $this->authRequired();

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            die('ID de contrato inválido');
        }

        $this->view('cobranza/reports.notificacion_reporte-constancia-recojo', [
            'idcontrato' => (int) $id
        ]);
    }

    /**
     *  Envía notificación SMS a cliente
     * 
     * Endpoint API que procesa envío de SMS de recordatorio de pago.
     * Recibe datos del cliente vía JSON (POST), valida campos requeridos,
     * formatea el monto en soles, y ejecuta envío mediante API SMS externa.
     *
     * @throws \Exception
     * @return void Envía respuesta JSON con resultado del envío o error
     */
    public function enviarSmsNotificacion(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $idContrato = $data['idcontrato'] ?? null;
            $telefono = $data['telefono'] ?? null;
            $nombreCliente = $data['cliente'] ?? null;
            $montoCuota = $data['monto_cuota'] ?? null;
            $fechaVencimiento = $data['fecha_vencimiento'] ?? null;

            if (!$telefono || !$nombreCliente || !$montoCuota || !$fechaVencimiento) {
                throw new Exception('Datos incompletos para enviar SMS');
            }

            $resultado = $this->cobranzaModel->enviarSmsNotificacion(
                $idContrato,
                $telefono,
                $nombreCliente,
                $montoCuota,
                $fechaVencimiento
            );

            echo json_encode($resultado);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al enviar SMS: ' . $e->getMessage()
            ]);
        }

    }

    /**
     * Actualiza teléfono del cliente
     * 
     * Endpoint API que procesa actualización de número telefónico del
     * cliente. Recibe datos vía JSON (POST), valida formato del teléfono
     * (9-15 dígitos), limpia caracteres no numéricos, y ejecuta
     * actualización en base de datos. Valida que el teléfono actual
     * coincida antes de actualizar (seguridad).
     *
     * @throws \Exception
     * @return void Envía respuesta JSON con resultado o error
     */
    public function actualizarTelefono(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $idContrato = $data['idcontrato'] ?? null;
            $telefonoActual = $data['telefono_actual'] ?? null;
            $telefonoNuevo = $data['telefono_nuevo'] ?? null;

            if (!$idContrato || !$telefonoActual || !$telefonoNuevo) {
                throw new Exception('Datos incompletos');
            }

            $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefonoNuevo);
            if (strlen($telefonoLimpio) < 9 || strlen($telefonoLimpio) > 15) {
                throw new Exception('El teléfono debe tener entre 9 y 15 dígitos');
            }

            $resultado = $this->cobranzaModel->actualizarTelefonoCliente(
                $idContrato,
                $telefonoActual,
                $telefonoLimpio
            );

            echo json_encode($resultado);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene datos para reporte PDF de notificación de atraso
     * 
     * Endpoint API que retorna todos los datos necesarios para generar
     * el PDF de notificación formal de atraso. Incluye datos del cliente,
     * contrato, vehículo, cuotas vencidas, y datos del colaborador que
     * genera el reporte (obtenidos desde sesión). Valida que el ID del
     * contrato esté presente.
     *
     * @throws \Exception
     * @return void Envía respuesta JSON con datos completos para PDF o error
     */
    public function getDatosReporteNotificacion(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $idContrato = $_GET['contrato'] ?? null;

            if (!$idContrato) {
                throw new Exception('ID de contrato no proporcionado');
            }

            $datos = $this->cobranzaModel->getReporteNotificacion($idContrato);

            if (!$datos) {
                throw new Exception('No se encontraron datos para el contrato');
            }

            //datos del usuario
            $currentUser = $_SESSION['user'] ?? null;
            if ($currentUser) {
                $usuarioModel = new Usuario();
                $usr = null;

                if (!empty($currentUser['id'])) {
                    $usr = $usuarioModel->getById((int) $currentUser['id']);
                }

                // preferimos datos frescos ($usr) si existen, si no usamos la sesión
                $source = $usr ?: $currentUser;

                // area
                if (!empty($source['area'])) {
                    $datos['colaborador_area'] = $source['area'];
                }

                // primer nombre + primer apellido
                $primerNombre = '';
                $primerApellido = '';

                if (!empty($source['nombres'])) {
                    $n = trim($source['nombres']);
                    $partsN = preg_split('/\s+/', $n);
                    $primerNombre = $partsN[0] ?? '';
                }
                if (!empty($source['apellidos'])) {
                    $a = trim($source['apellidos']);
                    $partsA = preg_split('/\s+/', $a);
                    $primerApellido = $partsA[0] ?? '';
                }
                // si no hay nombres/apellidos en $source, intentar con usernick
                /* if ($primerNombre === '' && !empty($source['usernick'])) {
                    $primerNombre = $source['usernick'];
                } */
                $datos['colaborador_nombre'] = trim($primerNombre . ' ' . $primerApellido);

                // telefono (probar telprimario o telefono)
                if (!empty($source['telprimario'])) {
                    $datos['colaborador_telefono'] = $source['telprimario'];
                } elseif (!empty($source['telefono'])) {
                    $datos['colaborador_telefono'] = $source['telefono'];
                }
            }

            echo json_encode([
                'success' => true,
                'data' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos del reporte: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene datos para reporte PDF de recojo vehicular
     * 
     * Endpoint API que retorna todos los datos necesarios para generar
     * el PDF de notificación formal de recojo vehicular. Incluye datos
     * del cliente, contrato, vehículo, historial de mora, fundamentos
     * legales, y datos del colaborador que genera el reporte (obtenidos
     * desde sesión). Valida que el ID del contrato esté presente.
     *
     * @throws \Exception
     * @return void Envía respuesta JSON con datos completos para PDF o error
     */
    public function getDatosReporteRecojoVehicular(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $idContrato = $_GET['contrato'] ?? null;

            if (!$idContrato) {
                throw new Exception('ID de contrato no proporcionado');
            }

            $datos = $this->cobranzaModel->getReporteRecojoVehicular($idContrato);

            if (!$datos) {
                throw new Exception('No se encontraron datos para el contrato');
            }

            //datos del usuario 
            $currentUser = $_SESSION['user'] ?? null;
            if ($currentUser) {
                $usuarioModel = new Usuario();
                $usr = null;

                if (!empty($currentUser['id'])) {
                    $usr = $usuarioModel->getById((int) $currentUser['id']);
                }

                $source = $usr ?: $currentUser;

                if (!empty($source['area'])) {
                    $datos['colaborador_area'] = $source['area'];
                }

                $primerNombre = '';
                $primerApellido = '';

                if (!empty($source['nombres'])) {
                    $n = trim($source['nombres']);
                    $partsN = preg_split('/\s+/', $n);
                    $primerNombre = $partsN[0] ?? '';
                }
                if (!empty($source['apellidos'])) {
                    $a = trim($source['apellidos']);
                    $partsA = preg_split('/\s+/', $a);
                    $primerApellido = $partsA[0] ?? '';
                }
                /* if ($primerNombre === '' && !empty($source['usernick'])) {
                    $primerNombre = $source['usernick'];
                } */
                $datos['colaborador_nombre'] = trim($primerNombre . ' ' . $primerApellido);

                if (!empty($source['telprimario'])) {
                    $datos['colaborador_telefono'] = $source['telprimario'];
                } elseif (!empty($source['telefono'])) {
                    $datos['colaborador_telefono'] = $source['telefono'];
                }
            }

            echo json_encode([
                'success' => true,
                'data' => $datos
            ]);
        } catch (Exception $error) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos del reporte: ' . $error->getMessage()
            ]);
        }
    }

    /**
     * Actualiza caché JSON de cuotas vencidas
     * 
     * Endpoint API que fuerza actualización del archivo JSON de cuotas
     * vencidas. Ejecuta consulta a base de datos, regenera archivo JSON
     * con datos frescos y metadatos actualizados. Retorna confirmación
     * con fecha de actualización y total de registros. Usado por botón
     * manual de actualización en vista de vencidos.
     *
     * @throws \Exception
     * @return void Envía respuesta JSON con confirmación o error
     */
    public function actualizarVencidos(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $resultado = $this->cobranzaModel->actualizarJsonVencidos();

            if ($resultado) {
                $jsonData = $this->cobranzaModel->leerJsonVencidos();

                echo json_encode([
                    'success' => true,
                    'message' => 'Datos actualizados correctamente',
                    'fecha_actualizacion' => $jsonData['date'],
                    'total_registros' => $jsonData['total_registros']
                ]);
            } else {
                throw new Exception('No se pudo actualizar el archivo JSON');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Verifica si el caché JSON necesita actualización
     * 
     * Endpoint API que verifica si el archivo JSON de cuotas vencidas
     * necesita actualización basándose en: existencia del archivo,
     * validez de estructura, y tiempo transcurrido desde última
     * actualización (más de 1 hora). Usado por frontend para decidir
     * si ejecutar actualización automática antes de cargar datos.
     *
     * @return void Envía respuesta JSON con flag booleano o error
     */
    public function verificarActualizacion(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        try {
            $necesita = $this->cobranzaModel->necesitaActualizacion();

            echo json_encode([
                'success' => true,
                'necesita_actualizacion' => $necesita
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene detalle de cuotas vencidas de un contrato
     * 
     * Endpoint API que retorna el desglose detallado de todas las cuotas
     * vencidas de un contrato específico: número de cuota, fecha de
     * vencimiento, días de mora, monto de capital, intereses moratorios,
     * y monto total a pagar.
     *
     * @param int $idContrato ID del contrato a consultar
     * @return void Envía respuesta JSON desde el modelo o error
     */
    public function getDetalleVencidas($idContrato): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getDetalleVencidas($idContrato);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalle de vencidas'
            ]);
        }
    }

}