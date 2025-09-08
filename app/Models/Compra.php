<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Compra
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    // Listado para ls vista principal de compras

    public function getAll(): array
    {
        $query = " SELECT 
            c.idcompra,
            c.idorden,
            DATE_FORMAT(c.fechacompra, '%d-%m-%Y') AS fechacompra,
            c.fecharecepcion,
            c.tipodoc,
            c.serie,
            c.numdocumento,
            c.rutadoc,
            con.nombrecomercial AS razon_concesionario
            
        FROM compras c
        INNER JOIN ordenescompra o ON c.idorden = o.idordencompra
        INNER JOIN tiendas t ON o.idtienda = t.idtienda
        INNER JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
        ORDER BY c.creado DESC;";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }


    // Obtiene todos los Concesionarios de la DB CON OC en 'Proceso' o  en 'Pagado':
    public function getConcesionariosConOCEnProcesoOPagado(): ?array
    {
        $query = "SELECT DISTINCT c.idconcesionario, c.razonsocial, c.nombrecomercial
                    FROM concesionarios c
                    JOIN tiendas t ON c.idconcesionario = t.idconcesionario
                    JOIN ordenescompra oc ON t.idtienda = oc.idtienda
                    WHERE oc.estado IN ('proceso', 'pagado');
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


    // OBTNER LOS DETALLES DE LA OC POR CONCESIONARIO - SIMEMPRE EN CUANDO LAS OC ESTE EN PROCEOS O PAGADO

    public function getDetOCByConcesionario(int $id): ?array
    {
        $query = "CALL sp_detalle_oc_por_concesionario(:idconcesionario);";

        try {

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idconcesionario' => $id));
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }





    public function create($params = []): int
    {

        $query = "CALL  sp_compra_registrar(:idorden,:idlogistica,:fechacompra,:tipodoc,:serie,:numdocumento,:rutadoc)";

        try {
             $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idorden' => $params['idorden'],
                ':idlogistica' => $idUsuario,
                ':fechacompra' => $params['fechacompra'],
                ':tipodoc' => $params['tipodoc'],
                ':serie' => $params['serie'],
                ':numdocumento' => $params['numdocumento'],
                'rutadoc' => $params['rutadoc']
            ));

            $id = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return isset($id['last_id']) ? (int) $id['last_id'] : 0;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}
