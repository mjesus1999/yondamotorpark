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

    public function create($params): int
    {
        $query = "INSERT INTO personas(apellidos,nombres, tipodoc, nrodoc, genero, fechanac, estadocivil, email, iddistrito, direccion, referencia, telprimario, telalternativo)
                  VALUES(:apellidos,:nombres,:tipodoc,:nrodoc,:genero,:fechanac,:estadocivil,:email,:iddistrito,:direccion,:referencia,:telprimario,:telalternativo)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':apellidos' => $params['apellidos'],
                ':nombres' => $params['nombres'],
                ':tipodoc' => $params['tipodoc'],
                ':nrodoc' => $params['nrodoc'],
                ':genero' => $params['genero'],
                ':fechanac' => $params['fechanac'],
                ':estadocivil' => $params['estadocivil'],
                ':email' => $params['email'], //null
                ':iddistrito' => $params['iddistrito'],
                ':direccion' => $params['direccion'], //null
                ':referencia' => $params['referencia'], //null
                ':telprimario' => $params['telprimario'],
                ':telalternativo' => $params['telalternativo']//null
            ));

            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    public function searchByDNI(string $dni): ?array
    {
        $query = "
            SELECT idpersona, apellidos, nombres
            FROM personas
            WHERE nrodoc = :dni
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':dni', $dni, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

}