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

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idorden' => $params['idorden'],
                ':idlogistica' => $params['idlogistica'],
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
