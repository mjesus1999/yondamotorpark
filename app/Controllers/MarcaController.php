<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Marca;
//use App\Models\Product;

class MarcaController extends Controller
{
  private Marca $model;

  public function __construct()
  {
    $this->model = new Marca();
  }

  public function index(): void
  {
    $this->authRequired();
    $this->view('marcas.index');
  }

  public function getAll(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    $lista = $this->model->getAll();
    echo json_encode([
      'success' => true,
      'marcas' => $lista
    ], JSON_UNESCAPED_UNICODE);
    exit;
  }

}