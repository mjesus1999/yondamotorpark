<?php
// app/Controllers/VehiculoController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Vehiculo;
use App\Models\Local;
use App\Models\Usuario;

class VehiculoController extends Controller
{

  private Vehiculo $vehiculoModel;
  private Local $localModel;
  private Usuario $usuarioModel;

  public function __construct()
  {
    $this->vehiculoModel = new Vehiculo();
    $this->localModel = new Local();
    $this->usuarioModel = new Usuario();
  }

  public function index(): void
  {
    $this->authRequired();
    // ESTADO DEL VEHICULO
    $estado = $_GET['estado'] ?? 'libre';
    $allwed = ['libre', 'proceso', 'separado', 'vendido'];
    if (!in_array($estado, $allwed, true)) {
      $estado = 'libre';
    }

    $vehiculos = $this->vehiculoModel->getAll($estado);
    $this->view(
      'vehiculos.index',
      [
        'vehiculos' => $vehiculos,
        'estadoActual' => $estado,
      ]
    );
  }

  public function create(): void
  {
    $this->authRequired();
    $this->view('vehiculos.create');
  }

  public function store(): void
  {
    $this->authRequired();
    header('Content-Type: application/json; charset=utf-8');

    $idcolaborador = $_SESSION['user']['id'];
    /* if (!$this->usuarioModel->esDeLogistica($idcolaborador)) {
      $_SESSION['error_message'] = 'Solo el personal de Logística puede registrar vehículos';
      header('Location: /vehiculos?estado=proceso');
      exit;
    } */

    $local = $this->localModel->getByTienda('Chincha');
    $idlocal = $local['idlocal'] ?? 3; //CHINCHA POR DEFECTO - Temporal

    $chasis = $_POST['chasis'] ?? null;
    $placa = $_POST['placa'] ?? null;
    $placaRotativa = $_POST['placarotativa'] ?? null;
    $serieMotor = $_POST['seriemotor'] ?? null;

    $idmodelo = (int) ($_POST['idmodelo'] ?? 0);
    if ($idmodelo <= 0) {
      echo json_encode(['error' => 'Debe elegir modelo y año válidos'], JSON_UNESCAPED_UNICODE);
      exit;
    }
    $version = trim((string) ($_POST['version'] ?? ''));
    if ($version === '') {
      echo json_encode(['error' => 'Debe indicar una versión válida'], JSON_UNESCAPED_UNICODE);
      exit;
    }

    $condicion = $_POST['condicion'];
    $idcombustible = (int) ($_POST['combustible'] ?? 0);
    $color = $_POST['color'] ?? null;
    $moneda = $_POST['moneda'] ?? 'USD';
    $precioventa = $_POST['precio'];

    try {
      $data = [
        'idmodelo' => $idmodelo,
        'version' => $version,
        'condicion' => $condicion,
        'idcombustible' => $idcombustible,
        'color' => $color,
        'chasis' => $chasis,
        'placa' => $placa,
        'placarotativa' => $placaRotativa,
        'seriemotor' => $serieMotor,
        'moneda' => $moneda,
        'precioventa' => $precioventa,
        'idlogistica' => $idcolaborador,
        'idlocal' => $idlocal,
      ];
      $this->vehiculoModel->create($data);

      $_SESSION['success_message'] = 'Vehículo(s) registrados correctamente';
      header('Location: /vehiculos?estado=proceso');
      exit;

    } catch (\Exception $e) {
      $_SESSION['error_message'] = 'Error al registrar vehículos: ' . $e->getMessage();
      header('Location: /vehiculos/create');
      exit;
    }
  }

  public function delete(): void
  {
    $this->authRequired();
    header('Content-Type: application/json; charset=utf-8');

    $idvehiculo = (int) ($_POST['idvehiculo'] ?? 0);

    if ($idvehiculo <= 0) {
      echo json_encode(['error' => 'ID de vehículo inválido']);
      exit;
    }

    try {
      $success = $this->vehiculoModel->delete($idvehiculo);

      if ($success) {
        echo json_encode(['success' => 'Vehículo eliminado correctamente']);
      } else {
        echo json_encode(['error' => 'No se pudo eliminar el vehículo']);
      }

    } catch (\Exception $e) {
      echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
  }

  public function edit(int $id): void
  {
    $vehiculo = $this->vehiculoModel->getById($id);
    if ($vehiculo) {
      $this->view('vehiculos.edit', ['vehiculo' => $vehiculo]);
    } else {
      http_response_code(404);
      $this->view('error.404');
    }
  }

}