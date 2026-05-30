<?php

/**
 * Controlador de Orden de Compra
 * 
 * app/Controllers/OrdenCompraController.php
 * 
 * Gestiona todas las operaciones relacionadas con órdenes de compra de vehículos,
 * incluyendo creación, seguimiento de estados, gestión de pagos, recepción de
 * mercadería y generación de reportes. Integra funcionalidades de múltiples
 * modelos para un flujo completo de compras.
 * 
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\OrdenCompra;
use App\Models\PagosOC;
use App\Models\EntidadPago;

/**
 * Clase OrdenCompraController
 * 
 * Controlador para la gestión integral de órdenes de compra.
 * Proporciona funcionalidades para CRUD, control de estados, gestión de pagos,
 * verificación de recepción y reportes ejecutivos. Coordina operaciones entre
 * múltiples modelos (OrdenCompra, PagosOC, EntidadPago).
 */
class OrdenCompraController extends Controller
{
    /**
     * Modelo de Orden de compra
     * @var OrdenCompra
     */
    private OrdenCompra $ordenCompraModel;

    /**
     * Modelo de Pagos de Orden de Compra
     * @var PagosOC
     */
    private PagosOC $pagosModel;

    /**
     * Modelo de entidad de Pago
     * @var EntidadPago
     */
    private EntidadPago $entidadesPagoModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa los modelos de OrdenCompra, PagosOC y EntidadPago
     * necesarios para las operaciones del controlador.
     */
    public function __construct()
    {
        $this->ordenCompraModel = new OrdenCompra();
        $this->pagosModel = new PagosOC();
        $this->entidadesPagoModel = new EntidadPago();
    }

    /**
     * Muestra el listado de órdenes de compra por estado
     * 
     * Renderiza la vista principal con las órdenes de compra filtradas
     * por su estado actual. Requiere autenticación.
     * 
     * Estados posibles: 'emitido', 'proceso', 'recepcionado', 'anulado'
     * 
     * @param string $estado Estado de las órdenes a mostrar (por defecto: 'emitido')
     * @return void
     */
    public function index(string $estado = 'emitido'): void
    {
        $this->authRequired();
        $ordenCompras = $this->ordenCompraModel->getByEstado($estado);
        $this->view('oc.index', ['ordenCompras' => $ordenCompras, 'estado' => $estado]);
    }

    /**
     * Muestra la vista de gestión de pagos de una orden de compra
     * 
     * Renderiza la vista para registrar y visualizar pagos de una OC específica.
     * Incluye historial de pagos, saldo restante, información del concesionario,
     * estado de vehículos y entidades de pago disponibles.
     * 
     * @param int $idorden ID de la orden de compra
     * @return void
     */
    public function indexPagos($idorden): void
    {
        $this->authRequired();
        $idorden = (int) $idorden;


        $result = $this->pagosModel->listarPagosByOC($idorden);
        $saldoRestante = $this->pagosModel->obtenerSaldoRestante($idorden);
        $concesionario = $this->ordenCompraModel->obtenerConcesionarioById($idorden);
        $pagos = $result['pagos'];
        $totalAmortizado = $result['totalAmortizado'];
        $infoAutos = $this->ordenCompraModel->getInfoAutosOC($idorden);
        $entidadesPago = $this->entidadesPagoModel->getAllEntidadesPago();
        error_log('SALDO RESTANTE: ' . print_r($saldoRestante, true));
        $this->view('oc.pagos', [
            'pagos' => $pagos,
            'totalAmortizado' => $totalAmortizado,
            'saldoRestante' => $saldoRestante,
            'concesionario' => $concesionario,
            'autos' => $infoAutos,
            'entidadesPago' => $entidadesPago
        ]);
    }

    /**
     * Muestra la vista de reportes por concesionario
     * 
     * Renderiza la vista para generar y visualizar reportes de órdenes
     * de compra agrupados por concesionario. Requiere autenticación.
     * 
     * @return void
     */
    public function indexReporteByConcesionario(): void
    {
        $this->authRequired();
        $this->view('oc.reporteBy-concesionario');
    }

