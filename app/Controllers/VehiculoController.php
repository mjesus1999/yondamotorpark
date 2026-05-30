<?php

/**
 * Controlador de Vehiculos
 * 
 * app/Controllers/VehiculoController.php
 * 
 * Maneja todas las operaciones relacionadas con la gestion de vehiculos, incluyendo
 * registros, actualizacion, ventas al contado, recepcion de ordenes de compra y busquedas.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Vehiculo;
use App\Models\Usuario;
use App\Models\Local;
use App\Models\Modelo;
use App\Config\ConceptosPago;


/**
 * Clase de VehiculoController
 * 
 * Controlador principal para la gestion de integridad de vehiculos 
 * Proporciona funcionalidades para:
 * - Visualizar y listado de vehiculos
 * - Registro de vehiculos (directo y pon orden de compra)
 * - Actualizacion de datos de vehiculos
 * - Recepcion de vehiculos desde ordenes de compra
 * - Ventas al contado
 * - Busqueda y filtrados
 * 
 */
class VehiculoController extends Controller
{
  /**
   * Instancia del modelo de Vehiculos
   * @var Vehiculo
   */
  private Vehiculo $vehiculoModel;
  /**
   * Instancia del modelo de Usuarios
   * @var Usuario
   */
  private Usuario $usuarioModel;
  /**
   * Instancia del modelo de Local
   * @var Local
   */
  private Local $localModel;
  /**
   * Instancia del modelo de Modelo 
   * @var Modelo
   */
  private Modelo $modeloModel;

  /**
   * Constructor del controlador
   * 
   * Inicializa las instancias de los modelos necesarios para las operaciones del controlador.
   */
  public function __construct()
  {
    $this->vehiculoModel = new Vehiculo();
    $this->usuarioModel = new Usuario();
    $this->localModel = new Local();
    $this->modeloModel = new Modelo();
  }

  /**
   * Muestra la pagina principal
   * 
   * Lista los vehiculos filtrados por estado de disponibilidad
   * Estado permitidos: libre, proceso, separado, vendido.
   * Por defecto muestra vehiculos en estado PROCESO.
   * 
   * @return void
   * 
   * @uses Vehiculo::getAll() Para obtener el listado de vehículos
   */
  public function index(): void
  {
    $this->authRequired();
    // ESTADO DEL VEHICULO
    $estado = $_GET['estado'] ?? 'proceso';
    $allwed = ['libre', 'proceso', 'separado', 'vendido'];
    if (!in_array($estado, $allwed, true)) {
      $estado = 'libre';
    }

    $vehiculos = $this->vehiculoModel->getAll($estado);
    $this->view(
      'vehiculos.index',
      [
        'vehiculos' => $vehiculos,
        'estadoActual' => $estado,
      ]
    );
  }

  /**
   * Muestra la lista de vehiculos vendidos al contado
   * 
   * Renderiza la interfaz para consultar y gestionar el historial de vehiculos vendidos
   * mediante pago al contado
   * 
   * @return void
   */
  public function indexVehiculosAlContado()
  {
    $this->authRequired();
    $this->view('vehiculos.vehiculosAlContado');
  }

  /**
   * Muestra la vista para recepcionar vehiculos de ordenes de compra
   * 
   * Despliega el listado de todas las ordenes de compra disponibles para iniciar el proceso de recepcion
   * de vehiculos.
   * 
   * @return void
   * 
   * @uses Vehiculo::getAllOCompras() Para obtener las órdenes de compra
   */
  public function indexRecepcionVehiculos()
  {
    $this->authRequired();
    $data = $this->vehiculoModel->getAllOCompras();
    $this->view('vehiculos.recepcion', ['data' => $data]);
  }

  /**
   * Muestra la vista de edicion para recepcion de vehiculos
   * 
   * Carga los detalles de una orden de compra especifica y sus vehiculos 
   * asociados para realizar el proceso de recepcion y actualizacion.
   * 
   * @param int  $idcompra ID de la orden de compra a recepcionar
   * @return void 
   *  
   * @uses Vehiculo::getAllDatosRecepcion() Para obtener datos de la orden y vehículos
   */
  public function recepcionEdit($idcompra)
  {
    $this->authRequired();
    $idcompra = (int) $idcompra;
    $datosRecepcion = $this->vehiculoModel->getAllDatosRecepcion($idcompra);
    $infoCompra = $datosRecepcion['info_compra'];
    $vehiculos = $datosRecepcion['vehiculos'];
    $this->view('vehiculos.recepcionEdit', ['info_compra' => $infoCompra, 'vehiculos' => $vehiculos]);
  }

