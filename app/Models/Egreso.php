<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

class Egreso
{

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    public function getAllEgresosByEstado(string $estado): array
    {
        try {
            $stmt = $this->db->prepare("CALL sp_egresos_por_estado(:estado)");
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener los egresos por estado: " . $e->getMessage());
            return [];
        }
    }



    /**
     * * Obtiene la lista de conceptos de egreso desde la base de datos.
     * @throws \Exception si ocurre un error en la consulta.
     * @return array Lista de conceptos de egreso.
     */
    public function getConceptosEgreso(): array
    {
        try {
            $stmt = $this->db->prepare("SELECT idconceptoegreso,concepto, descripcion FROM conceptoegreso ORDER BY descripcion");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener los conceptos de egreso: " . $e->getMessage());
            return [];
        }
    }

    public function getColaboradores(): array
    {
        $query = "
                SELECT
                col.idcolaborador,
                per.idpersona, CONCAT(per.apellidos, ' ', per.nombres) AS colaborador,
                car.cargo,
                ar.area
                    FROM
                        colaboradores col
                    JOIN
                        contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
                    JOIN
                        personas per ON per.idpersona = cl.idpersona
                    JOIN
                        cargos car ON car.idcargo = cl.idcargo
                    JOIN
                        areas ar ON ar.idarea = car.idarea
                    WHERE
                        car.cargo != 'Practicante'
                AND (cl.fechafin IS NULL OR cl.fechafin > CURDATE());";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Error al obtener los colaboradores: " . $error->getMessage());
            return [];
        }
    }



    public function add($params = []): int
    {

        $query = "CALL sp_egresos_add(:idconceptoegreso, :idcolacaja, :idsolicitante, :monto, :comentario, :requierecomprobante)";
        try {
            $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            // error_log("User ID in Egreso add: " . var_export($idUsuario, true));
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idconceptoegreso' => $params['idconceptoegreso'],
                ':idcolacaja' => $idUsuario,
                ':idsolicitante' => $params['idsolicitante'],
                ':monto' => $params['monto'],
                ':comentario' => $params['comentario'],
                ':requierecomprobante' => $params['requierecomprobante']
            ));

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['last_insert_id'] ?? 0;
        } catch (Exception $e) {
            error_log("Error al agregar el egreso: " . $e->getMessage());
            return 0;
        }
    }

    public function addComprobante($params = []): bool
    {
        $query = "CALL sp_egresos_add_comprobante(:idegreso,:idproovedor, :tipodocumento,:serie, :numdocumento, :monto,:rutacomprobante)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idegreso' => $params['idegreso'],
                ':idproovedor' => $params['idproovedor'],
                ':tipodocumento' => $params['tipodocumento'],
                ':serie' => $params['serie'],
                ':numdocumento' => $params['numdocumento'],
                ':monto' => $params['monto'],
                ':rutacomprobante' => $params['rutacomprobante']
            ));
            return true;
        } catch (Exception $e) {
            error_log("Error al agregar el comprobante: " . $e->getMessage());
            return false;
        }
    }
}
