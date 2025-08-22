<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagosOC;

class PagosOCController extends Controller
{
    private PagosOC $pagoOCModel;

    public function __construct()
    {
        $this->pagoOCModel = new PagosOC();
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido', 'id' => 0]);
            exit;
        }


        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idorden'      => (int)($data['idorden'] ?? 0),
            'idlogistica'  => 2,
            'amortizacion' => (float)($data['amortizacion'] ?? 0),
            'comprobante'  => '',
            'fecharealpago' => $data['fecharealpago']
        ];


        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['idorden'], 'Orden');
        $errores[] = Validador::campoObligatorio($registro['amortizacion'], 'Monto a pagar');
        $errores[] = Validador::campoObligatorio($registro['fecharealpago'], 'Fecha de pago');
        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id'      => 0
            ]);
            exit;
        }


        if (!empty($_FILES['comprobante']['name'])) {
            $nombreArchivo = uniqid('comprobante_') . '_' . basename($_FILES['comprobante']['name']);


            $directorioDestino = __DIR__ . '/../../storage/comprobantes/';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            $rutaCompleta = $directorioDestino . $nombreArchivo;

            if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaCompleta)) {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar el comprobante', 'id' => 0]);
                exit;
            }

            $registro['comprobante'] = "comprobantes/{$nombreArchivo}";
        }


        $idPago = $this->pagoOCModel->create($registro);

        echo json_encode([
            'success' => $idPago > 0,
            'message' => $idPago > 0 ? '¡Pago registrado correctamente!' : 'No se pudo registrar el pago',
            'id'      => $idPago
        ]);
    }
}
