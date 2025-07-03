<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Ubigeo;

class UbigeoController extends Controller
{
  private Ubigeo $model;

  public function __construct()
  {
    $this->model = new Ubigeo();
  }

  /**
   * GET /ubigeo/departamentos
   */
  public function getAllDepartamentos(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($this->model->getAllDepartamentos());
  }

  /**
   * GET /ubigeo/provincias?iddepartamento=#
   */
  public function getAllProvincias(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    $idDep = isset($_GET['iddepartamento']) ? (int) $_GET['iddepartamento'] : 0;
    echo json_encode($this->model->getAllProvincias($idDep));
  }

  /**
   * GET /ubigeo/distritos?idprovincia=#
   */
  public function getAllDistritos(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    $idProv = isset($_GET['idprovincia']) ? (int) $_GET['idprovincia'] : 0;
    echo json_encode($this->model->getAllDistritos($idProv));
  }

  /**
   * GET /ubigeo/distritos/all
   */
  public function getAllDistritosAll(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($this->model->getAllDistritosAll());
  }
}
