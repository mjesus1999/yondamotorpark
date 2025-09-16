<?php
//app/Controller/Cotizacion.php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;
use DateTime;
use DateInterval;

class Cotizacion
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    //ORDER BY fechaRegistro DESC LIMIT 10000
    public function getAll(): array
    {
        $query = "SELECT * FROM vwGetAllCotizacion";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las cotizaciones de un asesor específico
     * ORDER BY fechaRegistro DESC
     */
    public function getAllByAsesor(int $idasesor): array
    {
        $query = "SELECT * FROM vwGetAllCotizacion WHERE idasesor = :idasesor";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idasesor', $idasesor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClienteByDoc(string $tipo, string $doc): ?array
    {
        // Solo personas por DNI
        if (strtoupper($tipo) === 'DNI') {
            $sql = "
            SELECT c.idcliente,
                    p.apellidos, p.nombres,
                    p.telprimario, p.telalternativo, p.email, p.direccion
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

    public function calcularPagoMensual($importeTotal, $inicial, $meses, $tasaAnual = 0.65)
    {
        $tasaMensual = pow((1 + $tasaAnual), (1 / 12)) - 1;
        $montoFinanciar = $importeTotal - $inicial;
        $cuota = round($this->Pago($tasaMensual, $meses, $montoFinanciar), 2);
        return $cuota;
    }

    public function generarCronograma(float $importeTotal, float $inicial, int $meses, float $tasaAnual = 0.65): array
    {
        $tasaMensual = pow((1 + $tasaAnual), (1 / 12)) - 1;
        $montoFinanciar = $importeTotal - $inicial;
        $valorCuota = round($this->Pago($tasaMensual, $meses, $montoFinanciar), 2);

        $cronograma = [];
        $saldoCapital = $montoFinanciar;
        $fechaPago = new Datetime('now');

        for ($i = 1; $i <= $meses; $i++) {
            $interesExacto = $saldoCapital * $tasaMensual;
            $interes = round($interesExacto, 2);
            $abonoCapital = $valorCuota - $interes;

            if ($i === $meses) {
                $abonoCapital = $saldoCapital;
                $interes = $valorCuota - $abonoCapital;
            }

            $saldoCapital -= $abonoCapital;
            $saldoCapital = round($saldoCapital, 2);

            $fechaPago->add(new DateInterval('P1M'));

            $cronograma[] = [
                'item' => $i,
                'fecha_pago' => $fechaPago->format('d/m/Y'),
                'interes' => $interes,
                'abono_capital' => $abonoCapital,
                'valor_cuota' => $valorCuota,
                'saldo_capital' => $saldoCapital,
            ];
        }

        return $cronograma;
    }

    public function create(array $d): int
    {
        $sql = "INSERT INTO cotizaciones
        (idformato, idcliente, idvehiculo, moneda, precioventa,
        vigenciadias, inicial, numcuotas, valorcuota, gastosadministrativos, idasesor)
        VALUES
        (:idformato, :idcliente, :idvehiculo, :moneda, :precioventa,
        :vigenciadias, :inicial, :numcuotas, :valorcuota, :gastosadministrativos, :idasesor)";

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
            ':gastosadministrativos' => isset($d['gastosadministrativos']) ? (float) $d['gastosadministrativos'] : 0.00,
            ':idasesor' => $d['idasesor']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function createFinanciamiento(int $idcotizacion, int $numcuotas, float $inicial, float $valorcuota, string $moneda, float $precioventa): int
    {
        $sql = "INSERT INTO cotizacion_financiamiento
            (idcotizacion, numcuotas, inicial, valorcuota, moneda, precioventa)
            VALUES (:idcotizacion, :numcuotas, :inicial, :valorcuota, :moneda, :precioventa)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':idcotizacion' => $idcotizacion,
            ':numcuotas' => $numcuotas,
            ':inicial' => $inicial,
            ':valorcuota' => $valorcuota,
            ':moneda' => $moneda,
            ':precioventa' => $precioventa
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getFinanciamientos(int $idcotizacion): array
    {
        $sql = "SELECT idfinanciamiento, idcotizacion, numcuotas, inicial, valorcuota, moneda, precioventa, creado
            FROM cotizacion_financiamiento
            WHERE idcotizacion = :idcotizacion
            ORDER BY numcuotas ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idcotizacion' => $idcotizacion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $idcotizacion): ?array
    {
        $query = "SELECT * FROM vwGetCotizacionDetail WHERE idcotizacion = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $idcotizacion, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            if ($row) {
                $row['opciones_financiamiento'] = $this->getFinanciamientos($idcotizacion);
            }
            return $row;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtiene todas las cotizaciones vencidas
     */
    public function getAllVencidas(): array
    {
        $query = "SELECT * FROM vwGetAllCotizacionVencidas";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las cotizaciones vencidas de un asesor específico
     */
    public function getAllVencidasByAsesor(int $idasesor): array
    {
        $query = "SELECT * FROM vwGetAllCotizacionVencidas WHERE idasesor = :idasesor ORDER BY fechaRegistro DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idasesor', $idasesor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Funcion de reactivar una cotizacion con nueva fecha
     */
    public function reactivar(int $idcotizacion, int $vigenciadias = 7): bool
    {
        $sql = "UPDATE cotizaciones
            SET vigenciadias = :vig,
                modificado = NOW(),
                fechareactivacion = NOW(),
                estadocotizacion = 'P'
            WHERE idcotizacion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':vig' => $vigenciadias,
            ':id' => $idcotizacion
        ]);
        return $stmt->rowCount() > 0;
    }


}
