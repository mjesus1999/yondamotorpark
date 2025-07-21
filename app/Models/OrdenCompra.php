<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class OrdenCompra
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): ?array
    {

        $query = "
            SELECT 
                oc.idordencompra,
                oc.serie,
                oc.emision,
                oc.moneda,
                con.razonsocial
            FROM ordenescompra oc 
            JOIN tiendas t ON oc.idtienda = t.idtienda
            JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
            
            ORDER BY oc.idordencompra DESC;
                
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

    public function getDetOCByIdOC($idOC): ?array
    {
        $query = "CALL sp_detOC_By_IdOC(:idOC)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idOC' => $idOC));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    // TRAERA LOS DATOS DE LOS VEHICULOS A ACTUALIZAR EN LA TABLA DETALLE_OC, VERIFICAR SI HAN LLEGADO DE MANERA CORRECTA

    public function getInfoEsCorrecto($idOC): ?array
    {
        $query = "CALL sp_det_oc_escorrecto(:idOC)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idOC' => $idOC));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    //  METODO PARA ACTUAIZAR EL CAMPO ESCORRECTO EN LA TABLA DET_ORDEN_COMPRA DE LA DB

    public function updateEscorrectoDetOC($params = []): int
    {

        $query = "UPDATE detordencompra SET escorrecto=:escorrecto, modificado=NOW() WHERE idordencompra=:idordencompra;";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':escorrecto' => $params['escorrecto'],
                ':idordencompra' => $params['idordencompra']
            ));

            return  (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }



    public function create($params = []): int
    {
        $query = "call spu_oc_registrar(:idtienda,:idlogistica,:moneda,:serie,:numstock,:observaciones)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idtienda' => $params['idtienda'],
                ':idlogistica' => $params['idlogistica'],
                ':moneda' => $params['moneda'],
                ':serie' => $params['serie'],
                ':numstock' => $params['numstock'],
                ':observaciones' => $params['observaciones']

            ));

            $idOrdenCompra = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return isset($idOrdenCompra['last_id']) ? (int) $idOrdenCompra['last_id'] : 0;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}

// $orden = new OrdenCompra();

// var_dump($orden->getAll());
