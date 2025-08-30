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
   
    $this->view('marcas.index',['marcas' => $data]);
  }










  
  // API PARA TRAER LAS MARCAS:

  public function getMarcasDB():void {
    header('Content-Type: application/json');

    $marcas = $this->marcaModel->getAll();

    if($marcas) {echo json_encode($marcas);}
    else {http_response_code(404); echo json_encode([]);}
    exit();

  }


}