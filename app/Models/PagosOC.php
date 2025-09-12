<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class PagosOC
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Registrar un nuevo pago - tipo de cambio es null, cuando el pago es moneda de dolares- Obervaciones es null tambien.
     */
    public function create($params = []): int
    {
        $query = 'INSERT INTO pagosOC(idorden, idlogistica,identidadpago,fecharealpago,numtransaccion,moneda,tipocambio,valorUSD, amortizacion, comprobante,observaciones) 
                      VALUES(:idorden, :idlogistica,:identidadpago,:fecharealpago,:numtransaccion,:moneda,:tipocambio,:valorUSD,:amortizacion, :comprobante,:observaciones)';

        try {
            $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idorden' => $params['idorden'],
                ':idlogistica' => $idUsuario,
                ':identidadpago' => $params['identidadpago'],
                ':fecharealpago' => $params['fecharealpago'],
                ':numtransaccion' => $params['numtransaccion'],
                ':moneda' => $params['moneda'],
                ':tipocambio' => $params['tipocambio'],
                ':valorUSD' => $params['valorUSD'],
                ':amortizacion' => $params['amortizacion'],
                ':comprobante' => $params['comprobante'],
                ':observaciones' => $params['observaciones']

            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log('Error en create PagosOC: ' . $error->getMessage());
            return -1;
        }
    }

    /**
     * Listar todos los pagos de una orden de compra
     */
    public function listarPagosByOC(int $idorden): array
    {
        $query = "SELECT
                    p.idpagooc,
                    p.amortizacion,
                    p.moneda,
                    p.tipocambio,
                    p.numtransaccion,
                    p.moneda,
                    p.observaciones,
                    p.saldo,
                    P.tipocambio,
                    p.valorUSD,
                    p.comprobante,
                    p.fecharealpago,
                    ep.entidad,

                    CONCAT(per.apellidos, ' ', per.nombres) AS logistica
                FROM pagosOC p
                INNER JOIN colaboradores col ON p.idlogistica = col.idcolaborador
                INNER JOIN contratoslaborales cl ON col.idcontratolaboral = cl.idcontratolaboral
                INNER JOIN personas per ON cl.idpersona = per.idpersona
                INNER JOIN entidadespago ep ON p.identidadpago = ep.identidadpago
                WHERE p.idorden =:idorden
                ORDER BY p.fecha ASC;";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idorden' => $idorden]);
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcular el total amortizado en USD
            $totalAmortizado = 0;
            foreach ($pagos as $pago) {
                if ($pago['moneda'] === 'PEN' && $pago['tipocambio'] > 0) {
                    $totalAmortizado += $pago['amortizacion'] / $pago['tipocambio'];
                } else {
                    $totalAmortizado += $pago['amortizacion'];
                }
            }

            return [
                'pagos' => $pagos,
                'totalAmortizado' => $totalAmortizado
            ];
        } catch (PDOException $error) {
            error_log('Error en listarPorOC PagosOC: ' . $error->getMessage());
            return [
                'pagos' => [],
                'totalAmortizado' => 0
            ];
        }
    }



    public function getAllEntidadesPago(): array
    {
        $query = 'SELECT identidadpago, entidad FROM entidadespago ORDER BY entidad ASC;';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log('Error en getEntidadesPago PagosOC: ' . $error->getMessage());
            return [];
        }
    }
    /**
     * Obtiene el saldo restante de una orden de compra, considerando la conversión de PEN a USD.
     *
     * @param int $idorden ID de la orden de compra.
     * @return float El saldo restante en USD.
     */
    public function obtenerSaldoRestante(int $idorden): float
    {
                    $query = '
                    SELECT 
                        oc.idordencompra AS idorden,
                        IFNULL(SUM(d.preciocompra * 1.18), 0) AS totalOC,
                        (
                            SELECT IFNULL(SUM(
                                CASE 
                                    WHEN p.moneda = "PEN" AND p.tipocambio > 0 THEN p.amortizacion / p.tipocambio
                                    ELSE p.amortizacion
                                END
                            ), 0) 
                            FROM pagosOC p
                            WHERE p.idorden = oc.idordencompra
                        ) AS totalPagado,
                        (
                            IFNULL(SUM(d.preciocompra * 1.18), 0)
                            -
                            (
                                SELECT IFNULL(SUM(
                                    CASE 
                                        WHEN p.moneda = "PEN" AND p.tipocambio > 0 THEN p.amortizacion / p.tipocambio
                                        ELSE p.amortizacion
                                    END
                                ), 0) 
                                FROM pagosOC p
                                WHERE p.idorden = oc.idordencompra
                            )
                        ) AS saldoRestante
                    FROM ordenescompra oc
                    LEFT JOIN detordencompra d ON d.idordencompra = oc.idordencompra
                    WHERE oc.idordencompra = :idorden
                    GROUP BY oc.idordencompra;
                ';

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idorden' => $idorden]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log('Saldo restante para orden ' . $idorden . ': ' . ($row ? $row['saldoRestante'] : '0.0'));
            return $row ? (float) $row['saldoRestante'] : 0.0;
        } catch (PDOException $error) {
            error_log('Error en obtenerSaldoRestante: ' . $error->getMessage());
            return 0.0;
        }
    }

}


// $orden = new OrdenCompra();

// var_dump($orden->getAll());