  /**
   * Muestra el formulario de creacion de vehiculo
   * 
   * Renderiza la vista con el formulario para registrar un nuevo vehiculo de forma directa 
   * (no mediante orden de compra).
   * 
   * @return void
   */
  public function create(): void
  {
    $this->authRequired();
    $this->view('vehiculos.create');
  }

  /**
   * Registra un nuevo vehículo asociado a una orden de compra
   * 
   * Procesa la solicitud POST para crear un vehículo vinculado a una orden de compra.
   * Realiza validaciones de campos obligatorios y retorna respuesta JSON.
   * 
   * @return int ID del vehiculo creado
   * 
   * @throws \Exception Si ocurre un error durante la creación
   * 
   * @uses Validador::limpiar() Para sanitizar los datos de entrada
   * @uses Validador::campoObligatorio() Para validar campos requeridos
   * @uses Vehiculo::createVehiculoOC() Para registrar el vehículo
   * 
   * @api
   * @httpmethod POST
   * @response 200 JSON con success=true y el ID del vehículo
   * @response 405 Método no permitido
   * @response 422 Errores de validación
   */
  public function storeVehiculoOC(): int
  {
    $this->authRequired();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Método no permitido']);
      exit;
    }

    header('Content-Type: application/json');

    $data = array_map([Validador::class, 'limpiar'], $_POST);

    $registro = [
      'idmodelo' => $data['idmodelo'] ?? '',
      'idcombustible' => $data['idcombustible'] ?? '',
      'version' => $data['version'] ?? '',
      'condicion' => $data['condicion'] ?? '',
      'color' => $data['color'] ?? '',
      'chasis' => $data['chasis'] ?? '',
      'placa' => $data['placa'] ?? '',
      'placarotativa' => $data['placarotativa'] ?? '',
      'seriemotor' => $data['seriemotor'] ?? ''
    ];

    $errores = [];

    $errores[] = Validador::campoObligatorio($registro['idmodelo'], 'Modelo');
    $errores[] = Validador::campoObligatorio($registro['idcombustible'], 'Combustible');
    $errores[] = Validador::campoObligatorio($registro['version'], 'Versión');
    $errores = array_filter($errores);

    if (!empty($errores)) {
      echo json_encode([
        'success' => false,
        'message' => implode(',', $errores),
      ]);
      exit();
    }

    $idVehiculo = $this->vehiculoModel->createVehiculoOC($registro);

