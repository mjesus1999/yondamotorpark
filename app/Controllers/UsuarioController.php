<?php

/**
 * Controlador de Usuarios
 * 
 * Summary of namespace App\Controllers
 * app/Controllers/UsuarioController.php
 * 
 * Gestiona todas las Operaciones relacionada con usuarios, colaboradores, contratos laborales y autenticacion en el sistema
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\ContratoLaboral;
use App\Models\Colaborador;
use App\Helpers\Validation;
use App\Models\Local;

/**
 * Clase UsuarioController
 * 
 * Controlador principal para la gestion de usuarios del sistema.
 * Maneja operaciones CRUD, autenticacion, perfiles y restricciones horarias.
 */

class UsuarioController extends Controller
{

  /**
   * Modelo de Usuario
   * @var Usuario
   */
  private Usuario $usuarioModel;
  
  /**
   * Modelo de Contrato laboral
   * @var ContratoLaboral
   */
  private ContratoLaboral $contratoModel;

  /**
   * Modelo de Colaborador
   * @var Colaborador
   */
  private Colaborador $colaboradorModel;

  /**
   * Modelo de Validacion
   * @var Validation
   */
  private Validation $validator;

  /**
   * Modelo de Locales
   * @var Local
   */
  private Local $localModel;


  /**
   * Constructor del controlador 
   * 
   * Inicializa todos los modelos y helpers necesarios para el funcionamiento del controlador.
   * Tambien ajusta la inicializacion de sesion del controlador padre.
   */
  public function __construct()
  {
    parent::__construct(); // INICIO DE SESION H/CONTROLLER
    $this->usuarioModel = new Usuario();
    $this->contratoModel = new ContratoLaboral();
    $this->colaboradorModel = new Colaborador();
    $this->validator = new Validation();
    $this->localModel = new Local();
  }

  /**
   * Muestra el listado de todos los usuarios
   * 
   * Renderiza la vista principal con todos los usuarios del sistema.
   * Requiere autenticacion previa.
   * @return void
   */
  public function index(): void
  {
    $this->authRequired();
    $usuario = $this->usuarioModel->getAll();
    $this->view('usuarios.index', ['Usuarios' => $usuario]);
  }

  /**
   * Muestra el formulario de creacion de usuario
   * 
   * Carga las areas y locales disponibles para mostrarlos en el formulario
   * de registro de nuevo usuario.
   * @return void
   */
  public function create(): void
  {
    $areas = $this->usuarioModel->getAllAreas();
    /* $locales = $this->usuarioModel->getAllLocales(); */
    $locales = $this->localModel->getAllLocales();

    $this->view('usuarios.create', [
      'areas' => $areas,
      'locales' => $locales
    ]);
  }

  /**
   * Obtiene los cargos filtrados por área
   * 
   * EndPoint AJAX que devuelve los cargos disponibles para un area especifica en formato JSON
   * @return never
   */
  public function getCargosByArea(): void
  {
    $idArea = isset($_GET['idarea']) ? (int) $_GET['idarea'] : 0;
    $cargos = $this->usuarioModel->getCargosByArea($idArea);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($cargos);
    exit;
  }

