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

  public function getAll(string $estado = ''): array
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

}