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
        col.idcolaborador   AS idcolaborador,
        p.apellidos         AS apellidos,
        p.nombres           AS nombres,
        a.area              AS area,
        cg.cargo            AS cargo,
        DATE_FORMAT(cl.fechainicio, '%Y-%m-%d') AS fecha_inicio,
        IFNULL(
          DATE_FORMAT(cl.fechafin, '%Y-%m-%d'),
          'Indeterminado'
        )                   AS fecha_fin,
        col.usernick        AS usuario
      FROM colaboradores col
      INNER JOIN contratoslaborales cl
        ON col.idcontratolaboral = cl.idcontratolaboral
      INNER JOIN personas p
        ON cl.idpersona = p.idpersona
      INNER JOIN cargos cg
        ON cg.idcargo = cl.idcargo
      INNER JOIN areas a
        ON a.idarea = cg.idarea
      WHERE col.habilitado = 'S'
      ORDER BY col.idcolaborador
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
    $query = "SELECT
                col.idcolaborador,
                col.usernick,
                col.userpassword,
                col.habilitado,
                col.avatar,
                col.restriccionhoraria,
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
        col.idcolaborador,
        col.usernick,
        col.avatar,
        col.restriccionhoraria,
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
        cl.idcargo AS idcargo,
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

  //Actualiza el campo ultimoacceso a NOW()
  public function updateLastAccess(int $idColab): bool
  {
    $stmt = $this->db->prepare("
      UPDATE colaboradores
      SET ultimoacceso = NOW()
      WHERE idcolaborador = :id
    ");
    $stmt->bindValue(':id', $idColab, PDO::PARAM_INT);
    return $stmt->execute();
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

  //Obtener CONTRATO SIN COLABORADOR (Sin usuario registrado)

  public function getContractsWithoutColaborador(): array
  {
    $query = "
      SELECT
        cl.idcontratolaboral,
        cl.idpersona,
        p.apellidos,
        p.nombres,
        a.area,
        cg.cargo,
        DATE_FORMAT(cl.fechainicio, '%Y-%m-%d') AS fechainicio
      FROM contratoslaborales cl
      JOIN personas p ON p.idpersona = cl.idpersona
      JOIN cargos cg ON cg.idcargo = cl.idcargo
      JOIN areas a ON a.idarea = cg.idarea
      LEFT JOIN colaboradores col ON col.idcontratolaboral = cl.idcontratolaboral
      WHERE col.idcolaborador IS NULL
      ORDER BY p.apellidos, p.nombres
      LIMIT 0,1000
    ";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      return [];
    }
  }

  //EJEMPLO PARA BUSCAR EL EMAIL Y EL TELEFONO => PRUEBA
  public function findByEmailOrPhoneOrUsernick(string $identifier): ?array
  {
    try {
      $id = trim($identifier);

      if (strpos($id, ':') !== false) {
        $parts = explode(':', $id);
        $id = trim($parts[0]);
      }

      $idLower = mb_strtolower($id);
      $digits = preg_replace('/\D+/', '', $id);

      // 1) usernick exacto
      $stmt = $this->db->prepare("
          SELECT col.idcolaborador, col.usernick, col.userpassword, col.habilitado,
                  p.email, p.telprimario, p.telalternativo, p.apellidos, p.nombres
          FROM colaboradores col
          JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
          JOIN personas p ON p.idpersona = cl.idpersona
          WHERE BINARY col.usernick = :usernick
          LIMIT 1
        ");
      $stmt->bindValue(':usernick', $id);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row)
        return $row;

      // 2) email (case-insensitive)
      $stmt = $this->db->prepare("
          SELECT col.idcolaborador, col.usernick, col.userpassword, col.habilitado,
                  p.email, p.telprimario, p.telalternativo, p.apellidos, p.nombres
          FROM colaboradores col
          JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
          JOIN personas p ON p.idpersona = cl.idpersona
          WHERE LOWER(p.email) = :email
          LIMIT 1
        ");
      $stmt->bindValue(':email', $idLower);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row)
        return $row;

      // 3) teléfono: buscar candidatos con LIKE y comparar últimos N dígitos
      if ($digits !== '') {
        $like = '%' . $digits . '%';

        // Usamos nombres de parámetros diferentes (:like1 y :like2) para evitar problemas con parámetros repetidos
        $stmt = $this->db->prepare("
            SELECT col.idcolaborador, col.usernick, col.userpassword, col.habilitado,
                    p.email, p.telprimario, p.telalternativo, p.apellidos, p.nombres
            FROM colaboradores col
            JOIN contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
            JOIN personas p ON p.idpersona = cl.idpersona
            WHERE REPLACE(REPLACE(REPLACE(p.telprimario,' ',''),'+',''),'-','') LIKE :like1
                OR REPLACE(REPLACE(REPLACE(p.telalternativo,' ',''),'+',''),'-','') LIKE :like2
            LIMIT 20
          ");
        $stmt->bindValue(':like1', $like);
        $stmt->bindValue(':like2', $like);
        $stmt->execute();
        $cands = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($cands) {
          $lastN = 9; // comparar últimos 9 dígitos (ajusta si es necesario)
          $needle = substr($digits, -$lastN);
          foreach ($cands as $c) {
            $t1 = preg_replace('/\D+/', '', $c['telprimario'] ?? '');
            $t2 = preg_replace('/\D+/', '', $c['telalternativo'] ?? '');
            if ($t1 !== '' && substr($t1, -$lastN) === $needle)
              return $c;
            if ($t2 !== '' && substr($t2, -$lastN) === $needle)
              return $c;
          }
        }
      }

      return null;
    } catch (\Throwable $e) {
      error_log('[Usuario::findByEmailOrPhoneOrUsernick] Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
      return null;
    }
  }


}