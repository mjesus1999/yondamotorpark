<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\OrdenCompra;
use App\Models\PagosOC;
use App\Models\EntidadPago;

class OrdenCompraController extends Controller
{
    private OrdenCompra $ordenCompraModel;
    private PagosOC $pagosModel;
    private EntidadPago $entidadesPagoModel;
    public function __construct()
    {
        $this->ordenCompraModel = new OrdenCompra();
        $this->pagosModel =  new PagosOC();
        $this->entidadesPagoModel = new EntidadPago();
    }

    // Me enlistara todas las ordenes de compras, dependiendo de su estado:
    public function index(string $estado = 'emitido'): void
    {
        $this->authRequired();
        $ordenCompras = $this->ordenCompraModel->getByEstado($estado);
        $this->view('oc.index', ['ordenCompras' => $ordenCompras, 'estado' => $estado]);
    }


    // METODO QUE LLEVARA A LA VISTA PARA REGISTRAR LOS PAGOS
    public function indexPagos($idorden): void
    {
        $idorden = (int)$idorden;


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

    // METODO QUE ME LLEVARA A LS VISTA DE RPEORTES POR CONCESIONARIO

    public function indexReporteByConcesionario(): void
    {
        $this->authRequired();
        $this->view('oc.reporteBy-concesionario');
    }





    // Me llevará a la vista de crear
    public function create(): void
    {
        $this->view('oc.create');
    }

    public function store(): int
    {
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

    // METODO PARA CAMBIAR EL ESTADO  EN LA TABAL OC  = 'PROCESO,ANULADO'
    public function setEstado($estado, $idOC): void
    {
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

    // ACTUALIZA SI ES CORRECTO 
    public function update($idOC): int
    {
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


    // API PARA TRAER EL DETALLE DE UNA OC OR SU ID:

    public function searchtDetOCByIdOc($idOC): void
    {
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



    // API PARA TRAER LOS DATOS DEL AUTO A ACTULIZAR EN DETALLE_OC SI LLEGO CORRECTO

    public function searchInfoAutos($idOC)
    {
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

    public function getReporteOCProceso(): void
    {
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
}
