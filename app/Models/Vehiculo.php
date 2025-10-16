<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Vehiculo
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }


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

// Cambia el tipo de retorno para incluir 'false'
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






  // Se creará los vehículos para una orden de compra - Se mandará

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


  /// DEAYANNIRA

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

  // CREAR VEHICUKO DE FORMA NATURAL.
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

  public function delete(int $id): int
  {
    $stmt = $this->db->prepare("DELETE FROM vehiculos WHERE idvehiculo = :id");
    return $stmt->execute([':id' => $id]);
  }

  public function getById(int $id): ?array
  {
    $stmt = $this->db->prepare('SELECT * FROM vehiculos WHERE idvehiculo = :idvehiculo');
    $stmt->bindParam(':idvehiculo', $id);
    $stmt->execute();
    $vehiculo = $stmt->fetch();
    return $vehiculo ?: null;
  }

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
   * Método que actualizará los datos del vehículo: chasis, placa, color y disponibilidad.
   *
   * @param array $params Arreglo asociativo con los datos a actualizar.
   * @return int Número de filas afectadas (0 si no se actualizó nada).
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
