<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Concesionario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Obtiene todos los Concesionarios de la DB:
    public function getAll(): ?array
    {
        $query = 'SELECT idconcesionario, ruc, razonsocial, nombrecomercial FROM concesionarios ORDER BY creado DESC';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());

            return [];
        }
    }

    // Obtener los concesionarios por RUC EN LA DB
    public function getConcesionarioByRUC($ruc = ''): ?array
    {
        $query = 'SELECT idconcesionario, ruc, razonsocial, nombrecomercial FROM concesionarios WHERE ruc = ?';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($ruc));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    // Crear el concesionario.
    public function create($params = []): int
    {
        $query = 'INSERT INTO concesionarios (ruc, razonsocial, nombrecomercial) VALUES (:ruc,:razonsocial,:nombrecomercial)';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(
                array(
                    ':ruc' => $params['ruc'],
                    ':razonsocial' => $params['razonsocial'],
                    ':nombrecomercial' => $params['nombrecomercial']
                )
            );
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    public function update($params): int
    {
        try {
            $query = 'UPDATE concesionarios SET nombrecomercial = :nombrecomercial, modificado = NOW() WHERE idconcesionario = :idconcesionario';
            $stmt = $this->db->prepare($query);
            $stmt->execute(
                array(
                    ':nombrecomercial' => $params['nombrecomercial'],
                    ':idconcesionario' => $params['idconcesionario']
                )
            );
            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    // Obtener las ordenes de compra si es que se le ha hecho alguna compra - Devuelve 0(Se puede eliminar) - Devuelvre(1) NO!!
    public function getOC($idconcesionario = -1): int
    {
        try {
            $stmt = $this->db->prepare('call spu_concesionarios_obtener_oc(?,@registros)');
            $stmt->execute(
                array($idconcesionario)
            );
            $response = $this->db->query('SELECT @registros AS registros')->fetch(PDO::FETCH_ASSOC);
            return (int) $response['registros'];
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    public function delete($idconcesionario = -1): int
    {
        try {
            $stmt = $this->db->prepare('call spu_concesionarios_eliminar_todo(?)');
            $stmt->execute(array($idconcesionario));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }


    public function getConcesionariosWhitOCProceso(): array
    {
        try {
            $query = 'SELECT DISTINCT c.idconcesionario, c.ruc, c.razonsocial, c.nombrecomercial 
                      FROM tiendas ti
                      JOIN ordenescompra oc ON ti.idtienda = oc.idtienda
                      JOIN concesionarios c ON ti.idconcesionario = c.idconcesionario
                      WHERE oc.estado = "proceso"
                      ORDER BY c.razonsocial ASC;';
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }


    public function getReporteConcesionarioDetallado(int $id): ?array
    {
        // Asegúrate de que el nombre de tu procedimiento sea correcto y que espera un parámetro.
        $query = "CALL sp_reporte_concesionario_detallado(?)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            // 1. Obtener el Resumen Ejecutivo (primer conjunto de resultados)
            $resumenEjecutivo = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Avanzar al siguiente conjunto para obtener el Detalle de Pagos
            $stmt->nextRowset();
            $detallePagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Avanzar al siguiente conjunto para obtener el Detalle de Vehículos
            $stmt->nextRowset();
            $detalleVehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Opcional: Para depuración, puedes seguir usando error_log
            // error_log(print_r($resumenEjecutivo, true));
            // error_log(print_r($detallePagos, true));
            // error_log(print_r($detalleVehiculos, true));

            // Cierra el cursor para liberar la conexión a la base de datos
            $stmt->closeCursor();

            // Retornar los tres conjuntos de resultados en un solo array
            return [
                'resumenEjecutivo' => $resumenEjecutivo,
                'detallePagos' => $detallePagos,
                'detalleVehiculos' => $detalleVehiculos
            ];
        } catch (PDOException $e) {
            error_log("Error en el procedimiento almacenado: " . $e->getMessage());
            return null;
        }
    }
}
