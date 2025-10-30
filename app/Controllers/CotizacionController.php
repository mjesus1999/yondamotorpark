<?php

/**
 * Controlador de Cotización
 * 
 * Gestiona todas las operaciones relacionadas con cotizaciones de venta
 * de vehículos, incluyendo creación con múltiples opciones de financiamiento,
 * cálculos de cuotas con tasas de interés, gestión de pagos de inicial,
 * generación de reportes PDF, control de acceso por roles (supervisores/asesores),
 * y reactivación de cotizaciones vencidas.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cotizacion;
use App\Models\Vehiculo;
use App\Models\FormatoCotizacion;
use App\Helpers\Validador;
use App\Config\ConceptosPago;
use Exception;
use JsonException;
use PDOException;

/**
 * Clase CotizacionController
 * 
 * Controlador para la gestión integral de cotizaciones de venta.
 * Implementa control de acceso basado en roles (supervisores ven todas,
 * asesores solo las suyas), cálculo de financiamiento con interés compuesto,
 * generación de cronogramas de pago, registro de pagos iniciales con
 * conversión de moneda, y generación de documentos PDF para clientes.
 */
class CotizacionController extends Controller
{
    /**
     * Modelo de Cotizacion
     * @var Cotizacion
     */
    private Cotizacion $cotizacionModel;
    /**
     * Modelo de Vehiculo
     * @var Vehiculo
     */
    private Vehiculo $vehiculoModel;
    /**
     * Modelo de FormatoCotizacion
     * @var FormatoCotizacion
     */
    private FormatoCotizacion $formatoModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa los modelos de Cotizacion, Vehiculo y FormatoCotizacion
     * necesarios para las operaciones del controlador
     */
    public function __construct()
    {
        $this->cotizacionModel = new Cotizacion();
        $this->vehiculoModel = new Vehiculo();
        $this->formatoModel = new FormatoCotizacion();
    }

    /**
     * Muestra el listado de cotizaciones con control de acceso por rol
     * 
     * Renderiza la vista de cotizaciones aplicando filtros según el rol del usuario:
     * - Supervisores/Jefes: Ven todas las cotizaciones del estado seleccionado
     * - Asesores: Solo ven sus propias cotizaciones
     *
     * Estados válidos: 'P' (Pendiente), 'A' (Aprobada), 'S' (Separada), 
     * 'V' (Vencida), 'C' (Cancelada)
     * 
     * Cargos supervisores: 1 (Jefe Sistemas), 8 (Logística), 10 (RRHH),
     * 13 (Contabilidad), 14 (Marketing), 16 (Ventas), 17 (Caja)
     * 
     * @param string $estado Estado de las cotizaciones a mostrar (por defecto: 'P')
     * @return void
     */
    public function index(string $estado = 'P'): void
    {
        $this->authRequired();

        // Obtener información del usuario logueado
        $idasesor = $_SESSION['user']['id'] ?? null;
        $idcargo = $_SESSION['user']['idcargo'] ?? null;

        if (!$idasesor || !$idcargo) {

            $_SESSION['error_message'] = "No se pudo identificar al usuario.";
            header('Location: /login');
            exit;
        }

        // Definir cargos que pueden ver todas las cotizaciones (supervisores/jefes)
        $cargosSupervisores = [
            1,
            8,
            10,
            13,
            14,
            16,
            17
        ];

        // Verificar si el usuario puede ver todas las cotizaciones o solo las suyas
        $puedeVerTodas = in_array($idcargo, $cargosSupervisores);


        $cotizaciones = [];
        $estadoUpperCase = strtoupper($estado);


        if ($puedeVerTodas) {

            $cotizaciones = $this->cotizacionModel->getAll($estadoUpperCase);

            // error_log('USUARIO SUPERVISOR - Puede ver todas las cotizaciones de estado: ' . $estadoUpperCase);
            // error_log('COTIZACIONES CARGADAS: ' . count($cotizaciones));
        } else {
            // Asesores ven solo sus cotizaciones, FILTRADAS por estado
            $cotizaciones = $this->cotizacionModel->getAllByAsesor($idasesor, $estadoUpperCase);

            // error_log('USUARIO ASESOR - Solo ve sus cotizaciones de estado: ' . $estadoUpperCase);
            // error_log('COTIZACIONES DEL ASESOR ' . $idasesor . ': ' . count($cotizaciones));
        }

        $this->view("cotizacion.index", [
            'cotizaciones' => $cotizaciones,
            'puede_ver_todas' => $puedeVerTodas,
            'estadoActual' => $estadoUpperCase,
            'usuario_actual' => [
                'id' => $idasesor,
                'cargo' => $idcargo,
                'es_supervisor' => $puedeVerTodas
            ]
        ]);
    }

