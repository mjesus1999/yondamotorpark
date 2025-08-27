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
        $this->authRequired();
        $tiempoInicio = microtime(true);

        $datos = $this->pagoCronogramaModel->getHistorialPagosByContrato($id);
        $this->view('caja.historial', ['pagos' => $datos]);

        $tiempoFin = microtime(true);
        $tiempoEjecucion = $tiempoFin - $tiempoInicio;

        error_log("Tiempo de ejecución de indexHistorialPagos: " . number_format($tiempoEjecucion, 4) . " segundos.");
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



    private function guardarComprobante(array $archivo): ?string
    {

        if (!isset($archivo['error']) || is_array($archivo['error'])) {
            return null;
        }

        $nombreArchivo = uniqid('comprobante_') . '_' . basename($archivo['name']);

        $directorioDestino = __DIR__ . '/../../storage/comprobantes/';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {

            return 'comprobantes/' . $nombreArchivo;
        }

        return null;
    }




    // private function guardarComprobante(array $archivo): ?string
    // {
    //     // Verificar si el archivo es válido antes de procesarlo
    //     if (!isset($archivo['error']) || is_array($archivo['error'])) {
    //         return null;
    //     }

    //     $nombreArchivo = uniqid('comprobante_') . '_' . basename($archivo['name']);
    //     $directorioDestino = realpath(__DIR__ . '/../../storage/comprobantes/');


    //     if (!is_dir($directorioDestino)) {
    //         mkdir($directorioDestino, 0777, true);
    //     }

    //     $rutaCompleta = $directorioDestino . $nombreArchivo;

    //     if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
    //         return '/' . $rutaCompleta;
    //     }

    //     return null;
    // }


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

            $numeroTransaccion = $data['numerotransaccion'] ?? '';
            $numeroTransaccionPenalidad = (string)($data['numeroTransaccionPenalidad'] ?? '');

            $amortizacionCuota = (float)($data['amortizacionCuota'] ?? 0);
            $amortizacionPenalidad = (float)($data['amortizacionPenalidad'] ?? 0);

            $medioPagoPenalidad = $data['mediopagopenalidad'] ?? '';
            $medioPago = $data['mediopago'] ?? '';

            $idCuentaPago = $data['idcuentapago'] ?? null;
            $idCuentaPagoPenalidad = $data['idcuentapagopenalidad'] ?? null;

            $fechaPago = $data['fechapago'] ?? '';
            $observacion = $data['observacion'] ?? '';

            // --- Procesamiento de archivos antes de las validaciones ---
            $rutaComprobanteCuota = null;
            if (isset($_FILES['comprobanteCuota'])) {
                $rutaComprobanteCuota = $this->guardarComprobante($_FILES['comprobanteCuota']);
            }

            $rutaComprobantePenalidad = null;
            if (isset($_FILES['comprobantePenalidad'])) {
                $rutaComprobantePenalidad = $this->guardarComprobante($_FILES['comprobantePenalidad']);
            }



            if ($idCronograma <= 0) {
                $errores[] = 'Cuota a pagar no es válida.';
            }

            if ($amortizacionCuota <= 0 && $amortizacionPenalidad <= 0) {
                $errores[] = 'Debes ingresar un monto válido mayor a 0 (cuota o penalidad).';
            }

            // Validación del medio de pago para la CUOTA
            if ($amortizacionCuota > 0) {
                if (empty($medioPago)) {
                    $errores[] = 'El método de pago de la cuota es obligatorio.';
                }

                if ($medioPago !== 'Efectivo') {
                    if (empty($numeroTransaccion)) {
                        $errores[] = 'El número de transacción de la cuota es obligatorio.';
                    }
                    if ($medioPago === 'Transferencia Bancaria' && empty($idCuentaPago)) {
                        $errores[] = 'Debes seleccionar una cuenta bancaria para la cuota.';
                    }
                    // Se valida si la ruta del archivo existe, no si el archivo está en $_FILES
                    if (is_null($rutaComprobanteCuota)) {
                        $errores[] = 'Debe adjuntar un comprobante válido para el pago de cuota.';
                    }
                }
            }

            // Validación del medio de pago para la PENALIDAD
            if ($amortizacionPenalidad > 0) {
                if (empty($medioPagoPenalidad)) {
                    $errores[] = 'El método de pago de la penalidad es obligatorio.';
                }

                if ($medioPagoPenalidad !== 'Efectivo') {
                    if (empty($numeroTransaccionPenalidad)) {
                        $errores[] = 'El número de transacción de la penalidad es obligatorio.';
                    }
                    if ($medioPagoPenalidad === 'Transferencia Bancaria' && empty($idCuentaPagoPenalidad)) {
                        $errores[] = 'Debes seleccionar una cuenta bancaria para la penalidad.';
                    }
                    // Se valida si la ruta del archivo existe
                    if (is_null($rutaComprobantePenalidad)) {
                        $errores[] = 'Debe adjuntar un comprobante válido para el pago de penalidad.';
                    }
                }
            }

            // Validaciones generales de la fecha
            if (empty($fechaPago)) {
                $errores[] = 'La fecha de pago es obligatoria.';
            } elseif (!empty($fechaPago)) {
                $fechaActual = new DateTime();
                $fechaPagoObj = new DateTime($fechaPago);
                if ($fechaPagoObj > $fechaActual) {
                    $errores[] = 'La fecha de pago no puede ser futura.';
                }
            }

            // Si hay errores en los campos básicos, no continuamos
            if (!empty($errores)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode('<br>', $errores)
                ]);
                return;
            }

            // Obtener datos del cronograma y validaciones de negocio
            $cronogramaData = $this->pagoCronogramaModel->getCronogramaData($idCronograma);
            if (!$cronogramaData) {
                echo json_encode(['success' => false, 'message' => 'No se encontró la cuota.']);
                return;
            }

            $idContrato = $cronogramaData['idcontrato'];

            if (!$this->esCuotaHabilitadaParaPago($idContrato, $idCronograma)) {
                $errores[] = 'Solo puedes pagar la primera cuota pendiente';
            }

            if ($amortizacionCuota > 0 && $amortizacionCuota > $cronogramaData['cuotapendiente']) {
                $errores[] = "La cuota no puede exceder S/ {$cronogramaData['cuotapendiente']}";
            }

            $penalidadPendiente = (float)($cronogramaData['penalidadpendiente'] ?? 0);
            if ($amortizacionPenalidad > 0) {
                if ($penalidadPendiente <= 0) {
                    $errores[] = "No hay penalidad pendiente para esta cuota, la penalidad es: S/ {$penalidadPendiente}";
                } else if ($amortizacionPenalidad != $penalidadPendiente) {
                    $errores[] = "La penalidad debe pagarse completa: S/ {$penalidadPendiente}";
                }
            }

            // Si hay errores en las validaciones de negocio, se detiene la ejecución
            if (!empty($errores)) {
                echo json_encode([
                    'success' => false,
                    'message' => implode('<br>', $errores)
                ]);
                return;
            }

            // Preparar y registrar los pagos si no hay errores
            $pagoCuota = null;
            if ($amortizacionCuota > 0) {
                $pagoCuota = [
                    'idcronograma' => $idCronograma,
                    'idcuentapago' => empty($idCuentaPago) ? null : (int)$idCuentaPago,
                    // 'idcolcaja' => 2,
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
                    'idcuentapago' => empty($idCuentaPagoPenalidad) ? null : (int)$idCuentaPagoPenalidad,
                    // 'idcolcaja' => 2,
                    'mediopago' => $medioPagoPenalidad,
                    'numerotransaccion' => $numeroTransaccionPenalidad,
                    'fechapago' => $fechaPago,
                    'amortizacion' => $amortizacionPenalidad,
                    'comprobante' => $rutaComprobantePenalidad,
                    'observacion' => 'Pago de penalidad - ' . $observacion,
                    'tipo' => 'Penalidad'
                ];
            }


            $idPagos = $this->pagoCronogramaModel->addMultiplePagos($pagoCuota, $pagoPenalidad);
            if (!empty($idPagos)) {
                echo json_encode([
                    'success' => true,
                    'message' => '¡Pago registrado correctamente!',
                    'ids' => $idPagos

                ]);
                // Identificar la ruta
                $cacheFile = __DIR__ . "/../../storage/cache/cronograma-contratos/cronograma-contrato{$idContrato}.json";

                if (file_exists($cacheFile)) {
                    unlink($cacheFile);
                    error_log("Caché borrada para el contrato ID: {$idContrato}");
                } else {
                    error_log("ERROR: No se pudo borrar la caché para el contrato ID: {$idContrato}");
                }
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
