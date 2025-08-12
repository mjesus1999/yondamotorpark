<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagoCronograma;

class PagoCronogramaController extends Controller
{

    private PagoCronograma $pagoCronogramaModel;

    public function __construct()
    {
        $this->pagoCronogramaModel = new PagoCronograma();
    }



    public function indexHistorialPagos(int $id): void
    {

        $datos = $this->pagoCronogramaModel->getHistorialPagosByContrato($id);
        $this->view('caja.historial', ['pagos' => $datos]);
    }



    public function store(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idcronograma'       => (int)($data['idcronograma'] ?? 0),
            'idcuentapago'       => $data['idcuentapago'] ?? null,
            'idcolcaja'          => 2,
            'mediopago'          => $data['mediopago'] ?? '',
            'numerotransaccion'  => $data['numerotransaccion'] ?? '',
            'fechapago'          => $data['fechapago'] ?? '',
            'amortizacion'       => (float)($data['amortizacion'] ?? 0),
            'comprobante'        => '',
            'observacion'        => $data['observacion'] ?? ''
        ];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['idcronograma'], 'Cuota a pagar');
        $errores[] = Validador::campoObligatorio($registro['mediopago'], 'Método de pago');
        $errores[] = Validador::campoObligatorio($registro['amortizacion'], 'Monto de amortización');
        $errores[] = Validador::campoObligatorio($registro['fechapago'], 'Fecha de pago');

        if ($registro['mediopago'] === 'Transferencia Bancaria') {
            $errores[] = Validador::campoObligatorio($registro['idcuentapago'], 'Número de cuenta');
        }

        $errores = array_filter($errores);
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'message' => implode('<br>', $errores), 'id' => 0]);
            return;
        }

        if (!empty($_FILES['comprobante']['name'])) {
            $nombreArchivo = uniqid('comprobante_') . '_' . basename($_FILES['comprobante']['name']);
            $directorioDestino = 'storage/comprobantes/';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            $rutaCompleta = $directorioDestino . $nombreArchivo;

            if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaCompleta)) {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar el comprobante', 'id' => 0]);
                return;
            }

            $registro['comprobante'] = '/' . $rutaCompleta;
        }

        $registro['idcuentapago'] = empty($registro['idcuentapago']) ? null : (int)$registro['idcuentapago'];

        $idPago = $this->pagoCronogramaModel->add($registro);

        echo json_encode([
            'success' => $idPago > 0,
            'message' => $idPago > 0 ? '¡Pago registrado correctamente!' : 'No se pudo registrar el pago',
            'id'      => $idPago
        ]);
    }







    // TRAER LOS NUMEROS DE CUENTAS

    public function searchNumCuentasPagos(): void
    {
        header('Content-Type: application/json');
        $numCuentas = $this->pagoCronogramaModel->getNumCuentasPagos();


        if ($numCuentas) {
            echo json_encode($numCuentas);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}
