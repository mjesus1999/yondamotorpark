<?php
//app/Controller/Cotizacion.php
namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;
use DateTime;
use DateInterval;
use PDOException;

class Cotizacion
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }


    public function getAll(string $estado): array
    {

        $query = "SELECT * FROM vwGetAllCotizacion WHERE estadocotizacion = :estado 
              ORDER BY fechaRegistro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrecioVehiculoAlContado(int $idvehiculo): ?float
    {
        $sql = "SELECT precioventa FROM vehiculos WHERE idvehiculo = :idvehiculo LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':idvehiculo', $idvehiculo, PDO::PARAM_INT);
            $stmt->execute();

            $precio = $stmt->fetchColumn();

            return $precio !== false ? (float)$precio : null;
        } catch (PDOException $e) {
            error_log("Error en getPrecioVehiculoAlContado: " . $e->getMessage());
            return null;
        }
    }
    public function getAllByAsesor(int $idasesor, string $estado): array
    {
        $query = "SELECT * FROM vwGetAllCotizacion 
              WHERE idasesor = :idasesor AND estadocotizacion = :estado 
              ORDER BY fechaRegistro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idasesor", $idasesor, PDO::PARAM_INT);
        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function completoInicial(int $idcotizacion): int
    {
        $query = "
                SELECT 
                c.*,
                CASE 
                    WHEN c.estadocotizacion IN('A','S') 
                        AND c.totalpagado >= c.inicial
                    THEN 1 ELSE 0 
                END AS habilitar_contrato
            FROM (
                SELECT 
                    cot.idcotizacion,
                    cot.inicial,
                    cot.moneda,
                    cot.estadocotizacion,
                    COALESCE(SUM(
                        CASE
                            WHEN cot.moneda = 'PEN' THEN p.amortizacion
                            WHEN cot.moneda = 'USD' THEN
                                CASE
                                    WHEN p.moneda = 'USD' THEN p.montomonedaoriginal
                                    WHEN p.moneda = 'PEN' THEN (p.amortizacion / p.tipocambioaplicado)
                                    ELSE 0
                                END
                            ELSE 0
                        END
                    ), 0) AS totalpagado
                FROM cotizaciones cot
                LEFT JOIN pagos p 
                    ON p.idcotizacion = cot.idcotizacion
                    AND p.idconcepto IN (
                        SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial'
                    )
                WHERE cot.idcotizacion = :idcotizacion
                GROUP BY cot.idcotizacion, cot.inicial, cot.moneda, cot.estadocotizacion
            ) AS c
            LIMIT 1;

    ";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":idcotizacion", $idcotizacion, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? intval($result["habilitar_contrato"]) : 0;
        } catch (PDOException $e) {
            error_log("Error en completoInicial: " . $e->getMessage());
            return -1;
        }
    }



    public function getDatosCotizacion(string $id): array
    {

        $query = "SELECT 
              idcotizacion,
              idvehiculo,
              tipocotizacion,
              CONCAT(vehiculo, ' / ', color) AS vehiculo,
              precioventa,
              moneda,
              inicial,
              nombrecliente,
              documento,
              telefono,
              direccion,
              numcuotas,
              valorcuota,
              estadocotizacion
            FROM vwGetAllCotizacion
            WHERE idcotizacion = :idcotizacion LIMIT 1;";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idcotizacion", $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalPagadoYSaldoPendiente($idcotizacion): array
    {
        $query = "
            SELECT 
                c.idcotizacion,
                v.idvehiculo,
                c.moneda AS moneda_cotizacion, -- Moneda de la cotización (PEN o USD)
                c.inicial AS monto_inicial,
                COALESCE(SUM(
                    CASE
                        WHEN c.moneda = 'PEN' THEN p.amortizacion
                        WHEN c.moneda = 'USD' THEN
                            
                            IF(p.moneda = 'USD', p.montomonedaoriginal, 
                            
                               (p.amortizacion / p.tipocambioaplicado)
                            )
                        ELSE 0
                    END
                ), 0) AS totalpagado,
                COALESCE(
                    (
                        SELECT pp.saldorestante
                        FROM pagos pp
                        WHERE pp.idcotizacion = c.idcotizacion
                        AND pp.idconcepto = (SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial' LIMIT 1)
                        ORDER BY pp.fechapago DESC, pp.idpago DESC
                        LIMIT 1
                    ),
                    c.inicial
                ) AS saldorestante
                
            FROM cotizaciones c
            INNER JOIN vehiculos v 
                ON v.idvehiculo = c.idvehiculo
            LEFT JOIN pagos p 
                ON p.idcotizacion = c.idcotizacion
                AND p.idconcepto = (SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial' LIMIT 1)
            WHERE c.idcotizacion = :idcotizacion
            GROUP BY v.idvehiculo, c.idcotizacion, c.inicial, c.moneda;
        ";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":idcotizacion", $idcotizacion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

   public function getHistorialPagosInicial($idcotizacion): array
    {
        $query = "
        SELECT 
            DATE_FORMAT(p.fechapago,'%d-%m-%Y') AS fechapago,
            e.entidad AS entidadbancaria,
            cp.numcuenta,
            p.mediopago,
            p.numerotransaccion,
            CASE
                WHEN c.moneda = 'PEN' THEN p.amortizacion
                WHEN c.moneda = 'USD' THEN
                    IF(p.moneda = 'USD', p.montomonedaoriginal, (p.amortizacion / p.tipocambioaplicado))
                ELSE p.amortizacion 
            END AS monto_pago,
            p.saldorestante,
            CASE 
                WHEN c.moneda = 'PEN' THEN 'S/'
                WHEN c.moneda = 'USD' THEN '$'
                ELSE 'S/'
            END AS moneda_simbolo,
            p.comprobante,
            p.observacion
            
        FROM pagos p
        INNER JOIN cotizaciones c 
            ON c.idcotizacion = p.idcotizacion
        LEFT JOIN cuentaspago cp 
            ON p.idcuentapago = cp.idcuentapago
        LEFT JOIN entidadespago e 
            ON cp.identidadpago = e.identidadpago
        WHERE c.idcotizacion = :idcotizacion
          AND p.idconcepto = (SELECT idconcepto FROM conceptospago WHERE concepto = 'Inicial' LIMIT 1)
        ORDER BY p.fechapago ASC, p.idpago ASC;
        ";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":idcotizacion", $idcotizacion, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }


    public function addPagoInicial($params = [])
    {
        $query = "CALL sp_pagoInicial(:idconcepto, :idcotizacion, :idvehiculo, :idcuentapago, :idcolcaja, :mediopago, :numerotransaccion, :fechapago, :amortizacion, :saldorestante, :comprobante, :observacion,:moneda,:montomonedaoriginal,:tipocambioaplicado)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':idconcepto' => $params['idconcepto'],
            ':idcotizacion' => $params['idcotizacion'],
            ':idvehiculo' => $params['idvehiculo'],
            ':idcuentapago' => $params['idcuentapago'] ?? 0,
            ':idcolcaja' => $params['idcolcaja'] ?? null,
            ':mediopago' => $params['mediopago'],
            ':numerotransaccion' => $params['numerotransaccion'],
            ':fechapago' => $params['fechapago'],
            ':amortizacion' => $params['amortizacion'],
            ':saldorestante' => $params['saldorestante'],
            ':comprobante' => $params['comprobante'],
            ':observacion' => $params['observacion'],
            ':moneda' => $params['moneda'],
            ':montomonedaoriginal' => $params['montomonedaoriginal'],
            ':tipocambioaplicado' => $params['tipocambioaplicado'],
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int)($result['idpago'] ?? 0);
    }

    public function getDataActaSeparacionByIdCotizacion(int $idcotizacion): ?array
    {
        $query = 'CALL sp_getActaSeparacionByIdCotizacion(:idcotizacion)';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":idcotizacion", $idcotizacion, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }









    public function aprobarCotizacion(int $id): int
    {
        try {

            $query = "UPDATE cotizaciones SET estadocotizacion = 'A' WHERE idcotizacion = :id";
            $stmt = $this->db->prepare($query);

            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {

            error_log("Error al aprobar cotización ID {$id}: " . $e->getMessage());
            return 0;
        }
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
