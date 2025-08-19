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
    $query = "SELECT * FROM vwGetAllUser LIMIT 1000";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      // log $e->getMessage()
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
      // log $e->getMessage()
      return [];
    }
  }

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

  public function disabled(int $id): bool
  {
    $query = "UPDATE colaboradores SET habilitado = 'N' WHERE idcolaborador = :id";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      return $stmt->execute();
    } catch (Exception $e) {
      // log $e->getMessage()
      return false;
    }
  }

  // CONSULTAS PARA EL LOGIN
  public function searchByUsernick(string $usernick): ?array
  {
    $query = "SELECT * FROM vwSearchUsernick WHERE BINARY usernick = :usernick LIMIT 1";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->bindValue(':usernick', $usernick, PDO::PARAM_STR);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row ?: null;
    } catch (Exception $e) {
      // log $e->getMessage()
      return null;
    }
  }

  // mostrar el usuario por id
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

  // Actualiza el campo ultimoacceso a NOW()
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

  // actualizar avatar
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

  // IDLOGISTICA:
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



  // Obtener CONTRATO SIN COLABORADOR (Sin usuario registrado)
  public function getContractsWithoutColaborador(): array
  {
    $query = "SELECT * FROM vwContractsWithoutColaborador LIMIT 0,1000";
    try {
      $stmt = $this->db->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      // log $e->getMessage()
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