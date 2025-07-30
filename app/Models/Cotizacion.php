<?php
//app/Controller/Cotizacion.php

namespace App\Models;

use App\Core\Database;
use PDO;

class Cotizacion
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getPersonaByDoc(string $tipo, string $nrodoc): ?array
    {
        $query = "SELECT idpersona, apellidos, nombres, telprimario, telalternativo, email 
                FROM personas 
                WHERE tipodoc = :tipo
                AND nrodoc  = :nrodoc
                LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':tipo' => strtoupper($tipo),
            ':nrodoc' => $nrodoc
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getClienteByDoc(string $tipo, string $doc): ?array
    {
        if (in_array(strtoupper($tipo), ['DNI', 'CEX', 'PAS'], true)) {
            $sql = "
          SELECT c.idcliente,
                 p.apellidos, p.nombres,
                 p.telprimario, p.telalternativo, p.email
            FROM clientes c
            JOIN personas p ON p.idpersona = c.idpersona
           WHERE p.tipodoc = :tipo
             AND p.nrodoc  = :doc
           LIMIT 1";
            $params = [':tipo' => strtoupper($tipo), ':doc' => $doc];
        } else {
            $sql = "
          SELECT c.idcliente,
                 e.razonsocial AS apellidos,
                 e.nombrecomercial AS nombres,
                 e.telprimario, e.telalternativo, e.email
            FROM clientes c
            JOIN empresas e ON e.idempresa = c.idempresa
           WHERE e.ruc = :doc
           LIMIT 1";
            $params = [':doc' => $doc];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getEmpresaByRuc(string $ruc): ?array
    {
        $query = "SELECT idempresa, razonsocial AS apellidos, nombrecomercial AS nombres,
                    telprimario, telalternativo, email
                FROM empresas
                WHERE ruc = :ruc
                LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':ruc' => $ruc]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Inserta una nueva cotizacion
    public function createCotizacion(array $d): void
    {
        $sql = "
      INSERT INTO cotizaciones
        (idformato, idcliente, idvehiculo, moneda, precioventa,
         vigenciadias, inicial, numcuotas, valorcuota, idasesor)
      VALUES
        (:idformato, :idcliente, :idvehiculo, :moneda, :precioventa,
         :vigenciadias, :inicial, :numcuotas, :valorcuota, :idasesor)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':idformato' => $d['idformato'],
            ':idcliente' => $d['idcliente'],
            ':idvehiculo' => $d['idvehiculo'],
            ':moneda' => $d['moneda'],
            ':precioventa' => $d['precioventa'],
            ':vigenciadias' => $d['vigenciadias'],
            ':inicial' => $d['inicial'],
            ':numcuotas' => $d['numcuotas'],
            ':valorcuota' => $d['valorcuota'],
            ':idasesor' => $d['idasesor']
        ]);
    }

    /* public function getRequisitos(): array
    {
        $stmt = $this->pdo->prepare("SELECT idrequisito, requisito FROM requisitos ORDER BY idrequisito");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } */

}