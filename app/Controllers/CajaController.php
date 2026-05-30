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

use App\Config\CajaRoutes;
use App\Config\MediosPago;
use App\Core\Controller;
use App\Helpers\CronogramaAmortizacionHelper;
use App\Models\Caja;
use App\Models\PagoCronograma;
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

    private PagoCronograma $pagoCronogramaModel;

    private function limpiarCacheCronogramaPorContrato(int $idContrato): void
    {
        if ($idContrato <= 0) {
            return;
        }
        $cacheFile = __DIR__ . "/../../storage/cache/cronograma-contratos/cronograma-contrato{$idContrato}.json";
        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
        }
    }

    private function limpiarCacheCronogramaPorCliente(int $idCliente): void
    {
        if ($idCliente <= 0) {
            return;
        }
        $ids = $this->cajaModel->getIdsContratosPorCliente($idCliente);
        foreach ($ids as $idContrato) {
            $this->limpiarCacheCronogramaPorContrato((int) $idContrato);
        }
    }

    /**
     * Constructor del controlador
     * 
     * Inicializa la instancia del modelo Caja necesario para todas las
     * operaciones del controlador.
     */
    public function __construct()
    {
        $this->cajaModel = new Caja();
        $this->pagoCronogramaModel = new PagoCronograma();
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
        $this->authRequired();
        $datos = $this->cajaModel->getAllContratosDatos();
        if (!is_array($datos)) {
            $datos = [];
        }
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
        $this->view('caja.cobrosDenominacion', [
            'idConceptoVarios' => $this->cajaModel->getIdConceptoVariosCaja(),
            'mediosPago' => MediosPago::opciones(),
            'cajaRoutes' => [
                'lista' => CajaRoutes::LISTA_CONTRATOS,
                'buscar' => CajaRoutes::BUSCAR_DOCUMENTO,
                'cobro' => CajaRoutes::COBRO_CONCEPTOS,
            ],
        ]);
    }

    /** Redirección permanente desde /caja/buscar-cliente */
    public function redirectLegacyBuscarCliente(): void
    {
        $this->authRequired();
        $this->redirectPreservandoQuery(CajaRoutes::BUSCAR_DOCUMENTO);
    }

    /** Redirección permanente desde /caja/pagos/denominacion */
    public function redirectLegacyPagosDenominacion(): void
    {
        $this->authRequired();
        $this->redirectPreservandoQuery(CajaRoutes::COBRO_CONCEPTOS);
    }

    private function redirectPreservandoQuery(string $ruta): void
    {
        $qs = $_SERVER['QUERY_STRING'] ?? '';
        $destino = $ruta . ($qs !== '' ? '?' . $qs : '');
        header('Location: ' . $destino, true, 301);
        exit;
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
        $this->view('caja.cronograma', [
            'cronograma' => $datos,
            'mediosPago' => \App\Config\MediosPago::opciones(),
        ]);

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
        $this->authRequired();
        header('Content-Type: application/json');
        $conceptos = $this->cajaModel->getConceptosPagos();
        $list = is_array($conceptos) ? $conceptos : [];

        // Catálogo permitido para el combo de Caja (conceptos sugeridos).
        // Importante: no eliminamos conceptos en BD (pueden estar en pagos históricos),
        // solo los ocultamos del combo para evitar usar precios equivocados.
        $permitidos = [
            'RECOJO VEHICULAR',
            'BUSQUEDA DE LLAVE',
            'DUPLICADO DE CONTRATO',
            'DUPLICADO DE TARJETA',
            'CARTA PODER',
            'VIGENCIA DE PODER',
            'GPS',
            'VARIOS CAJA (MANUAL)', // mantener para montos variables
        ];
        $permitidosMap = array_fill_keys($permitidos, true);

        $normalizar = static function (?string $s): string {
            $s = trim((string) ($s ?? ''));
            $s = mb_strtoupper($s, 'UTF-8');
            // quitar tildes/acentos para comparar ("VEHÍCULAR" ~ "VEHICULAR")
            $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            if (is_string($t) && $t !== '') {
                $s = $t;
            }
            // normalizar espacios
            $s = preg_replace('/\s+/', ' ', $s) ?: $s;
            return $s;
        };

        $list = array_values(array_filter($list, static function ($row) use ($permitidosMap, $normalizar) {
            $c = is_array($row) ? ($row['concepto'] ?? '') : '';
            return isset($permitidosMap[$normalizar((string) $c)]);
        }));

        echo json_encode([
            'success' => true,
            'conceptos' => $list,
            'message' => $list === [] ? 'Catálogo de conceptos vacío' : null
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function searchClienteByDNI(string $dni): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $documento = preg_replace('/\D+/', '', $dni);
        if (strlen($documento) !== 8 && strlen($documento) !== 11) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Documento inválido. Use 8 dígitos (DNI) u 11 (RUC).'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $cliente = $this->cajaModel->getClienteByDni($documento);
        if (!$cliente) {
            $docsSync = [$documento];
            if (strlen($documento) === 11) {
                $last8 = substr($documento, -8);
                if ($last8 !== '' && $last8 !== $documento) {
                    $docsSync[] = $last8;
                }
            }
            foreach ($docsSync as $docSync) {
                if (strlen($docSync) !== 8) {
                    continue;
                }
                $syncOk = $this->cajaModel->sincronizarClienteDesdeRegistroVentasByDni($docSync);
                if ($syncOk) {
                    $cliente = $this->cajaModel->getClienteByDni($documento);
                    if (!$cliente) {
                        $cliente = $this->cajaModel->getClienteByDni($docSync);
                    }
                    if ($cliente) {
                        break;
                    }
                }
            }
        }

        if ($cliente) {
            echo json_encode(['success' => true, 'cliente' => $cliente], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'data' => [],
                'message' => 'No se encontró al cliente con ese documento.'
            ], JSON_UNESCAPED_UNICODE);
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
            $data = $_POST;
            $errores = [];

            $idCliente = (int) (($data['idcliente'] ?? 0));
            $medioPago = trim((string) ($data['mediopago'] ?? 'Efectivo'));
            $numTransaccion = trim((string) ($data['numerotransaccion'] ?? ''));
            $montoTotal = (float) ($data['monto_total'] ?? 0);
            $requestId = trim((string) ($data['request_id'] ?? ''));

            $idCuentaPago = null;
            $cuentaEspecifica = null;
            if (MediosPago::requiereCuenta($medioPago)) {
                if (empty($data['idcuentapago'])) {
                    $errores[] = $medioPago === MediosPago::INTERBANCARIO
                        ? 'Seleccione la cuenta destino (CCI) para el pago interbancario.'
                        : 'Seleccione una cuenta bancaria para la transferencia.';
                } else {
                    $idCuentaPago = (int) $data['idcuentapago'];
                    if (!$this->pagoCronogramaModel->cuentaValidaParaMedio($idCuentaPago, $medioPago)) {
                        $errores[] = 'La cuenta seleccionada no corresponde al medio de pago.';
                    } else {
                        $cuentaEspecifica = $this->cajaModel->getNumCuentaPagoById($idCuentaPago);
                    }
                }
            }

            $idColCaja = $_SESSION['user']['id'] ?? 0;

            $detallesArray = json_decode($detallesJsonRaw, true);
            if (!is_array($detallesArray)) {
                $detallesArray = [];
            }

            if ($idCliente <= 0) $errores[] = 'El ID de cliente es inválido.';
            if ($montoTotal <= 0) $errores[] = 'El monto total debe ser mayor a 0.';
            if (empty($detallesJsonRaw) || $detallesJsonRaw === '[]') $errores[] = 'No se han añadido conceptos.';
            if (empty($detallesArray)) $errores[] = 'El detalle de conceptos es inválido.';
            if ($requestId === '' || !preg_match('/^[A-Za-z0-9_-]{16,120}$/', $requestId)) {
                $errores[] = 'request_id inválido.';
            }
            if ($medioPago !== 'Efectivo' && $numTransaccion === '') {
                $errores[] = 'Ingrese el número de transacción para el medio de pago seleccionado.';
            }
            if ($medioPago === 'Efectivo') {
                $numTransaccion = null;
            }

            $sumaDetalles = 0.0;
            foreach ($detallesArray as $det) {
                $monto = (float) ($det['monto'] ?? 0);
                if ($monto < 0) {
                    $errores[] = 'Hay conceptos con monto negativo.';
                    break;
                }
                $sumaDetalles += $monto;
            }
            $sumaDetalles = round($sumaDetalles, 2);
            if (abs($sumaDetalles - round($montoTotal, 2)) > 0.01) {
                $errores[] = "El total no coincide con el detalle (detalle: {$sumaDetalles}, total: " . round($montoTotal, 2) . ").";
            }

            if (!empty($errores)) {
                echo json_encode(['success' => false, 'message' => implode('<br>', $errores)]);
                return;
            }

            $payloadHash = hash('sha256', json_encode([
                'idcliente' => $idCliente,
                'mediopago' => $medioPago,
                'numerotransaccion' => $numTransaccion,
                'idcuentapago' => $idCuentaPago,
                'monto_total' => round($montoTotal, 2),
                'detalles' => $detallesArray,
            ], JSON_UNESCAPED_UNICODE));

            $idem = $this->cajaModel->getIdempotenciaByRequestId($requestId);
            if ($idem && strtoupper((string) ($idem['estado'] ?? '')) === 'COMPLETED' && !empty($idem['idpago'])) {
                $pagoExistente = $this->cajaModel->getPagoById((int) $idem['idpago']);
                echo json_encode([
                    'success' => true,
                    'message' => 'Operación ya procesada anteriormente.',
                    'facturado' => !empty($pagoExistente['enlace_pdf_nubefact']),
                    'enlace_pdf' => $pagoExistente['enlace_pdf_nubefact'] ?? null,
                    'enlace_xml' => $pagoExistente['enlace_xml_nubefact'] ?? null,
                    'enlace_cdr' => $pagoExistente['enlace_del_cdr'] ?? null,
                    'id_pago' => (int) $idem['idpago'],
                ], JSON_UNESCAPED_UNICODE);
                return;
            }
            if ($idem && strtoupper((string) ($idem['estado'] ?? '')) === 'PROCESSING') {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Existe una operación en proceso para este request_id.'], JSON_UNESCAPED_UNICODE);
                return;
            }
            if (!$idem) {
                $okIdem = $this->cajaModel->crearIdempotenciaEnProceso($requestId, $idCliente, $montoTotal, $payloadHash);
                if (!$okIdem) {
                    $idemNow = $this->cajaModel->getIdempotenciaByRequestId($requestId);
                    if ($idemNow && strtoupper((string) ($idemNow['estado'] ?? '')) === 'PROCESSING') {
                        http_response_code(409);
                        echo json_encode(['success' => false, 'message' => 'Existe una operación en proceso para este request_id.'], JSON_UNESCAPED_UNICODE);
                        return;
                    }
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'No se pudo registrar idempotencia. Aplique el patch de BD de caja.'], JSON_UNESCAPED_UNICODE);
                    return;
                }
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
                $this->cajaModel->fallarIdempotencia($requestId, 'Fallo al guardar pago');
                throw new Exception('Fallo al guardar el pago en la base de datos.');
            }


            $mensajeExtra = "";
            $enlacePdf = null;
            $enlaceXml = null;
            $enlaceCdr = null;
            $nuevoNumero = null;
            $facturado = false;
            $mensajeFacturacion = null;
            $comprobanteSerie = null;
            $comprobanteNumero = null;
            $comprobanteTipoLetra = null;

            try {
                $clienteData = $this->cajaModel->getDatosCliente($idCliente);

                if ($clienteData) {
                    $esRUC = (strlen($clienteData['nrodoc'] ?? '') == 11);
                    // 1=Factura (RUC), 2=Boleta (DNI) — debe coincidir con Nubefact y con la serie FFF1/BBB1
                    $tipoComprobante = $esRUC ? 1 : 2;
                    $serieBoleta = $esRUC ? 'FFF1' : 'BBB1';
                    $tipoDocCliente = $esRUC ? 6 : 1;
                    $comprobanteTipoLetra = $esRUC ? 'F' : 'B';
                    $itemsFacturacion = [];
                    $totalGravada = 0.00;
                    $totalIGV = 0.00;
                    $totalVenta = 0.00;
                    $factorIGV = 1.18;

                    foreach ($detallesArray as $det) {
                        $montoItem = round((float) ($det['monto'] ?? 0), 2);
                        $nombreItem = trim((string) ($det['nombre'] ?? 'Concepto'));
                        if ($nombreItem === '') {
                            $nombreItem = 'Concepto';
                        }
                        // SUNAT/Nubefact: evitar descripciones excesivas o con espacios raros.
                        $nombreItem = mb_substr(preg_replace('/\s+/', ' ', $nombreItem) ?: $nombreItem, 0, 250);
                        if ($montoItem > 0) {
                            $valorUnitario = round($montoItem / $factorIGV, 2);
                            $igvItem = round($montoItem - $valorUnitario, 2);
                            $totalItem = round($valorUnitario + $igvItem, 2);

                            $itemsFacturacion[] = [
                                "unidad_de_medida" => "ZZ",
                                "descripcion" => $nombreItem,
                                "cantidad" => 1,
                                "valor_unitario" => $valorUnitario,
                                "precio_unitario" => $totalItem,
                                "subtotal" => $valorUnitario,
                                "tipo_de_igv" => 1,
                                "igv" => $igvItem,
                                "total" => $totalItem,
                            ];
                            $totalGravada += $valorUnitario;
                            $totalIGV += $igvItem;
                            $totalVenta += $totalItem;
                        }
                    }


                    $textoMedioPago = $medioPago;
                    if (MediosPago::requiereCuenta($medioPago) && is_array($cuentaEspecifica) && !empty($cuentaEspecifica['nombrecuenta'])) {
                        $textoMedioPago = $cuentaEspecifica['nombrecuenta'];
                    }

                    $comprobanteSerie = $serieBoleta;
                    try {
                        $nuevoNumero = $this->cajaModel->obtenerSiguienteCorrelativoBloqueado($serieBoleta);
                        $comprobanteNumero = $nuevoNumero;

                        $datosFacturacion = [
                            'tipo_comprobante' => $tipoComprobante,
                            'serie' => $serieBoleta,
                            'numero_comprobante' => $nuevoNumero,
                            'items' => $itemsFacturacion,
                            'mediopago' => $textoMedioPago,
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
                            $this->cajaModel->confirmarCorrelativoBloqueado($serieBoleta, (int) $nuevoNumero);

                            $this->cajaModel->actualizarDatosFacturacion(
                                $idPago,
                                $enlacePdf,
                                $enlaceXml,
                                $enlaceCdr,
                                (int) $nuevoNumero,
                                $serieBoleta,
                                $tipoComprobante
                            );
                            $facturado = true;
                        } else {
                            $this->cajaModel->cancelarCorrelativoBloqueado();
                            $msgNube = $respNube['message'] ?? 'Error al emitir comprobante';
                            error_log("Error NubeFact: " . $msgNube);
                            $mensajeExtra = " (Sin Boleta: " . $msgNube . ")";
                            $mensajeFacturacion = $msgNube;
                        }
                    } catch (\Throwable $tCorrelativo) {
                        $this->cajaModel->cancelarCorrelativoBloqueado();
                        throw $tCorrelativo;
                    }
                } else {
                    $mensajeExtra = " (No se pudo facturar: sin datos de cliente en BD.)";
                    $mensajeFacturacion = 'No hay datos de cliente para emitir comprobante.';
                }
            } catch (Exception $ex) {
                error_log("Excepción Facturación: " . $ex->getMessage());
                $mensajeExtra = " (Error interno de facturación)";
                $mensajeFacturacion = 'Error interno al emitir comprobante.';
            }

            foreach ($detallesArray as $det) {
                $source = strtolower(trim((string) ($det['source'] ?? '')));
                $dniMeta = preg_replace('/\D+/', '', (string) ($det['dni'] ?? ''));
                $numCuotaMeta = (int) ($det['numero_cuota'] ?? 0);
                $fuentesCuota = ['registro_ventas_vehiculares', 'import_excel'];
                if (
                    in_array($source, $fuentesCuota, true)
                    && strlen($dniMeta) >= 8
                    && $numCuotaMeta > 0
                ) {
                    $chasisMeta = trim((string) ($det['chasis'] ?? ''));
                    $this->cajaModel->marcarCuotaPagadaVentasPorDocumento(
                        $dniMeta,
                        $numCuotaMeta,
                        $idPago,
                        $chasisMeta !== '' ? $chasisMeta : null
                    );
                }
            }

            $this->limpiarCacheCronogramaPorCliente($idCliente);
            $this->cajaModel->completarIdempotencia($requestId, $idPago);

            echo json_encode([
                'success' => true,
                'message' => 'Pago registrado.' . $mensajeExtra,
                'facturado' => $facturado,
                'mensaje_facturacion' => $mensajeFacturacion,
                'enlace_pdf' => $enlacePdf,
                'enlace_xml' => $enlaceXml,
                'enlace_cdr' => $enlaceCdr,
                'id_pago' => $idPago,
                'comprobante_serie' => $comprobanteSerie,
                'comprobante_numero' => $comprobanteNumero,
                'comprobante_tipo' => $comprobanteTipoLetra,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            $requestId = trim((string) ($_POST['request_id'] ?? ''));
            if ($requestId !== '') {
                $this->cajaModel->fallarIdempotencia($requestId, $th->getMessage());
            }
            http_response_code(500);
            error_log($th->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $th->getMessage()]);
        }
    }



    public function getContratosCompletados(): void
    {
        $this->authRequired();
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
        $this->view('caja.buscarCliente', [
            'cajaRoutes' => [
                'lista' => CajaRoutes::LISTA_CONTRATOS,
                'buscar' => CajaRoutes::BUSCAR_DOCUMENTO,
                'cobro' => CajaRoutes::COBRO_CONCEPTOS,
            ],
        ]);
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

    /**
     * API JSON: cronograma de pagos por idcliente (usa contrato ACT más reciente).
     * Retorna cuotas con estado amigable: Pagado / Por saldar / Por abonar.
     */
    public function apiCronogramaPorCliente(string $id): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        $idcliente = (int) $id;
        if ($idcliente <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cliente inválido', 'data' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Preferimos ACT, pero si no hay, tomamos el contrato más reciente (FIN/INACT) para poder mostrar cronograma.
        $contratos = $this->cajaModel->getContratosActivosPorIdCliente($idcliente);
        if (empty($contratos)) {
            $contratos = $this->cajaModel->getContratosPorIdCliente($idcliente);
        }
        $gps = $this->cajaModel->getMontoConceptoPagoByNombre('GPS');

        // Fallback si no hay contrato: usar registro_ventas_vehiculares (Excel) para construir un cronograma estimado.
        if (empty($contratos)) {
            $cli = $this->cajaModel->getDatosCliente($idcliente);
            $dni = is_array($cli) ? preg_replace('/\\D+/', '', (string) ($cli['nrodoc'] ?? '')) : '';
            if ($this->responderCronogramaDesdeDocumentoSiExiste($dni, $gps)) {
                return;
            }

            echo json_encode([
                'success' => true,
                'message' => 'Sin contrato ni registro vehicular para este cliente.',
                'data' => [],
                'gps' => $gps,
                'source' => 'none'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $idContrato = (int) ($contratos[0]['idcontrato'] ?? 0);
        if ($idContrato <= 0) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Contrato inválido.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $cronograma = $this->cajaModel->getCronogramaByIdContrato($idContrato);

        // localizar primera cuota pendiente (para marcar "Por saldar")
        $primeraPendienteNum = null;
        foreach ($cronograma as $c) {
            $pendCuota = (float) ($c['saldocuota_pendiente'] ?? 0);
            $pendPen = (float) ($c['penalidad_pendiente'] ?? 0);
            $estado = strtolower(trim((string) ($c['estado'] ?? '')));
            if ($estado !== 'pagado' && ($pendCuota > 0 || $pendPen > 0)) {
                $primeraPendienteNum = (int) ($c['numcuota'] ?? 0);
                if ($primeraPendienteNum > 0) break;
            }
        }

        $rows = [];
        $today = new \DateTimeImmutable('today');
        $cuotasPagadas = 0;
        $totalInteres = 0.0;
        $totalCapital = 0.0;
        $cuotaMensual = 0.0;
        $montoFinanciar = 0.0;

        foreach ($cronograma as $c) {
            $num = (int) ($c['numcuota'] ?? 0);
            $estadoRaw = strtolower(trim((string) ($c['estado'] ?? '')));
            $estadoLabel = 'Por abonar';

            if ($estadoRaw === 'pagado') {
                $estadoLabel = 'Pagado';
                $cuotasPagadas++;
            } elseif ($primeraPendienteNum !== null && $num === $primeraPendienteNum) {
                $estadoLabel = 'Por saldar';
            }

            $valorCuota = (float) ($c['valorcuota'] ?? 0);
            $interes = (float) ($c['interes'] ?? 0);
            $abonoCapital = (float) ($c['abonocapital'] ?? 0);
            $saldoCapital = (float) ($c['saldocapital'] ?? 0);
            $penalidad = (float) ($c['penalidad'] ?? 0);
            $cuotaMensual = $valorCuota > 0 ? $valorCuota : $cuotaMensual;
            $totalInteres += $interes;
            $totalCapital += $abonoCapital;

            if ($num === 1) {
                $montoFinanciar = round($saldoCapital + $abonoCapital, 2);
            }

            $totalNormal = round($valorCuota + $gps, 2);
            $totalMora = round($totalNormal + max(0, $penalidad), 2);

            $conMora = false;
            $fechaDb = $c['fechapago'] ?? null;
            if ($estadoLabel === 'Por saldar' && $fechaDb) {
                try {
                    $venc = new \DateTimeImmutable((string) $fechaDb);
                    $toleranciaFin = $venc->modify('+3 day');
                    $conMora = $today > $toleranciaFin;
                } catch (\Throwable $e) {
                    $conMora = false;
                }
            }
            if ($conMora && $estadoLabel === 'Por saldar') {
                $estadoLabel = 'Por saldar (MORA)';
            }

            $rows[] = [
                'numcuota' => $num,
                'fechapago' => $c['fechapago'] ?? null,
                'fecha_cronograma' => $c['fechapago'] ?? null,
                'fecha_pago_cliente' => $c['fecha_pago_real'] ?? null,
                'interes' => round($interes, 2),
                'abonocapital' => round($abonoCapital, 2),
                'valorcuota' => round($valorCuota, 2),
                'saldocapital' => round($saldoCapital, 2),
                'gps' => round($gps, 2),
                'total_con_gps' => $totalNormal,
                'total_normal' => $totalNormal,
                'total_con_mora' => $totalMora,
                'total' => $conMora ? $totalMora : $totalNormal,
                'estado' => $estadoLabel,
                'estado_raw' => $c['estado'] ?? null,
            ];
        }

        $saldoActual = $montoFinanciar;
        if ($cuotasPagadas > 0) {
            foreach ($rows as $fila) {
                if ((int) $fila['numcuota'] === $cuotasPagadas) {
                    $saldoActual = (float) $fila['saldocapital'];
                    break;
                }
            }
        }

        $fechaInicioCrono = !empty($rows) ? ($rows[0]['fecha_cronograma'] ?? $rows[0]['fechapago'] ?? null) : null;
        $fechaFinCrono = !empty($rows) ? ($rows[count($rows) - 1]['fecha_cronograma'] ?? $rows[count($rows) - 1]['fechapago'] ?? null) : null;

        $meta = [
            'monto_financiar' => $montoFinanciar,
            'lo_que_debe_pagar_cliente' => $montoFinanciar,
            'cuota_por_mes' => round($cuotaMensual, 2),
            'duracion_meses' => count($rows),
            'cuotas_pagadas' => $cuotasPagadas,
            'cuotas_pendientes' => max(0, count($rows) - $cuotasPagadas),
            'saldo_capital_actual' => round($saldoActual, 2),
            'tasa_mensual_pct' => null,
            'tasa_anual_pct' => null,
            'total_interes_proyectado' => round($totalInteres, 2),
            'total_capital_proyectado' => round($totalCapital, 2),
            'gps_mensual' => round($gps, 2),
            'fecha_inicio_cronograma' => $fechaInicioCrono,
            'fecha_fin_cronograma' => $fechaFinCrono,
        ];

        $this->responderCronogramaDetalladoJson($rows, $meta, [
            'gps' => $gps,
            'idcontrato' => $idContrato,
            'vehiculo' => $contratos[0]['vehiculo_resumen'] ?? null,
            'contrato_estado' => $contratos[0]['estado'] ?? null,
            'source' => 'contrato',
        ]);
    }

    /**
     * API JSON: cronograma detallado por DNI/RUC (Excel/import sin cliente en BD).
     */
    public function apiCronogramaPorDocumento(string $dni): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        $documento = preg_replace('/\D+/', '', $dni);
        if (strlen($documento) !== 8 && strlen($documento) !== 11) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Documento inválido.', 'data' => []], JSON_UNESCAPED_UNICODE);
            return;
        }

        $gps = $this->cajaModel->getMontoConceptoPagoByNombre('GPS');
        if ($this->responderCronogramaDesdeDocumentoSiExiste($documento, $gps)) {
            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Sin registro vehicular para este documento.',
            'data' => [],
            'resumen' => null,
            'gps' => $gps,
            'source' => 'none',
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return bool true si respondió JSON con cronograma
     */
    private function responderCronogramaDesdeDocumentoSiExiste(string $documento, float $gps): bool
    {
        if (strlen($documento) !== 8 && strlen($documento) !== 11) {
            return false;
        }

        $rowsVeh = $this->cajaModel->getRegistroVentasVehicularesByDni($documento);
        if (empty($rowsVeh)) {
            return false;
        }

        $r = $rowsVeh[0];
        $plazo = (int) ($r['plazo_meses'] ?? 0);
        $pagada = (int) ($r['numero_cuota_pagada'] ?? 0);
        $cuotaTotal = (float) ($r['cuota_total_mensual'] ?? 0);
        $cuotaBase = (float) ($r['cuota_base'] ?? 0);
        $montoFin = (float) ($r['monto_financiar'] ?? 0);
        if ($montoFin <= 0) {
            $montoFin = max(0, (float) ($r['precio_total'] ?? 0) - (float) ($r['pago_inicial'] ?? 0));
        }
        $cuotaMensual = $cuotaTotal > 0 ? $cuotaTotal : $cuotaBase;

        $generado = CronogramaAmortizacionHelper::generar([
            'monto_financiar' => $montoFin,
            'cuota_mensual' => $cuotaMensual,
            'plazo_meses' => $plazo,
            'fecha_inicio' => (string) ($r['fecha_inicio_credito'] ?? ''),
            'cuotas_pagadas' => $pagada,
            'gps_monto' => $gps,
            'fechas_pago' => CronogramaAmortizacionHelper::fechasPagoDesdeImport($r),
        ]);

        $this->responderCronogramaDetalladoJson(
            $generado['filas'],
            $generado['meta'],
            [
                'gps' => $gps,
                'idcontrato' => null,
                'vehiculo' => trim((string) (($r['marca'] ?? '') . ' / ' . ($r['modelo'] ?? ''))),
                'contrato_estado' => null,
                'cliente_nombre' => $r['nombre_cliente'] ?? null,
                'source' => $r['fuente'] ?? 'registro_ventas_vehiculares',
                'message' => 'Cronograma calculado desde datos del Excel/import.',
            ]
        );

        return true;
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @param array<string, mixed> $meta
     * @param array<string, mixed> $extra
     */
    private function responderCronogramaDetalladoJson(array $filas, array $meta, array $extra): void
    {
        echo json_encode(array_merge([
            'success' => true,
            'data' => $filas,
            'resumen' => $meta,
        ], $extra), JSON_UNESCAPED_UNICODE);
    }

    /**
     * API JSON: registro ventas vehiculares por DNI (8) o RUC (11).
     */
    public function apiRegistroVentasVehicularesByDNI(string $dni): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        $documento = preg_replace('/\D+/', '', $dni);
        if (strlen($documento) !== 8 && strlen($documento) !== 11) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Documento inválido. Use 8 dígitos (DNI) u 11 (RUC).',
                'data' => []
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $rows = $this->cajaModel->getRegistroVentasVehicularesByDni($documento);
        echo json_encode([
            'success' => true,
            'data' => $rows
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * API JSON: consultar estado SUNAT en Nubefact y actualizar CDR en pagos.
     * body: { idpago, tipo_comprobante, serie, numero }
     */
    public function apiConsultarEstadoSunatNubefact(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $idpago = (int) ($_POST['idpago'] ?? 0);
        $tipo = (int) ($_POST['tipo_comprobante'] ?? 0);
        $serie = trim((string) ($_POST['serie'] ?? ''));
        $numero = (int) ($_POST['numero'] ?? 0);

        if ($idpago <= 0 || $tipo <= 0 || $serie === '' || $numero <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parámetros inválidos'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $nubefact = new ComprobanteNubefactController();
        $res = $nubefact->consultarEstadoComprobante($tipo, $serie, $numero);

        if (!empty($res['success'])) {
            $cdr = $res['enlace_cdr'] ?? null;
            if ($cdr) {
                $this->cajaModel->actualizarCdrFacturacion($idpago, $cdr);
            }
        }

        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}
