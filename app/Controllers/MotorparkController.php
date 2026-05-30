<?php

/**
 * Controlador de Motorpark
 * 
 * app/Controllers/MotorparkController.php
 * 
 * Gestiona las peticiones HTTP relacionadas con los motorpark
 * Proporciona endpoints API para consultar la información
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Motorpark;

/**
 * Clase MotorparkController
 * 
 * Controlador para la gestión de motorparks.
 * Hereda de Controller para acceder a funcionalidades base como
 * renderizado de vistas, validación de autenticación y manejo de sesiones.
 * Expone endpoints API REST para operaciones de consulta.
 */
class MotorparkController extends Controller
{
  /**
   * Instancia del modelo Motorpark
   * @var Motorpark
   */
  private Motorpark $motorParkModel;

  /**
   * Constructor del controlador
   * 
   * Inicializa la instancia del modelo Motorpark para
   * realizar operaciones de base de datos
   */
  public function __construct()
  {
    $this->motorParkModel = new Motorpark();
  }

  /**
   * Página principal de motorparks
   * 
   * Renderiza la vista principal del módulo de motorpark.
   * @return void
   */
  public function index(): void
  {
    // $locales = $this->motorParkModel->getMotorPark();
    // $this->view('locales.index', ['locales' => $locales]);
  }

  /**
   * Endpoint API: Obtener listado de motorparks
   * 
   * Retorna la lista completa de motorparks en formato JSON.
   * Requiere autenticación previa del usuario mediante token de sesión.
   * 
   * Restricciones:
   * - Usuario debe estar autenticado
   * 
   * @return void Envía respuesta JSON y termina la ejecución
   */
  public function getMotorPark(): void
  {
    $this->authRequired();
    header('Content-Type: application/json');
    $motorpark = $this->motorParkModel->getMotorPark();

    if ($motorpark) {
      echo json_encode(['success' => true, 'motorpark' => $motorpark]);
    } else {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => 'No se encontro motorpark']);
    }
    exit();
  }
}
