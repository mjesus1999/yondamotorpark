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
     * Registrar un nuevo pago
     */
    public function create($params = []): int
    {
        $query = 'INSERT INTO pagosOC(idorden, idlogistica, amortizacion, comprobante, fecharealpago) 
                      VALUES(:idorden, :idlogistica, :amortizacion, :comprobante, :fecharealpago)';

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idorden' => $params['idorden'],
                ':idlogistica' => $params['idlogistica'],
                ':amortizacion' => $params['amortizacion'],
                ':comprobante' => $params['comprobante'],
                ':fecharealpago' => $params['fecharealpago']
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
                    p.saldo,
                    p.comprobante,
                    p.fecharealpago,
                    CONCAT(per.apellidos, ' ', per.nombres) AS logistica
                FROM pagosOC p
                INNER JOIN colaboradores col ON p.idlogistica = col.idcolaborador
                INNER JOIN contratoslaborales cl ON col.idcontratolaboral = cl.idcontratolaboral
                INNER JOIN personas per ON cl.idpersona = per.idpersona
                WHERE p.idorden =:idorden
                ORDER BY p.fecha ASC;";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idorden' => $idorden]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log('Error en listarPorOC PagosOC: ' . $error->getMessage());
            return [];
        }
    }

    /**
     * Obtener saldo restante de la orden de compra (con IGV)
     */
    public function obtenerSaldoRestante(int $idorden): float
    {
        $query = 'SELECT 
                oc.idordencompra AS idorden,
                (SELECT IFNULL(SUM(preciocompra * 1.18),0) 
                FROM detordencompra 
                WHERE idordencompra = oc.idordencompra) AS totalOC,

                (SELECT IFNULL(SUM(amortizacion),0) 
                FROM pagosOC 
                WHERE idorden = oc.idordencompra) AS totalPagado,

                (
                (SELECT IFNULL(SUM(preciocompra * 1.18),0) 
                FROM detordencompra 
                WHERE idordencompra = oc.idordencompra)
                -
                (SELECT IFNULL(SUM(amortizacion),0) 
                FROM pagosOC 
                WHERE idorden = oc.idordencompra)
                ) AS saldoRestante
            FROM ordenescompra oc
            WHERE oc.idordencompra = :idorden;';

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idorden' => $idorden]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (float) $row['saldoRestante'] : 0.0;
        } catch (PDOException $error) {
            error_log('Error en obtenerSaldoRestante PagosOC: ' . $error->getMessage());
            return 0.0;
        }
    }
}

// $orden = new OrdenCompra();

// var_dump($orden->getAll());
