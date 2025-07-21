<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\OrdenCompra;

class OrdenCompraController extends Controller
{
    private OrdenCompra $ordenCompraModel;

    public function __construct()
    {
        $this->ordenCompraModel = new OrdenCompra();
    }

    // Me enlistara todas las ordenes de compras: 
    public function index(): void
    {
        $ordenCompras = $this->ordenCompraModel->getAll();
        $this->view('oc.index', ['ordenCompras' => $ordenCompras]);
    }

    /*public function indexReport($id): void {
        $ocDetalles = $this->ordenCompraModel->getDetOCByIdOC($id);
        $this->view('pdf/oc.reporte', ['ocDetalles' => $ocDetalles]);
    }*/

    

    public function html2pdfReport($id): void {
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
            'idlogistica' => 2, // Asignando un valor fijo de 2 para idlogistica
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
}
