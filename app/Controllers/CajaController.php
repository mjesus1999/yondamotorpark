<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Caja;


class CajaController extends Controller
{
    private Caja $cajaModel;


    public function __construct()
    {
        $this->cajaModel = new Caja();
    }

    // Me enlistara todos los contratos
    public function index(): void
    {
        $tiempoInicio = microtime(true);
        $datos = $this->cajaModel->getAllContratosDatos();

        $this->authRequired();
        $this->view('caja.index', ['contratos' => $datos]);

        $tiempoFin = microtime(true);
        $tiempoEjecucion = $tiempoFin - $tiempoInicio;

        error_log("Tiempo de ejecución de CAJA/Contratos: " . number_format($tiempoEjecucion, 4) . " segundos.");
    }

    // MEOTOD QUE ME MEUSTRA LA VISTA DE REPORTES POR FECHA:

    public function indexReporteByFecha()
    {
        $this->view('caja.reporte-by-fechas');
    }


    public function cronogramaByContrato(int $id): void
    {
        // Iniciar el cronómetro para medir el rendimiento
        $tiempoInicio = microtime(true);

        // Definir la ruta del archivo de caché y el tiempo de vida (TTL)
        $cacheFile = __DIR__ . "/../../storage/cache/cronograma-contratos/cronograma-contrato{$id}.json";
        $ttl = 3600; //  1 hora  en segundos

        $datos = [];

        // 1. Intentar cargar los datos desde la caché
        if (file_exists($cacheFile) && (filemtime($cacheFile) + $ttl > time())) {
            // El archivo de caché existe y no ha expirado
            $datos = json_decode(file_get_contents($cacheFile), true);
            error_log("Datos de CAJA/CRONOGRAMA cargados desde la caché para ID: {$id}");
        } else {
            // Si no hay caché, ejecutar la consulta a la base de datos
            $datos = $this->cajaModel->getCronogramaByIdContrato($id);
            // Guardar los resultados en la caché
            // Asegurarse de que el directorio exista
            if (!is_dir(dirname($cacheFile))) {
                mkdir(dirname($cacheFile), 0777, true);
            }
            file_put_contents($cacheFile, json_encode($datos));
            error_log("Datos de CAJA/CRONOGRAMA obtenidos de la BD y guardados en caché para ID: {$id}");
        }

        // Renderizar la vista con los datos
        $this->view('caja.cronograma', ['cronograma' => $datos]);

        // Detener el cronómetro y calcular el tiempo
        $tiempoFin = microtime(true);
        $tiempoEjecucion = $tiempoFin - $tiempoInicio;

        // Registrar el tiempo de ejecución en el log
        error_log("Tiempo de ejecución de CAJA/CRONOGRAMA: " . number_format($tiempoEjecucion, 4) . " segundos.");
    }



    public function getReporteIngresosCajaHoy()
    {
        header('Content-Type: application/json');
        $transacciones = $this->cajaModel->getReporteIngresosHoy();

        $reporteAgrupado = [];
        $totalGeneral = 0;

        foreach ($transacciones as $transaccion) {
            $metodoPago = $transaccion['metodo_pago'];
            $monto = (float)$transaccion['monto'];

            if (!isset($reporteAgrupado[$metodoPago])) {
                $reporteAgrupado[$metodoPago] = [
                    'metodo_pago' => $metodoPago,
                    'transacciones' => [],
                    'subtotal' => 0
                ];
            }

            $reporteAgrupado[$metodoPago]['transacciones'][] = $transaccion;

            // Sumamos al subtotal del grupo y al total general.
            $reporteAgrupado[$metodoPago]['subtotal'] += $monto;
            $totalGeneral += $monto;
        }

        $respuesta = [
            'success' => true,
            'data' => array_values($reporteAgrupado),
            'total_general' => $totalGeneral

        ];

        echo json_encode($respuesta);

        exit();
    }

    public function reportePagosByFecha()
    {
     
        header('Content-Type: application/json');

        $fechaInicio = $_GET['fecha_inicio'] ?? null;
        $fechaFin = $_GET['fecha_fin'] ?? null;

    
        if (!$fechaInicio || !$fechaFin) {
            http_response_code(400); 
            echo json_encode(['error' => 'Fechas de inicio y fin son requeridas.']);
            exit();
        }

        
        $datos = $this->cajaModel->getReporteByFecha($fechaInicio, $fechaFin);

        if ($datos) {
            echo json_encode([
                'success' => true,
                'data' => $datos
            ]);
        } else {
            
            http_response_code(404); 
            echo json_encode([
                'success' => false,
                'data' => [],
                'message' => 'No se encontraron datos para el rango de fechas proporcionado.'
            ]);
        }
        exit();
    }
}
