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

  public function getAll(): array
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
        INNER JOIN tipovehiculos tv ON tv.idtipovehiculo = m.idtipovehiculo;
    ";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  public function create(array $data): int
  {
    $sql = "
        INSERT INTO vehiculos (
            idmodelo,
            version,
            condicion,
            idcombustible,
            color,
            chasis,
            placa,
            placarotativa,
            seriemotor,
            moneda,
            precioventa,
            disponibilidad,
            origen,
            idlogistica,
            idlocal
        ) VALUES (
            :idmodelo,
            :version,
            :condicion,
            :idcombustible,
            :color,
            :chasis,
            :placa,
            :placarotativa,
            :seriemotor,
            :moneda,
            :precioventa,
            'proceso',
            'CTZ',
            :idlogistica,
            :idlocal
        )
    ";

    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':idmodelo', $data['idmodelo'], PDO::PARAM_INT);
      $stmt->bindParam(':version', $data['version'], PDO::PARAM_STR);
      $stmt->bindParam(':condicion', $data['condicion'], PDO::PARAM_STR);
      $stmt->bindParam(':idcombustible', $data['idcombustible'], PDO::PARAM_INT);
      $stmt->bindParam(':color', $data['color'], PDO::PARAM_STR);
      $stmt->bindParam(':chasis', $data['chasis'], PDO::PARAM_STR);
      $stmt->bindParam(':placa', $data['placa'], PDO::PARAM_STR);
      $stmt->bindParam(':placarotativa', $data['placarotativa'], PDO::PARAM_STR);
      $stmt->bindParam(':seriemotor', $data['seriemotor'], PDO::PARAM_STR);
      $stmt->bindParam(':moneda', $data['moneda'], PDO::PARAM_STR);
      $stmt->bindParam(':precioventa', $data['precioventa'], PDO::PARAM_STR);
      $stmt->bindParam(':idlogistica', $data['idlogistica'], PDO::PARAM_INT);
      $stmt->bindParam(':idlocal', $data['idlocal'], PDO::PARAM_INT);
      $stmt->execute();

      return (int) $this->db->lastInsertId();
    } catch (Exception $e) {
      throw new Exception("Error al registrar Vehiculo " . $e->getMessage());
    }
  }

  /*public function getDisponibles(int $idmarca, int $idtipovehiculo, string $modelo, string $anio): array
      {
          $sql = "
            SELECT
              v.idvehiculo,
              m.modelo,
              m.anio,
              v.version,
              v.color,
              v.placa
            FROM vehiculos v
            JOIN modelos m ON v.idmodelo = m.idmodelo
            WHERE m.idmarca = :idmarca
              AND m.idtipovehiculo = :idtipovehiculo
              AND m.modelo = :modelo
              AND m.anio = :anio
              AND v.disponibilidad = 'libre'
            ORDER BY v.version, v.placa
          ";
          try {
              $stmt = $this->db->prepare($sql);
              $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
              $stmt->bindParam(':idtipovehiculo', $idtipovehiculo, PDO::PARAM_INT);
              $stmt->bindParam(':modelo', $modelo, PDO::PARAM_STR);
              $stmt->bindParam(':anio', $anio, PDO::PARAM_STR);
              $stmt->execute();
              return $stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (Exception $e) {
              return [];
          }
      } */

}