    /**
     * Muestra la vista de gestión de pagos de inicial
     * 
     * Renderiza la interfaz para registrar y visualizar pagos del inicial
     * de una cotización, incluyendo total pagado, saldo pendiente, historial
     * de pagos y estado de completitud para habilitar contrato.
     *
     * @param mixed $idCotizacion ID de la cotización
     * @return void
     */
    public function indexPagoInicial($idCotizacion)
    {
        $datos = $this->cotizacionModel->getDatosCotizacion($idCotizacion);
        $completoIncial = $this->cotizacionModel->completoInicial($idCotizacion);
        $montosInfo = $this->cotizacionModel->getTotalPagadoYSaldoPendiente($idCotizacion);
        $historialPagos = $this->cotizacionModel->getHistorialPagosInicial($idCotizacion);
        $this->view('cotizacion.pagoInicial', ['cotizacion' => $datos, 'montosInfo' => $montosInfo, 'historialPagos' => $historialPagos, 'completoInicial' => $completoIncial]);
    }

    /**
     * Genera reporte PDF de cotización con control de acceso
     * 
     * Genera documento PDF de cotización con todas las opciones de financiamiento.
     * Aplica control de acceso: supervisores pueden generar cualquier cotización,
     * asesores solo las propias. Incluye validación de permisos antes de generar.
     *
     * @param mixed $id ID de la cotización
     * @return void
     */
    public function html2pdfReport($id): void
    {
        $this->authRequired();

        // Obtener información del usuario logueado
        $idasesor = $_SESSION['user']['id'] ?? null;
        $idcargo = $_SESSION['user']['idcargo'] ?? null;

        // Obtener cotización completa (incluye opciones_financiamiento dentro del modelo)
        $cotizacion = $this->cotizacionModel->getById((int) $id);

        if (!$cotizacion) {
            http_response_code(404);
            $_SESSION['error_message'] = "Cotización no encontrada.";
            header('Location: /cotizacion');
            exit;
        }

        // Definir cargos supervisores
        $cargosSupervisores = [1, 8, 10, 13, 14, 16, 17];

        // Verificar permisos: supervisores pueden ver cualquier cotización, asesores solo las suyas
        if (!in_array($idcargo, $cargosSupervisores) && $cotizacion['idasesor'] != $idasesor) {
            http_response_code(403);
            $_SESSION['error_message'] = "No tienes permisos para acceder a esta cotización.";
            header('Location: /cotizacion');
            exit;
        }

        // Asegurarnos de traer las opciones de financiamiento directamente desde el modelo
        $financiamientos = $this->cotizacionModel->getFinanciamientos((int) $id);
        $cotizacion['opciones_financiamiento'] = $financiamientos;

        // Pasamos tanto el id como la estructura completa al view
        $this->view('pdf/cotizacion/cotizacion-html2pdf', [
            'id' => (int) $id,
            'cotizacion' => $cotizacion
        ]);
    }

    /**
     * Muestra la vista de reportes de cotizaciones
     * 
     * Renderiza la interfaz para generar reportes y análisis de cotizaciones.
     * Requiere autenticación.
     *
     * @return void
     */
    public function indexReporteByCotizacion(): void
    {
        $this->authRequired();
        $this->view('cotizacion.reporte-cotizacion');
    }

    /**
     * Registra un pago del inicial de cotización
     * 
     * Endpoint AJAX que procesa pagos de inicial con validaciones exhaustivas,
     * manejo de comprobantes, conversión de moneda y rollback automático de
     * archivos en caso de error. Valida disponibilidad de vehículo mediante
     * triggers de BD.
     *
     * Validaciones realizadas:
     * - Campos obligatorios: concepto, vehículo, medio de pago, fecha, montos
     * - Comprobante: obligatorio para medios distintos a efectivo
     * - Conversión de moneda: calcula montos según moneda de cotización
     *
     * @throws \Exception 
     * @return void
     */
    public function storePagoInicial()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $rutaArchivoGuardado = null;

