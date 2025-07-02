<?php
// app/Controllers/UsuarioController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class UsuarioController extends Controller
{

  private Usuario $usuarioModel;

  public function __construct()
  {
    $this->usuarioModel = new Usuario();
  }

  public function index(): void
  {
    $usuario = $this->usuarioModel->getAll();
    $this->view('usuarios.index', ['Usuarios' => $usuario]);
  }

  public function create(): void
  {
    $areas = $this->usuarioModel->getAllAreas();
    $this->view('usuarios.create', ['areas' => $areas]);
  }

  public function getCargosByArea(): void
  {
    $idArea = isset($_GET['idarea']) ? (int) $_GET['idarea'] : 0;
    $cargos = $this->usuarioModel->getCargosByArea($idArea);
    echo json_encode($cargos);
  }

  public function storePersona(): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $tipodoc = trim($_POST['tipodoc'] ?? '');
      $nrodoc = trim($_POST['nrodoc'] ?? '');
      $apellidos = trim($_POST['apellidos'] ?? '');
      $nombres = trim($_POST['nombres'] ?? '');
      $genero = trim($_POST['genero'] ?? '');
      $fechanac = trim($_POST['fechanac'] ?? '');
      $estadocivil = trim($_POST[''] ?? '');
      $email = trim($_POST['email'] ?? '');
      $iddistrito = trim($_POST['iddistrito'] ?? '');
      $direccion = trim($_POST['direccion'] ?? '');
      $referencia = trim($_POST['referencia'] ?? '');
      $telprimario = trim($_POST['telprimario'] ?? '');
      $telalternativo = trim($_POST['telalternativo'] ?? '');

      if ($tipodoc && $nrodoc && $apellidos && $nombres && $genero && $fechanac && $estadocivil && $email && $iddistrito && $direccion && $referencia && $telprimario && $telalternativo) {
        if ($this->usuarioModel->createPersona($tipodoc, $nrodoc, $apellidos, $nombres, $genero, $fechanac, $estadocivil, $email, $iddistrito, $direccion, $referencia, $telprimario, $telalternativo)) {
          $this->redirect('/usuarios');
        } else {
          $this->view('usuarios.create', ['error' => 'Error al Registrar a una Personas']);

        }
      } else {
        $this->view('usuarios.create', ['error' => 'Llene los campos necesarios']);
      }
    }
  }

}