  /**
   * Almacena un nuevo usuario en el sistema
   * 
   * Procesa el formulario de registro, valida los datos, crea el contrato laboral y el colaborador asociado
   * Soporta llamadas AJAX y tradicionales.
   * Implementa rollback en caso de error
   * 
   * @throws \RuntimeException
   * @return void
   */
  public function store(): void
  {
    //obtener datos del formulario
    $idPersona = (int) ($_POST['idpersona'] ?? 0);
    $idCargo = (int) ($_POST['idcargo'] ?? 0);
    $idLocal = !empty($_POST['idlocal']) ? (int) $_POST['idlocal'] : null;
    $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
    $fechaFin = isset($_POST['sin_fecha_fin']) ? null : trim($_POST['fecha_fin'] ?? null);
    $usernick = trim($_POST['usuario'] ?? '');
    $pass1 = $_POST['password1'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    // detectar AJAX (para el modal)
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

    // helper local para responder errores (JSON o renderizar vista)
    /**
     * Helper local para responder errores
     * 
     * @param array $errors Array de mensajes error
     * @return void
     */
    $respondError = function (array $errors) use ($isAjax): void {
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
    };

    // validaciones
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
      $respondError($errors);
      return;
    }

    // Crear contrato laboral
    try {
      $idContrato = $this->contratoModel->create($idPersona, $idCargo, $fechaInicio, $fechaFin, 'P');
      if ($idContrato <= 0) {
        throw new \RuntimeException('No se pudo crear el contrato laboral');
      }
    } catch (\Throwable $e) {
      $respondError(['No se pudo crear el contrato laboral. Intente nuevamente.']);
      return;
    }

    // Crear colaborador con rollback si falla
    try {
      $passwordHash = password_hash($pass1, PASSWORD_DEFAULT);
      $restr = 'S';
      $idColab = $this->colaboradorModel->create($idContrato, $usernick, $passwordHash, $restr, $idLocal);

      if (empty($idColab) || $idColab <= 0) {
        // rollback contrato si existe método delete
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
      $respondError(['No se pudo crear el usuario. Revisa los logs o contacta al administrador.']);
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
    $_SESSION['success_message'] = "Usuario creado con éxito. ID colaborador: {$idColab}";

    $areas = $this->usuarioModel->getAllAreas();
    $this->view('usuarios.create', [
      'areas' => $areas,
      'success' => $_SESSION['success_message'],
      'old' => []
    ]);
  }

  /**
   * Cambia la contraseña de un colaborador
   * 
   * Valida y actualiza la contraseña de un usuario existente.
   * Responde en formato JSON.
   * 
   * @return void
   */
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

  /**
   * Deshabilita un usuario del sistema
   * 
   * Marca un usuario como deshabilitado sin eliminarlo de la base de datos.
   * @param int $id ID del colaboradora deshabilitar
   * @return void
   */
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

  /**
   * Muestra el perfil del usuario autenticado
   * 
   * Renderiza la vista del perfil con los datos del usuario en sesión
   * Requiere autenticacion.
   * 
   * @return void
   */
  public function profile(): void
  {
    $this->authRequired();
    //ID por URL, recógelo: $id = (int) $params['id'];
    $idColab = $_SESSION['user']['id'];

    $usuario = $this->usuarioModel->getById($idColab);
    $this->view('usuarios.profile', ['usuario' => $usuario]);
  }

  /**
   * Sube y actualiza el avatar del usuario
   * 
   * Procesa la imagen de avatar, la guarda en el servidor y actualiza la referencia en la base de datos.
   * Responde en formato JSON.
   * 
   * @return void
   */
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

    // Definir la carpeta
    $avatarsDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/avatar';

    // Crear carpeta si no existe
    if (!is_dir($avatarsDir)) {
      mkdir($avatarsDir, 0755, true);
    }
    $tmp = $_FILES['avatar']['tmp_name'];
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $id = $_SESSION['user']['id'];
    $filename = "avatar_{$id}." . $ext;
    $dest = $avatarsDir . '/' . $filename;

    // Mover el archivo
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
   * Muestra el formulario de creacion de cuentas desde contratos existentes
   * 
   * Lista los contratos laborales que aún no tienen un colaborador asociado para permitir la creación de cuentas de usuario.
   * Solo accesible por administradores.
   * @return void
   */
  public function showCreateFromContracts(): void
  {
    $contracts = $this->usuarioModel->getContractsWithoutColaborador();

    // Obtener locales desde Local::getAllLocales()
    $locales = $this->localModel->getAllLocales();

    $success = $_SESSION['success_message'] ?? null;
    unset($_SESSION['success_message']);

    $this->view('usuarios.createAccount', [
      'contracts' => $contracts,
      'locales' => $locales,
      'success' => $success
    ]);
  }

  //CREAR COLABORADOR CON PERSONAS QUE TIENEN CONTRATO PERO NO UNA CUENTA 
  /**
   * Crea un colaborador a partir de un contrato existente
   * 
   * Asocia una cuenta de usuario (colaborador) a un contrato laboral que previamente no tenía usuario asignado.
   * Preserva la sesion actual del administrador.
   * 
   * @throws \RuntimeException
   * @return void
   */
  public function createFromContract(): void
  {
    /* if (session_status() !== PHP_SESSION_ACTIVE)
      session_start(); */
    $prevUser = $_SESSION['user'] ?? null;

    $idContrato = (int) ($_POST['idcontrato'] ?? 0);
    $idLocal = !empty($_POST['idlocal']) ? (int) $_POST['idlocal'] : null;
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
      $idColab = $this->colaboradorModel->create($idContrato, $usernick, $passwordHash, $restr, $idLocal);
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

    // restaurar sesion original
    if ($prevUser !== null) {
      $_SESSION['user'] = $prevUser;
    }

    $_SESSION['success_message'] = "Cuenta creada correctamente para <strong>" . htmlspecialchars($usernick) . "</strong>";
    $this->redirect('/createAccount');
    return;
  }

  /**
   * Toggle de restricción horaria (S <-> N)
   * Accesible via POST /usuarios/toggleRestriccion/{id}
   */

  /**
   * Altera el estado de restriccion horaria de un colaborador
   * 
   * Cambia el valor de restriccion horaria entre:
   * 'S' (con restriccion)
   * 'N' (sin restriccion)
   * para un colaborador específico.
   * @return void
   */
  public function toggleRestriccion(): void
  {
    $this->authRequired();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/usuarios');
      return;
    }

    $idColab = (int) ($_POST['idcolaborador'] ?? 0);
    if ($idColab <= 0) {
      $_SESSION['error_message'] = 'ID inválido.';
      $this->redirect('/usuarios');
      return;
    }

    // obtener valor actual
    $actual = $this->usuarioModel->getRestriccionHoraria($idColab);
    if ($actual === null) {
      $_SESSION['error_message'] = 'No se pudo obtener el estado actual.';
      $this->redirect('/usuarios');
      return;
    }

    $nuevo = (strtoupper($actual) === 'S') ? 'N' : 'S';

    try {
      $ok = $this->usuarioModel->setRestriccionHoraria($idColab, $nuevo);
      if ($ok) {
        $_SESSION['success_message'] = ($nuevo === 'S')
          ? 'Restricción horaria activada.'
          : 'Restricción horaria desactivada.';
      } else {
        $_SESSION['error_message'] = 'No se pudo actualizar la restricción horaria.';
      }
    } catch (\Throwable $e) {
      $_SESSION['error_message'] = 'Error al actualizar.';
    }

    // vuelve al listado
    $this->redirect('/usuarios');
  }

  /**
   * Muestra el formulario de edición de usuario (VISTA EDITAR)
   * 
   * Carga los datos del usuario, areas, cargos, y locales disponibles para su edicion.
   * 
   * @param int $id ID del colaborador a editar
   * @return void
   */
  public function edit(int $id): void
  {
    $this->authRequired();

    $idColab = (int) $id;
    if ($idColab <= 0) {
      http_response_code(404);
      $this->view('errors.404');
      return;
    }

    $usuario = $this->usuarioModel->getById($idColab);
    if (!$usuario) {
      http_response_code(404);
      $this->view('errors.404');
      return;
    }
    $areas = $this->usuarioModel->getAllAreas();

    $idAreaUsuario = isset($usuario['idarea']) ? (int) $usuario['idarea'] : 0;
    $cargos = $this->usuarioModel->getCargosByArea($idAreaUsuario);

    $locales = $this->localModel->getAllLocales();

    $this->view('usuarios.edit', [
      'usuario' => $usuario,
      'areas' => $areas,
      'cargos' => $cargos,
      'locales' => $locales
    ]);
  }

  //ACTUALIZAR / EDIT
  /**
   * Actualiza los datos de un usuario existente
   * 
   * Procesa y valida los datos del formulario de edición, actualizando la informacion personal y laboral del colaborador.
   * @return void
   */
  public function update(): void
  {
    $this->authRequired();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/usuarios');
      return;
    }

    $idColab = (int) ($_POST['idcolaborador'] ?? 0);
    $idLocal = !empty($_POST['idlocal']) ? (int) $_POST['idlocal'] : null;
    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $idArea = (int) ($_POST['idarea'] ?? 0);
    $idCargo = (int) ($_POST['idcargo'] ?? 0);
    $nrodoc = trim($_POST['nrodoc'] ?? '');
    $fechainicio = trim($_POST['fechainicio'] ?? '');

    // Validaciones básicas
    $errors = $this->validator->validateUpdateUsuario([
      'idcolaborador' => $idColab,
      'nombres' => $nombres,
      'apellidos' => $apellidos,
      'idarea' => $idArea,
      'idcargo' => $idCargo,
      'nrodoc' => $nrodoc,
      'fechainicio' => $fechainicio,
    ]);

    if (!empty($errors)) {
      $_SESSION['error_message'] = implode(' ', $errors);
      $this->redirect('/usuarios/edit/' . $idColab);
      return;
    }

    // ejecutar actualización
    $ok = $this->usuarioModel->update($idColab, $nombres, $apellidos, $idArea, $idCargo, $nrodoc, $fechainicio, $idLocal);

    if ($ok) {
      $_SESSION['success_message'] = 'Datos actualizados de' . ' ' . $nombres . ' ' . $apellidos . ' ' . 'correctamente.';
    } else {
      $_SESSION['error_message'] = 'No se pudo actualizar. Verifica los datos o contacta al administrador.';
    }

    $this->redirect('/usuarios');
  }

}