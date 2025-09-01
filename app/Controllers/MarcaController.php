<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Marca;

class MarcaController extends Controller
{
  private Marca $marcaModel;

  public function __construct()
  {
    $this->marcaModel = new Marca();
  }

  public function index(): void
  {
    $this->authRequired();
    $data = $this->marcaModel->getAll();

    $this->view('marcas.index', ['marcas' => $data]);
  }

  public function store()
  {
    $marca = $_POST['marca'] ?? '';

    if (trim($marca) === '') {
      $_SESSION['error'] = "La marca no puede estar vacía";
      $this->redirect('/marcas');
      return;
    }

    $id = $this->marcaModel->create($marca);

    if ($id > 0) {
      $_SESSION['success'] = "Marca registrada correctamente";
    } else {
      $_SESSION['error'] = "Error al registrar la marca";
    }

    $this->redirect('/marcas');
  }










  // API PARA TRAER LAS MARCAS:

  public function getMarcasDB(): void
  {
    header('Content-Type: application/json');

    $marcas = $this->marcaModel->getAll();

    if ($marcas) {
      echo json_encode($marcas);
    } else {
      http_response_code(404);
      echo json_encode([]);
    }
    exit();
  }
}
