<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ArqueoCaja;
use Exception;

class ArqueoCajaController extends Controller
{
    private ArqueoCaja $arqueoCajaModel;

    public function __construct()
    {
        $this->arqueoCajaModel = new ArqueoCaja();
    }


    public function index(string $entregado = 'N'): void
    {

        $datosArqueoBase = $this->arqueoCajaModel->obtenerDatosUltimoArqueo();
        $saldoInicialParaArqueo = $datosArqueoBase['saldo_inicial_para_hoy'];
        $horaReferencia = $datosArqueoBase['hora_ultimo_arqueo'];
        $fechaReferencia = $datosArqueoBase['fecha_ultimo_arqueo'];
        $horaUltimaEntrega = $this->arqueoCajaModel->getHoraUltimaEntregaHoy();
        // error_log('HORA: ' .  $horaReferencia);
        // error_log('FECHA' . $fechaReferencia);
        $ingresosNuevos = $this->arqueoCajaModel->obtenerIngresosDesde($fechaReferencia, $horaReferencia);
        $egresosNuevos = $this->arqueoCajaModel->obtenerEgresosDesde($fechaReferencia, $horaReferencia);

        $ingresosEfectivoNuevos = $ingresosNuevos['ingresos_efectivo_nuevos'];
        $ingresosDigitalNuevos = $ingresosNuevos['ingresos_digital_nuevos'];
        $egresosDiaNuevos = $egresosNuevos;


        $montoTeorico = $saldoInicialParaArqueo + $ingresosEfectivoNuevos - $egresosDiaNuevos;


        $data = [
            'saldo_inicial' => number_format($saldoInicialParaArqueo, 2, '.', ''),
            'ingresos_efectivo' => number_format($ingresosEfectivoNuevos, 2, '.', ''),
            'ingresos_digital' => number_format($ingresosDigitalNuevos, 2, '.', ''),
            'egresos_dia' => number_format($egresosDiaNuevos, 2, '.', ''),
            'monto_teorico' => number_format($montoTeorico, 2, '.', ''),
            'entregado' => $entregado,
            'es_primer_arqueo' => ($saldoInicialParaArqueo == 0.00),
            'hora_ultima_entrega' => $horaUltimaEntrega,
        ];


        $data['arqueos'] = $this->arqueoCajaModel->listarConsolidado();


        $this->view('arqueo-caja.index', $data);
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
            exit;
        }

        try {
            if (!isset($_POST['saldo_inicial'], $_POST['monto_fisico'], $_POST['diferencia'])) {
                throw new Exception('Faltan campos obligatorios.');
            }

            $data = [
                'hora_inicio'       => $_POST['hora_inicio'] ?? null,
                'hora_fin'          => $_POST['hora_fin'] ?? null,
                'saldo_inicial'      => (float) ($_POST['saldo_inicial']),
                'monto_fisico'       => (float) ($_POST['monto_fisico']),
                'ingresos_efectivo'  => (float) ($_POST['ingresos_efectivo']),
                'ingresos_digital'   => (float) ($_POST['ingresos_digital']),
                'egresos_dia'        => (float) ($_POST['egresos_dia']),
                'monto_teorico'      => (float) ($_POST['monto_teorico']),
                'diferencia'         => (float) ($_POST['diferencia']),
                'observaciones'      => empty($_POST['observaciones']) ? null : $_POST['observaciones'],
            ];

            // Validación de observaciones para faltante
            if ($data['diferencia'] < 0 && empty($data['observaciones'])) {
                throw new Exception('Las observaciones son requeridas cuando hay un faltante.');
            }

            if ($this->arqueoCajaModel->registrar($data)) {
                echo json_encode([
                    'success' => true,
                    'message' => '¡Arqueo registrado correctamente!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo registrar el arqueo.'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function entregar(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validaciones de campos obligatorios
            if (!isset($data['ids_arqueos'], $data['tipodestino'], $data['montoentregado'])) {
                throw new Exception('Faltan campos obligatorios para el registro de la entrega.');
            }

            // Validación específica para entregas a Gerente
            if ($data['tipodestino'] === 'Gerente' && !isset($data['iddestino'])) {
                throw new Exception('El destino (Gerente) es obligatorio para este tipo de entrega.');
            }

            // Validación específica para depósitos múltiples
            if ($data['tipodestino'] === 'Deposito') {
                if (!isset($data['destinos_multiples']) || !is_array($data['destinos_multiples']) || count($data['destinos_multiples']) === 0) {
                    throw new Exception('Se requiere al menos un destino de depósito.');
                }

                // Validar que la suma de los montos parciales sea igual al monto total
                $totalDepositos = array_sum(array_column($data['destinos_multiples'], 'monto'));
                if (abs($totalDepositos - $data['montoentregado']) > 0.01) {
                    throw new Exception('La suma de los montos parciales no coincide con el monto total a entregar.');
                }
            }

            if ($this->arqueoCajaModel->registrarEntrega($data)) {
                echo json_encode(['success' => true, 'message' => 'Entrega registrada y arqueo(s) actualizados correctamente.']);
            } else {
                throw new Exception('No se pudo registrar la entrega y actualizar los arqueos.');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }





    public function destinos(string $tipo)
    {
        header('Content-Type: application/json');

        if ($tipo === 'Gerente') {
            $destinos = $this->arqueoCajaModel->obtenerGerentes();
            echo json_encode(['success' => true, 'destinos' => $destinos]);
        } elseif ($tipo === 'Deposito') {
            $destinos = $this->arqueoCajaModel->obtenerCuentasBancarias();
            echo json_encode(['success' => true, 'destinos' => $destinos]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tipo de destino no válido.']);
        }
    }



    public function reportePorSede()
    {
        header('Content-Type: application/json');

        $ids_arqueo = $_GET['ids_arqueo'] ?? '';
        $idlocal = (int)($_GET['idlocal'] ?? 0);

        if (empty($ids_arqueo) || $idlocal <= 0) {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
            return;
        }

        // Llamar al modelo
        $reporte = $this->arqueoCajaModel->getReporteArqueoBySede($ids_arqueo, $idlocal);

        if (!empty($reporte)) {
            echo json_encode(['success' => true, 'reporte' => $reporte]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo generar el reporte.']);
        }
    }


    public function getReporteArqueoPorCiclo(string $ids_arqueo): void
    {
        header('Content-Type: application/json');


        $data = $this->arqueoCajaModel->getReporteArqueoPorCiclo($ids_arqueo);

        if (!empty($data) && !empty($data['arqueo'])) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró reporte de arqueo para los IDs indicados.'
            ]);
        }
    }
}