    /**
     * Muestra el formulario de creación de orden de compra
     * 
     * Renderiza la vista con el formulario para crear una nueva orden
     * de compra. Requiere autenticación.
     * 
     * @return void
     */
    public function create(): void
    {
        $this->authRequired();
        $this->view('oc.create');
    }

    /**
     * Registra una nueva orden de compra
     * 
     * Endpoint AJAX que procesa el formulario de creación de OC.
     * Valida campos obligatorios y crea la orden en la base de datos.
     * Solo acepta peticiones POST y requiere autenticación.
     * 
     * Validaciones realizadas:
     * - Campos obligatorios: tienda, moneda, serie
     * 
     * @return int ID de la orden creada si exitoso, 0 en caso de error
     */
    public function store(): int
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');
        $data = array_map([Validador::class, 'limpiar'], $_POST);
        $registro = [
            'idtienda' => $data['idtienda'] ?? '',
            // 'idlogistica' => 2,  
            'moneda' => $data['moneda'] ?? '',
            'serie' => $data['serie'] ?? '',
            'numstock' => $data['numstock'] ?? '',
            'observaciones' => $data['observaciones'] ?? ''
        ];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['idtienda'], 'Tienda');
        $errores[] = Validador::campoObligatorio($registro['moneda'], 'Moneda');
        $errores[] = Validador::campoObligatorio($registro['serie'], 'Serie');
        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        $idOrdenCompra = $this->ordenCompraModel->create($registro);

        if ($idOrdenCompra > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Orden de compra creada exitosamente!',
                'id' => $idOrdenCompra
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear la orden de compra',
                'id' => 0
            ]);
            exit;
        }
    }

    /**
     * Actualiza el estado de una orden de compra
     * 
     * Endpoint AJAX que cambia el estado de una OC (proceso, anulado, etc.).
     * Si el estado es 'anulado', ejecuta lógica especial de anulación.
     * Solo acepta peticiones POST y requiere autenticación.
     * 
     * @param string $estado Nuevo estado de la orden
     * @param int $idOC ID de la orden de compra
     * @return never
     */
    public function setEstado($estado, $idOC): void
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'estado' => $estado,  // Viene desde la URL (proceso, anulado, etc.)
            'observaciones' => $data['observaciones'] ?? '',
            'idordencompra' => $idOC
        ];

        $rowAffects = $this->ordenCompraModel->updateEstado($registro);

        echo json_encode([
            'success' => $rowAffects > 0,
            'message' => $rowAffects > 0
                ? "¡Se actualizó la OC a {$estado}!"
                : "¡No se ha podido actualizar la OC a {$estado}!"
        ]);
        exit();
    }

    /**
     * Actualiza el estado de verificación del detalle de OC
     * 
     * Endpoint AJAX que marca si los vehículos de una orden fueron
     * recepcionados correctamente (campo 'escorrecto': S/N).
     * Solo acepta peticiones POST y requiere autenticación.
     * 
     * Validaciones realizadas:
     * - Campo obligatorio: escorrecto
     * 
     * @param int $idOC ID de la orden de compra
     * @return int Número de filas afectadas
     */
    public function update($idOC): int
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'escorrecto' => $data['escorrecto'],
            'idordencompra' => $idOC
        ];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['escorrecto'], '¿Es correcto?');
        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
            ]);
            exit();
        }

        $rowAffects = $this->ordenCompraModel->updateEscorrectoDetOC($registro);

        if ($rowAffects > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Detalle actualizado!',
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => '¡No se ha podido actualizar el detalle!',
            ]);
            exit();
        }
    }

    /**
     * API: Obtiene el detalle completo de una orden de compra
     * 
     * Endpoint AJAX que retorna el detalle de una OC en formato JSON estructurado,
     * separando información de la orden, concesionario, totales y vehículos.
     * Útil para visualización detallada o impresión de OC.
     * 
     * @param int $idOC ID de la orden de compra
     * @return void
     */
    public function searchtDetOCByIdOc($idOC): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $ocDet = $this->ordenCompraModel->getDetOCByIdOC($idOC);

        if (!$ocDet || count($ocDet) === 0) {
            echo json_encode([]);
            exit();
        }

        // Tomar los datos generales de la primera fila
        $first = $ocDet[0];

        $orden = [
            "numero_oc_interno" => $first["numero_oc_interno"],
            "anio_oc" => $first["anio_oc"],
            "numero_oc_formateado" => $first["numero_oc_formateado"],
            "fecha_emision_oc" => $first["fecha_emision_oc"],
            "observaciones_oc" => $first["observaciones_oc"],
            "moneda_oc" => $first["moneda_oc"],
            "concesionario" => [
                "razon_social" => $first["concesionario_razon_social"],
                "ruc" => $first["concesionario_ruc"],
                "direccion" => $first["concesionario_direccion"],
                "telefono" => $first["concesionario_telefono"],
                "ubigeo" => $first["concesionario_ubigeo_completo"],
                "vendedor_contacto" => $first["concesionario_vendedor_contacto"],
            ],
            "totales" => [
                "valor_venta" => $first["total_valor_venta_orden"],
                "igv" => $first["total_igv_orden"],
                "total" => $first["total_general_orden"]
            ]
        ];

        // Mapear los vehículos
        $vehiculos = array_map(function ($item) {
            return [
                "id" => $item["id_detalle_orden"],
                "marca" => $item["vehiculo_marca"],
                "modelo" => $item["vehiculo_modelo"],
                "version" => $item["vehiculo_version"],
                "combustible" => $item["vehiculo_combustible"],
                "anio_modelo" => $item["vehiculo_anio_modelo"],
                "placa" => $item["vehiculo_placa"],
                "placa_rotativa" => $item["vehiculo_placa_rotativa"],
                "chasis" => $item["vehiculo_chasis"],
                "serie_motor" => $item["vehiculo_serie_motor"],
                "color" => $item["vehiculo_color"],
                "precio_unitario" => $item["vehiculo_precio_unitario"],
                "valor_venta_unitario" => $item["valor_venta_unitario"],
                "igv_unitario" => $item["igv_unitario"],
                "total_unitario" => $item["total_unitario"]
            ];
        }, $ocDet);

        echo json_encode([
            "orden" => $orden,
            "vehiculos" => $vehiculos
        ]);
        exit();
    }

    /**
     * API: Obtiene información de vehículos para verificación
     * 
     * Endpoint AJAX que retorna los datos de los vehículos de una OC
     * con su estado de verificación (escorrecto) para el proceso de
     * recepción de mercadería.
     * 
     * @param int $idOC ID de la orden de compra
     * @return never
     */
    public function searchInfoAutos($idOC)
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $infoAuto = $this->ordenCompraModel->getInfoAutosOC($idOC);

        if ($infoAuto) {
            echo json_encode($infoAuto);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }

    /**
     * API: Genera reporte ejecutivo de órdenes en proceso
     * 
     * Endpoint AJAX que retorna un reporte completo con dos secciones:
     * resumen ejecutivo (totales, promedios) y detalle de órdenes en proceso.
     * Útil para dashboards y análisis gerencial.
     * 
     * @return void
     */
    public function getReporteOCProceso(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        try {
            $data = $this->ordenCompraModel->getReporteOCProceso();
            $resumenEjecutivo = $data['resumenEjecutivo'];
            $detalles = $data['detalleOrdenes'];
            echo json_encode(['success' => true, 'resumenEjecutivo' => $resumenEjecutivo, 'detallesOrdenes' => $detalles]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }


    public function getReporteGeneral(): void
    {
        header('Content-Type: application/json');
        try {
            
            $data = $this->ordenCompraModel->getReporteGeneral();

            $dataAgrupada = [
                'emitido'  => [],
                'proceso'  => [],
                'pagado'   => [],
                'anulado'  => []
            ];

    
            foreach ($data as $orden) {
                $estado = $orden['estado']; 

               
                if (isset($dataAgrupada[$estado])) {
                    $dataAgrupada[$estado][] = $orden;
                }
            }

            
            echo json_encode(['success' => true, 'data' => $dataAgrupada]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Compatibilidad con enlace /oc/reporte/{id}: el PDF se genera en el listado OC (pdfMake).
     */
    public function html2pdfReport($idOC): void
    {
        $this->authRequired();
        $_SESSION['info_message'] = 'Use el botón PDF en la lista de órdenes de compra para generar el documento.';
        $this->redirect('/oc');
    }
}
