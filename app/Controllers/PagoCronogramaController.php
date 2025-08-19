<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagoCronograma;
use App\Models\Caja;
use DateTime;
use GrahamCampbell\ResultType\Success;

class PagoCronogramaController extends Controller
{

    private PagoCronograma $pagoCronogramaModel;
    private Caja $cajaModel;

    public function __construct()
    {
        $this->pagoCronogramaModel = new PagoCronograma();
        $this->cajaModel = new Caja();
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

    private function esCuotaHabilitadaParaPago(int $idContrato, int $idCronograma): bool
    {
        $cronograma = $this->cajaModel->getCronogramaByIdContrato($idContrato);
        $primeraCuotaPendienteId = null;
        foreach ($cronograma as $cuota) {
            $cuotaPendiente = (float)($cuota['saldocuota_pendiente'] ?? 0);
            $penalidadPendiente = (float)($cuota['penalidad_pendiente'] ?? 0);


            if ($cuotaPendiente > 0 || $penalidadPendiente > 0) {
                $primeraCuotaPendienteId = intval($cuota['idcronograma']);
                break;
            }
        }

        return $primeraCuotaPendienteId !== null && $primeraCuotaPendienteId === $idCronograma;
    }



    public function store(): void
    {
        header('Content-Type: application/json');


        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                return;
            }

            $data = array_map([Validador::class, 'limpiar'], $_POST);
            $errores = [];

            //Asignar y validar campos básicos
            $idCronograma = (int)($data['idcronograma'] ?? 0);
            $amortizacionCuota = (float)($data['amortizacionCuota'] ?? 0);
            $amortizacionPenalidad = (float)($data['amortizacionPenalidad'] ?? 0);
            $numeroTransaccionPenalidad = (string)($data['numeroTransaccionPenalidad'] ?? '');
            $medioPago = $data['mediopago'] ?? '';
            $idCuentaPago = $data['idcuentapago'] ?? null;
            $fechaPago = $data['fechapago'] ?? '';
            $numeroTransaccion = $data['numerotransaccion'] ?? '';
            $observacion = $data['observacion'] ?? '';

            // Validaciones básicas de campos
            if ($idCronograma <= 0) {
                $errores[] = 'Cuota a pagar no es válida.';
            }

            if (empty($medioPago)) {
                $errores[] = 'Método de pago es obligatorio.';
            }

            if ($amortizacionCuota <= 0 && $amortizacionPenalidad <= 0) {
                $errores[] = 'Debes ingresar un monto válido mayor a 0  (cuota o penalidad).';
            }

            if (empty($fechaPago)) {
                $errores[] = 'La fecha de pago es obligatoria.';
            }

            if (!empty($fechaPago)) {
                $fechaActual = new DateTime();
                $fechaPagoObj = new DateTime($fechaPago);
                if ($fechaPagoObj > $fechaActual) {
                    $errores[] = 'La fecha de pago no puede ser futura.';
                }
            }

            // Validaciones condicionales
            if ($medioPago === 'Transferencia Bancaria' && empty($idCuentaPago)) {
                $errores[] = 'Debes seleccionar una cuenta bancaria.';
            }

            if ($amortizacionCuota > 0 && empty($numeroTransaccion)) {
                $errores[] = 'El número de transacción de la cuota es obligatorio.';
            }

            if ($amortizacionPenalidad > 0 && empty($numeroTransaccionPenalidad)) {
                $errores[] = 'El número de transacción de la penalidad es obligatorio.';
            }

            // Si hay errores en los campos básicos, no continuamos con la lógica de negocio
            if (!empty($errores)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode('<br>', $errores)
                ]);
                return;
            }

            // Obtener datos del cronograma 
            $cronogramaData = $this->pagoCronogramaModel->getCronogramaData($idCronograma);
            if (!$cronogramaData) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró la cuota.'
                ]);
                return;
            }

            $idContrato = $cronogramaData['idcontrato'];

            if (!$this->esCuotaHabilitadaParaPago($idContrato, $idCronograma)) {
                $errores[] = 'Solo puedes pagar la primera cuota pendiente';
            }

            //  Validaciones de negocio con los datos del cronograma
            if ($amortizacionCuota > 0) {
                if ($amortizacionCuota > $cronogramaData['cuotapendiente']) {
                    $errores[] = "La cuota no puede exceder S/ {$cronogramaData['cuotapendiente']}";
                }
            }
            // Validación de la penalidad usando el saldo pendiente
            $penalidadPendiente = (float)($cronogramaData['penalidadpendiente'] ?? 0);

            if ($amortizacionPenalidad > 0) {
                if ($penalidadPendiente <= 0) {
                    $errores[] = "No hay penalidad pendiente para esta cuota, la penalidad es: S/ {$penalidadPendiente}";
                } else if ($amortizacionPenalidad != $penalidadPendiente) {
                    $errores[] = "La penalidad debe pagarse completa: S/ {$penalidadPendiente}";
                }
            }


            // Validar y guardar archivos
            $rutaComprobanteCuota = null;
            if ($amortizacionCuota > 0) {
                if (!isset($_FILES['comprobanteCuota']) || empty($_FILES['comprobanteCuota']['name'])) {
                    $errores[] = 'Debe adjuntar un comprobante para el pago de cuota.';
                } else {
                    $rutaComprobanteCuota = $this->guardarComprobante($_FILES['comprobanteCuota']);
                    if (!$rutaComprobanteCuota) {
                        $errores[] = 'Error al guardar comprobante de cuota.';
                    }
                }
            }

            $rutaComprobantePenalidad = null;
            if ($amortizacionPenalidad > 0) {
                if (!isset($_FILES['comprobantePenalidad']) || empty($_FILES['comprobantePenalidad']['name'])) {
                    $errores[] = 'Debe adjuntar un comprobante para el pago de penalidad.';
                } else {
                    $rutaComprobantePenalidad = $this->guardarComprobante($_FILES['comprobantePenalidad']);
                    if (!$rutaComprobantePenalidad) {
                        $errores[] = 'Error al guardar comprobante de penalidad.';
                    }
                }
            }

            // Si hay errores en las validaciones de negocio o archivos
            if (!empty($errores)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode('<br>', $errores)
                ]);
                return;
            }

            // Si no hay errores, se procede a preparar y registrar los pagos
            $pagoCuota = null;
            if ($amortizacionCuota > 0) {
                $pagoCuota = [
                    'idcronograma' => $idCronograma,
                    'idcuentapago' => empty($idCuentaPago) ? null : (int)$idCuentaPago,
                    'idcolcaja' => 2,
                    'mediopago' => $medioPago,
                    'numerotransaccion' => $numeroTransaccion,
                    'fechapago' => $fechaPago,
                    'amortizacion' => $amortizacionCuota,
                    'comprobante' => $rutaComprobanteCuota,
                    'observacion' => $observacion,
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
                    'numerotransaccion' => $numeroTransaccionPenalidad,
                    'fechapago' => $fechaPago,
                    'amortizacion' => $amortizacionPenalidad,
                    'comprobante' => $rutaComprobantePenalidad,
                    'observacion' => 'Pago de penalidad - ' . $observacion,
                    'tipo' => 'Penalidad'
                ];
            }

            if ($pagoCuota === null && $pagoPenalidad !== null) {
                $pagoCuota = [
                    'idcronograma' => $idCronograma,
                    'amortizacion' => 0
                ];
            }

            $idPagos = $this->pagoCronogramaModel->addMultiplePagos($pagoCuota, $pagoPenalidad);
            if (!empty($idPagos)) {
                $cronogramaActualizado = $this->cajaModel->getCronogramaByIdContrato($idContrato);
                echo json_encode([
                    'success' => true,
                    'message' => '¡Pago registrado correctamente!',
                    'ids' => $idPagos,
                    'cronograma' => $cronogramaActualizado
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo registrar el pago'
                ]);
            }
        } catch (\Throwable $th) {
            http_response_code(500);
            error_log($th->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error inesperado del servidor. Intente más tarde.'
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
