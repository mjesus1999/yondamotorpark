<?php
// app/Controllers/UsuarioController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\ContratoLaboral;
use App\Models\Colaborador;
use Exception;

class UsuarioController extends Controller
{
  private Usuario $usuarioModel;
  private ContratoLaboral $contratoModel;
  private Colaborador $colaboradorModel;

  public function __construct()
  {
    $this->usuarioModel = new Usuario();
    $this->contratoModel = new ContratoLaboral();
    $this->colaboradorModel = new Colaborador();
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
    $_SESSION['success_message'] =
      "Usuario creado con éxito. ID colaborador: {$idColab}";
    $this->redirect('/usuarios');
  }

  public function changePassword(): void
  {
    session_start(); //tener sesión iniciada

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

    if (
      !isset($_FILES['avatar']) ||
      $_FILES['avatar']['error'] !== UPLOAD_ERR_OK
    ) {
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'error' => 'Archivo no recibido'
      ]);
      return;
    }

    //Definimos la carpeta
    $avatarsDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/avatar';

    //Creacion de la carpeta si no eciste
    if (!is_dir($avatarsDir)) {
      mkdir($avatarsDir, 0755, true);
    }

    //Se le desgina un nombre al avatar
    $tmp = $_FILES['avatar']['tmp_name'];
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $id = $_SESSION['user']['id'];
    $filename = "avatar_{$id}." . $ext;
    $dest = $avatarsDir . '/' . $filename;


    //Mueve el archivo ?
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
  public function showCreateFromContractsAuth(): void
  {
    //$this->authRequired(); // si solo administradores deben acceder
    $contracts = $this->usuarioModel->getContractsWithoutColaborador();
    $this->view('auth.createAccount', ['contracts' => $contracts]);
  }
  public function showCreateFromContracts(): void
  {
    $contracts = $this->usuarioModel->getContractsWithoutColaborador();
    $this->view('usuarios.createAccount', ['contracts' => $contracts]);
  }

  //CREAR CONTRATO (Dentro de auth/usuario->createAccount)
  public function createFromContract(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE)
      session_start();
    $prevUser = $_SESSION['user'] ?? null;

    $idContrato = (int) ($_POST['idcontrato'] ?? 0);
    $usernick = trim($_POST['usernick'] ?? '');
    $p1 = $_POST['password1'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    $errors = [];
    if ($idContrato <= 0)
      $errors[] = 'Selecciona un contrato.';
    if ($usernick === '')
      $errors[] = 'Introduce un nombre de usuario.';
    if ($p1 === '' || $p2 === '')
      $errors[] = 'Introduce y confirma la contraseña.';
    if ($p1 !== $p2)
      $errors[] = 'Las contraseñas no coinciden.';
    if (strlen($p1) < 8)
      $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    if ($this->usuarioModel->searchByUsernick($usernick))
      $errors[] = 'El usernick ya existe.';

    $contracts = $this->usuarioModel->getContractsWithoutColaborador();

    if ($errors) {
      $this->view('usuarios.createAccount', [
        'contracts' => $contracts,
        'error' => implode('<br>', $errors),
        'old' => $_POST
      ]);
      return;
    }

    // crear colaborador
    try {
      $passwordHash = password_hash($p1, PASSWORD_DEFAULT);
      $idColab = $this->colaboradorModel->create($idContrato, $usernick, $passwordHash);
      if (!$idColab)
        throw new \RuntimeException('No se pudo crear colaborador.');
    } catch (\Throwable $e) {
      error_log('Error al crear colaborador: ' . $e->getMessage());
      // restaurar sesión original si hacía falta
      if ($prevUser !== null)
        $_SESSION['user'] = $prevUser;
      $this->view('usuarios.createAccount', [
        'contracts' => $contracts,
        'error' => 'No se pudo crear la cuenta. Contacta al administrador.',
        'old' => $_POST
      ]);
      return;
    }

    // restaurar sesión original (evita login automático del nuevo usuario)
    if ($prevUser !== null) {
      $_SESSION['user'] = $prevUser;
    } else {
      unset($_SESSION['user']);
    }

    // actualizar lista
    $contracts = $this->usuarioModel->getContractsWithoutColaborador();
    $this->view('usuarios.createAccount', [
      'contracts' => $contracts,
      'success' => "Cuenta creada correctamente para <strong>" . htmlspecialchars($usernick) . "</strong>"
    ]);
  }

  public function createFromContractAuth(): void
  {
    $idContrato = (int) ($_POST['idcontrato'] ?? 0);
    $usernick = trim($_POST['usernick'] ?? '');
    $p1 = $_POST['password1'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    $errors = [];

    //algunas validaciones para el registro
    if ($idContrato <= 0) {
      $errors[] = 'Selecciona un contrato.';
    }
    if ($usernick === '') {
      $errors[] = 'Introduce un nombre de usuario.';
    }
    if ($p1 === '' || $p2 === '') {
      $errors[] = 'Introduce y confirma la contraseña.';
    }
    if ($p1 !== $p2) {
      $errors[] = 'Las contraseñas no coinciden.';
    }
    if (strlen($p1) < 8) {
      $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    //Comprobar si ya existe el usernick
    $exists = $this->usuarioModel->searchByUsernick($usernick);
    if ($exists) {
      $errors[] = 'El usernick ya existe.';
    }

    if ($errors) {
      $contracts = $this->usuarioModel->getContractsWithoutColaborador();
      $this->view('usuarios.createAccount', [
        'contracts' => $contracts,
        'error' => implode('<br>', $errors),
        'old' => $_POST
      ]);
      return;
    }

    // Crear colaborador: delegar creación al modelo correspondiente
    $passwordHash = password_hash($p1, PASSWORD_DEFAULT);

    try {
      $idColab = $this->colaboradorModel->create($idContrato, $usernick, $passwordHash);
    } catch (\Throwable $e) {
      error_log('Error al crear colaborador: ' . $e->getMessage());
      $contracts = $this->usuarioModel->getContractsWithoutColaborador();
      $this->view('usuarios.createAccount', [
        'contracts' => $contracts,
        'error' => 'No se pudo crear la cuenta. Contacta al administrador.',
        'old' => $_POST
      ]);
      return;
    }

    if (empty($idColab) || $idColab <= 0) {
      $contracts = $this->usuarioModel->getContractsWithoutColaborador();
      $this->view('usuarios.createAccount', [
        'contracts' => $contracts,
        'error' => 'No se pudo crear la cuenta. Revisa los logs.',
        'old' => $_POST
      ]);
      return;
    }

    // Obtener datos reales desde la BD para poblar la sesión
    $full = $this->usuarioModel->getById((int) $idColab);

    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    $_SESSION['user'] = [
      'id' => $full['idcolaborador'] ?? $idColab,
      'usernick' => $full['usernick'] ?? $usernick,
      'nombres' => $full['nombres'] ?? ($_POST['nombres'] ?? ''),
      'apellidos' => $full['apellidos'] ?? ($_POST['apellidos'] ?? ''),
      'avatar' => $full['avatar'] ?? '/assets/images/profile.jpg',
      'idcargo' => $full['idcargo'] ?? '',
      'cargo' => $full['cargo'] ?? '',
    ];

    header('Location: /');
    exit;
  }


  /* public function delete(int $id): void
  {
    $deleted = $this->usuarioModel->delete($id);
    if ($deleted) {
      $_SESSION['success_message'] = 'Usuario eliminado correctamente.';
    } else {
      $_SESSION['error_message'] = 'No se pudo eliminar el usuario.';
    }
    $this->redirect('/usuarios');
  } */
}