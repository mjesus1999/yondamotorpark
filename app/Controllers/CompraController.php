<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Compra;


class CompraController extends Controller
{
    private Compra $compraModel;

    public function __construct()
    {
        $this->compraModel = new Compra();
    }

    public function index(): void
    {
        $this->view('compras.index');
    }

    public function create(): void
    {
        $this->view('compras.create');
    }









    // APIS
    public function searchDetOCByConcesionario($id)
    {
        header('Content-Type: application/json');
        $datos = $this->compraModel->getDetOCByConcesionario($id);

        if ($datos) {
            // Reorganizar por orden de compra
            $resultado = [];

            foreach ($datos as $fila) {
                $idOrden = $fila['idordencompra'];

                if (!isset($resultado[$idOrden])) {
                    $resultado[$idOrden] = [
                        'idordencompra' => $idOrden,
                        'emision' => $fila['emision'],
                        'detalle' => []
                    ];
                }

                // Quitar campos duplicados que ya están al nivel superior
                unset($fila['idordencompra'], $fila['emision']);

                $resultado[$idOrden]['detalle'][] = $fila;
            }

            // Convertir de asociativo a numérico
            echo json_encode(array_values($resultado));
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}
