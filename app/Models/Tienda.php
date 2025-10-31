<?php

/**
 * Modelo de Tienda
 * 
 * app/Models/Tienda.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las tiendas
 * o sucursales de los concesionarios, incluyendo información de ubicación,
 * contacto y relación con concesionarios.
 * 
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Tienda
 * 
 * Modelo para la gestión de tiendas/sucursales de concesionarios.
 * Proporciona métodos CRUD completos para administrar tiendas,
 * incluyendo búsquedas por concesionario y gestión de datos de contacto.
 * 
 */
class Tienda
{

  /**
   * Instancia de conexion de la base de datos
   * @var PDO
   */
  private PDO $db;

  /**
   * Constructor del modelo
   * 
   * Inicializa la conexión a la base de datos
   */
  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Obtiene todas las tiendas de un concesionario específico
   * 
   * Ejecuta el procedimiento almacenado que retorna el listado de tiendas
   * asociadas a un concesionario determinado.
   * 
   * @param int $idconcesionario ID del concesionario a consulta
   * @return array Array asociativo con las tiendas del concesionario
   *               o array vacío en caso de error
   */
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

  /**
   * Obtiene los datos de una tienda por su ID
   * 
   * Ejecuta el procedimiento almacenado que retorna la información
   * completa de una tienda específica para su visualización o edición.
   * 
   * @param int $idTienda ID de la tienda a consultar
   * @return array Array asociativo con los datos de la tienda
   *               o array vacío en caso de error
   */
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

  /**
   * Crea una nueva tienda en el sistema
   * 
   * Registra una nueva tienda/sucursal asociada a un concesionario,
   * incluyendo información de ubicación y contacto.
   * 
   * @param array $params Array asociativo con los datos de la tienda:
   *                      - iddistrito: int (ID del distrito)
   *                      - idconcesionario: int (ID del concesionario propietario)
   *                      - direccion: string (Dirección física de la tienda)
   *                      - email: string (Email de contacto)
   *                      - telefono: string (Teléfono de contacto)
   *                      - contacto: string (Nombre de la persona de contacto)
   * @return int ID de la tienda creada o -1 en caso de error
   */
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

  /**
   * Actualiza los datos de una tienda existente
   * 
   * Modifica la información de ubicación y contacto de una tienda.
   * No permite modificar el concesionario propietario.
   * 
   * @param array $params Array asociativo con los datos a actualizar:
   *                      - idtienda: int (ID de la tienda, requerido)
   *                      - iddistrito: int (Nuevo distrito)
   *                      - direccion: string (Nueva dirección)
   *                      - email: string (Nuevo email)
   *                      - telefono: string (Nuevo teléfono)
   *                      - contacto: string (Nuevo contacto)
   * @return int Número de filas afectadas o -1 en caso de error
   */
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

  /**
   * Elimina una tienda del sistema
   * 
   * Elimina físicamente una tienda de la base de datos.
   * Esta operación puede fallar si existen registros relacionados
   * debido a restricciones de integridad referencial.
   * 
   * @param int $idTienda ID de la tienda a eliminar
   * @return int Número de filas eliminadas (1 si exitoso, 0 si no existe)
   *             o -1 en caso de error
   */
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
