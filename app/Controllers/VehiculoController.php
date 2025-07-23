<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Vehiculo;


//use App\Models\Product;

class VehiculoController extends Controller
{
  private Vehiculo $vehiculoModel;

  public function __construct()
  {
    $this->vehiculoModel = new Vehiculo();
  }



  public function storeVehiculoOC(): int
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Método no permitido']);
      exit;
    }

    header('Content-Type: application/json');

    $data = array_map([Validador::class, 'limpiar'], $_POST);

    $registro = [
      'idmodelo' => $data['idmodelo'] ?? '',
      'idcombustible' => $data['idcombustible'] ?? '',
      'version' => $data['version'] ?? '',
      'color' => $data['color'] ?? '',
      'chasis' => $data['chasis'] ?? '',
      'placa' => $data['placa'] ?? '',
      'placarotativa' => $data['placarotativa'] ?? '',
      'seriemotor' => $data['seriemotor'] ?? ''
    ];

    $errores = [];

    $errores[] = Validador::campoObligatorio($registro['idmodelo'], 'Modelo');
    $errores[] = Validador::campoObligatorio($registro['idcombustible'], 'Combustible');
    $errores[] = Validador::campoObligatorio($registro['version'], 'Versión');
    $errores = array_filter($errores);
    
    if (!empty($errores)) {
      echo json_encode([
        'success' => false,
        'message' => implode(',', $errores),
      ]);
      exit();
    }

    $idVehiculo = $this->vehiculoModel->createVehiculoOC($registro);

    if ($idVehiculo > 0) {
      echo json_encode([
        'success' => true,
        'message' => '¡Creado exitosamente!',
        'id' => $idVehiculo
      ]);
      exit;
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'No se pudo crear el vehículo',
        'id' => $idVehiculo
      ]);
      exit;
    }
  }


}
