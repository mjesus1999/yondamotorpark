<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class PagoCronograma
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function add($params = []): int
    {
        $query = "CALL sp_addPagoCronograma(:idcronograma,:idcuentapago,:idcolcaja,:mediopago,:numerotransaccion,:fechapago,:amortizacion,:comprobante,:observacion)";
        try {

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idcronograma' => $params['idcronograma'],
                ':idcuentapago' => $params['idcuentapago'] ?? null,
                ':idcolcaja' => $params['idcolcaja'],
                ':mediopago' => $params['mediopago'],
                ':numerotransaccion' => $params['numerotransaccion'],
                ':fechapago' => $params['fechapago'],
                ':amortizacion' => $params['amortizacion'],
                ':comprobante' => $params['comprobante'],
                ':observacion' => $params['observacion']
            ));

            $idPago = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return isset($idPago['last_insert_id']) ? (int) $idPago['last_insert_id'] : 0;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }


    // METODO PARA TRAER LOS NUEMROS DE CUNETAS PAGOS

    public function getNumCuentasPagos(): ?array
    {
        $query = " SELECT 
                        cp.idcuentapago,
                        CONCAT(ep.entidad, ' - ', cp.numcuenta) AS nombrecuenta
                    FROM 
                        cuentaspago cp
                    JOIN 
                        entidadespago ep ON cp.identidadpago = ep.identidadpago;
                    ";
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


    public function getHistorialPagosByContrato(int $id): array
    {

        $query = "CALL  sp_get_pagos_by_contrato(:idcontrato)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idcontrato' => $id));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }
}
