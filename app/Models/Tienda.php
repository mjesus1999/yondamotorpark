<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Tienda
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  // Obtiene todas las tiendas, dependiendo del ID del concesionario.
  public function getTiendasByIdConcesionario($idconcesionario = -1): array
  {
    try {
      $stmt = $this->db->prepare('call spu_tiendas_por_concesionario(?)');
      $stmt->execute(array($idconcesionario));
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $results;
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return [];
    }
  }

  // Obtiene los datos de la tienda para actualizar.

  public function getTiendasById($idTienda = -1): array
  {
    try {
      $stmt = $this->db->prepare('call spu_tiendas_obtener(?)');
      $stmt->execute(array($idTienda));
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $results;
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return [];
    }
  }

  // Crear la tienda

  public function create($params = []): int
  {
    $query = 'INSERT INTO tiendas (iddistrito, idconcesionario, direccion, email, telefono, contacto) VALUES (:iddistrito,:idconcesionario,:direccion,:email,:telefono,:contacto)';
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute(
        array(
          ':iddistrito' => $params['iddistrito'],
          ':idconcesionario' => $params['idconcesionario'],
          ':direccion' => $params['direccion'],
          ':email' => $params['email'],
          ':telefono' => $params['telefono'],
          ':contacto' => $params['contacto']
        )
      );
      return (int) $this->db->lastInsertId();
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return -1;
    }
  }

  public function update($params = []): int
  {
    $query = '
          UPDATE tiendas SET
            iddistrito = :iddistrito,
            direccion = :direccion,
            email = :email,
            telefono = :telefono,
            contacto = :contacto
          WHERE idtienda = :idtienda
        ';
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute(
        array(
          ':iddistrito' => $params['iddistrito'],
          ':direccion' => $params['direccion'],
          ':email' => $params['email'],
          ':telefono' => $params['telefono'],
          ':contacto' => $params['contacto'],
          ':idtienda' => $params['idtienda']
        )
      );

      return (int) $stmt->rowCount();
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return -1;
    }
  }

  public function delete($idTienda = -1): int
  {
    try {
      $stmt = $this->db->prepare('DELETE FROM tiendas WHERE idtienda = ?');
      $stmt->execute(array($idTienda));
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return (int) $stmt->rowCount();
    } catch (PDOException $error) {
      error_log($error->getMessage());
      return -1;
    }
  }
}
