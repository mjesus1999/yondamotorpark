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
      $stmt->bindParam(':tipodoc', $tipodoc, PDO::PARAM_STR);
      $stmt->bindParam(':nrodoc', $nrodoc, PDO::PARAM_STR);
      $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
      $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
      $stmt->bindParam(':genero', $genero, PDO::PARAM_STR);
      $stmt->bindParam(':fechanac', $fechanac, PDO::PARAM_STR);
      $stmt->bindParam(':estadocivil', $estadocivil, PDO::PARAM_STR);
      $stmt->bindParam(':email', $email, PDO::PARAM_STR);
      $stmt->bindParam(':iddistrito', $iddistrito, PDO::PARAM_INT);
      $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
      $stmt->bindParam(':referencia', $referencia, PDO::PARAM_STR);
      $stmt->bindParam(':telprimario', $telprimario, PDO::PARAM_STR);
      $stmt->bindParam(':telalternativo', $telalternativo, PDO::PARAM_STR);

      $stmt->execute();

      // El SP devuelve un resultset con { last_id }
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();

      return isset($row['last_id']) ? (int) $row['last_id'] : 0;
    } catch (Exception $e) {
      return 0;
    }
  }

}