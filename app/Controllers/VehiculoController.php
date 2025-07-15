<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;
use App\Models\Marca;
use App\Models\TipoVehiculo;


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

    $this->view('vehiculos.create', compact(
      'clientes',
      'marcas',
      'tipovehiculos'
    ));
  }

}