<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\TipoVehiculo;
use App\Models\Vehiculo;
use App\Models\FormatoCotizacion;


class VehiculoController extends Controller
{

  public function __construct()
  {
  }

  public function index(): void
  {
    $this->authRequired();
    $this->view('vehiculos.index');
  }

  public function create(): void
  {
    $this->authRequired();

    $clientes = (new Cliente())->getAll();
    $marcas = (new Marca())->getAll();
    $tipovehiculos = (new TipoVehiculo())->getAll();
    $formatos = (new FormatoCotizacion())->getAll();

    $this->view('vehiculos.create', compact(
      'clientes',
      'marcas',
      'tipovehiculos',
      'formatos'
    ));
  }

  public function getVehiculosDisponibles(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    $m = isset($_GET['marca']) ? (int) $_GET['marca'] : 0;
    $t = isset($_GET['tipo']) ? (int) $_GET['tipo'] : 0;
    $mo = $_GET['modelo'] ?? '';
    $a = $_GET['anio'] ?? '';

    if ($m <= 0 || $t <= 0 || $mo === '' || $a === '') {
      echo json_encode(['success' => false, 'vehiculos' => []]);
      exit;
    }

    $vehModel = new Vehiculo();
    $lista = $vehModel->getDisponibles($m, $t, $mo, $a);

    echo json_encode(['success' => true, 'vehiculos' => $lista], JSON_UNESCAPED_UNICODE);
    exit;
  }

}