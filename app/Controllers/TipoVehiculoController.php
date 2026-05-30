<?php

/**
 * Controlador de Tipo de Vehículo
 * 
 * app/Controllers/TipoVehiculoController.php
 * 
 * Gestiona las operaciones relacionadas con los tipos de vehículos,
 * incluyendo listados generales y filtrados por marca.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\TipoVehiculo;

/**
 * Clase TipoVehiculoController
 * 
 * Controlador para la gestión de tipos de vehículos.
 * Proporciona endpoints para obtener listados de tipos de vehículos,
 * tanto generales como filtrados por marca específica.
 * 
 */
class TipoVehiculoController extends Controller
{

    /**
     * Instancia de Tipo vehiculo
     * @var TipoVehiculo
     */
    private TipoVehiculo $tipoModel;

    /**
     * Constructor del controlador 
     * 
     * Inicializa la instancia del modelo de Tipo de Vehiculo.
     */
    public function __construct()
    {
        $this->tipoModel = new TipoVehiculo();
    }

    /**
     * Obtiene todos los tipos de vehículos
     * 
     * Endpoint AJAX que retorna el listado completo de tipos de vehículos
     * en formato JSON, ordenados alfabéticamente.
     * @return void Respuesta JSON con success=true y array de tipos
     * 
     * @uses TipoVehiculo::getAll() Para obtener todos los tipos
     * 
     * @api
     * @httpmethod GET
     * @response 200 JSON con success=true y data con tipos de vehículos
     * @response 200 JSON con success=false si no hay tipos disponibles
     * 
     * @example GET /tipos/getall
     */
    public function getall(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $data = $this->tipoModel->getAll();

        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron tipos de vehículo']);
        }
    }

    /**
     * Obtiene los tipos de vehículos asociados a una marca especifica
     * 
     * Endpoint AJAX que retorna los tipos de vehiculos que tienen modelos
     * registrados para una marca determinada. Útil para filtrar tipos
     * en cascada al seleccionar una marca
     * 
     * @param int $id ID de la marca a consultar
     * @return void Respuesta JSON con array de tipos de vehículos
     * 
     * @uses TipoVehiculo::getTipoVehiculoByMarca() Para obtener tipos filtrados
     * 
     * @example GET /api/getTipoVehiculoByMarca/2
     */
    public function getTipoVehiculoByMarca(int $id): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        $tipos = $this->tipoModel->getTipoVehiculoByMarca($id);

        if ($tipos) {
            echo json_encode($tipos);
        } else {
            echo json_encode([]);
        }
    }
}