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

    public function getAll(): array
    {
        $query = "SELECT * FROM vwGetAllCotizacion ORDER BY fechaRegistro DESC LIMIT 10";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las cotizaciones de un asesor específico
     */
    public function getAllByAsesor(int $idasesor): array
    {
        $query = "SELECT * FROM vwGetAllCotizacion WHERE idasesor = :idasesor ORDER BY fechaRegistro DESC";
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


    public function generarCronograma(float $importeTotal, float $inicial, int $meses): array
    {
        $tasaAnual = 0.65;

        $tasaMensual = pow((1 + $tasaAnual), (1 / 12)) - 1;
        // Monto a financiar
        $montoFinanciar = $importeTotal - $inicial;

        $valorCuota = round($this->Pago($tasaMensual, $meses, $montoFinanciar), 2);

        $cronograma = [];

        $saldoCapital = $montoFinanciar;
        $fechaPago = new Datetime('now');

        for ($i = 1; $i <= $meses; $i++) {
            // Calcular interés para el mes
            $interesExacto = $saldoCapital * $tasaMensual;
            $interes = round($interesExacto, 2);

            $abonoCapital = $valorCuota - $interes;

            if ($i === $meses) {
                $abonoCapital = $saldoCapital;
                $interes = $valorCuota - $abonoCapital;
            }

            // Actualizar el saldo de capital
            $saldoCapital -= $abonoCapital;
            $saldoCapital = round($saldoCapital, 2);

            // Formatear la fecha para cada pago
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

        return $cronograma; // Retornamos el cronograma como array.
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
        $query = "SELECT * FROM vwGetCotizacionDetail WHERE idcotizacion = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $idcotizacion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            // log $e->getMessage()
            return null;
        }
    }
    /* public function getRequisitos(): array
    {
        $stmt = $this->pdo->prepare("SELECT idrequisito, requisito FROM requisitos ORDER BY idrequisito");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } */
}
