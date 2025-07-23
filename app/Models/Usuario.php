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
    $query = "
    SELECT
      p.idpersona         AS idpersona,
      p.apellidos         AS apellidos,
      p.nombres           AS nombres,
      a.area              AS area,
      cg.cargo            AS cargo,
      cl.fechainicio      AS fecha_inicio,
      IFNULL(
        DATE_FORMAT(cl.fechafin, '%Y-%m-%d'),
        'Indeterminado'
      )                   AS fecha_fin,
      col.idcolaborador   AS idcolaborador, 
      col.usernick        AS usuario
    FROM personas p
    INNER JOIN contratoslaborales cl
      ON cl.idpersona = p.idpersona
    INNER JOIN cargos cg
      ON cg.idcargo = cl.idcargo
    INNER JOIN areas a
      ON a.idarea = cg.idarea
    INNER JOIN colaboradores col
      ON col.idcontratolaboral = cl.idcontratolaboral
    WHERE col.habilitado = 'S'
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

  public function searchByDNI(string $dni): ?array
  {
    $query = "
      SELECT idpersona, apellidos, nombres
      FROM personas
      WHERE nrodoc = :dni
      LIMIT 1
    ";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

  public function updatePassword(int $idColaborador, string $newHash): bool
  {
    $query = "
      UPDATE colaboradores
        SET userpassword = :userpassword
      WHERE idcolaborador = :idcolaborador
    ";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':userpassword', $newHash);
    $stmt->bindParam(':idcolaborador', $idColaborador, PDO::PARAM_INT);
    return $stmt->execute();
  }

  public function disabled(int $id): bool
  {
    $stmt = $this->db->prepare("
        UPDATE colaboradores
        SET habilitado = 'N'
        WHERE idcolaborador = :id
    ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  // CONSULTAS PARA EL LOGIN

  //buscar por nombre de usuario

  public function searchByUsernick(string $usernick): ?array
  {
    $query = "SELECT col.idcolaborador,
              col.usernick,
              col.userpassword,
              col.habilitado,
              col.avatar,
              p.nombres,
              p.apellidos,
              cl.idcargo,
              cg.cargo
            FROM colaboradores col
            JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
            JOIN personas p ON p.idpersona = cl.idpersona
            JOIN cargos cg ON cg.idcargo = cl.idcargo
            WHERE BINARY col.usernick = :usernick
            LIMIT 1";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':usernick', $usernick);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

  //mostrar el usuario por id
  public function getById(int $idColab): ?array
  {
    $stmt = $this->db->prepare("
      SELECT
        col.usernick,
        col.avatar,
        p.apellidos,
        p.nombres,
        p.tipodoc,
        p.nrodoc,
        p.genero,
        DATE_FORMAT(p.fechanac, '%Y-%m-%d') AS fechanac,
        p.estadocivil,
        p.email,
        p.iddistrito,
        d.distrito AS nombre_distrito,
        p.direccion,
        p.referencia,
        p.telprimario,
        p.telalternativo,
        DATE_FORMAT(cl.fechainicio, '%Y-%m-%d') AS fechainicio,
        IFNULL(DATE_FORMAT(cl.fechafin, '%Y-%m-%d'), 'Indeterminado') AS fechafin,
        cg.cargo,
        a.area
      FROM colaboradores col
      JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
      JOIN personas p         ON p.idpersona       = cl.idpersona
      JOIN cargos cg          ON cg.idcargo        = cl.idcargo
      JOIN areas a            ON a.idarea          = cg.idarea
      LEFT JOIN distritos d   ON d.iddistrito      = p.iddistrito
      WHERE col.idcolaborador = :id
    ");
    $stmt->bindValue(':id', $idColab, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
  }

  //actualizar avatar

  public function updateAvatar(int $idColab, string $url): bool
  {
    $stmt = $this->db->prepare("
      UPDATE colaboradores 
      SET avatar = :url, modificado = NOW() 
      WHERE idcolaborador = :id
    ");
    $stmt->bindValue(':url', $url);
    $stmt->bindValue(':id', $idColab, PDO::PARAM_INT);
    return $stmt->execute();
  }

  //IDLOGISTICA:

  public function esDeLogistica(int $idcolaborador): bool
  {
    $stmt = $this->db->prepare("
      SELECT a.idarea
      FROM colaboradores c
      INNER JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
      INNER JOIN cargos ca ON cl.idcargo = ca.idcargo
      INNER JOIN areas a ON ca.idarea = a.idarea
      WHERE c.idcolaborador = :id
      LIMIT 1
    ");
    $stmt->execute([':id' => $idcolaborador]);
    $areaId = $stmt->fetchColumn();

    return (int) $areaId === 9; // Logística
  }

  /* public function delete(int $id): bool
{
  $stmt = $this->db->prepare("
    DELETE
    FROM colaboradores
    WHERE idcolaborador = :id
  ");
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  return $stmt->execute();
} */

}