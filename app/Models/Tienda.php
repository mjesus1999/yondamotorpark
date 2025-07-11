<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Tienda
{

    private PDO $db;


    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Obtiene todas las tiendas, dependiendo del ID del concesionario.
    public function getTiendasByIdConcesionario($idconcesionario = -1): array
    {
        try {
            $cmd = $this->db->prepare("call spu_tiendas_por_concesionario(?)");
            $cmd->execute(array($idconcesionario));
            $results = $cmd->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }



    // Crear la tienda

    public function create($params = []): int
    {
        $query = "INSERT INTO tiendas (iddistrito, idconcesionario, direccion, email, telefono, contacto) VALUES (:iddistrito,:idconcesionario,:direccion,:email,:telefono,:contacto)";
        try {
            $cmd = $this->db->prepare($query);
            $cmd->execute(
                array(
                    ':iddistrito' => $params['iddistrito'],
                    ':idconcesionario' => $params['idconcesionario'],
                    ':direccion' => $params['direccion'],
                    ':email' => $params['email'],
                    ':telefono' => $params['telefono'],
                    ':contacto' => $params['contacto']
                )
            );
            return (int)$this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}
