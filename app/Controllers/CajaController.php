<?php

/**
 * Controlador de Caja
 * 
 * app/Controllers/CajaController.php
 * 
 * Gestiona todas las operaciones del módulo de caja y tesorería: visualización
 * de contratos activos con datos financieros, consulta de cronogramas de pago
 * con sistema de caché JSON, generación de reportes de ingresos
 * diarios agrupados por método de pago, y reportes personalizados por rangos
 * de fechas. Implementa optimización de rendimiento mediante caché de archivos
 * para cronogramas frecuentemente consultados, reduciendo carga en base de
 * datos. Todas las operaciones requieren autenticación y los reportes retornan
 * respuestas en formato JSON con datos estructurados para frontend.
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Caja;
use Exception;

/**
 * Clase CajaController
 * 
 * Controlador principal para el módulo de caja. Maneja todas las peticiones
 * relacionadas con operaciones de tesorería: visualización de contratos con
 * saldos pendientes, consulta de cronogramas de pago con caché inteligente,
 * generación de reportes de ingresos del día agrupados por método de pago,
 * y reportes personalizados por períodos con validación de parámetros.
 * 
 */
class CajaController extends Controller
{
    /**
     * Instancia del modelo de Caja
     * @var Caja
     */
    private Caja $cajaModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa la instancia del modelo Caja necesario para todas las
     * operaciones del controlador.
     */
    public function __construct()
    {
        $this->cajaModel = new Caja();
    }

    /**
     * Vista principal del módulo de caja
     * 
     * Renderiza la vista principal del módulo de caja con el listado completo
     * de contratos activos y sus datos financieros. Muestra información de
     * clientes, vehículos, montos, saldos pendientes y estados de pago.
     * Requiere autenticación
     * 
     * @return void
     */
    public function index(): void
    {
        // $tiempoInicio = microtime(true);
        $datos = $this->cajaModel->getAllContratosDatos();

        $this->authRequired();
        $this->view('caja.index', ['contratos' => $datos]);

        // $tiempoFin = microtime(true);
        // $tiempoEjecucion = $tiempoFin - $tiempoInicio;

        // error_log("Tiempo de ejecución de CAJA/Contratos: " . number_format($tiempoEjecucion, 2) . " segundos.");
    }

    public function indexContratosCompletados(): void
    {
        $this->authRequired();
        $this->view('caja.contratosCompletados');
    }

    /**
     * Muestra la vista de reportes por fechas personalizadas
     * 
     * Renderiza la interfaz para generar reportes de ingresos por rangos de
     * fechas personalizados. La vista incluye selector de fechas (date picker),
     * botón de generación de reporte, y área de visualización de resultados
     * con tablas y gráficos. Requiere autenticación.
     * 
     * @return void Renderiza vista caja.reporte-by-fechas
     */
    public function indexReporteByFecha()
    {
        $this->authRequired();
        $this->view('caja.reporte-by-fechas');
    }

    public function indexPagosDenominacion()
    {
        $this->authRequired();
        $this->view('caja.cobrosDenominacion');
    }