        try {
            $data = array_map([Validador::class, 'limpiar'], $_POST);
            $errores = [];

            $registro = [
                'idconcepto' => ConceptosPago::INICIAL_ID,
                'idcotizacion' => $data['idcotizacion'] ?? null,
                'idvehiculo' => $data['idvehiculo'] ?? null,
                'idcuentapago' => empty($data['idcuentapago']) ? null : $data['idcuentapago'],
                'mediopago' => $data['mediopago'] ?? null,
                'numerotransaccion' => empty($data['numerotransaccion']) ? null : $data['numerotransaccion'],
                'fechapago' => $data['fechapago'] ?? null,
                'amortizacion' => $data['amortizacion'] ?? null,
                'saldorestante' => $data['saldorestante'] ?? null,
                'comprobante' => null,
                'observacion' => empty($data['observacion']) ? null : $data['observacion'],
                'moneda' => $data['moneda'] ?? null,
                'montomonedaoriginal' => $data['montomonedaoriginal'] ?? null,
                'tipocambioaplicado' => empty($data['tipocambioaplicado']) ? null : $data['tipocambioaplicado']
            ];


            $errores[] = Validador::campoObligatorio($registro['idconcepto'], 'Concepto');
            $errores[] = Validador::campoObligatorio($registro['idvehiculo'], 'Identificador del vehículo');
            $errores[] = Validador::campoObligatorio($registro['mediopago'], 'Medio de pago');
            $errores[] = Validador::campoObligatorio($registro['fechapago'], 'Fecha de pago');
            $errores[] = Validador::campoObligatorio($registro['amortizacion'], 'Amortización');
            $errores[] = Validador::campoObligatorio($registro['saldorestante'], 'Saldo restante');


            if ($data['mediopago'] !== 'Efectivo') {

                if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
                    $errores[] = 'El archivo del comprobante es obligatorio para este medio de pago.';
                }
            }


            $errores = array_filter($errores);

