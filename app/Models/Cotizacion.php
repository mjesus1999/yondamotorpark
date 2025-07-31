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

    public function getAll(): array
    {
        $sql = "
        SELECT
            c.idcotizacion,
            COALESCE(
            CASE WHEN cl.tipocliente = 'P' THEN CONCAT(p.nombres, ' ', p.apellidos) END,
            e.razonsocial,
            'Cliente no definido'
            ) AS nombrecliente,

            COALESCE(
            CASE WHEN cl.tipocliente = 'P' THEN p.nrodoc END,
            e.ruc,
            ''
            ) AS documento,

            COALESCE(
            CASE WHEN cl.tipocliente = 'P' THEN p.telprimario END,
            e.telprimario,
            ''
            ) AS telefono,

            ma.marca AS marcaVehiculo,
            mo.modelo AS modeloVehiculo,
            mo.anio,
            v.color,
            c.creado AS fechaRegistro
        FROM cotizaciones c
        JOIN clientes cl ON c.idcliente = cl.idcliente
        LEFT JOIN personas p ON cl.idpersona = p.idpersona
        LEFT JOIN empresas e ON cl.idempresa = e.idempresa
        JOIN vehiculos v ON c.idvehiculo = v.idvehiculo
        JOIN modelos mo ON v.idmodelo = mo.idmodelo
        JOIN marcas ma ON mo.idmarca = ma.idmarca
        ORDER BY c.creado DESC
        LIMIT 10
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getClienteByDoc(string $tipo, string $doc): ?array
    {
        // Solo personas por DNI
        if (strtoupper($tipo) === 'DNI') {
            $sql = "
          SELECT c.idcliente,
                 p.apellidos, p.nombres,
                 p.telprimario, p.telalternativo, p.email
            FROM clientes c
            JOIN personas p ON p.idpersona = c.idpersona
           WHERE p.tipodoc = 'DNI'
             AND p.nrodoc  = :doc
           LIMIT 1
        ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':doc' => $doc]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        // Solo empresas por RUC
        if (strtoupper($tipo) === 'RUC') {
            $sql = "
          SELECT c.idcliente,
                 e.razonsocial AS apellidos,
                 e.nombrecomercial AS nombres,
                 e.telprimario, e.telalternativo, e.email
            FROM clientes c
            JOIN empresas e ON e.idempresa = c.idempresa
           WHERE e.ruc = :doc
           LIMIT 1
        ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':doc' => $doc]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        return null;
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
    public function create(array $d): void
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