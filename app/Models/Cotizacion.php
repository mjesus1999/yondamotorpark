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
            c.idformato
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
        JOIN formatocotizacion fc ON c.idformato = fc.idformato 
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

    private function Pago($tasaInteres, $numPagos, $montoPrestamo)
    {
        // Verificar si la tasa de interés es 0
        if ($tasaInteres == 0) {
            return $montoPrestamo / $numPagos;
        }

        // Calcular el pago utilizando la fórmula de anualidad
        $pago = ($montoPrestamo * $tasaInteres) / (1 - pow(1 + $tasaInteres, -$numPagos));
        return $pago;
    }

    public function calcularPagoMensual($importeTotal, $inicial, $meses)
    {
        $tasa = 0.65; //Tasa standard de YONDA 65%
        $tasaMensual = pow((1 + $tasa), (1 / 12)) - 1;
        $montoFinanciar = $importeTotal - $inicial;
        $cuota = round($this->Pago($tasaMensual, $meses, $montoFinanciar), 2);
        return $cuota;
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


    public function getById(int $idcotizacion): ?array
    {
        $sql = "
      SELECT
        c.idcotizacion,
        c.idformato,
        c.idcliente,
        c.idvehiculo,
        c.moneda,
        c.precioventa,
        c.vigenciadias,
        c.inicial,
        c.numcuotas,
        c.valorcuota,
        c.idasesor,
        c.creado AS fechaRegistro,

        -- Datos del cliente
        COALESCE(
          CASE WHEN cl.tipocliente = 'P' THEN CONCAT(p.nombres, ' ', p.apellidos) END,
          e.razonsocial
        ) AS cliente_nombre,
        COALESCE(
          CASE WHEN cl.tipocliente = 'P' THEN p.nrodoc END,
          e.ruc
        ) AS cliente_documento,
        COALESCE(
          CASE WHEN cl.tipocliente = 'P' THEN p.telprimario END,
          e.telprimario
        ) AS cliente_telefono,

        -- Vehículo
        ma.marca AS vehiculo_marca,
        mo.modelo AS vehiculo_modelo,
        mo.anio      AS vehiculo_anio,
        v.color      AS vehiculo_color

      FROM cotizaciones c
      JOIN clientes cl    ON c.idcliente = cl.idcliente
      LEFT JOIN personas p ON cl.idpersona = p.idpersona
      LEFT JOIN empresas e ON cl.idempresa = e.idempresa

      JOIN vehiculos v    ON c.idvehiculo = v.idvehiculo
      JOIN modelos mo     ON v.idmodelo   = mo.idmodelo
      JOIN marcas ma      ON mo.idmarca   = ma.idmarca

      WHERE c.idcotizacion = :id
      LIMIT 1
    ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idcotizacion]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* public function getRequisitos(): array
    {
        $stmt = $this->pdo->prepare("SELECT idrequisito, requisito FROM requisitos ORDER BY idrequisito");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } */

}