    /**
     * Muestra el cronograma de pagos de un contrato con sistema de caché
     * 
     * Renderiza la vista del cronograma de pagos completo de un contrato
     * específico. Implementa sistema de caché JSON inteligente con TTL de
     * 1 hora para optimizar rendimiento y reducir carga en base de datos.
     *
     * Verificación de caché:
     *    - Busca archivo en storage/cache/cronograma-contratos/
     *    - Verifica si existe y no ha expirado (< 1 hora)
     *    - Si es válido, carga datos desde json
     *  Ruta de caché: storage/cache/cronograma-contratos/cronograma-contrato{ID}.json
     *  TTL (Time To Live): 3600 segundos (1 hora)
     *
     * @param int $id ID del contrato para consultar cronograma
     * @return void Renderiza vista caja.cronograma con datos del cronograma
     */
    public function cronogramaByContrato(int $id): void
    {
        $this->authRequired();
        // Iniciar el cronómetro para medir el rendimiento
        // $tiempoInicio = microtime(true);

        // Definir la ruta del archivo de caché y el tiempo de vida (TTL)
        $cacheFile = __DIR__ . "/../../storage/cache/cronograma-contratos/cronograma-contrato{$id}.json";
        $ttl = 3600; //  1 hora  en segundos

        $datos = [];

        // 1. Intentar cargar los datos desde la caché
        if (file_exists($cacheFile) && (filemtime($cacheFile) + $ttl > time())) {
            // El archivo de caché existe y no ha expirado
            $datos = json_decode(file_get_contents($cacheFile), true);
            // error_log("Datos de CAJA/CRONOGRAMA cargados desde la caché para ID: {$id}");
        } else {
            // Si no hay caché, ejecutar la consulta a la base de datos
            $datos = $this->cajaModel->getCronogramaByIdContrato($id);
            // Guardar los resultados en la caché
            // Asegurarse de que el directorio exista
            if (!is_dir(dirname($cacheFile))) {
                mkdir(dirname($cacheFile), 0777, true);
            }
            file_put_contents($cacheFile, json_encode($datos));
            // error_log("Datos de CAJA/CRONOGRAMA obtenidos de la BD y guardados en caché para ID: {$id}");
        }

        // Renderizar la vista con los datos
        $this->view('caja.cronograma', ['cronograma' => $datos]);

        // Detener el cronómetro y calcular el tiempo
        // $tiempoFin = microtime(true);
        // $tiempoEjecucion = $tiempoFin - $tiempoInicio;

        // Registrar el tiempo de ejecución en el log
        // error_log("Tiempo de ejecución de CAJA/CRONOGRAMA: " . number_format($tiempoEjecucion, 4) . " segundos.");
    }

    /**
     * API: Obtiene reporte de ingresos del día agrupado por método de pago
     * 
     * Endpoint AJAX que genera un reporte detallado de todos los ingresos del
     * día actual, agrupados por método de pago (Efectivo, Transferencia, Yape,
     * POS, etc.). Calcula subtotales por método y total general del día.
     * Requiere autenticación.
     *
     * @return never Respuesta JSON con datos agrupados o error
     */
    public function getReporteIngresosCajaHoy(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $transacciones = $this->cajaModel->getReporteIngresosHoy();

        $reporteAgrupado = [];
        $totalGeneral = 0;

        foreach ($transacciones as $transaccion) {
            $metodoPago = $transaccion['metodo_pago'];
            $monto = (float) $transaccion['monto'];

            if (!isset($reporteAgrupado[$metodoPago])) {
                $reporteAgrupado[$metodoPago] = [
                    'metodo_pago' => $metodoPago,
                    'transacciones' => [],
                    'subtotal' => 0
                ];
            }

            $reporteAgrupado[$metodoPago]['transacciones'][] = $transaccion;

            // Sumamos al subtotal del grupo y al total general.
            $reporteAgrupado[$metodoPago]['subtotal'] += $monto;
            $totalGeneral += $monto;
        }

        $respuesta = [
            'success' => true,
            'data' => array_values($reporteAgrupado),
            'total_general' => $totalGeneral

        ];

        echo json_encode($respuesta);

        exit();
    }

    /**
     * API: Obtiene reporte de pagos por rango de fechas personalizado
     * 
     * Endpoint AJAX que genera un reporte detallado de pagos entre dos fechas
     * específicas. Valida parámetros obligatorios y retorna error HTTP 400 si
     * faltan fechas. Útil para reportes mensuales, trimestrales o períodos
     * personalizados. Requiere autenticación.
     *
     * Validaciones implementadas:
     * - Presencia de ambas fechas (obligatorio)
     * - Formato de fecha válido (manejado por BD)
     * - Retorno apropiado si no hay datos
     *
     * @return never Respuesta JSON con datos del período, error de validación o sin resultados
     */
    public function reportePagosByFecha(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        $fechaInicio = $_GET['fecha_inicio'] ?? null;
        $fechaFin = $_GET['fecha_fin'] ?? null;


        if (!$fechaInicio || !$fechaFin) {
            http_response_code(400);
            echo json_encode(['error' => 'Fechas de inicio y fin son requeridas.']);
            exit();
        }


        $datos = $this->cajaModel->getReporteByFecha($fechaInicio, $fechaFin);

        if ($datos) {
            echo json_encode([
                'success' => true,
                'data' => $datos
            ]);
        } else {

            http_response_code(404);
            echo json_encode([
                'success' => false,
                'data' => [],
                'message' => 'No se encontraron datos para el rango de fechas proporcionado.'
            ]);
        }
        exit();
    }


