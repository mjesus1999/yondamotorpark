<?php
// app/Controllers/UsuarioController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\ContratoLaboral;
use App\Models\Colaborador;
use App\Helpers\Validation;

use Exception;

class UsuarioController extends Controller
{
  private Usuario $usuarioModel;
  private ContratoLaboral $contratoModel;
  private Colaborador $colaboradorModel;
  private Validation $validator;

  public function __construct()
  {
    parent::__construct(); // INICIO DE SESION H/CONTROLLER
    $this->usuarioModel = new Usuario();
    $this->contratoModel = new ContratoLaboral();
    $this->colaboradorModel = new Colaborador();
    $this->validator = new Validation();
  }

  public function index(): void
  {
    $this->authRequired();
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

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($cargos);
    exit;
  }

  public function store(): void
  {
    /* if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    } */

    // DEBUG TEMPORAL: id de sesión y usuario en logs
    //error_log('STORE START - SID=' . session_id() . ' USER=' . json_encode($_SESSION['user'] ?? null));

    //obtener datos del formulario
    $idPersona = (int) ($_POST['idpersona'] ?? 0);
    $idCargo = (int) ($_POST['idcargo'] ?? 0);
    $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
    $fechaFin = isset($_POST['sin_fecha_fin']) ? null : trim($_POST['fecha_fin'] ?? null);
    $usernick = trim($_POST['usuario'] ?? '');
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    //detectar AJAX (para el modal) de registro de personas
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

    //validaciones desde Helpers
    $dataForValidation = [
      'idpersona' => $idPersona,
      'idcargo' => $idCargo,
      'fecha_inicio' => $fechaInicio,
      'fecha_fin' => $fechaFin,
      'usuario' => $usernick,
      'password1' => $pass1,
      'password2' => $pass2
    ];
    $errors = $this->validator->validateUsuarioData($dataForValidation, $this->usuarioModel);

    if (!empty($errors)) {
      if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
      }
      $areas = $this->usuarioModel->getAllAreas();
      $this->view('usuarios.create', [
        'areas' => $areas,
        'error' => implode('<br>', $errors),
        'old' => $_POST
      ]);
      return;
    }

