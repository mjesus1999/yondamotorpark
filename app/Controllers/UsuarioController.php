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
    $fechaFin = isset($_POST['sin_fecha_fin']) ? null : trim($_POST['fecha_fin'] ?? null);
    $tipoContrato = 'P'; // por defecto ahora esta como planilla (cambiar si se debe)

    $usernick = trim($_POST['usuario'] ?? '');
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    // 2) Validaciones
    $errors = [];
    if ($idPersona <= 0)
      $errors[] = 'Debe registrar primero la persona.';
    if ($idCargo <= 0)
      $errors[] = 'Debe seleccionar un cargo.';
    if (empty($fechaInicio))
      $errors[] = 'La fecha de inicio es obligatoria.';
    if ($pass1 !== $pass2)
      $errors[] = 'Las contraseñas no coinciden.';

    if ($errors) {
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
      $tipoContrato
    );
    if ($idContrato <= 0) {
      throw new Exception('No se pudo crear el contrato laboral');
    }

    // 4) Crear colaborador (hashea la contraseña primero)
    $passwordHash = password_hash($pass1, PASSWORD_DEFAULT);
    $idColab = $this->colaboradorModel->create(
      $idContrato,
      $usernick,
      $passwordHash
    );
    if ($idColab <= 0) {
      throw new Exception('No se pudo crear el usuario');
    }

    // 5) Éxito y redirección
    $_SESSION['success_message'] = "Usuario creado con éxito. ID colaborador: {$idColab}";
    $this->redirect('/usuarios');
  }

  public function changePassword(): void
  {
    header('Content-Type: application/json; charset=utf-8');

    $idColab = (int) ($_POST['idcolaborador'] ?? 0);
    $p1 = $_POST['password1'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    if ($idColab <= 0 || $p1 === '' || $p1 !== $p2) {
      echo json_encode([
        'success' => false,
        'error' => 'Datos inválidos o contraseñas no coinciden.'
      ]);
      return;
    }

    $newHash = password_hash($p1, PASSWORD_DEFAULT);
    $ok = $this->usuarioModel->updatePassword($idColab, $newHash);

    if ($ok) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode([
        'success' => false,
        'error' => 'No se pudo actualizar la contraseña.'
      ]);
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

  // PERFIL DEL USUARIO
  public function profile(): void
  {
    $this->authRequired();
    //ID por URL, recógelo: $id = (int) $params['id'];
    $idColab = $_SESSION['user']['id'];

    //Datos completos del usuario
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

    // 6) Responde éxito y nueva URL
    echo json_encode([
      'success' => true,
      'avatarUrl' => $avatarUrl
    ]);
  }

}