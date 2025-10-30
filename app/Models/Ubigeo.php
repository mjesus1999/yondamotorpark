<?php

/**
 * Modelo de Ubigeo
 * 
 * Gestiona las operaciones de base de datos relacionadas con la división
 * política territorial del Perú, incluyendo departamentos, provincias y distritos.
 * Proporciona datos de ubicación geográfica para el sistema.
 * 
 */
namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

/**
 * Clase Ubigeo
 * 
 * Modelo para la gestión de datos de ubicación geográfica del Perú.
 * Proporciona métodos de consulta para obtener departamentos, provincias
 * y distritos de forma jerárquica y ordenada alfabéticamente.
 */
class Ubigeo
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
   * Obtiene todos los departamentos del Perú
   * 
   * Retorna una lista completa de los departamentos del Perú
   * ordenados alfabéticamente por nombre.
   * 
   * @return array Array asociativo con los departamentos (iddepartamento, departamento)
   *               o array vacío en caso de error
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
   * Obtiene todas las provincias de un departamento específico
   * 
   * Retorna una lista de provincias pertenecientes a un departamento
   * determinado, ordenadas alfabéticamente por nombre.
   * 
   * @param int $iddepartamento ID del departamento del cual consultar sus provincias
   * @return array Array asociativo con las provincias (idprovincia, provincia, iddepartamento)
   *               o array vacío en caso de error
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
   * Obtiene todos los distritos de una provincia específica
   * 
   * Retorna una lista de distritos pertenecientes a una provincia
   * determinada, ordenados alfabéticamente por nombre.
   * 
   * @param int $idprovincia ID de la provincia de la cual consultar sus distritos
   * @return array Array asociativo con los distritos (iddistrito, distrito, idprovincia)
   *               o array vacío en caso de error
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

  /**
   * Obtiene todos los distritos del Perú
   * 
   * Retorna una lista completa de todos los distritos del Perú
   * sin filtrar por provincia o departamento, ordenados alfabéticamente.
   * Útil para listados generales o búsquedas sin jerarquía.
   * 
   * @return array Array asociativo con los distritos (iddistrito, distrito)
   *               o array vacío en caso de error
   */
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