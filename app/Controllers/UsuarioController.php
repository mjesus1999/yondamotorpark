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
    echo json_encode($cargos);
  }

  public function storePersona(): int
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'error' => 'Método no permitido']);
      return 0;
    }

    // 1) Sanear input
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

    // 2) Validar
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
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Email inválido.';
    }
    if ($iddistrito <= 0)
      $errors[] = 'Debe seleccionar un distrito.';
    if (!$direccion)
      $errors[] = 'Dirección requerida.';
    /* if (!$referencia)
      $errors[] = 'Referencia requerida.'; */
    if (!$telprimario)
      $errors[] = 'Teléfono primario requerido.';

    if (!empty($errors)) {
      http_response_code(422);
      echo json_encode([
        'success' => false,
        'error' => implode(' ', $errors)
      ]);
      return -1;
    }

    // 3) Intentar insertar
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
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos al crear persona.'
      ]);
      return -2;
    }

    // 4) Responder éxito con JSON y devolver el ID
    if ($newId > 0) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode([
        'success' => true,
        'data' => [
          'idpersona' => $newId,
          'nrodoc' => $nrodoc,
          'apellidos' => $apellidos,
          'nombres' => $nombres,
        ]
      ]);
      return $newId;
    }

    // 5) Falla inesperada
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'error' => 'No se pudo crear la persona.'
    ]);
    return 0;
  }

  public function store(): int
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'error' => 'Método no permitido']);
      return 0;
    }

    // 1) Sanear
    $post = array_map('trim', $_POST);
    $idPersona = (int) ($post['idpersona'] ?? 0);
    $idCargo = (int) ($post['idcargo'] ?? 0);
    $fechaInicio = $post['fecha_inicio'] ?? '';
    $sinFechaFin = isset($_POST['sin_fecha_fin']);
    $fechaFin = $sinFechaFin ? null : ($post['fecha_fin'] ?? '');
    $usuario = $post['usuario'] ?? '';
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    // 2) Validar
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
      http_response_code(422);
      echo json_encode([
        'success' => false,
        'error' => implode(' ', $errors),
      ]);
      return -1;
    }

    // 3) Guardar en BD
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
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos al crear colaborador.',
      ]);
      return -2;
    }

    if (isset($res['idcolaborador']) && $res['idcolaborador'] > 0) {
      // Éxito
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode([
        'success' => true,
        'data' => $res,
      ]);
      return (int) $res['idcolaborador'];
    }

    // Caso inesperado
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'error' => 'No se pudo crear el colaborador.',
    ]);
    return 0;
  }

  public function searchByDNI(): void
  {
    $dni = trim($_GET['nrodoc'] ?? '');
    if ($dni === '') {
      http_response_code(422);
      echo json_encode(['success' => false, 'error' => 'Debe enviar nrodoc']);
      return;
    }

    $persona = $this->usuarioModel->searchByDNI($dni);
    if ($persona) {
      echo json_encode(['success' => true, 'data' => $persona]);
    } else {
      http_response_code(404);
      echo json_encode(['success' => false, 'error' => 'Persona no encontrada']);
    }
  }

  public function changePassword(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'error' => 'Método no permitido']);
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
      http_response_code(422);
      echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
      return;
    }

    $newHash = password_hash($pass1, PASSWORD_BCRYPT);
    try {
      $updated = $this->usuarioModel->updatePassword($idColaborador, $newHash);
      if ($updated) {
        echo json_encode(['success' => true]);
      } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo actualizar contraseña.']);
      }
    } catch (Exception $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'error' => 'Error del servidor.']);
    }
  }

}