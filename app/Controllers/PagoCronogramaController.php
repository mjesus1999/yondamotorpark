<?php

/**
 * Controlador de Pago de Cronograma
 *
 * app/Controllers/PagoCronogramaController.php
 *
 * Gestiona el registro de pagos de cuotas y penalidades de contratos
 * de financiamiento de vehículos. Incluye validaciones exhaustivas,
 * manejo de archivos de comprobantes, control de secuencia de pagos
 * y gestión de caché.
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagoCronograma;
use App\Models\Caja;
use App\Controllers\ComprobanteNubefactController;
use DateTime;
use Exception;

/**
 * Clase PagoCronogramaController
 *
 * Controlador para la gestión de pagos de cronogramas de financiamiento.
 * Proporciona funcionalidades para registrar pagos de cuotas y penalidades,
 * validar secuencia de pagos (primera cuota pendiente), gestionar comprobantes
 * y mantener historial de transacciones.
 */
class PagoCronogramaController extends Controller
{

    /**
     * Modelo de PagoCronograma
     * @var PagoCronograma
     */
    private PagoCronograma $pagoCronogramaModel;

    /**
     * Modelo de Caja
     * @var Caja
     */
    private Caja $cajaModel;

    /**
     * Constructor del controlador
     *
     * Inicializa los modelos de PagoCronograma y Caja necesarios
     * para las operaciones del controlador.
     */
    public function __construct()
    {
        $this->pagoCronogramaModel = new PagoCronograma();
        $this->cajaModel = new Caja();
    }

    /**
     * Muestra el historial de pagos de un contrato
     *
     * Renderiza la vista con el historial completo de pagos realizados
     * para un contrato específico. Requiere autenticación.
     *
     * @param int $id ID del contrato a consultar
     * @return void
     */
    public function indexHistorialPagos(int $id): void
    {
        $this->authRequired();
        $cacheFile = __DIR__ . "/../../storage/cache/historial-pagos-contratos/historial-pagos-contrato{$id}.json";
        $ttl = 3600;
        $datos = [];

        if (file_exists($cacheFile) && (filemtime($cacheFile) + $ttl > time())) {
            $datos = json_decode(file_get_contents($cacheFile), true);
        } else {
            $datos = $this->pagoCronogramaModel->getHistorialPagosByContrato($id);
            if (!is_dir(dirname($cacheFile))) {
                mkdir(dirname($cacheFile), 0777, true);
            }
            file_put_contents($cacheFile, json_encode($datos));
        }


        // $tiempoInicio = microtime(true);

        // $datos = $this->pagoCronogramaModel->getHistorialPagosByContrato($id);
        $this->view('caja.historial', ['pagos' => $datos]);

        // $tiempoFin = microtime(true);
        // $tiempoEjecucion = $tiempoFin - $tiempoInicio;
        // error_log("Tiempo de ejecución de indexHistorialPagos: " . number_format($tiempoEjecucion, 4) . " segundos.");
    }

    /**
     * Verifica si una cuota está habilitada para ser pagada
     *
     * Método privado que valida si la cuota seleccionada es la primera
     * cuota pendiente de pago del contrato. Esto asegura que los pagos
     * se realicen en orden secuencial.
     *
     * @param int $idContrato ID del contrato
     * @param int $idCronograma ID de la cuota a validar
     * @return bool True si la cuota es la primera pendiente, false en caso contrario
     */
    private function esCuotaHabilitadaParaPago(int $idContrato, int $idCronograma): bool
    {
        $this->authRequired();
        $cronograma = $this->cajaModel->getCronogramaByIdContrato($idContrato);
        $primeraCuotaPendienteId = null;
        foreach ($cronograma as $cuota) {
            $cuotaPendiente = (float) ($cuota['saldocuota_pendiente'] ?? 0);
            $penalidadPendiente = (float) ($cuota['penalidad_pendiente'] ?? 0);


            if ($cuotaPendiente > 0 || $penalidadPendiente > 0) {
                $primeraCuotaPendienteId = intval($cuota['idcronograma']);
                break;
            }
        }

        return $primeraCuotaPendienteId !== null && $primeraCuotaPendienteId === $idCronograma;
    }