    public function searchConceptosPagos(): void
    {

        header('Content-Type: application/json');
        $conceptos = $this->cajaModel->getConceptosPagos();

        if ($conceptos) {
            echo json_encode(['success' => true, 'conceptos' => $conceptos]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'data' => [],
                'message' => 'No se encontraron datos para el rango de fechas proporcionado.'
            ]);
        }
        exit();
    }

    public function searchClienteByDNI(string $dni): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $cliente = $this->cajaModel->getClienteByDni($dni);

        if ($cliente) {
            echo json_encode(["success" => true, "cliente" => $cliente]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'data' => [],
                'message' => 'No se encontró al cliente'
            ]);
        }
        exit();
    }





    public function storePagoCompuesto(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                return;
            }



            $detallesJsonRaw = $_POST['detalles_json'] ?? '[]';
            $data = array_map([Validador::class, 'limpiar'], $_POST);
            $errores = [];

            $idCliente = (int) ($data['idcliente'] ?? 0);
            $medioPago = $data['mediopago'] ?? 'Efectivo';
            $numTransaccion = $data['numerotransaccion'] ?? null;
            $montoTotal = (float) ($data['monto_total'] ?? 0);

            $idCuentaPago = null;
            if ($medioPago === 'Transferencia Bancaria' && !empty($data['idcuentapago'])) {
                $idCuentaPago = (int) $data['idcuentapago'];
                $cuentaEspecifica = $this->cajaModel->getNumCuentaPagoById($idCuentaPago);
            }

            $idColCaja = $_SESSION['user']['id'] ?? 0;


            if ($idCliente <= 0) $errores[] = 'El ID de cliente es inválido.';
            if ($montoTotal <= 0) $errores[] = 'El monto total debe ser mayor a 0.';
            if (empty($detallesJsonRaw) || $detallesJsonRaw === '[]') $errores[] = 'No se han añadido conceptos.';

            if (!empty($errores)) {
                echo json_encode(['success' => false, 'message' => implode('<br>', $errores)]);
                return;
            }


            $idPago = $this->cajaModel->registrarPagoCompuesto(
                $idCliente,
                $idColCaja,
                $medioPago,
                $numTransaccion,
                $idCuentaPago,
                $montoTotal,
                $detallesJsonRaw
            );

            if ($idPago <= 0) {
                throw new Exception('Fallo al guardar el pago en la base de datos.');
            }


            $mensajeExtra = "";
            $enlacePdf = null;
            $enlaceXml = null;
            $enlaceCdr = null;
            $nuevoNumero = null;

            try {
                $clienteData = $this->cajaModel->getDatosCliente($idCliente);

                if ($clienteData) {
                    $esRUC = (strlen($clienteData['nrodoc'] ?? '') == 11);
                    $tipoComprobante = $esRUC ? 1 : 2;
                    $serieBoleta = $esRUC ? 'FFF1' : 'BBB1';
                    $tipoDocCliente = $esRUC ? 6 : 1;

                    $nuevoNumero = $this->cajaModel->obtenerNuevoCorrelativo($serieBoleta);


                    $detallesArray = json_decode($detallesJsonRaw, true);
                    $itemsFacturacion = [];
                    $totalGravada = 0.00;
                    $totalIGV = 0.00;
                    $totalVenta = 0.00;
                    $factorIGV = 1.18;

                    foreach ($detallesArray as $det) {
                        $montoItem = (float)$det['monto'];
                        $nombreItem = $det['nombre'] ?? 'Concepto';
                        if ($montoItem > 0) {
                            $valorUnitario = round($montoItem / $factorIGV, 10);
                            $igvItem = round($montoItem - $valorUnitario, 2);

                            $itemsFacturacion[] = [
                                "unidad_de_medida" => "ZZ",
                                "descripcion" => $nombreItem,
                                "cantidad" => 1,
                                "valor_unitario" => $valorUnitario,
                                "precio_unitario" => $montoItem,
                                "subtotal" => $valorUnitario,
                                "tipo_de_igv" => 1,
                                "igv" => $igvItem,
                                "total" => $montoItem,
                            ];
                            $totalGravada += $valorUnitario;
                            $totalIGV += $igvItem;
                            $totalVenta += $montoItem;
                        }
                    }


                    $datosFacturacion = [
                        'tipo_comprobante' => 2,
                        'tipo_de_comprobante' => $tipoComprobante,
                        'serie' => $serieBoleta,
                        'numero_comprobante' => $nuevoNumero,
                        'items' => $itemsFacturacion,
                        'mediopago' => $medioPago == 'Transferencia Bancaria' ? $cuentaEspecifica['nombrecuenta'] : $medioPago,
                        'totales' => [
                            'total_gravada' => round($totalGravada, 2),
                            'total_igv' => round($totalIGV, 2),
                            'total_venta' => round($totalVenta, 2)
                        ],
                        'datos_cliente' => [
                            'tipo_documento' => $tipoDocCliente,
                            'numero_documento' => $clienteData['nrodoc'],
                            'denominacion' => $clienteData['razon_social'] ?? 'CLIENTE',
                            'direccion' => $clienteData['direccion'] ?? '-',
                            'email' => $clienteData['email'] ?? ''
                        ]
                    ];

                    $nubefactController = new ComprobanteNubefactController();
                    $respNube = $nubefactController->procesarPagoYEmitirComprobante($datosFacturacion);

                    if ($respNube['success']) {
                        $enlacePdf = $respNube['enlace_pdf'];
                        $enlaceXml = $respNube['enlace_xml'];


                        $enlaceCdr = $respNube['enlace_cdr'] ?? null;

                        $this->cajaModel->actualizarDatosFacturacion($idPago, $enlacePdf, $enlaceXml, $enlaceCdr, $nuevoNumero);
                    } else {
                        error_log("Error NubeFact: " . $respNube['message']);
                        $mensajeExtra = " (Sin Boleta: " . $respNube['message'] . ")";
                    }
                }
            } catch (Exception $ex) {
                error_log("Excepción Facturación: " . $ex->getMessage());
                $mensajeExtra = " (Error interno de facturación)";
            }

            echo json_encode([
                'success' => true,
                'message' => 'Pago registrado.' . $mensajeExtra,
                'enlace_pdf' => $enlacePdf,
                'enlace_xml' => $enlaceXml,
                'enlace_cdr' => $enlaceCdr,
                'id_pago' => $idPago
            ]);
        } catch (\Throwable $th) {
            http_response_code(500);
            error_log($th->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $th->getMessage()]);
        }
    }



    public function getContratosCompletados(): void
    {
        // $this->authRequired();
        header('Content-Type: application/json');

        $contratos = $this->cajaModel->getContratosCompletados();

        if (!$contratos) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'data' => [],
                'message' => 'No se encontraron contratos completados.'
            ]);
            exit();
        }

        echo json_encode([
            'success' => true,
            'data' => $contratos
        ]);
        exit();
    }

    /**
     * Pantalla de caja: buscar cliente por DNI y ver contratos ACT para ir al cronograma.
     */
    public function indexBuscarCliente(): void
    {
        $this->authRequired();
        $this->view('caja.buscarCliente');
    }

    /**
     * API JSON: contratos activos de un cliente (idcliente).
     */
    public function apiContratosPorCliente(string $id): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');
        $idcliente = (int) $id;
        if ($idcliente <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cliente inválido', 'data' => []]);
            return;
        }
        $rows = $this->cajaModel->getContratosActivosPorIdCliente($idcliente);
        echo json_encode(['success' => true, 'data' => $rows]);
    }
}
