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
            return (int)$idVehiculo['last_id'];

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
}
