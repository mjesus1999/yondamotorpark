<?php

/**
 * Modelo de Vehiculos
 * 
 * app/Models/Vehiculo.php
 * 
 * Gestiona las operaciones de base de datos relacionados con los vehiculos, incluyendo registros,
 * actualizacion, ventas al contado, ordenes de compra y recepcion de inventario.
 * 
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;


/**
 * Clase vehiculo
 * 
 * Modelo para la gestion integral de vehiculos.
 * Proporciona metodos para operaciones CRUD, 
 * gestion de ordenes de compra, ventas al contado, busquedas avanzadas y control de disponibilidad.
 * 
 */
class Vehiculo
{

  /**
   * Instancia de conexion a la base de datos
   * @var PDO
   */
  private PDO $db;

  /**
   * Constructor del modelo 
   * 
   * Inicializa la conexion a la base de datos
   */
  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Obtiene todas las ordenes de compra
   * 
   * Ejecuta el procedimiento almacenado para obtener el listado completo de ordenes de compra
   * de vehiculos.
   * 
   * @return array Array asociativo con las ordenes de compra o array vacio en caso de error
   */
  public function getAllOCompras(): array
  {
    $query = "CALL sp_getAll_OC_Compras()";
    try {

      $stmt = $this->db->prepare($query);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $result;
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return [];
    }
  }

