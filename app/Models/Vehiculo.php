<?php
//app/Controllers/Vehiculo.php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class Vehiculo
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

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
          v.placarotativa
      FROM vehiculos v
      INNER JOIN modelos m ON m.idmodelo = v.idmodelo
      INNER JOIN marcas mc ON mc.idmarca = m.idmarca
      INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo
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
    } catch (Exception $e) {
      return [];
    }
  }

  /**
   * ejemplo de gellAll
   * @param string $estado
   * @return array
   */
  public function getAll1(string $estado = ''): array
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
        v.disponibilidad
      FROM vehiculos v
      INNER JOIN modelos m ON m.idmodelo = v.idmodelo
      INNER JOIN marcas mc ON mc.idmarca = m.idmarca
      INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo
    ";
    if ($estado !== '') {
      $query .= " WHERE v.disponibilidad = :estado";
    }
    $query .= " ORDER BY v.idvehiculo DESC";
    try {
      $stmt = $this->db->prepare($query);
      if ($estado !== '') {
        $stmt->bindValue(':estado', $estado);
      }
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

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

  /* public function getModeloDetalle(int $idmodelo): ?array
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
  } */


}