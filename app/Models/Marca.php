<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class Marca
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Retorna una arreglo conteniendo una lista de marcas
   * @return array
   */
  public function getAll(): array
  {
    $query = "
    SELECT
      MR.idmarca, MR.marca, COUNT(MD.idmodelo) 'modelos'
      FROM marcas MR
        LEFT JOIN modelos MD ON MD.idmarca = MR.idmarca
        GROUP BY MR.idmarca, MR.marca;
    ";
    try{
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e){
      return [];
    }
  }

  /**
   * Registra una nueva marca
   * @param string $marca Marca a registrar
   * @return int Retorna la PK obtenida, en caso de error, retornará negativo
   */
  public function create(string $marca = ''): int
  {
    $query = "INSERT INTO marcas (marca) VALUES (:marca)";

    try{
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
      $stmt->execute();
      return (int) $this->db->lastInsertId();
    }
    catch(Exception $e){
      return -1;
    }
  }

  /**
   * Elimina de manera física un registro
   * @param int $idmarca Clave primaria que para identificar el registro a eliminar
   * @return int Cantidad de registros afectados, 0 indica que no se borró ningún registro, un negativo que el proceso generó una excepción
   */
  public function delete(int $idmarca): int
  {
    $query = "DELETE FROM marcas WHERE idmarca = :idmarca";

    try{
      $stmt = $this->db->prepare($query);
      $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->rowCount();
    }
    catch(Exception $e){
      return -1;
    }
  }
}