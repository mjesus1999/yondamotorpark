<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class Modelo
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Retorna una lista de modelos de vehículos en función de la marca indicada
   * @param int $idmarca Clave primaria de la marca de los modelos a mostrar
   * @return array
   */
  public function getAll(int $idmarca): array
  {
    try {
      $stmt = $this->db->prepare("call spu_modelos_obtener_por_marca(:idmarca)");
      $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  /**
   * Retorna una lista de modelos de vehículos según la marca y tipo pasado como parámetros
   * @param int $idmarca
   * @param int $idtipovehiculo
   * @return array
   */
  public function getModelosByTipoMarca(int $idmarca, int $idtipovehiculo): array
  {
    $query = "
    SELECT
    MD.idmodelo, MD.modelo, MD.anio
    FROM modelos MD
      INNER JOIN marcas MR ON MR.idmarca = MD.idmarca
      INNER JOIN tipovehiculos TV ON TV.idtipovehiculo = MD.idtipovehiculo
      WHERE MR.idmarca = :idmarca AND TV.idtipovehiculo = :idtipovehiculo
      ORDER BY MD.modelo, MD.anio;
    ";

    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
      $stmt->bindParam(':idtipovehiculo', $idtipovehiculo, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  /**
   * Registra un nuevo modelo de vehículo, para esto se requiere su marca, tipo, modelo y año
   * @param array $params Arreglo asociativo que contiene los parámetros requeridos
   * @return int Retorna la clave primaria del nuevo registro, en caso de error un negativo
   */
  public function create(array $params): int
  {
    $query = "
    INSERT INTO modelos (idmarca, idtipovehiculo, modelo, anio) 
      VALUES	(:idmarca, :idtipovehiculo, :modelo, :anio)
    ";

    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':idmarca', $params['idmarca'], PDO::PARAM_INT);
      $stmt->bindParam(':idtipovehiculo', $params['idtipovehiculo'], PDO::PARAM_INT);
      $stmt->bindParam(':modelo', $params['modelo'], PDO::PARAM_STR);
      $stmt->bindParam(':anio', $params['anio'], PDO::PARAM_STR);
      $stmt->execute();
      return (int) $this->db->lastInsertId();
    } catch (Exception $e) {
      return -1;
    }
  }


  /**
   * Elimina físicamente un modelo de la base de datos
   * @param int $idmodelo Clave primaria del registro a eliminar
   * @return int Número de registros afectados
   */
  public function delete(int $idmodelo): int
  {
    $query = "DELETE FROM modelos WHERE idmodelo = :idmodelo";

    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':idmodelo', $idmodelo, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->rowCount();
    } catch (Exception $e) {
      return -1;
    }
  }

  public function addYearToModelo(int $idBase, int $anio): int
  {
    $sql = "INSERT INTO modelos (modelo, anio, idmarca, idtipovehiculo)
      SELECT modelo, :anio, idmarca, idtipovehiculo FROM modelos WHERE idmodelo = :idbase LIMIT 1";
    $stmt = $this->db->prepare($sql);
    if ($stmt->execute([':anio' => $anio, ':idbase' => $idBase])) {
      return (int) $this->db->lastInsertId();
    }
    return -1;
  }

  /* public function getAnosByModelo($idmodelo)
  {
    $query = "SELECT DISTINCT anio FROM modelos WHERE idmodelo = :idmodelo ORDER BY anio DESC";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':idmodelo', $idmodelo, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  } */

}