    if ($idVehiculo > 0) {
      echo json_encode([
        'success' => true,
        'message' => '¡Creado exitosamente!',
        'id' => $idVehiculo
      ]);
      exit;
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'No se pudo crear el vehículo',
        'id' => $idVehiculo
      ]);
      exit;
    }
  }

  /**
   * Registra un nuevo vehículo de forma directa
   * 
   * Crea un vehículo sin estar asociado a una orden de compra.
   * Solo personal de logística puede realizar esta operación.
   * Asigna automáticamente el local de Chincha y el colaborador de la sesión.
   * 
   * 
   * @return void Redirige a la pagina de vehiculos con mensajes de resultado 
   * 
   * throws \Exception Si ocurre un error durante el registro
   * 
   * @uses Usuario::esDeLogistica() Para verificar permisos del usuario
   * @uses Local::getByTienda() Para obtener el local por defecto
   * @uses Vehiculo::create() Para registrar el vehículo
   * 
   * @security Requiere rol de logística
   */
  public function store(): void
  {
    $this->authRequired();
    header('Content-Type: application/json; charset=utf-8');

    $idcolaborador = $_SESSION['user']['id'];

    //SOLO PODRA REGISTRAR USUARIO DE LOGISTICA
    if (!$this->usuarioModel->esDeLogistica($idcolaborador)) {
      $_SESSION['error_message'] = 'Solo el personal de Logística puede registrar vehículos';
      header('Location: /vehiculos?estado=proceso');
      exit;
    }

    $local = $this->localModel->getByTienda('Chincha');
    $idlocal = $local['idlocal'] ?? 1; //CHINCHA POR DEFECTO - Temporal

    $chasis = $_POST['chasis'] ?? null;
    $placa = $_POST['placa'] ?? null;
    $placaRotativa = $_POST['placarotativa'] ?? null;
    $serieMotor = $_POST['seriemotor'] ?? null;

    $idmodelo = (int) ($_POST['idmodelo'] ?? 0);
    if ($idmodelo <= 0) {
      echo json_encode(['error' => 'Debe elegir modelo y año válidos'], JSON_UNESCAPED_UNICODE);
      exit;
    }
    $version = trim((string) ($_POST['version'] ?? ''));
    if ($version === '') {
      echo json_encode(['error' => 'Debe indicar una versión válida'], JSON_UNESCAPED_UNICODE);
      exit;
    }

    $condicion = $_POST['condicion'];
    $idcombustible = (int) ($_POST['combustible'] ?? 0);
    $color = $_POST['color'] ?? null;
    $moneda = $_POST['moneda'] ?? 'USD';
    $precioventa = $_POST['precio'];

    try {
      $data = [
        'idmodelo' => $idmodelo,
        'version' => $version,
        'condicion' => $condicion,
        'idcombustible' => $idcombustible,
        'color' => $color,
        'chasis' => $chasis,
        'placa' => $placa,
        'placarotativa' => $placaRotativa,
        'seriemotor' => $serieMotor,
        'moneda' => $moneda,
        'precioventa' => $precioventa,
        'idlogistica' => $idcolaborador,
        'idlocal' => $idlocal,
      ];
      $this->vehiculoModel->create($data);

      $_SESSION['success_message'] = 'Vehículo(s) registrados correctamente';
      header('Location: /vehiculos?estado=proceso');
      exit;
    } catch (\Exception $e) {
      $_SESSION['error_message'] = 'Error al registrar vehículos: ' . $e->getMessage();
      header('Location: /vehiculos/create');
      exit;
    }
  }

  /**
   * Registra una venta al contado de un vehículo
   * 
   * Procesa el pago completo de un vehículo al contado, validando:
   * - Monto recibido coincida con el precio del vehículo
   * - Tipo de cambio aplicado correctamente según moneda
   * - Disponibilidad del vehículo
   * 
   * Soporta pagos en USD y PEN con conversión automática.
   * Permite adjuntar comprobante de pago (PDF, JPG, JPEG, PNG, WEBP).
   * 
   * @throws \Exception Si hay inconsistencias en el monto o errores de validacion
   * @return void Respuesta JSON con resultado de la operacion
   * 
   * @uses Validador::limpiar() Para sanitizar datos
   * @uses Validador::campoObligatorio() Para validar campos requeridos
   * @uses Vehiculo::getPrecioVehiculoAlContado() Para obtener precio real
   * @uses Vehiculo::createPagoAlContado() Para registrar la venta
   * 
   * @api
   * @httpmethod POST
   * @response 200 JSON con success=true y el ID del pago
   * @response 405 Método no permitido
   * @response 422 Errores de validación
   * @response 500 Error en el proceso de registro
   * 
   * @filesupport Acepta archivo 'comprobante' en formato PDF, JPG, PNG, WEBP

   */
  public function storePagoALContado()
  {
    $this->authRequired();
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Método no permitido']);
      exit;
    }

    $data = array_map([Validador::class, 'limpiar'], $_POST);

    // Validaciones de campos obligatorios iniciales
    $errores = [];
    $errores[] = Validador::campoObligatorio($data['idcliente'] ?? null, 'Cliente');
    $errores[] = Validador::campoObligatorio($data['idvehiculo'] ?? null, 'Vehículo');
    $errores[] = Validador::campoObligatorio($data['mediopago'] ?? null, 'Medio de pago');
    $errores[] = Validador::campoObligatorio($data['fechapago'] ?? null, 'Fecha de pago');
    $errores[] = Validador::campoObligatorio($data['amortizacion'] ?? null, 'Amortización');
    $errores[] = Validador::campoObligatorio($data['moneda'] ?? null, 'Moneda');
    if (($data['moneda'] ?? '') === 'PEN') {
      $errores[] = Validador::campoObligatorio($data['tipocambioaplicado'] ?? null, 'Tipo de cambio');
    }

    $errores = array_filter($errores);

    if (!empty($errores)) {
      http_response_code(422);
      echo json_encode([
        'success' => false,
        'message' => implode('<br>', $errores),
      ]);
      exit;
    }

    $rutaCompletaArchivo = null;

    try {
      $idvehiculo = intval($data['idvehiculo']);
      $monedaPago = $data['moneda']; // 'USD' o 'PEN'
      $montoRecibido = floatval($data['amortizacion']);
      $tipoCambioAplicado = floatval($data['tipocambioaplicado'] ?? 1.0);

      // OBTENER EL PRECIO REAL DESDE LA BASE DE DATOS
      $precioVehiculoEnUSD = $this->vehiculoModel->getPrecioVehiculoAlContado($idvehiculo);

      if (!$precioVehiculoEnUSD) {
        throw new \Exception('El vehículo no existe o no tiene un precio asignado.');
      }
      $precioVehiculoEnUSD = floatval($precioVehiculoEnUSD);

      // CALCULAR CUÁNTO DEBERÍA HABER PAGADO EL CLIENTE
      $montoEsperado = 0;
      if ($monedaPago === 'USD') {
        $montoEsperado = $precioVehiculoEnUSD;
      } elseif ($monedaPago === 'PEN') {
        if ($tipoCambioAplicado <= 0) {
          throw new \Exception('El tipo de cambio debe ser un valor positivo.');
        }
        $montoEsperado = $precioVehiculoEnUSD * $tipoCambioAplicado;
      } else {
        throw new \Exception('La moneda de pago no es válida.');
      }

      // COMPARAR EL MONTO RECIBIDO CON EL MONTO ESPERADO

      $epsilon = 0.01;
      if (abs($montoRecibido - $montoEsperado) > $epsilon) {
        $mensajeError = sprintf(
          "Inconsistencia en el monto del pago. Se esperaba %.2f %s pero se recibió %.2f %s.",
          $montoEsperado,
          $monedaPago,
          $montoRecibido,
          $monedaPago
        );
        throw new \Exception($mensajeError);
      }

      // La amortización final siempre se guarda en SOLES
      $amortizacionParaGuardar = $precioVehiculoEnUSD * $tipoCambioAplicado;

      $registro = [
        'idcliente' => $data['idcliente'],
        'idconcepto' => ConceptosPago::CONTADO_ID,
        'idvehiculo' => $idvehiculo,
        'idcuentapago' => empty($data['idcuentapago']) ? null : $data['idcuentapago'],
        'mediopago' => $data['mediopago'],
        'numerotransaccion' => empty($data['numerotransaccion']) ? null : $data['numerotransaccion'],
        'fechapago' => $data['fechapago'],
        'amortizacion' => $amortizacionParaGuardar,
        'montomonedaoriginal' => $precioVehiculoEnUSD,
        'tipocambioaplicado' => $tipoCambioAplicado,
        'moneda' => $monedaPago,
        'observacion' => empty($data['observacion']) ? null : $data['observacion'],
        'comprobante' => null
      ];



      $rutaCompletaArchivo = null; // Para que no me de error.
      // Procesar la subida del comprobante (si existe)
      if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
        $subdirectorio = 'comprobantes';
        $nombreArchivo = uniqid('pago_') . '_' . basename($_FILES['comprobante']['name']);
        $directorioDestino = __DIR__ . '/../../storage/' . $subdirectorio . '/';

        if (!is_dir($directorioDestino)) {
          mkdir($directorioDestino, 0777, true);
        }
        $rutaCompletaArchivo = $directorioDestino . $nombreArchivo;

        $extension = strtolower(pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $extensionesPermitidas)) {
          throw new \Exception('El tipo de archivo del comprobante no es válido.');
        }

        if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaCompletaArchivo)) {
          throw new \Exception('No se pudo guardar el archivo del comprobante.');
        }
        $registro['comprobante'] = $subdirectorio . '/' . $nombreArchivo;
      }

      $idPago = $this->vehiculoModel->createPagoAlContado($registro);

      if ($idPago > 0) {
        echo json_encode([
          'success' => true,
          'message' => '¡Venta al contado registrada exitosamente!',
          'id' => $idPago
        ]);
      } else {
        throw new \Exception('No se pudo registrar el pago. Verifique los datos o la disponibilidad del vehículo.');
      }
    } catch (\Exception $e) {
      // Si algo falla, borra el archivo subido
      if ($rutaCompletaArchivo && file_exists($rutaCompletaArchivo)) {
        @unlink($rutaCompletaArchivo);
      }
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
      ]);
    }
  }

  /**
   * Elimina un vehiculo
   * 
   * Elimina fisicamente un registro de vehículo de la base de datos.
   * Retorna respuesta JSON con el resultado de la operacion.
   * 
   * @return void Respuesta JSON indicando exito o error
   * 
   * @throws \Exception Si ocurre un error durante la eliminación
   * 
   * @uses Vehiculo::delete() Para eliminar el vehículo
   * 
   * @api
   * @httpmethod POST
   * @param int $idvehiculo ID del vehículo a eliminar (vía POST)
   * @response 200 JSON con success o error
   */
  public function delete(): void
  {
    $this->authRequired();
    header('Content-Type: application/json; charset=utf-8');

    $idvehiculo = (int) ($_POST['idvehiculo'] ?? 0);

    if ($idvehiculo <= 0) {
      echo json_encode(['error' => 'ID de vehículo inválido']);
      exit;
    }

    try {
      $success = $this->vehiculoModel->delete($idvehiculo);

      if ($success) {
        echo json_encode(['success' => 'Vehículo eliminado correctamente']);
      } else {
        echo json_encode(['error' => 'No se pudo eliminar el vehículo']);
      }
    } catch (\Exception $e) {
      echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
  }

  /**
   * Muestra el formulario de edición de un vehículo
   * 
   * Carga los datos completos del vehiculo y su modelo asociado
   * para mostrar el formulario de edicion.
   * 
   * @param int $id ID del vehiculo a editar
   * @return void Renderiza vista de edicion o pagina 404 si no existe
   * 
   * @uses Vehiculo::getById() Para obtener datos del vehículo
   * @uses Vehiculo::getModeloDetalle() Para obtener información del modelo
   */
  public function edit(int $id): void
  {
    $vehiculo = $this->vehiculoModel->getById($id);
    if ($vehiculo) {
      // obtener información del modelo (marca, tipo, modelo, año)
      $modeloDetalle = $this->vehiculoModel->getModeloDetalle((int) $vehiculo['idmodelo']);
      $this->view('vehiculos.edit', [
        'vehiculo' => $vehiculo,
        'modeloDetalle' => $modeloDetalle
      ]);
    } else {
      http_response_code(404);
      $this->view('errors.404');
    }
  }

  /**
   * Actualiza los datos de un vehículo existente
   * 
   * Procesa la actualizacion de informacion del vehículo incluyendo
   * modelo, version, condicion, caracteristicas fisicas y precio.
   * Redirige a la lista de vehiculos con mensaje de resultado.
   * 
   * @return void Redirige con mensaje de éxito o error en sesión
   * 
   * @throws \Exception Si ocurre un error durante la actualización
   * 
   * @uses Vehiculo::update() Para actualizar el vehículo
   * 
   * @httpmethod POST
   * @param int $idvehiculo ID del vehículo (vía POST)
   */
  public function update(): void
  {
    $this->authRequired();
    // recibir POST
    $idvehiculo = (int) ($_POST['idvehiculo'] ?? 0);
    if ($idvehiculo <= 0) {
      $_SESSION['error_message'] = 'ID de vehículo inválido';
      header('Location: /vehiculos');
      exit;
    }

    // validar idmodelo
    $idmodelo = (int) ($_POST['idmodelo'] ?? 0);
    if ($idmodelo <= 0) {
      $_SESSION['error_message'] = 'Debe seleccionar modelo y año válidos';
      header('Location: /vehiculos/edit/' . $idvehiculo);
      exit;
    }

    // tomar version (ya sea select o input, según el JS)
    $version = trim((string) ($_POST['version'] ?? ''));

    $data = [
      'idmodelo' => $idmodelo,
      'version' => $version,
      'condicion' => $_POST['condicion'] ?? null,
      'idcombustible' => (int) ($_POST['combustible'] ?? 0),
      'color' => $_POST['color'] ?? null,
      'chasis' => $_POST['chasis'] ?? null,
      'placa' => $_POST['placa'] ?? null,
      'placarotativa' => $_POST['placarotativa'] ?? null,
      'seriemotor' => $_POST['seriemotor'] ?? null,
      'moneda' => $_POST['moneda'] ?? 'USD',
      'precioventa' => $_POST['precio'] ?? 0,
    ];

    try {
      $ok = $this->vehiculoModel->update($idvehiculo, $data);
      if ($ok) {
        $_SESSION['success_message'] = 'Vehículo actualizado correctamente';
      } else {
        $_SESSION['error_message'] = 'No se pudo actualizar el vehículo';
      }
    } catch (\Exception $e) {
      $_SESSION['error_message'] = 'Error al actualizar: ' . $e->getMessage();
    }

    header('Location: /vehiculos?estado=proceso');
    exit;
  }

  /**
   * Actualiza datos de vehículo durante recepción de orden de compra
   * 
   * Procesa la actualización de datos físicos del vehículo (chasis, placa, color)
   * y asigna local y estado de disponibilidad durante el proceso de recepción.
   * El colaborador de logística se obtiene automáticamente de la sesión.
   * 
   * @return void Respuesta JSON con resultado de la operación
   * 
   * @uses Validador::limpiar() Para sanitizar datos
   * @uses Validador::campoObligatorio() Para validar campos requeridos
   * @uses Vehiculo::updateVehiculoRecepcionOc() Para actualizar el vehículo
   * 
   * @api
   * @httpmethod POST
   * @response 200 JSON con success=true si se actualizó
   * @response 405 Método no permitido
   * @response 422 Errores de validación
   */
  public function updateVehiculoRecepcionOC()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Método no permitido']);
      exit;
    }

    header('Content-Type: application/json');

    $data = array_map([Validador::class, 'limpiar'], $_POST);

    $registro = [
      'idvehiculo' => $data['idvehiculo'],
      'idlocal' => $data['idlocal'],
      'chasis' => $data['chasis'],
      'placa' => $data['placa'],
      'placarotativa' => $data['placarotativa'],
      'seriemotor' => $data['seriemotor'],
      'color' => $data['color'],
      'disponibilidad' => $data['disponibilidad']
    ];
    $errores = [];

    $errores = [];
    $errores[] = Validador::campoObligatorio($registro['idvehiculo'], 'Vehículo');
    $errores[] = Validador::campoObligatorio($registro['idlocal'], 'Tienda');
    $errores[] = Validador::campoObligatorio($registro['chasis'], 'chasis');
    // $errores[] = Validador::campoObligatorio($registro['placa'], 'Placa');
    $errores[] = Validador::campoObligatorio($registro['seriemotor'], 'Serie Motor');
    $errores[] = Validador::campoObligatorio($registro['color'], 'Color');
    $errores[] = Validador::campoObligatorio($registro['disponibilidad'], 'Disponibilidad');
    // $errores[] = Validador::campoObligatorio($registro['placarotativa'], 'Placa Rotativa');
    $errores = array_filter($errores);

    if (!empty($errores)) {
      echo json_encode([
        'success' => false,
        'message' => implode('<br>', $errores),
      ]);
      exit;
    }

    $rowAffects = $this->vehiculoModel->updateVehiculoRecepcionOc($registro);

    if ($rowAffects > 0) {
      echo json_encode([
        'success' => true,
        'message' => 'Se han actualizado los datos correctamente'
      ]);
      exit();
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'No se pudo actualizar los datos',
        'id' => 0
      ]);
      exit;
    }
  }

  /**
   * Busca vehículos por criterio de búsqueda
   * 
   * Realiza una búsqueda flexible de vehículos por marca, modelo, placa,
   * u otros campos. Retorna resultados en formato JSON.
   * 
   * @return void Respuesta JSON con vehiculos encontrados
   * 
   * @uses Vehiculo::searchvehiculos() Para realizar la búsqueda
   * 
   * @api
   * @httpmethod GET
   * @param string $q Criterio de búsqueda (vía GET)
   * @response 200 JSON con success=true y array de vehículos
   * @response 200 JSON con success=false si no hay resultados
   * 
   * @example GET /vehiculos/search?q=toyota
   */
  public function searchVehiculo()
  {
    header('Content-Type: application/json');

    $search = $_GET['q'] ?? '';
    $vehiculos = $this->vehiculoModel->searchvehiculos($search);

    if (!empty($vehiculos)) {
      echo json_encode([
        'success' => true,
        'total' => count($vehiculos),
        'data' => $vehiculos
      ]);
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'No se encontraron vehículos disponibles.'
      ]);
    }
  }

  /**
   * Obtiene el listado de vehiculos vendidos al contado
   * 
   * Retorna el historial completo de vehiculos que han sido vendidos
   * mediante pago al contado en formato JSON.
   * 
   * @return void Respuesta JSON con array de vehiculos vendidos
   * 
   * @uses Vehiculo::getVehiculosVendidosAlContado() Para obtener el listado
   * 
   * @api
   * @httpmethod GET
   * @response 200 JSON con success = true y array de vehículos
   * @response 500 Error de base de datos
   */
  public function getVehiculosVendidosAlContado()
  {
    header('Content-Type: application/json; charset=utf-8');

    $vehiculos = $this->vehiculoModel->getVehiculosVendidosAlContado();
    if ($vehiculos === false) {
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'message' => 'Ocurrió un error al consultar la base de datos.'
      ]);
      return;
    }
    echo json_encode([
      'success' => true,
      'vehiculos' => $vehiculos
    ]);
  }

  /**
   * Obtiene los datos completos de un vehículo para venta al contado
   * 
   * Retorna toda la información necesaria para procesar una venta al contado
   * de un vehículo específico, incluyendo precio, disponibilidad y detalles.
   * 
   * @param int $idvehiculo ID del vehículo a consultar
   * @return void Respuesta JSON con datos del vehiculo
   * 
   * @uses Vehiculo::getDataVehiculoAlContado() Para obtener los datos
   * 
   * @api
   * @httpmethod GET
   * @response 200 JSON con success=true y datos del vehículo
   * @response 500 Error de base de datos
   * 
   * @example GET /vehiculos/data-contado/123
   */
  public function getDataVehiculoAlContado(int $idvehiculo)
  {
    header('Content-Type: application/json; charset=utf-8');

    $vehiculo = $this->vehiculoModel->getDataVehiculoAlContado($idvehiculo);
    if ($vehiculo === false) {
      http_response_code(500);
      echo json_encode([
        'success' => false,
        'message' => 'Ocurrió un error al consultar la base de datos.'
      ]);
      return;
    }

    echo json_encode([
      'success' => true,
      'data' => $vehiculo
    ]);
  }

  /**
   * Agrega un nuevo año a un modelo existente
   * 
   * Endpoint AJAX que permite crear una nueva variante de un modelo
   * con un año diferente. Copia los datos del modelo base (marca, tipo,
   * nombre, imagen) y crea un nuevo registro con el año especificado.
   * 
   * Útil cuando se quiere registrar el mismo modelo pero de un año nuevo
   * sin tener que ingresar todos los datos manualmente.
   * 
   * @return void JSON con resultado de la operación
   * 
   * @uses Modelo::addYearToModelo() Para crear el modelo con nuevo año
   * 
   * @api
   * @httpmethod POST
   * @param int $idmodelo_base ID del modelo a copiar (vía JSON body)
   * @param int $anio Nuevo año para el modelo (vía JSON body)
   * @response 200 JSON con success=true, idmodelo y anio
   * @response 400 Parámetros inválidos
   * @response 500 No se pudo crear el año
   * 
   * @example POST /vehiculos/agregarAnio
   * Body: {"idmodelo_base": 15, "anio": 2025}
   */
  public function agregarAnio(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    $body = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int) ($body['idmodelo_base'] ?? 0);
    $anio = (int) ($body['anio'] ?? 0);

    if ($id <= 0 || $anio <= 0) {
      http_response_code(400);
      echo json_encode(['success' => false, 'error' => 'Parámetros inválidos']);
      return;
    }

    $nuevoId = $this->modeloModel->addYearToModelo($id, $anio);
    if ($nuevoId > 0) {
      echo json_encode(['success' => true, 'idmodelo' => $nuevoId, 'anio' => $anio]);
    } else {
      http_response_code(500);
      echo json_encode(['success' => false, 'error' => 'No se pudo crear el año']);
    }
  }


}