    /**
     * Guarda un archivo de comprobante de pago
     *
     * Método privado que procesa y almacena un archivo de comprobante,
     * generando un nombre único y creando el directorio si no existe.
     *
     * @param array $archivo Array del archivo $_FILES con información del upload
     * @return string|null Ruta relativa del archivo guardado (comprobantes/nombre.ext)
     *                     o null si falla la operación
     */
    private function guardarComprobante(array $archivo): ?string
    {
        $this->authRequired();
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

    /**
     * Registra un pago de cuota y/o penalidad
     *
     * Endpoint AJAX que procesa el registro de pagos de cronograma.
     * Maneja pagos individuales o combinados (cuota + penalidad) con
     * validaciones exhaustivas y control de secuencia de pagos.
     *
     * Validaciones:
     * - Campos obligatorios según medio de pago
     * - Fecha de pago no futura
     * - Monto de cuota no excede el pendiente
     * - Penalidad debe pagarse completa
     * - Solo permite pagar la primera cuota pendiente
     * - Comprobante obligatorio para transferencias/depósitos
     *
     * @return void
     */
    public function store(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                return;
            }

            $data = array_map([Validador::class, 'limpiar'], $_POST);
            $errores = [];
            $idCronograma = (int) ($data['idcronograma'] ?? 0);
            $numeroTransaccion = $data['numerotransaccion'] ?? '';
            $numeroTransaccionPenalidad = (string) ($data['numeroTransaccionPenalidad'] ?? '');
            $amortizacionCuota = (float) ($data['amortizacionCuota'] ?? 0);
            $amortizacionPenalidad = (float) ($data['amortizacionPenalidad'] ?? 0);
            $medioPagoPenalidad = $data['mediopagopenalidad'] ?? '';
            $medioPago = $data['mediopago'] ?? '';
            $idCuentaPago = $data['idcuentapago'] ?? null;
            $idCuentaPagoPenalidad = $data['idcuentapagopenalidad'] ?? null;
            $fechaPago = $data['fechapago'] ?? '';
            $observacion = $data['observacion'] ?? '';

            $totalInteresProrrateado = (float) ($data['total_interes_prorrateado'] ?? 0);
            $totalCapitalProrrateado = (float) ($data['total_capital_prorrateado'] ?? 0);

            $rutaComprobanteCuota = isset($_FILES['comprobanteCuota']) ? $this->guardarComprobante($_FILES['comprobanteCuota']) : null;
            $rutaComprobantePenalidad = isset($_FILES['comprobantePenalidad']) ? $this->guardarComprobante($_FILES['comprobantePenalidad']) : null;
          

            if ($idCronograma <= 0) {
                $errores[] = 'Cuota a pagar no es válida.';
            }
            if ($amortizacionCuota <= 0 && $amortizacionPenalidad <= 0) {
                $errores[] = 'Debes ingresar un monto válido mayor a 0 (cuota o penalidad).';
            }

            // Validaciones Cuota
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
                    if (is_null($rutaComprobanteCuota)) {
                        $errores[] = 'Debe adjuntar un comprobante válido para el pago de cuota.';
                    }
                }
            }

            // Validaciones Penalidad
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
                    if (is_null($rutaComprobantePenalidad)) {
                        $errores[] = 'Debe adjuntar un comprobante válido para el pago de penalidad.';
                    }
                }
            }

            // Validaciones Fecha
            if (empty($fechaPago)) {
                $errores[] = 'La fecha de pago es obligatoria.';
            } elseif (!empty($fechaPago)) {
                $fechaActual = new DateTime();
                $fechaPagoObj = new DateTime($fechaPago);
                if ($fechaPagoObj > $fechaActual) {
                    $errores[] = 'La fecha de pago no puede ser futura.';
                }
            }


            $cronogramaData = $this->pagoCronogramaModel->getCronogramaData($idCronograma);
            if (!$cronogramaData) {
                $errores[] = 'No se encontró la cuota.';
            } else {
                $idContrato = $cronogramaData['idcontrato'];
            }

            if (isset($idContrato) && !$this->esCuotaHabilitadaParaPago($idContrato, $idCronograma)) {
                $errores[] = 'Solo puedes pagar la primera cuota pendiente';
            }
            if ($amortizacionCuota > 0 && $amortizacionCuota > $cronogramaData['cuotapendiente']) {
                $errores[] = "La cuota no puede exceder S/ {$cronogramaData['cuotapendiente']}";
            }

            $penalidadPendiente = (float) ($cronogramaData['penalidadpendiente'] ?? 0);
            if ($amortizacionPenalidad > 0 && ($penalidadPendiente <= 0 || $amortizacionPenalidad != $penalidadPendiente)) {
                $errores[] = "La penalidad debe pagarse completa: S/ {$penalidadPendiente}";
            }

            if (!empty($errores)) {
                echo json_encode(['success' => false, 'message' => implode('<br>', $errores)]);
                return;
            }

            $idContrato = $cronogramaData['idcontrato'];

            $nombreCuentaCuotaBoleta = $medioPago;
            if ($amortizacionCuota > 0 && $medioPago === 'Transferencia Bancaria' && !empty($idCuentaPago)) {
                $datosCuenta = $this->pagoCronogramaModel->getNumCuentaPagoById((int) $idCuentaPago);
                if ($datosCuenta) {
                    $nombreCuentaCuotaBoleta = $datosCuenta['nombrecuenta']; 
                }
            }

            $nombreCuentaPenalidadBoleta = $medioPagoPenalidad;
            if ($amortizacionPenalidad > 0 && $medioPagoPenalidad === 'Transferencia Bancaria' && !empty($idCuentaPagoPenalidad)) {
                $datosCuenta = $this->pagoCronogramaModel->getNumCuentaPagoById((int) $idCuentaPagoPenalidad);
                if ($datosCuenta) {
                    $nombreCuentaPenalidadBoleta = $datosCuenta['nombrecuenta']; 
                }
            }
          
            $medioPagoBoleta = ($amortizacionCuota > 0) ? $nombreCuentaCuotaBoleta : $nombreCuentaPenalidadBoleta;
   

            $pagoCuota = null;
            if ($amortizacionCuota > 0) {
                $pagoCuota = [
                    'idcronograma' => $idCronograma,
                    'idcuentapago' => empty($idCuentaPago) ? null : (int) $idCuentaPago,
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
                    'idcuentapago' => empty($idCuentaPagoPenalidad) ? null : (int) $idCuentaPagoPenalidad,
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

                $enlacePdf = null;
                $enlaceXml = null;
                $enlaceCdr = null;
                $mensajeExtra = "";
                if ($amortizacionCuota > 0) {
                    try {
                        $serieBoleta = 'BBB1';
                        $clienteData = $this->cajaModel->getDatosClientePorCronograma($idCronograma);
                        $nuevoNumero = $this->cajaModel->obtenerNuevoCorrelativo($serieBoleta);

                        if ($clienteData && $nuevoNumero) {

                            $itemsFacturacion = [];
                            $totalGravada = 0.00;
                            // totalInafecta debe ser 0.00 para evitar el error si se envía solo Gravada
                            $totalInafecta = 0.00;
                            $totalIGV = 0.00;
                            $totalVenta = 0.00;

                            // IGV 
                            $porcentajeIGV = 0.18;
                            $factorIGV = 1 + $porcentajeIGV;


                            // Item Capital - Tipo de IGV 1
                            if ($totalCapitalProrrateado > 0) {
                                $totalCapitalConIGV = round($totalCapitalProrrateado, 2);
                                // Calcular valor unitario (sin IGV) y el IGV
                                $valorUnitarioCapital = round($totalCapitalConIGV / $factorIGV, 10);
                                $igvCapital = round($totalCapitalConIGV - $valorUnitarioCapital, 2);

                                $itemsFacturacion[] = [
                                    "unidad_de_medida" => "ZZ",
                                    "descripcion" => "Abono a Capital",
                                    "cantidad" => 1,
                                    "valor_unitario" => $valorUnitarioCapital,
                                    "precio_unitario" => $totalCapitalConIGV,
                                    "subtotal" => $valorUnitarioCapital,
                                    "tipo_de_igv" => 1, // GRAVADO ES => TIPO_DE_IGV = 1
                                    "igv" => $igvCapital,
                                    "total" => $totalCapitalConIGV
                                ];
                                $totalGravada += $valorUnitarioCapital;
                                $totalIGV += $igvCapital;
                                $totalVenta += $totalCapitalConIGV;
                            }

                           
                            if ($totalInteresProrrateado > 0) {
                                $totalInteresConIGV = round($totalInteresProrrateado, 2);
                               
                                $valorUnitarioInteres = round($totalInteresConIGV / $factorIGV, 10);
                                $igvInteres = round($totalInteresConIGV - $valorUnitarioInteres, 2);

                                $itemsFacturacion[] = [
                                    "unidad_de_medida" => "ZZ",
                                    "descripcion" => "Interés",
                                    "cantidad" => 1,
                                    "valor_unitario" => $valorUnitarioInteres,
                                    "precio_unitario" => $totalInteresConIGV,
                                    "subtotal" => $valorUnitarioInteres,
                                    "tipo_de_igv" => 1,
                                    "igv" => $igvInteres,
                                    "total" => $totalInteresConIGV
                                ];
                                $totalGravada += $valorUnitarioInteres;
                                $totalIGV += $igvInteres;
                                $totalVenta += $totalInteresConIGV;
                            }

                            // Penalidad - Mantenido como Gravado 
                            if ($amortizacionPenalidad > 0) {
                                $totalMoraConIGV = round($amortizacionPenalidad, 2);
                                $valorUnitarioMora = round($totalMoraConIGV / $factorIGV, 10);
                                $igvMora = round($totalMoraConIGV - $valorUnitarioMora, 2);

                                $itemsFacturacion[] = [
                                    "unidad_de_medida" => "ZZ",
                                    "descripcion" => "Mora",
                                    "cantidad" => 1,
                                    "valor_unitario" => $valorUnitarioMora,
                                    "precio_unitario" => $totalMoraConIGV,
                                    "subtotal" => $valorUnitarioMora,
                                    "tipo_de_igv" => 1,
                                    "igv" => $igvMora,
                                    "total" => $totalMoraConIGV
                                ];
                                $totalGravada += $valorUnitarioMora;
                                $totalIGV += $igvMora;
                                $totalVenta += $totalMoraConIGV;
                            }

                            $totalGravada = round($totalGravada, 2);
                            $totalIGV = round($totalIGV, 2);
                            $totalVenta = round($totalVenta, 2);


                            $datosFacturacion = [
                                'tipo_comprobante' => 2, // Boleta 
                                'serie' => $serieBoleta,
                                'mediopago' => $medioPagoBoleta,
                                'numero_comprobante' => $nuevoNumero,
                                'items' => $itemsFacturacion,
                                'totales' => [
                                    'total_gravada' => $totalGravada,
                                    'total_inafecta' => 0.00, // 
                                    'total_exonerada' => 0.00,
                                    'total_igv' => $totalIGV,
                                    'total_venta' => $totalVenta
                                ],

                                'datos_cliente' => [
                                    'tipo_documento' => 1, // DNI 
                                    'numero_documento' => $clienteData['nrodoc'],
                                    'denominacion' => $clienteData['razon_social'],
                                    'direccion' => $clienteData['direccion'] ?? 'LIMA',
                                    'email' => $clienteData['email'] ?? ''
                                ]
                            ];


                            $nubefactController = new ComprobanteNubefactController();
                            $respNube = $nubefactController->procesarPagoYEmitirComprobante($datosFacturacion);

                            if ($respNube['success']) {

                                $enlacePdf = $respNube['enlace_pdf'];
                                $enlaceXml = $respNube['enlace_xml'];
                                $enlaceCdr = $respNube['enlace_cdr'];

                                foreach ($idPagos as $idpago) {
                                    $this->pagoCronogramaModel->actualizarEnlaceYDeclarado($idpago, $enlacePdf, $enlaceXml, $enlaceCdr, $nuevoNumero);
                                }
                            } else {
                                error_log("Error NubeFact (Emisión): " . $respNube['message']);
                                $mensajeExtra = " Pago OK, pero Boleta con Error: " . $respNube['message'] . ")";
                            }
                        } else {
                            $mensajeExtra = " Pago registrado, pero faltan datos del cliente para la Boleta";
                        }
                    } catch (Exception $ex) {
                        error_log("Excepción Facturación: " . $ex->getMessage());
                        $mensajeExtra = " Pago guardado, pero falló la comunicación con NubeFact";
                    }
                }

                // Limpieza de caché
                $cacheFile = __DIR__ . "/../../storage/cache/cronograma-contratos/cronograma-contrato{$idContrato}.json";
                $cacheFileHistorial = __DIR__ . "/../../storage/cache/historial-pagos-contratos/historial-pagos-contrato{$idContrato}.json";
                if (file_exists($cacheFile)) {
                    unlink($cacheFile);
                }
                if (file_exists($cacheFileHistorial)) {
                    unlink($cacheFileHistorial);
                }

                echo json_encode([
                    'success' => true,
                    'message' => '¡Pago registrado correctamente!' . $mensajeExtra,
                    'enlace_pdf' => $enlacePdf,
                    'enlace_xml' => $enlaceXml,
                    'enlace_cdr' => $enlaceCdr,
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo registrar el pago en la base de datos.'
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
    /**
     * API: Obtiene las cuentas de pago disponibles
     *
     * Endpoint AJAX que retorna todas las cuentas bancarias disponibles
     * para registrar pagos, con formato concatenado (Entidad - NumCuenta - Moneda).
     *
     * @return void
     */
    public function searchNumCuentasPagos(): void
    {
        $this->authRequired();
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
    public function getCuenta(int $id): void
    {
        header('Content-Type: application/json');
        $numCuenta = $this->pagoCronogramaModel->getNumCuentaPagoById($id);


        if ($numCuenta) {
            echo json_encode($numCuenta);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}