            if (count($errores) > 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => implode('<br>', $errores)]);
                return;
            }

            // Subida de archivo
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
                $directorioDestino = __DIR__ . '/../../storage/comprobantes/';

                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0777, true);
                }

                $nombreArchivo = 'pagoInicial_' . uniqid() . '_' . basename($_FILES['comprobante']['name']);
                $rutaArchivoGuardado = $directorioDestino . $nombreArchivo;

                if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaArchivoGuardado)) {
                    throw new Exception('No se pudo guardar el archivo del comprobante.');
                }

                $registro['comprobante'] = 'comprobantes/' . $nombreArchivo;
            }

            // Insertar pago
            $idPago = $this->cotizacionModel->addPagoInicial($registro);

            if ($idPago > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Pago registrado correctamente',
                    'id' => $idPago
                ]);
            } else {

                throw new Exception('No se pudo registrar el pago de inicial.');
            }
        } catch (PDOException $e) {

            if ($rutaArchivoGuardado && file_exists($rutaArchivoGuardado))
                @unlink($rutaArchivoGuardado);

            if (strpos($e->getMessage(), 'El vehículo ya fue separado') !== false || strpos($e->getMessage(), 'vendido al contado') !== false) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error de base de datos: ' . $e->getMessage()
                ]);
            }
        } catch (Exception $e) {

            if ($rutaArchivoGuardado && file_exists($rutaArchivoGuardado))
                @unlink($rutaArchivoGuardado);

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Obtiene datos completos de una cotización para visualización
     * 
     * Endpoint AJAX que retorna información estructurada de cotización incluyendo
     * cliente, vehículo, precios con conversión de moneda, asesor, requisitos
     * dinámicos (gastos administrativos procesados), y todas las opciones de
     * financiamiento. Maneja fechas de reactivación y valores por defecto.
     *
     * Procesamiento especial:
     * - Requisitos: Reemplaza "gastos administrativos" con monto real
     * - Fechas: Prioriza fecha de reactivación sobre fecha de registro
     * - Precios: Formatea según moneda de cotización (PEN/USD)
     *
     * @param int $idcotizacion ID de la cotización
     * @return never
     */
    public function apiShow(int $idcotizacion): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $cot = $this->cotizacionModel->getById($idcotizacion);
        if (!$cot) {
            http_response_code(404);
            echo json_encode(['error' => 'Cotización no encontrada']);
            exit;
        }

        $idformato = isset($cot['idformato']) ? (int) $cot['idformato'] : null;
        $requisitos = $idformato ? $this->formatoModel->getDetalleRequisitos($idformato) : [];
        $financiamientos = $this->cotizacionModel->getFinanciamientos($idcotizacion);

        $fechaRegistro = $cot['fechaRegistro'] ?? null;
        $fechaReact = $cot['fechareactivacion'] ?? null;

        // usar la fecha de reactivación si existe; si no, la fecha de registro
        $fechaParaMostrar = $fechaReact ?: $fechaRegistro;

        // PROCESAR REQUISITOS DINÁMICOS
        $gastosAdmin = (float) ($cot['gastosadministrativos'] ?? 1500.00);
        $requisitosProcesados = [];

        foreach ($requisitos as $requisito) {
            $textoRequisito = $requisito['requisito'] ?? '';
            $textoOriginal = mb_strtolower($textoRequisito, 'UTF-8');

            // Detectar si es el requisito de gastos administrativos con múltiples variantes
            if (
                strpos($textoOriginal, 'gastos administrativos') !== false ||
                strpos($textoOriginal, 'pago único') !== false ||
                strpos($textoOriginal, 'pago unico') !== false ||
                (strpos($textoOriginal, 'gastos') !== false && strpos($textoOriginal, 'administrativos') !== false)
            ) {

                // Crear el texto dinámico con el monto real
                $montoProcesado = ($gastosAdmin > 0) ? $gastosAdmin : 1500.00;
                $requisitosProcesados[] = [
                    'idrequisito' => $requisito['idrequisito'] ?? null,
                    'requisito' => "Pago único por gastos administrativos S/ " . number_format($montoProcesado, 2, '.', ',')
                ];
            } else {
                // Mantener el requisito original
                $requisitosProcesados[] = $requisito;
            }
        }

        $vehColorRaw = trim((string) ($cot['vehiculo_color'] ?? ''));
        $vehColor = $vehColorRaw === '' ? 'Por definir' : $vehColorRaw;

        echo json_encode([
            'cotizacion' => [
                'id' => $idcotizacion,
                'idformato' => $idformato,
                'tipocotizacion' => $cot['tipocotizacion'] ?? null,
                'fecha' => $fechaParaMostrar,
                /* 'fecha' => $cot['fechaRegistro'] ?? null, */
                'moneda' => $cot['moneda'] ?? 'PEN', // Asegurar que siempre esté presente
                'cliente' => [
                    'nombre' => $cot['cliente_nombre'] ?? null,
                    'dni' => $cot['cliente_documento'] ?? null,
                    'celular' => $cot['cliente_telefono'] ?? null,
                ],
                'vehiculo' => [
                    'marca' => $cot['vehiculo_marca'] ?? null,
                    'modelo' => $cot['vehiculo_modelo'] ?? null,
                    'anio' => $cot['vehiculo_anio'] ?? null,
                    'color' => $vehColor,
                ],
                'precios' => [
                    // MEJORADO: Enviar precio según la moneda de la cotización
                    'precio_original' => number_format($cot['precioventa'] ?? 0, 2, '.', ''),
                    'precio_usd' => number_format($cot['precioventa'] ?? 0, 2, '.', ''),
                    'precio_pen' => number_format($cot['precioventa'] ?? 0, 2, '.', ''),
                    'inicial_soles' => number_format($cot['inicial'] ?? 0, 2, '.', ''),
                    // Determinar cuál precio mostrar según la moneda
                    'precio_mostrar' => number_format($cot['precioventa'] ?? 0, 2, '.', ''),
                    'moneda_precio' => $cot['moneda'] ?? 'PEN'
                ],
                'asesor' => [
                    'nombre' => $cot['asesor_nombre'] ?? 'CHARLY YACTAYO ORTIZ',
                    'nombre_completo' => $cot['asesor_nombre_completo'] ?? 'CHARLY YACTAYO ORTIZ',
                    'cargo' => $cot['asesor_cargo'] ?? 'Ejecutivo de Ventas',
                    'telefono' => $cot['asesor_telefono'] ?? '934 008 037',
                    'telefono_alt' => $cot['asesor_telefono_alt'] ?? null,
                    'usuario' => $cot['asesor_usuario'] ?? null
                ],
                'requisitos' => $requisitosProcesados,
                'opciones_financiamiento' => $financiamientos,
                'gastosadministrativos' => $gastosAdmin
            ]
        ]);
        exit;
    }

    /**
     * Muestra el formulario de creación de cotización
     * 
     * Renderiza la vista con formulario de nueva cotización, incluyendo
     * listados de formatos disponibles y vehículos en estado Libre o Proceso.
     *
     * @return void
     */
    public function create(): void
    {
        $this->authRequired();
        $formatos = $this->formatoModel->getAll();
        $vehiculos = $this->vehiculoModel->getAll(['Libre', 'Proceso']);
        $this->view('cotizacion.create', [
            'vehiculos' => $vehiculos,
            'formatos' => $formatos
        ]);
    }

    /**
     * API: Obtiene requisitos de un formato de cotización
     * 
     * Endpoint AJAX que retorna los requisitos asociados a un formato específico.
     *
     * @param int $idformato ID del formato de cotización
     * @return void
     */
    public function requisitos(int $idformato): void
    {
        $this->authRequired();

        $detalle = $this->formatoModel->getDetalleRequisitos($idformato);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($detalle);
        exit;
    }

    /**
     * API: Busca cliente por tipo y número de documento
     * 
     * Endpoint AJAX que busca clientes registrados por DNI (personas) o
     * RUC (empresas). Retorna datos completos del cliente si existe,
     * o indica que no fue encontrado para permitir registro.
     *
     * @return void
     */
    public function buscarCliente(): void
    {
        $this->authRequired();

        $tipo = strtolower($_GET['tipo'] ?? '');
        $doc = trim($_GET['doc'] ?? '');

        if (!$tipo || !$doc) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros']);
            exit;
        }

        // Llamamos al único método que trae idcliente + datos
        $data = $this->cotizacionModel->getClienteByDoc($tipo, $doc);

        header('Content-Type: application/json; charset=utf-8');
        /* echo json_encode($data ?: ['notFound' => true]);
        exit; */

        if ($data) {
            echo json_encode($data);
        } else {
            //Agregar parámetro para identificar origen
            echo json_encode([
                'notFound' => true,
                'documento' => $doc,
                'tipo' => $tipo
            ]);
        }
        exit;
    }

    /**
     * Registra una nueva cotización con múltiples opciones de financiamiento
     * 
     * Procesa formulario de cotización creando múltiples registros (uno por cada
     * opción de plazo). Valida datos, ordena opciones por número de cuotas,
     * y crea cotizaciones relacionadas con mismo cliente/vehículo/timestamp.
     * Requiere autenticación y asigna automáticamente el asesor desde sesión.
     *
     * @throws \Exception
     * @return void
     */
    public function store(): void
    {
        $this->authRequired();

        $idasesor = $_SESSION['user']['id'] ?? null;
        if (!$idasesor) {
            $_SESSION['error'] = "No se encontró al usuario.";
            header('Location: /cotizacion/create');
            exit;
        }

        // Opciones de financiamiento (las opciones que vienen del formulario)
        $opcionesJson = $_POST['opciones_financiamiento'] ?? '[]';
        $opciones = json_decode($opcionesJson, true);
        if (!is_array($opciones) || empty($opciones)) {
            $_SESSION['error'] = "Debe haber al menos una opción de financiamiento.";
            header('Location: /cotizacion/create');
            exit;
        }

        // Ordenar por número de cuotas
        usort($opciones, function ($a, $b) {
            return (int) ($a['numcuotas'] ?? 0) <=> (int) ($b['numcuotas'] ?? 0);
        });

        $idsCotizacionesCreadas = [];

        try {
            // CREAR UNA COTIZACIÓN POR CADA OPCIÓN
            foreach ($opciones as $opcion) {

                $numcuotas = (int) ($opcion['numcuotas'] ?? 0);
                $valorcuota = (float) ($opcion['valorcuota'] ?? 0.00);
                $inicial = (float) ($opcion['inicial'] ?? 0.00);
                $precioventa = (float) ($opcion['precioventa'] ?? ($_POST['precioventa'] ?? 0));
                $tasaanual = (float) ($opcion['tasaanual'] ?? 65.00);
                $tasamensual = (float) ($opcion['tasamensual'] ?? 0.00);

                if ($numcuotas <= 0)
                    continue;

                $input = [
                    'idformato' => $_POST['modalidad'] ?? null,
                    'idcliente' => $_POST['idcliente'] ?? null,
                    'idvehiculo' => $_POST['idvehiculo'] ?? null,
                    'moneda' => $_POST['moneda'] ?? 'PEN',
                    'precioventa' => $precioventa,
                    'vigenciadias' => $_POST['vigenciadias'] ?? 7,
                    'inicial' => $inicial,
                    'numcuotas' => $numcuotas,
                    'valorcuota' => $valorcuota,
                    'tasaanual' => $tasaanual,
                    'tasamensual' => $tasamensual,
                    'gastosadministrativos' => (float) ($_POST['gastosadministrativos'] ?? 0.00),
                    'idasesor' => $idasesor,
                ];

                if (!$input['idcliente'] || !$input['idvehiculo'] || !$input['idformato']) {
                    throw new Exception('Faltan datos obligatorios para crear la cotización.');
                }


                $idcot = $this->cotizacionModel->create($input);
                $idsCotizacionesCreadas[] = $idcot;

                $this->cotizacionModel->createFinanciamiento(
                    $idcot,
                    $numcuotas,
                    $inicial,
                    $valorcuota,
                    $_POST['moneda'] ?? 'PEN',
                    $precioventa
                );
            }
            $_SESSION['success_message'] = "Cotizaciones registradas correctamente. Total: " . count($idsCotizacionesCreadas);
            header('Location: /cotizacion');
            exit;
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error al registrar cotización: " . $e->getMessage();
            header('Location: /cotizacion/create');
            exit;
        }
    }

    /**
     * API: Obtiene el tipo de cambio actual
     * 
     * Endpoint AJAX que consulta API externa para obtener el tipo de cambio
     * USD/PEN actualizado. Utilizado en cálculos de cotización.
     *
     * @return void
     */
    public function tipoCambio(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/../Helpers/Api.php';
        $tc = obtenerTipoCambio();
        echo json_encode(['tipo_cambio' => $tc]);
        exit;
    }

    /**
     * API: Calcula el pago mensual de un financiamiento
     * 
     * Endpoint AJAX que aplica fórmula de anualidad para calcular cuota mensual
     * con tasa de interés compuesto. Acepta tasa anual como parámetro opcional.
     *
     * @param float $importeTotal Precio total del vehículo
     * @param float $inicial Monto del pago inicial
     * @param int $meses Plazo en meses
     * @return void
     */
    public function calcularPagoMensual(float $importeTotal, float $inicial, int $meses): void
    {
        header('Content-Type: application/json');
        $tasaPercent = isset($_GET['tasa']) ? floatval($_GET['tasa']) : 65.0; // en %
        $tasaAnual = max(0.0, $tasaPercent) / 100.0; // a decimal

        $pagoMensual = $this->cotizacionModel->calcularPagoMensual($importeTotal, $inicial, $meses, $tasaAnual);
        echo json_encode(["pago_mensual" => $pagoMensual]);
        exit();
    }

    /**
     * API: Genera cronograma completo de pagos
     * 
     * Endpoint AJAX que genera cronograma detallado mes a mes con distribución
     * de interés y capital, mostrando la amortización del préstamo.
     *
     * @param float $importeTotal Precio total del vehivulo
     * @param float $inicial Monto del pago inicial
     * @param int $meses Plazo en meses del financiamiento
     * @return void Respuesta JSON con array de pagos mensuales
     */
    public function generarCronograma(float $importeTotal, float $inicial, int $meses): void
    {
        header('Content-Type: application/json');
        $tasaPercent = isset($_GET['tasa']) ? floatval($_GET['tasa']) : 65.0; // en %
        $tasaAnual = max(0.0, $tasaPercent) / 100.0;

        $cronograma = $this->cotizacionModel->generarCronograma($importeTotal, $inicial, $meses, $tasaAnual);
        echo json_encode($cronograma);
        exit();
    }

    /**
     * API: Obtiene el último cliente registrado desde la sesión
     * 
     * Endpoint AJAX que recupera información del último cliente registrado
     * almacenado en sesión. Verifica que el registro sea reciente (menos de 30 minutos)
     * y limpia automáticamente registros obsoletos. Utilizado para auto-completar
     * formularios de cotización después de registrar un nuevo cliente.
     * 
     * @return never Respuesta JSON con datos del cliente o mensaje de error
     */
    public function ultimoClienteRegistrado(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $ultimoCliente = $_SESSION['ultimo_cliente_registrado'] ?? null;

        if ($ultimoCliente) {
            // Verificar si el registro es reciente (menos de 30 minutos)
            $tiempoTranscurrido = time() - $ultimoCliente['timestamp'];

            if ($tiempoTranscurrido < 1800) { // 30 minutos = 1800 segundos
                echo json_encode([
                    'success' => true,
                    'cliente' => $ultimoCliente
                ]);
            } else {
                // Si es muy antiguo, eliminar de sesión
                unset($_SESSION['ultimo_cliente_registrado']);
                echo json_encode([
                    'success' => false,
                    'message' => 'No hay cliente reciente'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No hay cliente reciente'
            ]);
        }
        exit;
    }

    /**
     * API: Limpia el último cliente registrado de la sesión
     * 
     * Endpoint AJAX que elimina de la sesión la información del último cliente
     * registrado. Útil para limpiar el estado después de completar una cotización
     * o cuando el usuario decide no usar los datos precargados.
     * 
     * @return never Respuesta JSON confirmando la limpieza
     */
    public function limpiarUltimoCliente(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (isset($_SESSION['ultimo_cliente_registrado'])) {
            unset($_SESSION['ultimo_cliente_registrado']);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Sesión limpiada correctamente'
        ]);
        exit;
    }

    /**
     * Muestra el historial de cotizaciones vencidas
     * 
     * Renderiza la vista de historial con cotizaciones en estado Vencido ('V').
     * Aplica control de acceso por rol:
     * - Supervisores/Jefes: Ven todas las cotizaciones vencidas con información del asesor
     * - Asesores: Solo ven sus propias cotizaciones vencidas
     * 
     * Permite identificar cotizaciones que requieren reactivación o seguimiento.
     * Cargos supervisores: 1, 8, 10, 13, 14, 16, 17
     * 
     * @return void 
     * @throws Exception Si no se puede identificar al usuario
     */
    public function historial(): void
    {
        $this->authRequired();

        // Obtener información del usuario logueado
        $idasesor = $_SESSION['user']['id'] ?? null;
        $idcargo = $_SESSION['user']['idcargo'] ?? null;

        if (!$idasesor || !$idcargo) {
            $_SESSION['error_message'] = "No se pudo identificar al usuario.";
            header('Location: /login');
            exit;
        }

        // Definir cargos que pueden ver todas las cotizaciones (supervisores/jefes)
        $cargosSupervisores = [
            1,  // Jefe de sistemas
            8,  // Jefe de Logística
            10, // Jefe de Recursos Humanos
            13, // Jefe de Contabilidad
            14, // Jefe de Marketing
            16, // Jefe de Ventas
            17  // Jefe de Caja
        ];

        // Verificar si el usuario puede ver todas las cotizaciones o solo las suyas
        $puedeVerTodas = in_array($idcargo, $cargosSupervisores);

        if ($puedeVerTodas) {
            // Supervisores/Jefes ven todas las cotizaciones vencidas con información del asesor
            $cotizaciones = $this->cotizacionModel->getAllVencidas();

            // Log para debugging (opcional)
            error_log('USUARIO SUPERVISOR - Historial: Puede ver todas las cotizaciones vencidas');
            error_log('COTIZACIONES VENCIDAS CARGADAS: ' . count($cotizaciones));
        } else {
            // Asesores y otros cargos ven solo sus cotizaciones vencidas
            $cotizaciones = $this->cotizacionModel->getAllVencidasByAsesor($idasesor);

            // Log para debugging (opcional)
            error_log('USUARIO ASESOR - Historial: Solo ve sus cotizaciones vencidas');
            error_log('COTIZACIONES VENCIDAS DEL ASESOR ' . $idasesor . ': ' . count($cotizaciones));
        }

        $this->view("cotizacion.historial", [
            'cotizaciones' => $cotizaciones,
            'puede_ver_todas' => $puedeVerTodas,
            'usuario_actual' => [
                'id' => $idasesor,
                'cargo' => $idcargo,
                'es_supervisor' => $puedeVerTodas
            ]
        ]);
    }

    /**
     * Reactiva una cotización vencida
     * 
     * Endpoint AJAX que reactiva una cotización vencida cambiando su estado a Pendiente
     * y actualizando su fecha de vigencia. Aplica control de acceso: supervisores
     * pueden reactivar cualquier cotización, asesores solo las propias.
     * 
     * Proceso de reactivación:
     * - Cambia estado de 'V' (Vencida) a 'P' (Pendiente)
     * - Registra fecha de reactivación
     * - Extiende vigencia por días configurados (default: 7 días)
     * - Recupera opciones de financiamiento actualizadas
     *
     * @param int $idcotizacion ID de la cotización a reactivar
     * @return never Respuesta JSON con resultado de la operación
     */
    public function reactivar(int $idcotizacion): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $this->authRequired();

        $idasesor = $_SESSION['user']['id'] ?? null;
        $idcargo = $_SESSION['user']['idcargo'] ?? null;

        $cot = $this->cotizacionModel->getById($idcotizacion);
        if (!$cot) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Cotización no encontrada.']);
            exit;
        }

        $cargosSupervisores = [1, 8, 10, 13, 14, 16, 17];

        if (!in_array($idcargo, $cargosSupervisores) && (int) ($cot['idasesor'] ?? 0) !== (int) $idasesor) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para reactivar esta cotización.']);
            exit;
        }

        $vig = 7;
        try {
            $ok = $this->cotizacionModel->reactivar($idcotizacion, $vig);

            // RE-RECUPERAR la cotización actualizada (getById ya agrega opciones_financiamiento)
            $updated = $this->cotizacionModel->getById($idcotizacion);

            // Asegurarnos también de obtener explícitamente los financiamientos
            $financiamientos = $this->cotizacionModel->getFinanciamientos($idcotizacion) ?: [];

            $fecha_react_raw = $updated['fechareactivacion'] ?? null;
            $fecha_venc = $fecha_react_raw ? (new \DateTime($fecha_react_raw))->modify("+{$vig} days")->format('d/m/Y') : date('d/m/Y', strtotime("+{$vig} days"));

            echo json_encode([
                'success' => true,
                'message' => $ok ? 'Cotización reactivada correctamente.' : 'No se realizaron cambios (ya estaba con esos valores).',
                'fechareactivacion' => $fecha_react_raw,
                'fecha_vencimiento' => $fecha_venc,
                'cotizacion' => $updated,
                'financiamientos' => $financiamientos
            ]);
            exit;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al reactivar: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * API: Obtiene datos para acta de separación vehicular
     * 
     * Endpoint AJAX que recupera toda la información necesaria para generar
     * el acta de separación de un vehículo asociado a una cotización.
     * Incluye datos del cliente, vehículo, financiamiento y pagos realizados.
     * 
     * Utilizado para documentos legales de separación de vehículo previo
     * a la firma del contrato de compra-venta.
     *
     * @param int $idcotizacion ID de la cotización
     * @return void Respuesta JSON con datos del acta o error
     */
    public function getDataSeparacionVehicular(int $idcotizacion): void
    {

        header('Content-Type: application/json; charset=utf-8');


        $data = $this->cotizacionModel->getDataActaSeparacionByIdCotizacion($idcotizacion);
        if ($data === false) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Ocurrió un error al consultar la base de datos.'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * API: Obtiene historial de pagos del inicial de un cliente
     * 
     * Endpoint AJAX que recupera el historial completo de pagos realizados
     * por el cliente para cubrir el inicial de una cotización específica.
     * Incluye información de cada transacción: fecha, monto, medio de pago,
     * comprobante y observaciones.
     * 
     * verificar el estado de completitud del inicial y generar
     * reportes de pagos del cliente.
     *
     * @param int $idcotizacion ID de la cotización
     * @return void Respuesta JSON con array de pagos o error
     */
    public function getPagosCliente(int $idcotizacion): void
    {

        header('Content-Type: application/json; charset=utf-8');
        $pagos = $this->cotizacionModel->getHistorialPagosInicial($idcotizacion);

        if ($pagos === false) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Ocurrió un error al consultar la base de datos'
            ]);
        }
        echo json_encode([
            'success' => true,
            'data' => $pagos
        ]);
    }

}
