<?php
// app/Controllers/ProductController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Vehiculo;
use App\Models\Usuario;
use App\Models\Local;
use App\Models\Modelo;
use App\Config\ConceptosPago;

//use App\Models\Product;

class VehiculoController extends Controller
{
  private Vehiculo $vehiculoModel;
  private Usuario $usuarioModel;
  private Local $localModel;
  private Modelo $modeloModel;

  public function __construct()
  {
    $this->vehiculoModel = new Vehiculo();
    $this->usuarioModel = new Usuario();
    $this->localModel = new Local();
    $this->modeloModel = new Modelo();
  }

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

  public function indexVehiculosAlContado()
  {
    $this->authRequired();
    $this->view('vehiculos.vehiculosAlContado');
  }




  /**
   * Método que muestra la vista para recepcionar los vehículos provenientes de una Orden de Compra (OC).
   *
   * @return void No retorna ningún valor; únicamente carga la vista correspondiente.
   */
  public function indexRecepcionVehiculos()
  {
    $this->authRequired();
    $data = $this->vehiculoModel->getAllOCompras();
    $this->view('vehiculos.recepcion', ['data' => $data]);
  }

  /**
   * Método que muestra la vista de editar los datos del vehículo
   * 
   * @return void No retorna ningun valor; únicamente carga la vista correspondiente
   */
  public function recepcionEdit($idcompra)
  {
    $this->authRequired();  
    $idcompra = (int)$idcompra;
    $datosRecepcion = $this->vehiculoModel->getAllDatosRecepcion($idcompra);
    $infoCompra = $datosRecepcion['info_compra'];
    $vehiculos = $datosRecepcion['vehiculos'];
    $this->view('vehiculos.recepcionEdit', ['info_compra' => $infoCompra, 'vehiculos'  => $vehiculos]);
  }

  public function create(): void
  {
    $this->authRequired();
    $this->view('vehiculos.create');
  }


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

  // STORE DEYANIRA:
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
        'idcliente'           => $data['idcliente'],
        'idconcepto'          => ConceptosPago::CONTADO_ID,
        'idvehiculo'          => $idvehiculo,
        'idcuentapago'        => empty($data['idcuentapago']) ? null : $data['idcuentapago'],
        'mediopago'           => $data['mediopago'],
        'numerotransaccion'   => empty($data['numerotransaccion']) ? null : $data['numerotransaccion'],
        'fechapago'           => $data['fechapago'],
        'amortizacion'        => $amortizacionParaGuardar,
        'montomonedaoriginal' => $precioVehiculoEnUSD,
        'tipocambioaplicado'  => $tipoCambioAplicado,
        'moneda'              => $monedaPago,
        'observacion'         => empty($data['observacion']) ? null : $data['observacion'],
        'comprobante'         => null
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
      $this->view('error.404');
    }
  }

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

    $nuevoId = $this->modeloModel->addYearToModelo($id, $anio); // <-- sin "new" aquí
    if ($nuevoId > 0) {
      echo json_encode(['success' => true, 'idmodelo' => $nuevoId, 'anio' => $anio]);
    } else {
      http_response_code(500);
      echo json_encode(['success' => false, 'error' => 'No se pudo crear el año']);
    }
  }





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
      'success'   => true,
      'vehiculos' => $vehiculos
    ]);
  }


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
}
