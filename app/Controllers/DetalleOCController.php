<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\DetalleOC;

class DetalleOCController extends Controller
{
    private DetalleOC $detalleOCModel;

    public function __construct()
    {
        $this->detalleOCModel = new DetalleOC();
    }



    public function store():int {

        $this->authRequired();
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class,'limpiar'], $_POST);

        $registro = [
            'idordencompra' => $data['idordencompra'],
            'idvehiculo' => $data['idvehiculo'],
            'preciocompra' => $data['preciocompra']
        ];

        $idDetalleOC = $this->detalleOCModel->create($registro);

        if ($idDetalleOC > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Se ha creado la orden de compra con sus detalles!',
                'id' => $idDetalleOC
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo crear la orden de compra',
                'id' => $idDetalleOC
            ]);
            exit;
        }

    }


}