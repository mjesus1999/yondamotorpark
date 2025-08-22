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

        $this->view('caja.index', ['contratos' => $datos]);

        $tiempoFin = microtime(true);
        $tiempoEjecucion = $tiempoFin - $tiempoInicio;

        error_log("Tiempo de ejecución de CAJA/Contratos: " . number_format($tiempoEjecucion, 4) . " segundos.");
    }

    public function cronogramaByContrato(int $id): void
    {
        // Iniciar el cronómetro para medir el rendimiento
        $tiempoInicio = microtime(true);

        // Definir la ruta del archivo de caché y el tiempo de vida (TTL)
        $cacheFile = __DIR__ . "/../../storage/cache/cronograma-contrato{$id}.json";
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

    // public function cronogramaByContrato(int $id): void
    // {
    //     $tiempoInicio = microtime(true);
    //     $cacheFile = "storage/cache/cronograma-contrato{$id}.json";
    //     $ttl = 86400; //Definir 24 horas en segundos.
    //     $datos = [];

    //     if (file_exists($cacheFile) && filemtime($cacheFile) + $ttl > time()) {
    //         $datos = json_decode(file_get_contents($cacheFile),true);
    //         error_log("Datos de CAJA/CRONOGRAMA cargados desde la caché para ID: {$id}");
    //     } else {

    //     }

    //     $datos = $this->cajaModel->getCronogramaByIdContrato($id);
    //     $this->view('caja.cronograma', ['cronograma' => $datos]);
    //     $tiempoFin = microtime(true);
    //     $tiempoEjecucion = $tiempoFin - $tiempoInicio;

    //     error_log("Tiempo de ejecución de CAJA/CRONOGRAMA: " . number_format($tiempoEjecucion, 4) . " segundos.");
    // }
}
