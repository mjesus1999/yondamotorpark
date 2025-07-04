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
    $sql = "
      CALL spu_pers_registrar(
        :tipodoc, :nrodoc, :apellidos, :nombres,
        :genero, :fechanac, :estadocivil, :email,
        :iddistrito, :direccion, :referencia,
        :telprimario, :telalternativo
      )
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':tipodoc', $tipodoc);
    $stmt->bindParam(':nrodoc', $nrodoc);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':nombres', $nombres);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':fechanac', $fechanac);
    $stmt->bindParam(':estadocivil', $estadocivil);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':iddistrito', $iddistrito, PDO::PARAM_INT);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':referencia', $referencia);
    $stmt->bindParam(':telprimario', $telprimario);
    $stmt->bindParam(':telalternativo', $telalternativo);

    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return isset($row['last_id']) ? (int) $row['last_id'] : 0;
  }

  public function createContratoLaboral(
    int $idPersona,
    int $idCargo,
    string $fechaInicio,
    ?string $fechaFin,
    string $tipoContrato
  ): int {
    $sql = "
      INSERT INTO contratoslaborales
        (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
      VALUES
        (:idpersona, :idcargo, :fechainicio, :fechafin, :tipocontrato)
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':idpersona', $idPersona, PDO::PARAM_INT);
    $stmt->bindParam(':idcargo', $idCargo, PDO::PARAM_INT);
    $stmt->bindParam(':fechainicio', $fechaInicio);
    if ($fechaFin !== null) {
      $stmt->bindParam(':fechafin', $fechaFin);
    } else {
      // para NULL hay que usar bindValue con PDO::PARAM_NULL
      $stmt->bindValue(':fechafin', null, PDO::PARAM_NULL);
    }
    $stmt->bindParam(':tipocontrato', $tipoContrato);

    $stmt->execute();
    return (int) $this->db->lastInsertId();
  }

  public function createColaborador(
    int $idContrato,
    string $usernick,
    string $passHash
  ): int {
    $sql = "
      INSERT INTO colaboradores
        (idcontratolaboral, usernick, userpassword)
      VALUES
        (:idcontratolaboral, :usernick, :userpassword)
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':idcontratolaboral', $idContrato, PDO::PARAM_INT);
    $stmt->bindParam(':usernick', $usernick);
    $stmt->bindParam(':userpassword', $passHash);

    $stmt->execute();
    return (int) $this->db->lastInsertId();
  }

  public function create(int $idPersona, array $c, array $u): array
  {
    try {
      $this->db->beginTransaction();

      $idContrato = $this->createContratoLaboral(
        $idPersona,
        $c['idcargo'],
        $c['fechainicio'],
        $c['fechafin'],
        $c['tipocontrato']
      );

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
    $sql = "
      SELECT idpersona, apellidos, nombres
      FROM personas
      WHERE nrodoc = :dni
      LIMIT 1
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

  public function updatePassword(int $idColaborador, string $newHash): bool
  {
    $sql = "
      UPDATE colaboradores
        SET userpassword = :userpassword
      WHERE idcolaborador = :idcolaborador
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':userpassword', $newHash);
    $stmt->bindParam(':idcolaborador', $idColaborador, PDO::PARAM_INT);
    return $stmt->execute();
  }

}