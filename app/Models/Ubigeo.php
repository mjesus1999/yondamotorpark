<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class Ubigeo
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Retorna una lista de los departamentos del Perú en orden alfabético
   * @return array
   */
  public function getAllDepartamentos(): array
  {
    $query = "SELECT * FROM departamentos ORDER BY departamento";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  /**
   * Retorna una lista de las provincias de un determinado departamento del Perú en orden alfabético
   * @param int $iddepartamento Clave primaria del departamento del que se quiere consultar sus provincias
   * @return array
   */
  public function getAllProvincias(int $iddepartamento): array
  {
    $query = "SELECT * FROM provincias WHERE iddepartamento = :iddepartamento ORDER BY provincia";

    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':iddepartamento', $iddepartamento, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  /**
   * Retorna una lista de los distritos de una determinada provincia
   * @param int $idprovincia Clave primaria de la provincia de la que se quiere consultar sus distritos
   * @return array
   */
  public function getAllDistritos(int $idprovincia): array
  {
    $query = "SELECT * FROM distritos WHERE idprovincia = :idprovincia ORDER BY distrito";

    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':idprovincia', $idprovincia, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }


  // DEYANIRA:

  public function getAllDistritosAll(): array
  {
    $sql = "SELECT iddistrito, distrito FROM distritos ORDER BY distrito";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }


}