<?php

/**
 * Controlador de Arqueo de Caja
 * 
 * app/Controllers/ArqueoController.php
 * 
 * Gestiona todas las operaciones de interfaz para el módulo de arqueo de caja.
 * Coordina el flujo entre vistas y modelo para: visualización de formularios de
 * arqueo con cálculos automáticos, registro de nuevos arqueos con validaciones,
 * registro de entregas de dinero (gerente/banco) con transacciones, obtención
 * de catálogos (gerentes/cuentas bancarias), y generación de reportes por ciclo
 * y por sede. Implementa autenticación obligatoria en todas las operaciones,
 * validaciones de datos de entrada, manejo de errores con respuestas JSON, y
 * formateo de números para presentación en vistas.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ArqueoCaja;
use Exception;

/**
 * Clase ArqueoCajaController
 * 
 * Controlador principal para la gestión de arqueos de caja.
 * Hereda de Controller para aprovechar funcionalidades base como
 * autenticación, carga de vistas y utilidades comunes.
 */
class ArqueoCajaController extends Controller
{
    /**
     * Instancia del modelo ArqueoCaja
     * @var ArqueoCaja
     */
    private ArqueoCaja $arqueoCajaModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa el controlador verificando autenticación del usuario
     * e instanciando el modelo de ArqueoCaja para operaciones de base de datos.
     */
    public function __construct()
    {
        $this->authRequired();
        $this->arqueoCajaModel = new ArqueoCaja();
    }

    /**
     * Muestra la vista principal del módulo de arqueo de caja
     * 
     * Carga y procesa todos los datos necesarios para mostrar la interfaz de arqueo:
     * obtiene datos del último arqueo, calcula ingresos y egresos desde la última
     * entrega o arqueo, calcula el monto teórico esperado en caja, formatea
     * números para presentación, y lista todos los arqueos históricos. La vista
     * muestra un formulario prellenado con cálculos automáticos y una tabla de
     * arqueos registrados.
     * 
     * @param string $entregado Filtro de estado de entrega: 'N' = pendientes (default), 'S' = entregados
     * @return void Renderiza la vista 'arqueo-caja.index' con los datos calculados
     */
    public function index(string $entregado = 'N'): void
    {
        $this->authRequired();

        $datosArqueoBase = $this->arqueoCajaModel->obtenerDatosUltimoArqueo();
        $saldoInicialParaArqueo = $datosArqueoBase['saldo_inicial_para_hoy'];
        $horaReferencia = $datosArqueoBase['hora_ultimo_arqueo'];
        $fechaReferencia = $datosArqueoBase['fecha_ultimo_arqueo'];
        $horaUltimaEntrega = $this->arqueoCajaModel->getHoraUltimaEntregaHoy();
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

    /**
     * Registra un nuevo arqueo de caja
     * 
     * Procesa el formulario de registro de arqueo mediante petición POST.
     * Valida campos obligatorios, formatea datos numéricos, valida que las
     * observaciones sean obligatorias cuando hay faltante, y registra el
     * arqueo en base de datos. Retorna respuesta JSON con el resultado.
     *
     * @throws \Exception Si faltan campos obligatorios o hay faltante sin observaciones
     * @return void Retorna respuesta JSON con estructura:
     *   - success (bool): true si se registró correctamente, false en caso contrario
     *   - message (string): Mensaje descriptivo del resultado
     */
    public function store(): void
    {
        $this->authRequired();
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
                'hora_inicio' => $_POST['hora_inicio'] ?? null,
                'hora_fin' => $_POST['hora_fin'] ?? null,
                'saldo_inicial' => (float) ($_POST['saldo_inicial']),
                'monto_fisico' => (float) ($_POST['monto_fisico']),
                'ingresos_efectivo' => (float) ($_POST['ingresos_efectivo']),
                'ingresos_digital' => (float) ($_POST['ingresos_digital']),
                'egresos_dia' => (float) ($_POST['egresos_dia']),
                'monto_teorico' => (float) ($_POST['monto_teorico']),
                'diferencia' => (float) ($_POST['diferencia']),
                'observaciones' => empty($_POST['observaciones']) ? null : $_POST['observaciones'],
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

    /**
     * Registra una entrega de dinero (gerente o depósito bancario)
     * 
     * Procesa el registro de entrega de dinero mediante petición POST con JSON.
     * Soporta dos tipos de entrega: a un gerente específico o a múltiples cuentas
     * bancarias. Valida campos según tipo de destino, verifica que la suma de
     * montos parciales coincida con el total (para depósitos múltiples), ejecuta
     * transacción ACID en el modelo, y actualiza automáticamente el estado de los
     * arqueos asociados a 'entregado'. Retorna respuesta JSON con el resultado.
     *
     * @throws \Exception Si faltan campos, datos inválidos o sumas no coinciden
     * @return void Retorna respuesta JSON con estructura:
     *   - success (bool): true si se registró correctamente, false en caso contrario
     *   - message (string): Mensaje descriptivo del resultado
     */
    public function entregar(): void
    {
        $this->authRequired();
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

    /**
     * Obtiene catálogo de destinos (gerentes o cuentas bancarias)
     * 
     * Retorna listado de destinos disponibles según el tipo solicitado.
     * Para tipo 'Gerente' retorna personal del área de gerencia activo.
     * Para tipo 'Deposito' retorna cuentas bancarias disponibles.
     * Usado para poblar selectores dinámicos en el formulario de entregas.
     *
     * @param string $tipo Tipo de destino: 'Gerente' o 'Deposito'
     * @return void Retorna respuesta JSON con estructura:
     *   - success (bool): true si se obtuvieron destinos, false si tipo inválido
     *   - destinos (array): Listado de destinos con estructura {id, nombre}
     *   - message (string): Mensaje de error si tipo no válido
     */
    public function destinos(string $tipo)
    {
        $this->authRequired();
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

    /**
     * Genera reporte de arqueo filtrado por sede específica
     * 
     * Obtiene reporte detallado de uno o más arqueos para una sede/local
     * particular mediante parámetros GET. Retorna datos consolidados (resumen)
     * e ingresos digitales de esa sede. Usado para generar reportes
     * descargables o visualización por sede.
     *
     * @return void Retorna respuesta JSON con estructura:
     *   - success (bool): true si se generó el reporte, false en caso contrario
     *   - reporte (array): Datos del reporte con {resumen, ingresos_digitales}
     *   - message (string): Mensaje de error si faltan parámetros o no hay datos
     */
    public function reportePorSede()
    {
        $this->authRequired();
        header('Content-Type: application/json');

        $ids_arqueo = $_GET['ids_arqueo'] ?? '';
        $idlocal = (int) ($_GET['idlocal'] ?? 0);

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

    /**
     * Genera reporte de arqueo agrupado por ciclo académico
     * 
     * Obtiene reporte completo de uno o más arqueos agrupando información
     * por ciclos académicos o períodos. Retorna tres conjuntos de datos:
     * resumen del arqueo, detalle de egresos por categoría, y detalle de
     * ingresos digitales. Usado para análisis histórico y auditorías por período.
     *
     * @param string $ids_arqueo IDs de arqueos separados por comas 
     * @return void Retorna respuesta JSON con estructura:
     *   - success (bool): true si se generó el reporte, false si no hay datos
     *   - data (array): Datos del reporte con {arqueo, egresos, ingresos_digitales}
     *   - message (string): Mensaje de error si no se encontraron datos
     */
    public function getReporteArqueoPorCiclo(string $ids_arqueo): void
    {
        $this->authRequired();
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
