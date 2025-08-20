<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagoCronograma;
use App\Models\Caja;
use DateTime;


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

            // Asignar y validar campos básicos
            $idCronograma = (int)($data['idcronograma'] ?? 0);

            $numeroTransaccion = $data['numerotransaccion'] ?? ''; // NUMERO DE TRANSACCION DE CUOTA
            $numeroTransaccionPenalidad = (string)($data['numeroTransaccionPenalidad'] ?? ''); // NUMERO DE TRANSACCION DE PENALIDAD

            $amortizacionCuota = (float)($data['amortizacionCuota'] ?? 0);
            $amortizacionPenalidad = (float)($data['amortizacionPenalidad'] ?? 0);

            $medioPagoPenalidad = $data['mediopagopenalidad'] ?? ''; // ESTE ES EL MEDIO DE PAGO DE PENALIDAD
            $medioPago = $data['mediopago'] ?? ''; // ESTE ES EL MEDIO DE PAGO DE CUOTa

            $idCuentaPago = $data['idcuentapago'] ?? null; // ESTE ES EL IDCUENTAPAGO DE CUOTA
            $idCuentaPagoPenalidad = $data['idcuentapagopenalidad'] ?? null; // ESTE ES EL IDCUENTAPAGO DE PENALIDAD

            $fechaPago = $data['fechapago'] ?? '';
            $observacion = $data['observacion'] ?? '';

            // Validaciones condicionales de los montos y sus campos asociados
            if ($idCronograma <= 0) {
                $errores[] = 'Cuota a pagar no es válida.';
            }

            if ($amortizacionCuota <= 0 && $amortizacionPenalidad <= 0) {
                $errores[] = 'Debes ingresar un monto válido mayor a 0 (cuota o penalidad).';
            }

            // Validación del medio de pago para la CUOTA (solo si se está pagando)
            if ($amortizacionCuota > 0) {
                if (empty($medioPago)) {
                    $errores[] = 'El método de pago de la cuota es obligatorio.';
                }
                // Validaciones para cuota que NO es en efectivo
                if ($medioPago !== 'Efectivo') {
                    if (empty($numeroTransaccion)) {
                        $errores[] = 'El número de transacción de la cuota es obligatorio.';
                    }
                    if ($medioPago === 'Transferencia Bancaria' && empty($idCuentaPago)) {
                        $errores[] = 'Debes seleccionar una cuenta bancaria para la cuota.';
                    }
                    if (!isset($_FILES['comprobanteCuota']) || empty($_FILES['comprobanteCuota']['name'])) {
                        $errores[] = 'Debe adjuntar un comprobante para el pago de cuota.';
                    }
                }
            }

            // Validación del medio de pago para la PENALIDAD (solo si se está pagando)
            if ($amortizacionPenalidad > 0) {
                if (empty($medioPagoPenalidad)) {
                    $errores[] = 'El método de pago de la penalidad es obligatorio.';
                }
                // Validaciones para penalidad que NO es en efectivo
                if ($medioPagoPenalidad !== 'Efectivo') {
                    if (empty($numeroTransaccionPenalidad)) {
                        $errores[] = 'El número de transacción de la penalidad es obligatorio.';
                    }
                    if ($medioPagoPenalidad === 'Transferencia Bancaria' && empty($idCuentaPagoPenalidad)) {
                        $errores[] = 'Debes seleccionar una cuenta bancaria para la penalidad.';
                    }
                    if (!isset($_FILES['comprobantePenalidad']) || empty($_FILES['comprobantePenalidad']['name'])) {
                        $errores[] = 'Debe adjuntar un comprobante para el pago de penalidad.';
                    }
                }
            }

            // Validaciones generales
            if (empty($fechaPago)) {
                $errores[] = 'La fecha de pago es obligatoria.';
            } elseif (!empty($fechaPago)) {
                $fechaActual = new DateTime();
                $fechaPagoObj = new DateTime($fechaPago);
                if ($fechaPagoObj > $fechaActual) {
                    $errores[] = 'La fecha de pago no puede ser futura.';
                }
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

            // Validaciones de negocio con los datos del cronograma
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

            // Si hay errores en las validaciones de negocio o archivos
            if (!empty($errores)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode('<br>', $errores)
                ]);
                return;
            }

            // Guardar archivos después de las validaciones de negocio
            $rutaComprobanteCuota = null;
            if ($amortizacionCuota > 0 && $medioPago !== 'Efectivo') {
                $rutaComprobanteCuota = $this->guardarComprobante($_FILES['comprobanteCuota']);
                if (!$rutaComprobanteCuota) {
                    $errores[] = 'Error al guardar comprobante de cuota.';
                }
            }

            $rutaComprobantePenalidad = null;
            if ($amortizacionPenalidad > 0 && $medioPagoPenalidad !== 'Efectivo') {
                $rutaComprobantePenalidad = $this->guardarComprobante($_FILES['comprobantePenalidad']);
                if (!$rutaComprobantePenalidad) {
                    $errores[] = 'Error al guardar comprobante de penalidad.';
                }
            }

            // Volver a verificar los errores después de intentar guardar los archivos
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
                    'comprobante' => ($medioPago === 'Efectivo') ? null : $rutaComprobanteCuota,
                    'observacion' => $observacion,
                    'tipo' => 'Cuota'
                ];
            }

            $pagoPenalidad = null;
            if ($amortizacionPenalidad > 0) {
                $pagoPenalidad = [
                    'idcronograma' => $idCronograma,
                    'idcuentapago' => empty($idCuentaPagoPenalidad) ? null : (int)$idCuentaPagoPenalidad,
                    'idcolcaja' => 2,
                    'mediopago' => $medioPagoPenalidad,
                    'numerotransaccion' => $numeroTransaccionPenalidad,
                    'fechapago' => $fechaPago,
                    'amortizacion' => $amortizacionPenalidad,
                    'comprobante' => ($medioPagoPenalidad === 'Efectivo') ? null : $rutaComprobantePenalidad,
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
