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

  /* public function store(): void
  {
    // Solo procesar POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // 1) Capturar y sanear
      $idPersona = (int) trim($_POST['idpersona'] ?? 0);
      $idCargo = (int) trim($_POST['idcargo'] ?? 0);
      $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
      $sinFin = isset($_POST['sin_fecha_fin']);
      $fechaFin = trim($_POST['fecha_fin'] ?? '');
      $usuario = trim($_POST['usuario'] ?? '');
      $pass1 = $_POST['password1'] ?? '';
      $pass2 = $_POST['password2'] ?? '';

      // 2) Validar
      $valid = true;
      $error = '';
      if ($idPersona <= 0) {
        $valid = false;
        $error = 'Debe registrar primero la persona.';
      } elseif ($idCargo <= 0) {
        $valid = false;
        $error = 'Área y cargo requeridos.';
      } elseif (!$fechaInicio) {
        $valid = false;
        $error = 'Fecha de inicio requerida.';
      } elseif (!$sinFin && !$fechaFin) {
        $valid = false;
        $error = 'Fecha fin o “Indeterminado” requerido.';
      } elseif (!$usuario) {
        $valid = false;
        $error = 'Usuario requerido.';
      } elseif (!$pass1 || !$pass2) {
        $valid = false;
        $error = 'Ambas contraseñas son requeridas.';
      } elseif ($pass1 !== $pass2) {
        $valid = false;
        $error = 'Las contraseñas no coinciden.';
      } elseif (
        !preg_match(
          '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/',
          $pass1
        )
      ) {
        $valid = false;
        $error = 'La contraseña no cumple el patrón de seguridad.';
      }

      // 3) Si pasó validación, insertar; si no, mostrar error
      if ($valid) {
        $contratoData = [
          'idcargo' => $idCargo,
          'fechainicio' => $fechaInicio,
          'fechafin' => $sinFin ? null : $fechaFin,
          'tipocontrato' => 'P',
        ];
        $colaboradorData = [
          'usernick' => $usuario,
          'userpassword' => password_hash($pass1, PASSWORD_BCRYPT),
        ];

        if ($this->usuarioModel->create($idPersona, $contratoData, $colaboradorData)) {
          // Redirige al listado
          $this->redirect('/usuarios');
        } else {
          // Error de inserción
          $areas = $this->usuarioModel->getAllAreas();
          $this->view('usuarios.create', [
            'areas' => $areas,
            'error' => 'Error al registrar el usuario.'
          ]);
        }
      } else {
        // Muestra el formulario con mensaje de validación
        $areas = $this->usuarioModel->getAllAreas();
        $this->view('usuarios.create', [
          'areas' => $areas,
          'error' => $error,
          'old' => $_POST,  // para repoblar si quieres
        ]);
      }
    } else {
      // Si entran por GET, mostrar el formulario
      $areas = $this->usuarioModel->getAllAreas();
      $this->view('usuarios.create', ['areas' => $areas]);
    }
  } */

  public function store(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'error' => 'Método no permitido']);
      return;
    }

    $post = array_map('trim', $_POST);
    $idPersona = (int) ($post['idpersona'] ?? 0);
    $idCargo = (int) ($post['idcargo'] ?? 0);
    $fechaInicio = $post['fecha_inicio'] ?? '';
    $sinFechaFin = isset($_POST['sin_fecha_fin']);
    $fechaFinInput = $post['fecha_fin'] ?? '';
    $usuario = $post['usuario'] ?? '';
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    $errors = [];
    if ($idPersona <= 0)
      $errors[] = 'Persona no registrada.';
    if ($idCargo <= 0)
      $errors[] = 'Área/cargo requerido.';
    if (!$fechaInicio)
      $errors[] = 'Fecha de inicio requerida.';
    if (!$sinFechaFin && !$fechaFinInput)
      $errors[] = 'Fecha fin o indeterminado.';
    if (!$usuario)
      $errors[] = 'Usuario requerido.';
    if (!$pass1 || !$pass2)
      $errors[] = 'Ambas contraseñas son requeridas.';
    if ($pass1 !== $pass2)
      $errors[] = 'Las contraseñas no coinciden.';
    if (
      !preg_match(
        '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/',
        $pass1
      )
    )
      $errors[] = 'La contraseña no cumple el patrón.';

    if ($errors) {
      http_response_code(422);
      echo json_encode([
        'success' => false,
        'error' => implode(' ', $errors)
      ]);
      return;
    }

    // prepara datos
    $contratoData = [
      'idcargo' => $idCargo,
      'fechainicio' => $fechaInicio,
      'fechafin' => $sinFechaFin ? null : $fechaFinInput,
      'tipocontrato' => 'P',
    ];
    $colaboradorData = [
      'usernick' => $usuario,
      'userpassword' => password_hash($pass1, PASSWORD_BCRYPT),
    ];

    // intenta guardar
    try {
      $this->usuarioModel->create($idPersona, $contratoData, $colaboradorData);

      http_response_code(201);
      echo json_encode([
        'success' => true,
        // le decimos al cliente a dónde ir
        'redirect' => '/usuarios'
      ]);
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => 'Error en el servidor: ' . $e->getMessage()
      ]);
    }
  }

  public function searchByDNI(): void
  {
    $dni = trim($_GET['nrodoc'] ?? '');
    if ($dni === '') {
      http_response_code(422);
      echo json_encode([
        'success' => false,
        'error' => 'Debe enviar un nrodoc'
      ]);
      return;
    }

    $persona = $this->usuarioModel->searchByDNI($dni);
    if ($persona) {
      echo json_encode([
        'success' => true,
        'data' => $persona
      ]);
    } else {
      http_response_code(404);
      echo json_encode([
        'success' => false,
        'error' => 'Persona no encontrada'
      ]);
    }
  }

  public function changePassword(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    // 1) Validar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'error' => 'Método no permitido']);
      return;
    }

    // 2) Recoger datos
    $idColaborador = isset($_POST['idcolaborador'])
      ? (int) $_POST['idcolaborador']
      : 0;
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    // 3) Validar inputs
    $errors = [];
    if ($idColaborador <= 0)
      $errors[] = 'Usuario inválido.';
    if (!$pass1 || !$pass2)
      $errors[] = 'Ambas contraseñas son requeridas.';
    if ($pass1 !== $pass2)
      $errors[] = 'Las contraseñas no coinciden.';
    if (
      !preg_match(
        '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/',
        $pass1
      )
    )
      $errors[] = 'La contraseña no cumple el patrón de seguridad.';

    if ($errors) {
      http_response_code(422);
      echo json_encode([
        'success' => false,
        'error' => implode(' ', $errors)
      ]);
      return;
    }

    // 4) Hash y actualizar
    $newHash = password_hash($pass1, PASSWORD_BCRYPT);
    try {
      $updated = $this->usuarioModel->updatePassword($idColaborador, $newHash);
      if ($updated) {
        echo json_encode(['success' => true]);
      } else {
        http_response_code(500);
        echo json_encode([
          'success' => false,
          'error' => 'No se pudo actualizar la contraseña.'
        ]);
      }
    } catch (\Exception $e) {
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
      ]);
    }
  }

}