<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Vehiculo;
use App\Models\Usuario;
use App\Models\Local;


//use App\Models\Product;

class VehiculoController extends Controller
{
  private Vehiculo $vehiculoModel;
  private Usuario $usuarioModel;
  private Local $localModel;
  public function __construct()
  {
    $this->vehiculoModel = new Vehiculo();
    $this->usuarioModel = new Usuario();
    $this->localModel = new Local();
  }


  public function index(): void
  {
    $this->authRequired();
    // ESTADO DEL VEHICULO
    $estado = $_GET['estado'] ?? 'proceso';
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
      'condicion' => $data['condicion'] ?? '',
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

  // STORE DEYANIRA:
  public function store(): void
  {
    $this->authRequired();
    header('Content-Type: application/json; charset=utf-8');

    $idcolaborador = $_SESSION['user']['id'];
    //SOLO PODRA REGISTRAR USUARIO DE LOGISTICA
    if (!$this->usuarioModel->esDeLogistica($idcolaborador)) {
      $_SESSION['error_message'] = 'Solo el personal de Logística puede registrar vehículos';
      header('Location: /vehiculos?estado=proceso');
      exit;
    }

    $local = $this->localModel->getByTienda('Chincha');
    $idlocal = $local['idlocal'] ?? 1; //CHINCHA POR DEFECTO - Temporal

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
      // obtener información del modelo (marca, tipo, modelo, año)
      $modeloDetalle = $this->vehiculoModel->getModeloDetalle((int) $vehiculo['idmodelo']);
      $this->view('vehiculos.edit', [
        'vehiculo' => $vehiculo,
        'modeloDetalle' => $modeloDetalle
      ]);
    } else {
      http_response_code(404);
      $this->view('error.404');
    }
  }

  public function update(): void
  {
    $this->authRequired();
    // recibir POST
    $idvehiculo = (int) ($_POST['idvehiculo'] ?? 0);
    if ($idvehiculo <= 0) {
      $_SESSION['error_message'] = 'ID de vehículo inválido';
      header('Location: /vehiculos');
      exit;
    }

    // validar idmodelo
    $idmodelo = (int) ($_POST['idmodelo'] ?? 0);
    if ($idmodelo <= 0) {
      $_SESSION['error_message'] = 'Debe seleccionar modelo y año válidos';
      header('Location: /vehiculos/edit/' . $idvehiculo);
      exit;
    }

    // tomar version (ya sea select o input, según el JS)
    $version = trim((string) ($_POST['version'] ?? ''));

    $data = [
      'idmodelo' => $idmodelo,
      'version' => $version,
      'condicion' => $_POST['condicion'] ?? null,
      'idcombustible' => (int) ($_POST['combustible'] ?? 0),
      'color' => $_POST['color'] ?? null,
      'chasis' => $_POST['chasis'] ?? null,
      'placa' => $_POST['placa'] ?? null,
      'placarotativa' => $_POST['placarotativa'] ?? null,
      'seriemotor' => $_POST['seriemotor'] ?? null,
      'moneda' => $_POST['moneda'] ?? 'USD',
      'precioventa' => $_POST['precio'] ?? 0,
    ];

    try {
      $ok = $this->vehiculoModel->update($idvehiculo, $data);
      if ($ok) {
        $_SESSION['success_message'] = 'Vehículo actualizado correctamente';
      } else {
        $_SESSION['error_message'] = 'No se pudo actualizar el vehículo';
      }
    } catch (\Exception $e) {
      $_SESSION['error_message'] = 'Error al actualizar: ' . $e->getMessage();
    }

    header('Location: /vehiculos?estado=proceso');
    exit;
  }


}
