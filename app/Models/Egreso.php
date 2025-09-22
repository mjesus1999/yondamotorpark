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


    public function getProovedores(): array
    {
        $query = "SELECT idproovedor, nombrecomercial, ruc FROM proovedores ORDER BY razonsocial";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Error al obtener los proovedores: " . $error->getMessage());
            return [];
        }
    }

    public function getDetalleEgresoById(int $id)
    {
        $query = "
                SELECT
                    c.idcomprobante,
                    c.idegreso,
                    c.idproovedor,
                    c.tipodoc,
                    c.serie,
                    c.numdocumento,
                    c.monto AS montocomprobante,
                    c.cargadocontabilidad,
                    c.rutacomprobante,
                    e.monto AS montoegreso,
                    CONCAT(p.nombrecomercial, ' - ', p.ruc ) AS proovedor
                    FROM comprobantes c
                    JOIN egresos e ON c.idegreso = e.idegreso
                    JOIN proovedores p ON c.idproovedor = p.idproovedor
                    WHERE c.idegreso = $id AND e.requierecomprobante = 'S';
                        
        
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener el detalle del egreso: " . $e->getMessage());
            return [];
        }
    }


    public function getEgresoWithComprobantesValidados(): array
    {
        $query = "SELECT 
                        eg.idegreso,
                        cm.idcomprobante,
                        cm.tipodoc,
                        DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
                        ce.concepto,
                        CONCAT(pro.razonsocial ) AS proovedor,
                        CONCAT(p.apellidos,' ',p.nombres) AS solicitante,
                        eg.monto,
                        eg.comentario,
                        cm.numdocumento,
                        cm.monto AS monto_validado,
                        cm.rutacomprobante,
                        DATE_FORMAT(cm.modificado, '%d-%m-%Y %H:%i') AS modificado
                    FROM egresos eg
                    JOIN conceptoegreso ce ON eg.idconceptoegreso = ce.idconceptoegreso
                    JOIN colaboradores c ON eg.idcolsolicitante = c.idcolaborador
                    JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
                    JOIN personas p ON cl.idpersona = p.idpersona
                    JOIN comprobantes cm ON eg.idegreso = cm.idegreso
                    JOIN proovedores pro ON pro.idproovedor = cm.idproovedor
                    WHERE eg.requierecomprobante = 'S' AND cm.cargadocontabilidad = 'S'
                    ORDER BY cm.modificado DESC;";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log($error->getMessage());
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

    public function addComprobante($params = []): int
    {
        $query = "CALL sp_egresos_add_comprobante(:idegreso,:idproovedor, :tipodoc,:serie, :numdocumento, :monto,:rutacomprobante)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idegreso' => $params['idegreso'],
                ':idproovedor' => $params['idproovedor'],
                ':tipodoc' => $params['tipodoc'],
                ':serie' => $params['serie'],
                ':numdocumento' => $params['numdocumento'],
                ':monto' => $params['monto'],
                ':rutacomprobante' => $params['rutacomprobante']
            ));

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['last_insert_id'] ?? 0;
        } catch (Exception $e) {
            error_log("Error al agregar el comprobante: " . $e->getMessage());
            return 0;
        }
    }

    public function validarComprobante(int $idComprobante): bool
    {
        $query = "UPDATE comprobantes SET cargadocontabilidad = 'S', modificado = NOW() WHERE idcomprobante = :idcomprobante";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idcomprobante', $idComprobante, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            return true;
        } catch (Exception $e) {
            error_log("Error al validar el comprobante: " . $e->getMessage());
            return false;
        }
    }




    public function deleteEgreso($id): bool
    {
        $query = "DELETE FROM egresos WHERE idegreso = :idegreso";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idegreso', $id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            return true;
        } catch (Exception $e) {
            error_log("Error al eliminar el egreso: " . $e->getMessage());
            return false;
        }
    }


    public function getReporteByFecha($fechaInicio, $fechaFin): array
    {

        $query = "CALL sp_obtener_reporte_egresos_completo(:fechainicio, :fechafin);";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fechainicio', $fechaInicio);
            $stmt->bindParam(':fechafin', $fechaFin);
            $stmt->execute();

            $allResults = [];
            $resultCount = 0;


            do {

                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


                if ($results) {
                    $allResults[$resultCount] = $results;
                    $resultCount++;
                }
            } while ($stmt->nextRowset());


            return $allResults;
        } catch (PDOException $error) {

            error_log("Error al llamar al procedimiento almacenado: " . $error->getMessage());
            return [];
        }
    }
}
