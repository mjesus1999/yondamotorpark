<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Empresa
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }



    public function getAllEmpresasCliente(): ?array
    {
        try {

            $query = "SELECT
                            c.idcliente,
                            e.idempresa,
                            CONCAT(dep.departamento, ' / ', p.provincia, ' / ', d.distrito) AS ubicacion,
                            e.direccion,
                            e.representante AS responsable,
                            e.ruc,
                            e.nombrecomercial,
                            e.telprimario,
                            e.email,
                            e.estado
                        FROM clientes c
                        INNER JOIN empresas e ON c.idempresa = e.idempresa
                        INNER JOIN distritos d ON e.iddistrito = d.iddistrito
                        INNER JOIN provincias p ON d.idprovincia = p.idprovincia
                        INNER JOIN departamentos dep ON p.iddepartamento = dep.iddepartamento
                        WHERE c.tipocliente = 'E'  AND c.estado = 'ACT'";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }



    public function getById($idempresa = 0): ?array
    {
        $query = "
                SELECT
                    e.idempresa,
                    e.razonsocial,
                    e.nombrecomercial,
                    e.ruc,
                    e.representante,
                    e.email,
                    e.telprimario
                FROM empresas e
              
                WHERE e.idempresa = ?;
                
                ";
        try {

            $cmd = $this->db->prepare($query);
            $cmd->execute(array($idempresa));
            $results = $cmd->fetch(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }






    public function create($params = []): int
    {
        $query = "INSERT INTO empresas (
                    iddistrito,
                    razonsocial,
                    nombrecomercial,
                    ruc,
                    representante,
                    email,
                    direccion,
                    referencia,
                    latitud,
                    longitud,
                    telprimario,
                    telsecundario
                ) VALUES(:iddistrito,:razonsocial,:nombrecomercial,:ruc,:representante,:email,:direccion,:referencia,:latitud,:longitud,:telprimario,:telsecundario) ";

        try {

            $cmd = $this->db->prepare($query);
            $cmd->execute(array(
                ':iddistrito' => $params['iddistrito'],
                ':razonsocial' => $params['razonsocial'],
                ':nombrecomercial' => $params['nombrecomercial'],
                ':ruc' => $params['ruc'],
                ':representante' => $params['representante'],
                ':email' => $params['email'], // null
                ':direccion' => $params['direccion'],
                ':referencia' => $params['referencia'], // null
                ':latitud' => $params['latitud'], // null
                ':longitud' => $params['longitud'], // null
                ':telprimario'  => $params['telprimario'],
                ':telsecundario' => $params['telsecundario'] //null

            ));

            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }




    public function update($params = []): int
    {
        try {
            $query = "UPDATE empresas SET  
                razonsocial= ?, 
                nombrecomercial = ?, 
                ruc = ?,
                representante = ?,
                email = ?,
                telprimario = ?
              WHERE idempresa  = ?";

            $cmd = $this->db->prepare($query);
            $cmd->execute([
                $params['razonsocial'],
                $params['nombrecomercial'],
                $params['ruc'],
                $params['representante'],
                $params['email'],
                $params['telprimario'],
                $params['idempresa']
            ]);

            return (int) $cmd->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    public function rucExiste(string $ruc, ?int $excluirId = null): bool
    {

        $sql = "SELECT COUNT(*) as total FROM empresas  WHERE ruc=:ruc";
        $params = [":ruc" => $ruc];

        if ($excluirId !== null) {
            $sql .= " AND idempresa !=:id";
            $params[":id"] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $resultado = $stmt->fetch();

        return $resultado['total'] > 0;
    }
}


