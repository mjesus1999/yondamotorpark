<?php
//app/Controllers/Cliente.php

namespace App\Models;

use App\Core\Database;
use PDO;

class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /*     public function getAll(): array
        {
            $query = "SELECT
                c.idcliente,
            CASE
                WHEN c.tipocliente = 'P'
                THEN CONCAT(p.nombres, ' ', p.apellidos)
                ELSE CONCAT(e.razonsocial, ' – ', e.nombrecomercial)
            END AS label,
            CASE
                WHEN c.tipocliente = 'P' THEN p.nrodoc
                ELSE e.ruc
            END AS nrodoc,
            CASE
                WHEN c.tipocliente = 'P' THEN p.telprimario
                ELSE e.telprimario
            END AS telprimario
            FROM clientes c
            LEFT JOIN personas p ON c.idpersona = p.idpersona
            LEFT JOIN empresas e ON c.idempresa = e.idempresa
            ORDER BY label;
            ";
            try {
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
        } */

    public function getAll(): array
    {
        $query = "
            SELECT
            c.idcliente,
            CASE
                WHEN c.tipocliente = 'P'
                THEN CONCAT(p.nombres, ' ', p.apellidos)
                ELSE CONCAT(e.razonsocial, ' - ', e.nombrecomercial)
            END AS label,
            CASE
                WHEN c.tipocliente = 'P' THEN p.nrodoc
                ELSE e.ruc
            END AS nrodoc,
            CASE
                WHEN c.tipocliente = 'P' THEN p.telprimario
                ELSE e.telprimario
            END AS telprimario
            FROM clientes c
            LEFT JOIN personas p ON c.idpersona = p.idpersona
            LEFT JOIN empresas e ON c.idempresa = e.idempresa
            ORDER BY label;
        ";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}
