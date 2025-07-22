<?php
// app/Controllers/VehiculoController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Vehiculo;
use App\Models\Local;

class VehiculoController extends Controller
{

  private Vehiculo $vehiculoModel;
  private Local $localModel;

  public function __construct()
  {
    $this->vehiculoModel = new Vehiculo();
    $this->localModel = new Local();
  }

  public function index(): void
  {
    $this->authRequired();
    $this->view('vehiculos.index');
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

    //usuario logueado
    $idlogistica = $_SESSION['user']['id'];

    //Buscar dinámicamente el local “Chincha” -> termporal
    $localModel = new Local();
    $local = $localModel->getByTienda('Chincha');
    $idlocal = $local['idlocal'] ?? 5;

    //Arrays dinámicos
    $chasisArr = $_POST['chasis'] ?? [];
    $placaArr = $_POST['placa'] ?? [];
    $placaRotArr = $_POST['placa_rotativa'] ?? [];
    $serieArr = $_POST['serie'] ?? [];

    $idmodelo = (int) ($_POST['idmodelo'] ?? 0);
    if ($idmodelo <= 0) {
      echo json_encode(['error' => 'Debe elegir modelo y año válidos'], JSON_UNESCAPED_UNICODE);
      exit;
    }
    $version = ($_POST['version-ls'] === 'ESP')
      ? ($_POST['version-in'] ?? '')
      : $_POST['version-ls'];
    $condicion = $_POST['condicion'];
    $idcombustible = (int) ($_POST['combustible'] ?? 0);
    $color = $_POST['color'] ?? null;
    $moneda = $_POST['moneda'] ?? 'USD';
    $precioventa = $_POST['precio'];

    $results = [];
    foreach ($chasisArr as $i => $chasis) {
      $data = [
        'idmodelo' => $idmodelo,
        'version' => $version,
        'condicion' => $condicion,
        'idcombustible' => $idcombustible,
        'color' => $color,
        'chasis' => $chasis,
        'placa' => $placaArr[$i] ?? null,
        'placarotativa' => $placaRotArr[$i] ?? null,
        'seriemotor' => $serieArr[$i] ?? null,
        'moneda' => $moneda,
        'precioventa' => $precioventa,
        'idlogistica' => $idlogistica,
        'idlocal' => $idlocal
      ];
      $insertId = $this->vehiculoModel->create($data);
      $results[] = $insertId > 0
        ? ['success' => true, 'id' => $insertId]
        : ['success' => false, 'error' => "Error en fila {$i}"];
    }

    $_SESSION['flash_success'] = 'Vehículos registrados correctamente';
    header('Location: /vehiculos');
    exit;

    /* echo json_encode(['results' => $results], JSON_UNESCAPED_UNICODE);
    exit; */
  }

}