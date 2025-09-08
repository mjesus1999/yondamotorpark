<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Persona
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getById($idpersona = 0): ?array
    {

        $query = "
                SELECT
                    p.idpersona,
                    p.nombres,
                    p.apellidos,
                    p.estadocivil,
                    p.email,
                    p.direccion,
                    p.telprimario,
                    p.fechanac,
                    p.latitud,
                    p.longitud,
                    d.iddistrito,
                    d.distrito,
                    pr.idprovincia,
                    pr.provincia,
                    dp.iddepartamento,
                    dp.departamento
                FROM personas p
                INNER JOIN distritos d ON p.iddistrito = d.iddistrito
                INNER JOIN provincias pr ON d.idprovincia = pr.idprovincia
                INNER JOIN departamentos dp ON pr.iddepartamento = dp.iddepartamento
                WHERE p.idpersona = ?";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($idpersona));
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    public function getAllPersonasCliente(): ?array
    {
        try {

            $query = "SELECT
                            c.idcliente,
                            c.idpersona,
                            CONCAT(de.departamento, ' / ', pr.provincia, ' / ', di.distrito) AS ubicacion,
                            p.direccion,
                            CONCAT(p.apellidos, ' ', p.nombres) AS nombrecompleto,
                            p.tipodoc,
                            p.nrodoc,
                            p.email,
                            p.telprimario
                        FROM clientes c
                        INNER JOIN personas p ON c.idpersona = p.idpersona
                        LEFT JOIN distritos di ON p.iddistrito = di.iddistrito
                        LEFT JOIN provincias pr ON di.idprovincia = pr.idprovincia
                        LEFT JOIN departamentos de ON pr.iddepartamento = de.iddepartamento
                        WHERE c.tipocliente = 'P' AND c.estado = 'ACT'
                        ORDER BY p.creado DESC;";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }


    public function create($params): int
    {
        $query = "INSERT INTO personas(apellidos,nombres, tipodoc, nrodoc, genero,iddistrito, direccion, referencia, telprimario, telalternativo,latitud, longitud)
                  VALUES(:apellidos,:nombres,:tipodoc,:nrodoc,:genero,:iddistrito,:direccion,:referencia,:telprimario,:telalternativo,:latitud,:longitud)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':apellidos' => $params['apellidos'],
                ':nombres' => $params['nombres'],
                ':tipodoc' => $params['tipodoc'],
                ':nrodoc' => $params['nrodoc'],
                ':genero' => $params['genero'],
                // ':fechanac' => $params['fechanac'],
                // ':estadocivil' => $params['estadocivil'],
                // ':email' => $params['email'] ?? null, //null
                ':iddistrito' => $params['iddistrito'],
                ':direccion' => $params['direccion'] ?? null, //null
                ':referencia' => $params['referencia'] ?? null, //null
                ':telprimario' => $params['telprimario'] ?? null, //null
                ':telalternativo' => $params['telalternativo'] ?? null, //null
                ':latitud' => $params['latitud'] ?? null, //null
                ':longitud' => $params['longitud'] ?? null //null

            ));

            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    public function update($params): int
    {
        try {
            $query = "UPDATE personas SET  
                nombres = :nombres, 
                apellidos = :apellidos, 
                email = :email,
                estadocivil = :estadocivil,
                direccion = :direccion,
                telprimario = :telprimario,
                latitud   = :latitud,
                longitud = :longitud,
                iddistrito = :iddistrito,
                fechanac = :fechanac,
                modificado = NOW()
              WHERE idpersona  = :idpersona";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':nombres' => $params['nombres'],
                ':apellidos' => $params['apellidos'],
                ':estadocivil' => $params['estadocivil'],
                ':email' => $params['email'],
                ':direccion' => $params['direccion'],
                ':telprimario' => $params['telprimario'],
                ':latitud' => $params['latitud'],
                ':longitud' => $params['longitud'],
                ':iddistrito' => $params['iddistrito'],
                ':fechanac' => $params['fechanac'],
                ':idpersona' => $params['idpersona']
            ]);

            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }


    // DEYANIRA
    /* public function searchByDNI(string $dni): ?array
    {
        $query = "SELECT idpersona, apellidos, nombres FROM personas WHERE nrodoc = :dni LIMIT 1";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':dni', $dni, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {

            return [];
        }
    } */
}
