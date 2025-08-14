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


    private function guardarComprobante(array $archivo): ?string
    {
        $nombreArchivo = uniqid('comprobante_') . '_' . basename($archivo['name']);
        $directorioDestino = 'storage/comprobantes/';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return '/' . $rutaCompleta;
        }

        return null;
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

        $idCronograma = (int)($data['idcronograma'] ?? 0);
        $amortizacionCuota = (float)($data['amortizacionCuota'] ?? 0);
        $amortizacionPenalidad = (float)($data['amortizacionPenalidad'] ?? 0);
        $medioPago = $data['mediopago'] ?? '';
        $idCuentaPago = $data['idcuentapago'] ?? null;
        $fechaPago = $data['fechapago'] ?? '';

        // Validaciones iniciales
        if ($idCronograma <= 0) {
            echo json_encode(['success' => false, 'message' => 'Cuota a pagar no es válida.']);
            return;
        }
        if (empty($medioPago)) {
            echo json_encode(['success' => false, 'message' => 'Método de pago es obligatorio.']);
            return;
        }
        if ($medioPago === 'Transferencia Bancaria' && empty($idCuentaPago)) {
            echo json_encode(['success' => false, 'message' => 'Debes seleccionar una cuenta bancaria.']);
            return;
        }
        if ($amortizacionCuota <= 0 && $amortizacionPenalidad <= 0) {
            echo json_encode(['success' => false, 'message' => 'Debes ingresar un monto válido (cuota o penalidad).']);
            return;
        }

        $cronogramaData = $this->pagoCronogramaModel->getCronogramaData($idCronograma);
        if (!$cronogramaData) {
            echo json_encode(['success' => false, 'message' => 'No se encontró la cuota.']);
            return;
        }

        if ($amortizacionCuota > $cronogramaData['valorcuota']) {
            echo json_encode(['success' => false, 'message' => "La cuota no puede exceder S/ {$cronogramaData['valorcuota']}"]);
            return;
        }

        if ($amortizacionPenalidad > $cronogramaData['penalidad']) {
            echo json_encode(['success' => false, 'message' => "La penalidad no puede exceder S/ {$cronogramaData['penalidad']}"]);
            return;
        }

        // Cargar comprobante para cuota
        $rutaComprobanteCuota = null;
        if ($amortizacionCuota > 0 && isset($_FILES['comprobanteCuota']) && !empty($_FILES['comprobanteCuota']['name'])) {
            $rutaComprobanteCuota = $this->guardarComprobante($_FILES['comprobanteCuota']);
            if (!$rutaComprobanteCuota) {
                echo json_encode(['success' => false, 'message' => 'Error al guardar comprobante de cuota.']);
                return;
            }
        }

        // Cargar comprobante para penalidad
        $rutaComprobantePenalidad = null;
        if ($amortizacionPenalidad > 0 && isset($_FILES['comprobantePenalidad']) && !empty($_FILES['comprobantePenalidad']['name'])) {
            $rutaComprobantePenalidad = $this->guardarComprobante($_FILES['comprobantePenalidad']);
            if (!$rutaComprobantePenalidad) {
                echo json_encode(['success' => false, 'message' => 'Error al guardar comprobante de penalidad.']);
                return;
            }
        }

        // Armar arrays de pago
        $pagoCuota = null;
        if ($amortizacionCuota > 0) {
            $pagoCuota = [
                'idcronograma' => $idCronograma,
                'idcuentapago' => empty($idCuentaPago) ? null : (int)$idCuentaPago,
                'idcolcaja' => 2,
                'mediopago' => $medioPago,
                'numerotransaccion' => $data['numerotransaccion'] ?? null,
                'fechapago' => $fechaPago,
                'amortizacion' => $amortizacionCuota,
                'comprobante' => $rutaComprobanteCuota,
                'observacion' => $data['observacion'] ?? null,
                'tipo' => 'Cuota'
            ];
        }

        $pagoPenalidad = null;
        if ($amortizacionPenalidad > 0) {
            $pagoPenalidad = [
                'idcronograma' => $idCronograma,
                'idcuentapago' => empty($idCuentaPago) ? null : (int)$idCuentaPago,
                'idcolcaja' => 2,
                'mediopago' => $medioPago,
                'numerotransaccion' => $data['numerotransaccion'] ?? null,
                'fechapago' => $fechaPago,
                'amortizacion' => $amortizacionPenalidad,
                'comprobante' => $rutaComprobantePenalidad,
                'observacion' => 'Pago de penalidad - ' . ($data['observacion'] ?? ''),
                'tipo' => 'Penalidad'
            ];
        }

        //$pagoCuota sea un array si solo se paga penalidad.
        if ($pagoCuota === null && $pagoPenalidad !== null) {
            $pagoCuota = [
                'idcronograma' => $idCronograma,
                'amortizacion' => 0
            ];
        }

        // Llamar a la función del modelo si hay algo que pagar
        if ($pagoCuota !== null) {
            $idPagos = $this->pagoCronogramaModel->addMultiplePaymentsAndCheckStatus($pagoCuota, $pagoPenalidad);

            if (!empty($idPagos)) {
                echo json_encode([
                    'success' => true,
                    'message' => '¡Pagos registrados correctamente!',
                    'ids' => $idPagos
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo registrar los pagos',
                    'ids' => []
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo registrar los pagos',
                'ids' => []
            ]);
        }
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
