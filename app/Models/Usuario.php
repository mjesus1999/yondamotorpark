<?php
/**
 * Modelo de Usuario
 * app/Models/Usuario.php
 * 
 * Gestiona todas las operaciones de base de datos relacionadas con:
 * - Usuarios
 * - Colaboradores
 * - Areas
 * - Cargos
 * - Autenticacion del sistema
 * 
 */

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

/**
 * Clase Usuario
 * 
 * Modelo principal para la gestion de usuarios del sistema.
 * Proporciona metodos para CRUD de:
 * - Usuarios
 * - Autenticacion
 * - Perfiles
 * - Restriccion horaria
 * - Relaciones con contratos laborales
 */

class Usuario
{

  /**
   * Instancia de conexion a la base de datos
   * @var PDO
   */
  private PDO $db;

  /**
   * Constructor del modelo
   * 
   * Inicializa la conexion a la base de datos.
   */
  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Obtiene todos los usuarios del sistema
   * 
   * Retorna un listado completo de usuarios la vista vwGetAllUser, ordenados por ID descendente con limite de 1000 registros.
   * 
   * @return array Array asociativo con los datos de todos los usuarios
   */
  public function getAll(): array
  {
    $query = "SELECT * FROM vwGetAllUser ORDER BY idcolaborador DESC LIMIT 1000";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      // log $e->getMessage()
      return [];
    }
  }

  /**
   * Obtiene todas las areas disponibles
   * 
   * Retorna el listado de areas organizacionales ordenadas alfabeticamente.
   * 
   * @return array Array asociativo con idarea y nombre del area
   */
  public function getAllAreas(): array
  {
    $query = "SELECT idarea, area FROM areas ORDER BY area";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      // log $e->getMessage()
      return [];
    }
  }

  /**
   * Obtiene los cargos filtrados por area
   * 
   * Retorna los cargos disponibles para un area organizacial especifica
   * 
   * @param int $idArea ID del area a consultar
   * @return array Array asociativo con idcargo y nombre del cargo
   */
  public function getCargosByArea(int $idArea): array
  {
    $query = "SELECT idcargo, cargo FROM cargos WHERE idarea = :idarea ORDER BY cargo";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':idarea', $idArea, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      // log $e->getMessage()
      return [];
    }
  }

  /**
   * Actualiza la contraseña de un colaborador
   * 
   * Actualiza el hash de la contraseña en la base de datos para el colaborador especifico.
   * 
   * @param int $idColaborador ID del colaborador
   * @param string $newHash Hash de la nueva contraseña
   * @return bool True si se actualizo correctamente, false en caso contrario
   */
  public function updatePassword(int $idColaborador, string $newHash): bool
  {
    $query = "UPDATE colaboradores SET userpassword = :userpassword WHERE idcolaborador = :idcolaborador";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':userpassword', $newHash, PDO::PARAM_STR);
      $stmt->bindValue(':idcolaborador', $idColaborador, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (Exception $e) {
      // log $e->getMessage()
      return false;
    }
  }

  /**
   * Deshabilita un usuario del sistema
   * 
   * Marca el usuario como deshabilitado, ofuscando su nombre de usuario y contraseña para evitar reutilizacion.
   * No elimina el registro fisicamente
   * 
   * @param int $id ID del colaborador a deshabilitar
   * @return bool True si se deshabilito correctamente, false en caso contrario
   */
  public function disabled(int $id): bool
  {
    $query = "UPDATE colaboradores
              SET habilitado = 'N',
                  usernick = CONCAT('deleted_', idcolaborador, '_', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')),
                  userpassword = CONCAT('deleted_', UUID()),
                  modificado = NOW()
              WHERE idcolaborador = :id";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (Exception $e) {
      // log $e->getMessage()
      return false;
    }
  }

  /**
   * Busca un usuario por su nombre de usuario
   * 
   * Realiza una busqueda sensible a mayusculas/minusculas del nombre de usuario.
   * Utilizado principalmente en el proceso de autenticacion.
   * 
   * @param string $usernick Nombre de usuario a buscar
   * @return array|null Array con los datos del usuario o null si no se encuentra
   */
  public function searchByUsernick(string $usernick): ?array
{
    
    $query = "SELECT * FROM vwsearchusernick WHERE usernick = :usernick LIMIT 1";
    
    try {
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':usernick', $usernick, PDO::PARAM_STR);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($row) {
            error_log("Usuario encontrado: " . print_r($row, true));
        } else {
            error_log("Usuario NO encontrado para el nick: " . $usernick);
        }

        return $row ?: null;
    } catch (Exception $e) {
        
        error_log("Error en searchByUsernick: " . $e->getMessage());
        return null;
    }
}

  /**
   * Obtiene un usuario por su ID
   * 
   * Retorna los datos detallados de un colaborador especifico mediante la vista vwGetUserDetails
   * 
   * @param int $idColab ID del colaborador
   * @return array|null Array con los datos del usuario o null si no existe
   */
  public function getById(int $idColab): ?array
  {
    $query = "SELECT * FROM vwGetUserDetail WHERE idcolaborador = :id";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':id', $idColab, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Exception $e) {
      // log $e->getMessage()
      return null;
    }
  }

  /**
   * Actualiza la fecha del ultimo acceso del usuario
   * 
   * Registra la fecha y hora actual en el campo ultimo acceso del colaborador.
   * Util para auditoria y control de sesiones.
   * 
   * @param int $idColab ID del colaborador
   * @return bool True si se actualizo correctamente, false en caso contrario
   */
  public function updateLastAccess(int $idColab): bool
  {
    $query = "UPDATE colaboradores SET ultimoacceso = NOW() WHERE idcolaborador = :id";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':id', $idColab, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (Exception $e) {
      // log $e->getMessage()
      return false;
    }
  }

  /**
   * Actualiza el avatar del usuario
   * 
   * Guarda la url del avatar en la base de datos y actualiza la fecha de modificacion del registro.
   * 
   * @param int $idColab ID del colaborador
   * @param string $url URL del nuevo avatar
   * @return bool True si se actualizo correctamente, false en caso contrario
   */
  public function updateAvatar(int $idColab, string $url): bool
  {
    $query = "UPDATE colaboradores SET avatar = :url, modificado = NOW() WHERE idcolaborador = :id";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':url', $url, PDO::PARAM_STR);
      $stmt->bindValue(':id', $idColab, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (Exception $e) {
      // log $e->getMessage()
      return false;
    }
  }

  /**
   * Verifica si un colaborador pertenece al area de Logistica
   * 
   * Comprueba si el colaborador esta asignado al area de Logistica (ID->9 temporal)
   * Util para permisos y restricciones especificas del area
   * 
   * @param int $idcolaborador ID del colaborador e verificar
   * @return bool True si pertenece a Logistica, false en caso contrario
   */
  public function esDeLogistica(int $idcolaborador): bool
  {
    $query = "
      SELECT a.idarea
      FROM colaboradores c
      INNER JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
      INNER JOIN cargos ca ON cl.idcargo = ca.idcargo
      INNER JOIN areas a ON ca.idarea = a.idarea
      WHERE c.idcolaborador = :id
      LIMIT 1";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':id', $idcolaborador, PDO::PARAM_INT);
      $stmt->execute();
      $areaId = $stmt->fetchColumn();
      if ($areaId === false) {
        return false;
      }
      return ((int) $areaId === 9); // 9 => Logística
    } catch (Exception $e) {
      // log $e->getMessage()
      return false;
    }
  }

  /**
   * Obtiene contratos laborales sin colaborador asginado
   * 
   * Retorna los contratos que no tienen un Usuario/Colaborador asociado, util para la creacion de cuentas desde contratos existentes.
   * 
   * @return array Array asociativo con los contratos sin colaborador
   */
  public function getContractsWithoutColaborador(): array
  {
    $query = "SELECT * FROM vwContractsWithoutColaborador ORDER BY fechainicio DESC LIMIT 0,1000";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      // log $e->getMessage()
      return [];
    }
  }

  /**
   * Obtiene el estado de restriccion horaria de un colaborador
   * 
   * Consulta si el colaborador tiene restriccion horaria activa.
   * 
   * @param int $idcolaborador ID del colaborador 
   * @return string|null 'S' si tiene restriccion, 'N' si no, null en caso de error
   */
  public function getRestriccionHoraria(int $idcolaborador): ?string
  {
    $sql = "SELECT restriccionhoraria FROM colaboradores WHERE idcolaborador = :id LIMIT 1";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':id', $idcolaborador, PDO::PARAM_INT);
      $stmt->execute();
      $val = $stmt->fetchColumn();
      return $val === false ? null : (string) $val;
    } catch (\Throwable $e) {
      //log $e->getMessage()
      return null;
    }
  }

  /**
   * Establece el estado de restriccion horaria
   * 
   * Actualiza el valor de restriccion horaria a 
   * 'S' (con restriccion) o 'N' (sin restriccion) 
   * para un colaborador especifico.
   * 
   * @param int $idcolaborador ID del colaborador
   * @param string $valor Valor de restriccion: 'S' o 'N'
   * @return bool True si se actualizo correctamente, false en caso contrario
   */
  public function setRestriccionHoraria(int $idcolaborador, string $valor): bool
  {
    $valor = strtoupper($valor) === 'S' ? 'S' : 'N';
    $sql = "UPDATE colaboradores SET restriccionhoraria = :v, modificado = NOW() WHERE idcolaborador = :id";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':v', $valor, PDO::PARAM_STR);
      $stmt->bindValue(':id', $idcolaborador, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (\Throwable $e) {
      //log $e->getMessage()
      return false;
    }
  }

  /**
   * Actualiza los datos completos de un usuario
   * 
   * Actualiza informacion personal, laboral y de ubicacion del colaborador.
   * Realiza validaciones cruzadas y ejecuta todas las operaciones dentro de una transaccion
   * para garantizar la integridad de los datos.
   * 
   * @param int $idColaborador    ID del colaborador a actualizar
   * @param string $nombres       Nombres de la persona
   * @param string $apellidos     Apellidos de la persona
   * @param int $idArea           ID del area organizacional
   * @param int $idCargo          ID del cargo
   * @param string $nrodoc        Numero de documento de identidad
   * @param string $fechaInicio   Fecha de inicio del contrato
   * @param int|null $idLocal        ID del local asignado
   * @return bool True si se actualizo correctamente, false en caso contrario
   */
  public function update(int $idColaborador, string $nombres, string $apellidos, int $idArea, int $idCargo, string $nrodoc, string $fechaInicio, ?int $idLocal = null): bool
  {
    try {
      $this->db->beginTransaction();

      // 1) obtener idcontratolaboral desde colaboradores
      $sql = "SELECT idcontratolaboral FROM colaboradores WHERE idcolaborador = :id LIMIT 1";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':id', $idColaborador, PDO::PARAM_INT);
      $stmt->execute();
      $idContrato = $stmt->fetchColumn();
      if ($idContrato === false) {
        $this->db->rollBack();
        return false; // colaborador no encontrado
      }

      // 2) obtener idpersona desde contratoslaborales
      $sql = "SELECT idpersona FROM contratoslaborales WHERE idcontratolaboral = :id LIMIT 1";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':id', $idContrato, PDO::PARAM_INT);
      $stmt->execute();
      $idPersona = $stmt->fetchColumn();
      if ($idPersona === false) {
        $this->db->rollBack();
        return false; // contrato sin persona => no
      }

      // 3) validar que el cargo pertenezca al area
      $sql = "SELECT COUNT(1) FROM cargos WHERE idcargo = :idcargo AND idarea = :idarea";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':idcargo', $idCargo, PDO::PARAM_INT);
      $stmt->bindValue(':idarea', $idArea, PDO::PARAM_INT);
      $stmt->execute();
      $validCargo = (bool) $stmt->fetchColumn();
      if (!$validCargo) {
        $this->db->rollBack();
        return false; // cargo no pertenece al area indicada
      }

      // 4) actualizar tabla personas
      $sql = "UPDATE personas SET nombres = :nombres, apellidos = :apellidos, nrodoc = :nrodoc WHERE idpersona = :idpersona";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':nombres', $nombres, PDO::PARAM_STR);
      $stmt->bindValue(':apellidos', $apellidos, PDO::PARAM_STR);
      $stmt->bindValue(':nrodoc', $nrodoc, PDO::PARAM_STR);
      $stmt->bindValue(':idpersona', (int) $idPersona, PDO::PARAM_INT);
      $stmt->execute();

      // 5) actualizar contrato (idcargo y fechainicio)
      $sql = "UPDATE contratoslaborales SET idcargo = :idcargo, fechainicio = :fechainicio, modificado = NOW() WHERE idcontratolaboral = :idcontrato";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(':idcargo', $idCargo, PDO::PARAM_INT);
      $stmt->bindValue(':fechainicio', $fechaInicio, PDO::PARAM_STR);
      $stmt->bindValue(':idcontrato', (int) $idContrato, PDO::PARAM_INT);
      $stmt->execute();

      // 6) actualizar colaboradores.idlocal si se paso
      if ($idLocal !== null) {
        $sql = "SELECT COUNT(1) FROM locales WHERE idlocal = :idlocal";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':idlocal', $idLocal, PDO::PARAM_INT);
        $stmt->execute();
        $exists = (bool) $stmt->fetchColumn();
        if (!$exists) {
          $this->db->rollBack();
          return false; // idlocal inválido
        }

        $sql = "UPDATE colaboradores SET idlocal = :idlocal, modificado = NOW() WHERE idcolaborador = :idcolab";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':idlocal', $idLocal, PDO::PARAM_INT);
        $stmt->bindValue(':idcolab', $idColaborador, PDO::PARAM_INT);
        $stmt->execute();
      }

      $this->db->commit();
      return true;
    } catch (\Throwable $e) {
      if ($this->db->inTransaction()) {
        $this->db->rollBack();
      }
      // log $e->getMessage()
      return false;
    }
  }

}