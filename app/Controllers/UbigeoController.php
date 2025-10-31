<?php

/**
 * Controlador de Ubigeo
 * 
 * app/Controllers/UbigeoController.php
 * 
 * Gestiona los endpoints API para consulta de datos geográficos del Perú.
 * Proporciona información jerárquica de departamentos, provincias y distritos
 * en formato JSON para uso en selectores dependientes y formularios.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ubigeo;

/**
 * Clase UbigeoController
 * 
 * Controlador API para la consulta de ubicación geográfica del Perú.
 * Todos los métodos son endpoints AJAX que retornan datos en formato JSON,
 * típicamente utilizados para poblar selectores dependientes en formularios.
 * 
 */
class UbigeoController extends Controller
{

    /**
     * Modelo de Ubigeo 
     * @var Ubigeo
     */
    private Ubigeo $ubigeoModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa el modelo de Ubigeo necesario para las operaciones
     * del controlador.
     */
    public function __construct()
    {
        $this->ubigeoModel = new Ubigeo();
    }

    /**
     * API: Obtiene todos los departamentos del Perú
     * 
     * Endpoint AJAX que retorna la lista completa de departamentos
     * ordenados alfabéticamente en formato JSON. Usado como primer nivel
     * en selectores de ubicación geográfica.
     * 
     * Respuesta HTTP:
     * - 200 OK: Array de departamentos en formato JSON
     * 
     * Formato de respuesta:
     * [
     *   {"iddepartamento": "1", "departamento": "Amazonas"}
     * ]
     * @return void
     */
    public function departamentos(): void
    {
        $departamentos = $this->ubigeoModel->getAllDepartamentos();


        header('Content-Type: application/json');

        http_response_code(200);

        echo json_encode($departamentos);

        exit;
    }

    /**
     * API: Obtiene las provincias de un departamento específico
     * 
     * Endpoint AJAX que retorna las provincias de un departamento dado,
     * ordenadas alfabéticamente en formato JSON. Usado como segundo nivel
     * en selectores dependientes de ubicación.
     * 
     * Validaciones:
     * - ID del departamento debe ser mayor a 0
     * 
     * Respuestas HTTP:
     * - 200 OK: Array de provincias en formato JSON
     * - 400 Bad Request: ID de departamento inválido
     * 
     * @param int $iddepartamento ID del departamento del cual obtener sus provincias
     * @return void
     */
    public function provincias(int $iddepartamento): void
    {
        // Validación básica del ID
        if ($iddepartamento <= 0) {
            header('Content-Type: application/json');
            http_response_code(400); // 400 Bad Request
            echo json_encode(['error' => 'ID de departamento inválido']);
            exit;
        }

        $provincias = $this->ubigeoModel->getAllProvincias($iddepartamento);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($provincias);
        exit;
    }

    /**
     * API: Obtiene los distritos de una provincia específica
     * 
     * Endpoint AJAX que retorna los distritos de una provincia dada,
     * ordenados alfabéticamente en formato JSON. Usado como tercer nivel
     * en selectores dependientes de ubicación.
     * 
     * Validaciones:
     * - ID de la provincia debe ser mayor a 0
     * 
     * 
     * @param int $idprovincia ID de la provincia de la cual obtener sus distritos
     * @return void
     */
    public function distritos(int $idprovincia): void
    {
        // Validación básica del ID
        if ($idprovincia <= 0) {
            header('Content-Type: application/json');
            http_response_code(400); // 400 Bad Request
            echo json_encode(['error' => 'ID de provincia inválido']);
            exit;
        }

        $distritos = $this->ubigeoModel->getAllDistritos($idprovincia);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($distritos);
        exit;
    }

    /**
     * API: Obtiene todos los distritos del Perú
     * 
     * Endpoint AJAX que retorna la lista completa de todos los distritos
     * del Perú sin filtrar por provincia o departamento, ordenados alfabéticamente.
     * Útil para búsquedas generales o cuando no se requiere jerarquía geográfica.
     * 
     * @return void
     */
    public function getAllDistritosAll(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->ubigeoModel->getAllDistritosAll());
    }
}
