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
    // 2) Recuperar campos
    $tipodoc = trim($_POST['tipodoc'] ?? '');
    $nrodoc = trim($_POST['nrodoc'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $nombres = trim($_POST['nombres'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $fechanac = trim($_POST['fechanac'] ?? '');
    $estadocivil = trim($_POST['estadocivil'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $iddistrito = (int) trim($_POST['iddistrito'] ?? 0);
    $direccion = trim($_POST['direccion'] ?? '');
    $referencia = trim($_POST['referencia'] ?? '');
    $telprimario = trim($_POST['telprimario'] ?? '');
    $telalternativo = trim($_POST['telalternativo'] ?? '');

    // 3) Validar
    $errors = [];
    if (!$tipodoc)
      $errors[] = 'Tipo de documento requerido.';
    if (!$nrodoc)
      $errors[] = 'Número de documento requerido.';
    if (!$apellidos)
      $errors[] = 'Apellidos requeridos.';
    if (!$nombres)
      $errors[] = 'Nombres requeridos.';
    if (!$genero)
      $errors[] = 'Género requerido.';
    if (!$fechanac)
      $errors[] = 'Fecha de nacimiento requerida.';
    if (!$estadocivil)
      $errors[] = 'Estado civil requerido.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Email inválido.';
    }
    if ($iddistrito <= 0)
      $errors[] = 'Debe seleccionar un distrito.';
    if (!$direccion)
      $errors[] = 'Dirección requerida.';
    if (!$referencia)
      $errors[] = 'Referencia requerida.';
    if (!$telprimario)
      $errors[] = 'Teléfono primario requerido.';

    if ($errors) {
      http_response_code(422);
      echo json_encode([
        'success' => false,
        'error' => implode(' ', $errors),
      ]);
      exit;
    }

    // 4) Grabar en BD
    try {
      $newId = $this->usuarioModel->createPersona(
        $tipodoc,
        $nrodoc,
        $apellidos,
        $nombres,
        $genero,
        $fechanac,
        $estadocivil,
        $email,
        $iddistrito,
        $direccion,
        $referencia,
        $telprimario,
        $telalternativo
      );
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode([
        'success' => false,
        // Opcional: en desarrollo muestra $e->getMessage()
        'error' => 'Error en la base de datos: ' . $e->getMessage()
      ]);
      exit;
    }

    if ($newId > 0) {
      http_response_code(201);
      echo json_encode([
        'success' => true,
        'data' => [
          'idpersona' => $newId,
          'nrodoc' => $nrodoc,
          'apellidos' => $apellidos,
          'nombres' => $nombres,
        ]
      ]);
      exit;
    } else {
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => 'Error interno al registrar la persona.',
      ]);
      exit;
    }
  }

}