  /**
   * Obtiene los vehiculos vendidos al contado
   * 
   * Ejecuta el procedimiento almacenado que retorna el historial de vehiculos 
   * que han sido vendidos mediante pago al contado.
   * 
   * @return array|bool Array asociativo con los vehiculos vendidos.  
   */
  public function getVehiculosVendidosAlContado(): array|false
  {
    $query = "CALL sp_vehiculosVendidoAlContado();";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $error) {
      error_log("Error en getVehiculosVendidosAlContado: " . $error->getMessage());
      return false;
    }
  }

  /**
   * Busca vehiculos por criterio de busqueda
   * 
   * Realiza una busqueda flexible de vehiculos mediante el procedimiento almacenado "sp_buscar_vehiculos", 
   * permitiendo busqueda por multiples campos
   * 
   * @param string $busqueda Criterio de busqueda (marca, modelo, placa, entre otros)
   * @return array Array asociativo con los vehiculos encontrados o array vacio con los vehiculos encontrados o 
   *                array vacio si no hay resultados
   */
  public function searchvehiculos(string $busqueda = ''): array
  {
    $query = "CALL sp_buscar_vehiculos(:busqueda)";

    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(":busqueda", trim($busqueda), PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return [];
    }
  }

  /**
   * Obtiene todos de una orden de compra para recepcion 
   * 
   * Ejecuta el procedimineto almacenado de "sp_get_OC_details_for_recepcion" que retornara multiples conjuntos de resultados:
   * informacion general de la compra y listado de vehiculos.
   * utilizando en el proceso de recepcion de vehiculos.
   * 
   * @param int $idcompra ID de la orden de compra 
   * @return array|array Array con dos claves:
   *                      - 'info_compra': Información general de la orden
   *                      - 'vehiculos': Array de vehículos de la orden
   *                      -  Array vacío en caso de error
   */
  public function getAllDatosRecepcion(int $idcompra): array
  {

    $query = "CALL sp_get_OC_details_for_recepcion(?)";

    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(1, $idcompra, PDO::PARAM_INT);
      $stmt->execute();

      $infoCompra = $stmt->fetch(PDO::FETCH_ASSOC);
      error_log("INFO_COMPRA: " . print_r($infoCompra, true));

      $stmt->nextRowset();
      $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
      error_log("VEHICULOS: " . print_r($vehiculos, true));

      $stmt->closeCursor();
      return [
        'info_compra' => $infoCompra,
        'vehiculos' => $vehiculos
      ];
    } catch (PDOException $error) {
      error_log("PDOException: " . $error->getMessage());
      return [];
    }
  }

  /**
   * Crea un vehiculo asociado a una orden de compra 
   * 
   * Registra un nuevo vehiculo mediante el procedimiento almacenado "sp_vehiculo_OC_registrar",
   * vinculado a una orden de compra existente.
   * 
   * @param array $params Array asociativo con los datos del vehículo:
   *                      - idmodelo: int (ID del modelo)
   *                      - idcombustible: int (ID del tipo de combustible)
   *                      - version: string (Versión del vehículo)
   *                      - condicion: string (Nuevo/Usado)
   *                      - color: string (Color del vehículo)
   *                      - chasis: string (Número de chasis)
   *                      - placa: string (Placa del vehículo)
   *                      - placarotativa: string (Placa rotativa si aplica)
   *                      - seriemotor: string (Serie del motor)
   * @return int ID del vehiculo agregado o -1 en caso de error
   */
  public function createVehiculoOC($params = []): int
  {

    $query = "CALL sp_vehiculo_OC_registrar(:idmodelo,:idcombustible,:version,:condicion,:color,:chasis,:placa,:placarotativa,:seriemotor)";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute(array(
        ':idmodelo' => $params['idmodelo'],
        ':idcombustible' => $params['idcombustible'],
        ':version' => $params['version'],
        ':condicion' => $params['condicion'],
        ':color' => $params['color'],
        ':chasis' => $params['chasis'],
        ':placa' => $params['placa'],
        ':placarotativa' => $params['placarotativa'],
        ':seriemotor' => $params['seriemotor']

      ));

      $idVehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return (int) $idVehiculo['last_id'];
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return -1;
    }
  }

  /**
   * Obtiene todos los vehiculos con filtro opcional de estado
   * 
   * Retorna el listado de vehiculos con informacion completa de marca,
   * modelo, tipo de vehiculo y combustible.
   * Permite filtrar por estado de disponibilidad unico o multiple.
   * 
   * @param string|array $estado Estado de disponibilidad a filtrar:
   *                              - String: un solo estado
   *                              - array
   * @return array Array asociativo con los vehiculos o array vacio en caso de error 
   */
  public function getAll($estado = ''): array
  {
    $query = "
      SELECT 
        v.idvehiculo,
        mc.marca,
        tv.tipovehiculo,
        m.modelo,
        m.anio,
        v.version,
        v.condicion,
        v.color,
        v.disponibilidad,
        v.placa,
        v.placarotativa,
        c.idcombustible,
        c.combustible,
        v.moneda,
        v.precioventa
      FROM vehiculos v
      INNER JOIN modelos m ON m.idmodelo = v.idmodelo
      INNER JOIN marcas mc ON mc.idmarca = m.idmarca
      INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo
      INNER JOIN combustibles c ON c.idcombustible = v.idcombustible
    ";

    $params = [];
    if ($estado !== '') {
      if (is_array($estado)) {
        // Construir placeholders para IN
        $placeholders = implode(", ", array_map(fn($i) => ":estado$i", array_keys($estado)));
        $query .= " WHERE v.disponibilidad IN ($placeholders)";
        foreach ($estado as $i => $est) {
          $params[":estado$i"] = $est;
        }
      } else {
        $query .= " WHERE v.disponibilidad = :estado";
        $params[':estado'] = $estado;
      }
    }

    $query .= " ORDER BY v.idvehiculo DESC";

    try {
      $stmt = $this->db->prepare($query);
      foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
      }
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      return [];
    }
  }

  /**
   * Crea un vehiculo de forma directa (no por OC)
   * 
   * Registra un nuevo vehiculo mediante el procedimiento almacenado "spu_vehiculos_registrar",
   * incluyendo precio de venta y asignacion de local y responsable logistico.
   * 
   * @param array $data Array asociativo con los datos del vehiculo:
   *                    - idmodelo: int
   *                    - version: string
   *                    - condicion: string
   *                    - idcombustible: int
   *                    - color: string
   *                    - chasis: string
   *                    - placa: string
   *                    - placarotativa: string
   *                    - seriemotor: string
   *                    - moneda: string (USD/PEN)
   *                    - precioventa: float
   *                    - idlogistica: int (ID del colaborador de logística)
   *                    - idlocal: int (ID del local asignado)
   * @return int ID del vehiculo creado
   */
  public function create(array $data): int
  {
    $stmt = $this->db->prepare("CALL spu_vehiculos_registrar(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $data['idmodelo'],
      $data['version'],
      $data['condicion'],
      $data['idcombustible'],
      $data['color'],
      $data['chasis'],
      $data['placa'],
      $data['placarotativa'],
      $data['seriemotor'],
      $data['moneda'],
      $data['precioventa'],
      $data['idlogistica'],
      $data['idlocal']
    ]);

    $idvehiculo = $stmt->fetchColumn();
    return (int) $idvehiculo;
  }

  /**
   * Obtiene el precio de venta de un vehiculo al contado 
   * 
   * Consulta el precio de venta configurado para el vehiculo especifico.
   * 
   * @param int $idvehiculo ID del vehiculo
   * @return float|null Precio de venta o null si no existe o hay error 
   */
  public function getPrecioVehiculoAlContado(int $idvehiculo): ?float
  {
    $sql = "SELECT precioventa FROM vehiculos WHERE idvehiculo = :idvehiculo LIMIT 1";

    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':idvehiculo', $idvehiculo, PDO::PARAM_INT);
      $stmt->execute();

      $precio = $stmt->fetchColumn(); // solo el valor de la primera columna

      return $precio !== false ? (float) $precio : null;
    } catch (PDOException $e) {
      error_log("Error en getPrecioVehiculoAlContado: " . $e->getMessage());
      return null;
    }
  }

  /**
   * Obtiene los datos completos de un vehiculo para venta al contado
   * 
   * Ejecuta el procedimiento almacenado "sp_getDataVentaVehiculoContado" que retorna la imformacion
   * necesaria para procesar una venta al contado.
   * 
   * @param int $idvehiculo ID del vehiculo
   */
  public function getDataVehiculoAlContado(int $idvehiculo): ?array
  {
    $query = "CALL sp_getDataVentaVehiculoContado(:idvehiculo);";

    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':idvehiculo', $idvehiculo, PDO::PARAM_INT);
      $stmt->execute();
      $venta = $stmt->fetch(PDO::FETCH_ASSOC);
      return $venta !== false ? $venta : null;
    } catch (PDOException $e) {
      error_log("Error en getVentaVehiculoAlContado: " . $e->getMessage());
      return null;
    }
  }

  /**
   * Registra una venta al contado de un vehiculo
   * 
   * Procesa una venta completa al contado mediante procedimiento almacenado, incluyendo informacion de pago, cliente, asesor y comprobante.
   * El ID del asesor vendedor se obtiene automaticamente de la sesion.
   * 
   * @param array $params Array asociativo con los datos de la venta:
   *                      - idcliente: int (ID del cliente comprador)
   *                      - idconcepto: int (ID del concepto de pago)
   *                      - idvehiculo: int (ID del vehículo vendido)
   *                      - idcuentapago: int (ID de la cuenta bancaria)
   *                      - mediopago: string (Efectivo/Transferencia/etc.)
   *                      - numerotransaccion: string (Número de transacción / si aplica)
   *                      - fechapago: string (Fecha del pago)
   *                      - amortizacion: float (Monto pagado)
   *                      - montomonedaoriginal: float (Monto en moneda original)
   *                      - tipocambioaplicado: float (Tipo de cambio usado)
   *                      - moneda: string (USD/PEN)
   *                      - comprobante: string (Número de comprobante)
   *                      - observacion: string (Observaciones)
   * @return int ID del pago registrado o 0 en caso de error
   */
  public function createPagoAlContado(array $params = []): int
  {

    $query = "CALL sp_registrarVentaContado(
        :idcliente, :idconcepto, :idvehiculo, :idasesorvendedor, :idcuentapago, 
        :mediopago, :numerotransaccion, :fechapago, :amortizacion, 
        :montomonedaoriginal, :tipocambioaplicado, :moneda, :comprobante, :observacion)";

    try {

      $idUsuario = $_SESSION['user']['id'] ?? null;
      $stmt = $this->db->prepare($query);


      $stmt->bindValue(':idcliente', $params['idcliente']);
      $stmt->bindValue(':idconcepto', $params['idconcepto']);
      $stmt->bindValue(':idvehiculo', $params['idvehiculo']);
      $stmt->bindValue(':idasesorvendedor', $idUsuario);
      $stmt->bindValue(':idcuentapago', $params['idcuentapago']);
      $stmt->bindValue(':mediopago', $params['mediopago']);
      $stmt->bindValue(':numerotransaccion', $params['numerotransaccion']);
      $stmt->bindValue(':fechapago', $params['fechapago']);
      $stmt->bindValue(':amortizacion', $params['amortizacion']);
      $stmt->bindValue(':montomonedaoriginal', $params['montomonedaoriginal']);
      $stmt->bindValue(':tipocambioaplicado', $params['tipocambioaplicado']);
      $stmt->bindValue(':moneda', $params['moneda']);
      $stmt->bindValue(':comprobante', $params['comprobante']);
      $stmt->bindValue(':observacion', $params['observacion']);
      $stmt->execute();

      $idPago = (int) $stmt->fetchColumn();
      $stmt->closeCursor();

      return $idPago > 0 ? $idPago : 0;
    } catch (PDOException $e) {
      error_log('Error en sp_registrarVentaContado: ' . $e->getMessage());
      return 0;
    }
  }

  /**
   * Elimina un vehiculo.
   * 
   * Elimina fisicamente un registro de vehiculo en la base de datos.
   * 
   * @param int $id ID del vehiculo a eliminar
   * @return bool Resultado de la ejecucion
   */
  public function delete(int $id): int
  {
    $stmt = $this->db->prepare("DELETE FROM vehiculos WHERE idvehiculo = :id");
    return $stmt->execute([':id' => $id]);
  }

  /**
   * Obtiene el vehiculo por su ID
   * 
   * Retorna todos los datos de un vehiculo especifico.
   * 
   * @param int $id ID del vehiculo
   */
  public function getById(int $id): ?array
  {
    $stmt = $this->db->prepare('SELECT * FROM vehiculos WHERE idvehiculo = :idvehiculo');
    $stmt->bindParam(':idvehiculo', $id);
    $stmt->execute();
    $vehiculo = $stmt->fetch();
    return $vehiculo ?: null;
  }

  /**
   * Obtiene los detalles completos de un modelo de vehiculo
   * 
   * Retorna informacion detallada de un modelo incluyendo marca, tipo de vehiculo y año.
   * 
   * @param int $idmodelo ID del modelo
   */
  public function getModeloDetalle(int $idmodelo): ?array
  {
    $query = "
        SELECT 
            m.idmodelo,
            m.modelo,
            m.anio,
            m.idmarca,
            mc.marca,
            m.idtipovehiculo,
            tv.tipovehiculo
        FROM modelos m
        INNER JOIN marcas mc ON mc.idmarca = m.idmarca
        INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo
        WHERE m.idmodelo = :idmodelo
    ";

    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':idmodelo', $idmodelo, PDO::PARAM_INT);
    $stmt->execute();
    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
    return $detalle ?: null;
  }

  /**
   * Actualiza los datos de un vehiculo existente
   * 
   * Modifica la informacion de un vehiculo incluyendo modelo, version, condicion, caracteristicas fisicas y precio de venta.
   * 
   * @param int $idvehiculo ID del vehiculo a actualizar
   * @param array $data Array asociativo con los datos a actualizar 
   * @return bool True si se actualizo y false en caso de error 
   */
  public function update(int $idvehiculo, array $data): bool
  {
    $sql = "UPDATE vehiculos SET
            idmodelo = :idmodelo,
            version = :version,
            condicion = :condicion,
            idcombustible = :idcombustible,
            color = :color,
            chasis = :chasis,
            placa = :placa,
            placarotativa = :placarotativa,
            seriemotor = :seriemotor,
            moneda = :moneda,
            precioventa = :precioventa
          WHERE idvehiculo = :idvehiculo";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
      ':idmodelo' => $data['idmodelo'] ?? null,
      ':version' => $data['version'] ?? null,
      ':condicion' => $data['condicion'] ?? null,
      ':idcombustible' => $data['idcombustible'] ?? null,
      ':color' => $data['color'] ?? null,
      ':chasis' => $data['chasis'] ?? null,
      ':placa' => $data['placa'] ?? null,
      ':placarotativa' => $data['placarotativa'] ?? null,
      ':seriemotor' => $data['seriemotor'] ?? null,
      ':moneda' => $data['moneda'] ?? 'USD',
      ':precioventa' => $data['precioventa'] ?? 0,
      ':idvehiculo' => $idvehiculo,
    ]);
  }

  /**
   * Actualiza un vehiculo durante el proceso de recepcion de OC
   * 
   * Actualiza los datos fisicos del vehiculos (chasis, placa, color) y su disponibilidad durante el proceso de recepcion de orden de compra.
   * Registra el colaborador de logistica y local asignado.
   * El ID del colaborador se obtiene automaticamente de la sesion.
   * 
   * @param array $params
   * @return int
   */
  public function updateVehiculoRecepcionOc(array $params): int
  {
    $query = "CALL sp_vehiculo_update_recepcion_OC(:idvehiculo,:idlogistica,:idlocal,:chasis,:placa,:placarotativa,:seriemotor,:color,:disponibilidad)";

    try {
      $idUsuario = $_SESSION['user']['id'] ?? null;

      $stmt = $this->db->prepare($query);
      $stmt->execute([
        ':idvehiculo' => $params['idvehiculo'],
        ':idlogistica' => $idUsuario,
        ':idlocal' => $params['idlocal'],
        ':chasis' => $params['chasis'],
        ':placa' => $params['placa'],
        ':placarotativa' => $params['placarotativa'],
        ':seriemotor' => $params['seriemotor'],
        ':color' => $params['color'],
        ':disponibilidad' => $params['disponibilidad']
      ]);

      $rowAffects = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();

      return isset($rowAffects['filas_afectadas']) ? (int) $rowAffects['filas_afectadas'] : 0;
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return -1;
    }
  }

}
