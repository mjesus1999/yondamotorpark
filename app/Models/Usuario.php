<?php
// app/Models/Usuario.php
namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class Usuario
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  /**
   * Lista los usuarios
   * @return array
   */
  public function getAll(): array
  {
    $query = "SELECT
      p.idpersona     AS idpersona,
      p.apellidos     AS apellidos,
      p.nombres       AS nombres,
      a.area          AS area,
      cg.cargo        AS cargo,
      cl.fechainicio  AS fecha_inicio,
      IFNULL(
        DATE_FORMAT(cl.fechafin, '%Y-%m-%d'),
        'Indeterminado'
      )               AS fecha_fin,
      col.idcolaborador AS idcolaborador, 
      col.usernick    AS usuario
    FROM personas p
    INNER JOIN contratoslaborales cl
      ON cl.idpersona = p.idpersona
    INNER JOIN cargos cg
      ON cg.idcargo = cl.idcargo
    INNER JOIN areas a
      ON a.idarea = cg.idarea
    INNER JOIN colaboradores col
      ON col.idcontratolaboral = cl.idcontratolaboral
    ORDER BY p.idpersona
    LIMIT 0,1000;
    ";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  /**
   * listar las areas
   * @return array
   */
  public function getAllAreas(): array
  {
    $query = "SELECT idarea, area FROM areas ORDER BY area";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  /**
   * conforme a la area seleccionada se muestran los campos
   * @param int $idArea
   * @return array
   */
  public function getCargosByArea(int $idArea): array
  {
    $query = "SELECT idcargo, cargo
            FROM cargos
            WHERE idarea = :idarea
            ORDER BY cargo";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':idarea', $idArea, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  /**
   * Summary of createPersona
   * @param string $tipodoc
   * @param string $nrodoc
   * @param string $apellidos
   * @param string $nombres
   * @param string $genero
   * @param string $fechanac
   * @param string $estadocivil
   * @param mixed $email
   * @param int $iddistrito
   * @param mixed $direccion
   * @param mixed $referencia
   * @param string $telprimario
   * @param mixed $telalternativo
   * @return int
   */
  public function createPersona(
    string $tipodoc,
    string $nrodoc,
    string $apellidos,
    string $nombres,
    string $genero,
    string $fechanac,
    string $estadocivil,
    ?string $email,
    int $iddistrito,
    ?string $direccion,
    ?string $referencia,
    string $telprimario,
    ?string $telalternativo
  ): int {
    try {
      // Preparamos la llamada al SP
      $stmt = $this->db->prepare("
        CALL spu_pers_registrar(
            :tipodoc,
            :nrodoc,
            :apellidos,
            :nombres,
            :genero,
            :fechanac,
            :estadocivil,
            :email,
            :iddistrito,
            :direccion,
            :referencia,
            :telprimario,
            :telalternativo
          )
      ");

      // Vinculamos parámetros
      $stmt->bindParam(':tipodoc', $tipodoc);
      $stmt->bindParam(':nrodoc', $nrodoc);
      $stmt->bindParam(':apellidos', $apellidos);
      $stmt->bindParam(':nombres', $nombres);
      $stmt->bindParam(':genero', $genero);
      $stmt->bindParam(':fechanac', $fechanac);
      $stmt->bindParam(':estadocivil', $estadocivil);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':iddistrito', $iddistrito);
      $stmt->bindParam(':direccion', $direccion);
      $stmt->bindParam(':referencia', $referencia);
      $stmt->bindParam(':telprimario', $telprimario);
      $stmt->bindParam(':telalternativo', $telalternativo);

      $stmt->execute();

      // El SP devuelve un resultset con { last_id }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();

      return isset($row['last_id']) ? (int) $row['last_id'] : 0;
    } catch (Exception $e) {
      error_log("SP spu_pers_registrar fallo: " . $e->getMessage());
      // 2) Vuelve a lanzar para que el controller lo capture
      throw $e;
    }
  }


  /**
   * Registrar un contrato
   * @param int $idPersona
   * @param int $idCargo
   * @param string $fechaInicio
   * @param mixed $fechaFin
   * @param string $tipoContrato
   * @return int
   */
  public function createContratoLaboral(int $idPersona, int $idCargo, string $fechaInicio, ?string $fechaFin, string $tipoContrato): int
  {
    $sql = "INSERT INTO contratoslaborales
          (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
        VALUES
          (:idpersona, :idcargo, :fechainicio, :fechafin, :tipocontrato)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':idpersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindValue(':idcargo', $idCargo, PDO::PARAM_INT);
    $stmt->bindValue(':fechainicio', $fechaInicio);
    if ($fechaFin !== null) {
      // Si hay fecha, la pasamos como string (por defecto PDO::PARAM_STR)
      $stmt->bindValue(':fechafin', $fechaFin);
    } else {
      // Si no hay fecha, lo bindearmos explícitamente como NULL
      $stmt->bindValue(':fechafin', null, PDO::PARAM_NULL);
    }
    $stmt->bindValue(':tipocontrato', $tipoContrato);
    $stmt->execute();
    return (int) $this->db->lastInsertId();
  }

  /**
   * Registrar un colaborador
   * @param int $idContrato
   * @param string $usernick
   * @param string $passHash
   * @return int
   */
  public function createColaborador(int $idContrato, string $usernick, string $passHash): int
  {
    $sql = "INSERT INTO colaboradores
          (idcontratolaboral, usernick, userpassword)
        VALUES
          (:idcontratolaboral, :usernick, :userpassword)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':idcontratolaboral', $idContrato, PDO::PARAM_INT);
    $stmt->bindValue(':usernick', $usernick);
    $stmt->bindValue(':userpassword', $passHash);
    $stmt->execute();
    return (int) $this->db->lastInsertId();
  }

  /**
   * Orquesta persona + contrato + colaborador en transacción
   */

  public function create(int $idPersona, array $c, array $u): array
  {
    try {
      $this->db->beginTransaction();

      // 1) Contrato
      $idContrato = $this->createContratoLaboral(
        $idPersona,
        $c['idcargo'],
        $c['fechainicio'],
        $c['fechafin'],
        $c['tipocontrato']
      );

      // 2) Colaborador
      $idColab = $this->createColaborador(
        $idContrato,
        $u['usernick'],
        $u['userpassword']
      );

      $this->db->commit();

      return [
        'idcontratolaboral' => $idContrato,
        'idcolaborador' => $idColab,
      ];
    } catch (Exception $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function searchByDNI(string $dni): ?array
  {
    $sql = "SELECT idpersona, apellidos, nombres
            FROM personas
            WHERE nrodoc = :dni
            LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':dni', $dni);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row !== false ? $row : null;
  }

  public function updatePassword(int $idColaborador, string $newHash): bool
  {
    $sql = "UPDATE colaboradores
            SET userpassword = :userpassword
            WHERE idcolaborador = :idcolaborador";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':userpassword', $newHash);
    $stmt->bindValue(':idcolaborador', $idColaborador, PDO::PARAM_INT);
    return $stmt->execute();
  }
}