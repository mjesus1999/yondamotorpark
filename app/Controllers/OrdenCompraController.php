<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\OrdenCompra;
use App\Models\PagosOC;

class OrdenCompraController extends Controller
{
    private OrdenCompra $ordenCompraModel;

    public function __construct()
    {
        $this->ordenCompraModel = new OrdenCompra();
    }

    // Me enlistara todas las ordenes de compras, dependiendo de su estado:
    public function index(string $estado = 'emitido'): void
    {
        $ordenCompraModel = new OrdenCompra();
        $ordenCompras = $ordenCompraModel->getByEstado($estado);
        $this->view('oc.index', ['ordenCompras' => $ordenCompras, 'estado' => $estado]);
    }
    
    
    // METODO QUE LLEVARA A LA VISTA PARA REGISTRAR LOS PAGOS
    public function indexPagos($idorden): void
    {
        $idorden = (int)$idorden;
    
        $pagosModel = new PagosOC();
        $pagos = $pagosModel->listarPagosByOC($idorden);
        $saldoRestante = $pagosModel->obtenerSaldoRestante($idorden);
        $ordenCompra = $this->ordenCompraModel->obtenerConcesionarioById($idorden);
        $infoAutos = $this->ordenCompraModel->getInfoAutosOC($idorden);
    
        $this->view('oc.pagos', [
            'pagos' => $pagos,
            'saldoRestante' => $saldoRestante,
            'ordenCompra' => $ordenCompra,
            'autos' => $infoAutos
        ]);
    }
    
    public function html2pdfReport($id): void
    {
        // Solo necesitamos pasar el ID, los datos se cargarán via JavaScript
        // El PDF se generará automáticamente sin mostrar la vista
        $this->view('pdf/oc/oc-html2pdf', ['id' => $id]);
    }

    // Me llevará a la voista de crear
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
            'idlogistica' => 2,  // Asignando un valor fijo de 2 para idlogistica
            'moneda' => $data['moneda'] ?? '',
            'serie' => $data['serie'] ?? '',
            'numstock' => $data['numstock'] ?? '',
            'observaciones' => $data['observaciones'] ?? ''
        ];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['idtienda'], 'Tienda');
        $errores[] = Validador::campoObligatorio($registro['idlogistica'], 'Logística');
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

    // API PARA TRAER EL DETALLE DE UNA PC OR SU ID:

    public function searchtDetOCByIdOc($idOC): void
    {
        header('Content-Type: application/json');
        $ocDet = $this->ordenCompraModel->getDetOCByIdOC($idOC);

        if ($ocDet) {
            echo json_encode($ocDet);
        } else {
            echo json_encode([]);
        }
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
}
