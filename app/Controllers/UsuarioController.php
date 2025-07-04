<?php
// app/Controllers/UsuarioController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use Exception;

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
    $this->view('usuarios.cargos', ['cargos' => $cargos]); // Asegúrate de tener la vista 'usuarios.cargos'
  }

  public function storePersona(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      $this->view('errors.405'); // Vista de error para método no permitido
      return;
    }

    // Sanear input
    $post = array_map('trim', $_POST);
    $tipodoc = $post['tipodoc'] ?? '';
    $nrodoc = $post['nrodoc'] ?? '';
    $apellidos = $post['apellidos'] ?? '';
    $nombres = $post['nombres'] ?? '';
    $genero = $post['genero'] ?? '';
    $fechanac = $post['fechanac'] ?? '';
    $estadocivil = $post['estadocivil'] ?? '';
    $email = $post['email'] ?? '';
    $iddistrito = (int) ($post['iddistrito'] ?? 0);
    $direccion = $post['direccion'] ?? '';
    $referencia = $post['referencia'] ?? '';
    $telprimario = $post['telprimario'] ?? '';
    $telalternativo = $post['telalternativo'] ?? '';

    // Validación
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
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))
      $errors[] = 'Email inválido.';
    if ($iddistrito <= 0)
      $errors[] = 'Debe seleccionar un distrito.';
    if (!$direccion)
      $errors[] = 'Dirección requerida.';
    if (!$telprimario)
      $errors[] = 'Teléfono primario requerido.';

    if (!empty($errors)) {
      $this->view('usuarios.create', ['errors' => $errors, 'data' => $_POST]);
      return;
    }

    // Intentar insertar en la base de datos
    try {
      $newId = $this->usuarioModel->createPersona(
        $tipodoc,
        $nrodoc,
        $apellidos,
        $nombres,
        $genero,
        $fechanac,
        $estadocivil,
        $email ?: null,
        $iddistrito,
        $direccion ?: null,
        $referencia ?: null,
        $telprimario,
        $telalternativo ?: null
      );
    } catch (Exception $e) {
      $this->view('errors.db_error'); // Vista de error en base de datos
      return;
    }

    // Si todo sale bien, redirigimos a la lista de usuarios
    if ($newId > 0) {
      $this->redirect('/usuarios');
    } else {
      $this->view('errors.unknown_error');
    }
  }

  public function store(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      $this->view('errors.405'); // Vista de error para método no permitido
      return;
    }

    // Sanear input
    $post = array_map('trim', $_POST);
    $idPersona = (int) ($post['idpersona'] ?? 0);
    $idCargo = (int) ($post['idcargo'] ?? 0);
    $fechaInicio = $post['fecha_inicio'] ?? '';
    $sinFechaFin = isset($_POST['sin_fecha_fin']);
    $fechaFin = $sinFechaFin ? null : ($post['fecha_fin'] ?? '');
    $usuario = $post['usuario'] ?? '';
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    // Validación
    $errors = [];
    if ($idPersona <= 0)
      $errors[] = 'Primero registre la persona.';
    if ($idCargo <= 0)
      $errors[] = 'Área/Cargo requerido.';
    if (!$fechaInicio)
      $errors[] = 'Fecha de inicio requerida.';
    if (!$sinFechaFin && !$fechaFin)
      $errors[] = 'Fecha fin o indeterminado requerido.';
    if (!$usuario)
      $errors[] = 'Usuario requerido.';
    if (!$pass1 || !$pass2)
      $errors[] = 'Ambas contraseñas son requeridas.';
    if ($pass1 !== $pass2)
      $errors[] = 'Las contraseñas no coinciden.';
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/', $pass1)) {
      $errors[] = 'La contraseña no cumple el patrón de seguridad.';
    }

    if (!empty($errors)) {
      $this->view('usuarios.create', ['errors' => $errors, 'data' => $_POST]);
      return;
    }

    // Guardar en la base de datos
    $contratoData = [
      'idcargo' => $idCargo,
      'fechainicio' => $fechaInicio,
      'fechafin' => $fechaFin,
      'tipocontrato' => 'P',
    ];
    $colaboradorData = [
      'usernick' => $usuario,
      'userpassword' => password_hash($pass1, PASSWORD_BCRYPT),
    ];

    try {
      $res = $this->usuarioModel->create($idPersona, $contratoData, $colaboradorData);
    } catch (Exception $e) {
      $this->view('errors.db_error');
      return;
    }

    if (isset($res['idcolaborador']) && $res['idcolaborador'] > 0) {
      $this->redirect('/usuarios');
    } else {
      $this->view('errors.unknown_error');
    }
  }

  public function searchByDNI(): void
  {
    $dni = trim($_GET['nrodoc'] ?? '');
    if ($dni === '') {
      http_response_code(422);
      $this->view('errors.missing_dni'); // Vista para error de falta de DNI
      return;
    }

    $persona = $this->usuarioModel->searchByDNI($dni);
    if ($persona) {
      $this->view('usuarios.search', ['persona' => $persona]);
    } else {
      $this->view('errors.persona_not_found'); // Vista para error de persona no encontrada
    }
  }

  public function changePassword(): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      $this->view('errors.405');
      return;
    }

    $idColaborador = isset($_POST['idcolaborador']) ? (int) $_POST['idcolaborador'] : 0;
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    $errors = [];
    if ($idColaborador <= 0)
      $errors[] = 'Usuario inválido.';
    if (!$pass1 || !$pass2)
      $errors[] = 'Ambas contraseñas son requeridas.';
    if ($pass1 !== $pass2)
      $errors[] = 'Las contraseñas no coinciden.';
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/', $pass1)) {
      $errors[] = 'La contraseña no cumple el patrón de seguridad.';
    }

    if (!empty($errors)) {
      $this->view('errors.validation', ['errors' => $errors]);
      return;
    }

    $newHash = password_hash($pass1, PASSWORD_BCRYPT);
    try {
      $updated = $this->usuarioModel->updatePassword($idColaborador, $newHash);
      if ($updated) {
        $this->redirect('/usuarios');
      } else {
        $this->view('errors.db_error');
      }
    } catch (Exception $e) {
      $this->view('errors.server_error');
    }
  }

  public function delete(int $id): void
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      $this->view('errors.405');
      return;
    }

    try {
      if ($this->usuarioModel->delete($id)) {
        $this->redirect('/usuarios');
      } else {
        $this->view('errors.delete_failed');
      }
    } catch (Exception $e) {
      $this->view('errors.server_error');
    }
  }
  
}