    //Crear contrato laboral
    try {
      $idContrato = $this->contratoModel->create(
        $idPersona,
        $idCargo,
        $fechaInicio,
        $fechaFin,
        'P'
      );
      if ($idContrato <= 0) {
        throw new \RuntimeException('No se pudo crear el contrato laboral');
      }
    } catch (\Throwable $e) {
      //error_log('Error al crear contrato: ' . $e->getMessage());
      $msg = 'No se pudo crear el contrato laboral. Intente nuevamente.';
      if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'errors' => [$msg]]);
        exit;
      }
      $areas = $this->usuarioModel->getAllAreas();
      $this->view('usuarios.create', [
        'areas' => $areas,
        'error' => $msg,
        'old' => $_POST
      ]);
      return;
    }

    //Crear colaborador (con rollback si falla) / por el momento
    try {
      $passwordHash = password_hash($pass1, PASSWORD_DEFAULT);
      $restr = 'S';
      $idColab = $this->colaboradorModel->create($idContrato, $usernick, $passwordHash, $restr);

      if (empty($idColab) || $idColab <= 0) {
        if (method_exists($this->contratoModel, 'delete') && is_callable([$this->contratoModel, 'delete'])) {
          try {
            $this->contratoModel->delete($idContrato);
          } catch (\Throwable $ex) {
            //error_log('Rollback fail: ' . $ex->getMessage());
          }
        }
        throw new \RuntimeException('No se pudo crear el usuario');
      }
    } catch (\Throwable $e) {
      //error_log('Error al crear colaborador: ' . $e->getMessage());
      $msg = 'No se pudo crear el usuario. Revisa los logs o contacta al administrador.';
      if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'errors' => [$msg]]);
        exit;
      }
      $areas = $this->usuarioModel->getAllAreas();
      $this->view('usuarios.create', [
        'areas' => $areas,
        'error' => $msg,
        'old' => $_POST
      ]);
      return;
    }

    if ($isAjax) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode([
        'success' => true,
        'idcolaborador' => $idColab,
        'idcontrato' => $idContrato
      ]);
      exit;
    }

    /* if (session_status() !== PHP_SESSION_ACTIVE)
      session_start(); */
    $_SESSION['success_message'] = "Usuario creado con éxito. ID colaborador: {$idColab}";

    $areas = $this->usuarioModel->getAllAreas();
    $this->view('usuarios.create', [
      'areas' => $areas,
      'success' => $_SESSION['success_message'],
      'old' => []
    ]);
    return;
  }

  //STORE ANTERIORO (NO ESTA EN USO)
  public function store1(): void
  {
    // 1) Recoger todo lo del formulario completo
    $idPersona = (int) ($_POST['idpersona'] ?? 0);
    $idCargo = (int) ($_POST['idcargo'] ?? 0);
    $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
    $fechaFin = isset($_POST['sin_fecha_fin'])
      ? null
      : trim($_POST['fecha_fin'] ?? null);
    $usernick = trim($_POST['usuario'] ?? '');
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    // 2) Validaciones
    $errors = [];
    if ($idPersona <= 0)
      $errors[] = 'Debe registrar primero la persona.';
    if ($idCargo <= 0)
      $errors[] = 'Debe seleccionar un cargo.';
    if ($fechaInicio === '')
      $errors[] = 'La fecha de inicio es obligatoria.';
    if ($pass1 !== $pass2)
      $errors[] = 'Las contraseñas no coinciden.';

    //peticion ajax
    $isAjax =
      !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
      && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

    if ($errors) {
      if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        $response = ['success' => false];
        $response['errors'] = $errors;
        echo json_encode($response);
        exit;
      }
      // flujo normal (no-AJAX)
      $areas = $this->usuarioModel->getAllAreas();
      $this->view('usuarios.create', [
        'areas' => $areas,
        'error' => implode('<br>', $errors),
        'old' => $_POST
      ]);
      return;

    }

    // 3) Crear contrato laboral
    $idContrato = $this->contratoModel->create(
      $idPersona,
      $idCargo,
      $fechaInicio,
      $fechaFin,
      'P'
    );
    if ($idContrato <= 0) {
      throw new Exception('No se pudo crear el contrato laboral');
    }

    // 4) Crear colaborador
    $passwordHash = password_hash($pass1, PASSWORD_DEFAULT);
    $idColab = $this->colaboradorModel->create(
      $idContrato,
      $usernick,
      $passwordHash
    );
    if ($idColab <= 0) {
      throw new Exception('No se pudo crear el usuario');
    }

    // 5) Respuesta exitosa
    if ($isAjax) {
      header('Content-Type: application/json; charset=utf-8');
      $response = ['success' => true];
      $response['idcolaborador'] = $idColab;
      $response['idcontrato'] = $idContrato;
      echo json_encode($response);
      exit;
    }

    // flujo normal
    $_SESSION['success_message'] = "Usuario creado con éxito. ID colaborador: {$idColab}";
    $this->redirect('/usuarios/create');
    return;
  }

  public function changePassword(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $idColab = (int) ($_POST['idcolaborador'] ?? 0);
    $p1 = $_POST['password1'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    $errors = $this->validator->validateChangePassword([
      'idcolaborador' => $idColab,
      'password1' => $p1,
      'password2' => $p2
    ]);
    if (!empty($errors)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'errors' => $errors]);
      return;
    }

    try {
      $newHash = password_hash($p1, PASSWORD_DEFAULT);
      $ok = $this->usuarioModel->updatePassword($idColab, $newHash);

      if ($ok) {
        $_SESSION['success_message'] = 'Contraseña actualizada correctamente.';
        echo json_encode(['success' => true]);
      } else {
        http_response_code(500);
        $_SESSION['error_message'] = 'No se pudo actualizar la contraseña.';
        echo json_encode(['success' => false, 'errors' => ['No se pudo actualizar la contraseña.']]);
      }
    } catch (\Throwable $e) {
      error_log('Error changePassword: ' . $e->getMessage());
      http_response_code(500);
      echo json_encode(['success' => false, 'errors' => ['Error interno al procesar la solicitud.']]);
    }
  }

  //ACTUALIZAR CONTRASEÑAS PERO POR EL MOMENTO NO ESTA EN USO
  public function changePassword1(): void
  {
    /* if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    } */
    header('Content-Type: application/json; charset=utf-8');
    $idColab = (int) ($_POST['idcolaborador'] ?? 0);
    $p1 = $_POST['password1'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    if ($idColab <= 0 || $p1 === '' || $p1 !== $p2) {
      $_SESSION['error_message'] = 'Datos inválidos o contraseñas no coinciden.';
      echo json_encode(['success' => false]);
      return;
    }

    $newHash = password_hash($p1, PASSWORD_DEFAULT);
    $ok = $this->usuarioModel->updatePassword($idColab, $newHash);

    if ($ok) {
      $_SESSION['success_message'] = 'Contraseña actualizada correctamente.';
      echo json_encode(['success' => true]);
    } else {
      $_SESSION['error_message'] = 'No se pudo actualizar la contraseña.';
      echo json_encode(['success' => false]);
    }
  }

  public function disabled(int $id): void
  {
    $disabled = $this->usuarioModel->disabled($id);
    if ($disabled) {
      $_SESSION['success_message'] = 'Usuario deshabilitado correctamente.';
    } else {
      $_SESSION['error_message'] = 'No se pudo deshabilitar el usuario.';
    }
    $this->redirect('/usuarios');
  }

  // PERFIL DEL USUARIO
  public function profile(): void
  {
    $this->authRequired();
    //ID por URL, recógelo: $id = (int) $params['id'];
    $idColab = $_SESSION['user']['id'];

    $usuario = $this->usuarioModel->getById($idColab);
    $this->view('usuarios.profile', ['usuario' => $usuario]);
  }

  public function uploadAvatar(): void
  {
    $this->authRequired();
    header('Content-Type: application/json; charset=utf-8');
    $avatarErrors = $this->validator->validateAvatarUpload($_FILES['avatar'] ?? null);
    if ($avatarErrors) {
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'error' => implode(' ', $avatarErrors)
      ]);
      return;
    }

    //Definimos la carpeta
    $avatarsDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/avatar';

    //Creacion de la carpeta si no eciste
    if (!is_dir($avatarsDir)) {
      mkdir($avatarsDir, 0755, true);
    }
    $tmp = $_FILES['avatar']['tmp_name'];
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $id = $_SESSION['user']['id'];
    $filename = "avatar_{$id}." . $ext;
    $dest = $avatarsDir . '/' . $filename;

    //Mueve el archivo
    if (!move_uploaded_file($tmp, $dest)) {
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'error' => 'No se pudo guardar archivo'
      ]);
      return;
    }

    //URL pública para la imagen
    $avatarUrl = '/assets/images/avatar/' . $filename;

    //se actualiza la url en la BD
    $ok = $this->usuarioModel->updateAvatar($id, $avatarUrl);
    $_SESSION['user']['avatar'] = $avatarUrl;
    if (!$ok) {
      echo json_encode([
        'success' => false,
        'error' => 'No se pudo actualizar BD'
      ]);
      return;
    }

    echo json_encode([
      'success' => true,
      'avatarUrl' => $avatarUrl
    ]);
  }

  /**
   * FUNCIONES PARA MOSTRAR VISTAS Y FORMULARIOS PARA REGISTRAR CONTRATOS DESDE EL LOGIN
   * CREAR CUENTA (SOLO EL ADMIN)
   */

  //Mostrar Crear Cuenta

  public function showCreateFromContracts(): void
  {
    $contracts = $this->usuarioModel->getContractsWithoutColaborador();

    $success = $_SESSION['success_message'] ?? null;
    unset($_SESSION['success_message']);

    $this->view('usuarios.createAccount', [
      'contracts' => $contracts,
      'success' => $success
    ]);
  }

  //CREAR COLABORADOR CON PERSONAS QUE TIENEN CONTRATO PERO NO UNA CUENTA 
  public function createFromContract(): void
  {
    /* if (session_status() !== PHP_SESSION_ACTIVE)
      session_start(); */
    $prevUser = $_SESSION['user'] ?? null;

    $idContrato = (int) ($_POST['idcontrato'] ?? 0);
    $usernick = trim($_POST['usernick'] ?? '');
    $p1 = $_POST['password1'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    $errors = $this->validator->validateCreateFromContract([
      'idcontrato' => $idContrato,
      'usernick' => $usernick,
      'password1' => $p1,
      'password2' => $p2
    ], $this->usuarioModel);

    $contracts = $this->usuarioModel->getContractsWithoutColaborador();

    if ($errors) {
      $this->view('usuarios.createAccount', [
        'contracts' => $contracts,
        'error' => implode('<br>', $errors),
        'old' => $_POST
      ]);
      return;
    }

    // recoger restriccionhoraria (valor esperado 'S' o 'N')
    $restr = (isset($_POST['restriccionhoraria']) && $_POST['restriccionhoraria'] === 'N') ? 'N' : 'S';

    //crear colaborador
    try {
      $passwordHash = password_hash($p1, PASSWORD_DEFAULT);
      $idColab = $this->colaboradorModel->create($idContrato, $usernick, $passwordHash, $restr);
      if ($idColab <= 0)
        throw new \RuntimeException('No se pudo crear colaborador.');

    } catch (\Throwable $e) {
      error_log('Error al crear colaborador: ' . $e->getMessage());

      // restaurar sesion original si hacia falta
      if ($prevUser !== null)
        $_SESSION['user'] = $prevUser;
      $this->view('usuarios.createAccount', [
        'contracts' => $contracts,
        'error' => 'No se pudo crear la cuenta. Contacta al administrador.',
        'old' => $_POST
      ]);
      return;
    }
    // restaurar sesion original (evita login automatico del nuevo usuario)
    if ($prevUser !== null) {
      $_SESSION['user'] = $prevUser;
    }

    // actualizar lista
    /* if (session_status() !== PHP_SESSION_ACTIVE)
      session_start(); */
    $_SESSION['success_message'] = "Cuenta creada correctamente para <strong>" . htmlspecialchars($usernick) . "</strong>";
    $this->redirect('/createAccount');
    return;
  }

}