<?php

/**
 * Modelo de Pagos de Orden de Compra
 * 
 * Gestiona las operaciones de base de datos relacionadas con los pagos
 * de órdenes de compra de vehículos, incluyendo conversión de monedas,
 * cálculo de saldos y gestión de amortizaciones.
 * 
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase PagosOC
 * 
 * Modelo para la gestión de pagos de órdenes de compra.
 * Proporciona métodos para registrar pagos, calcular amortizaciones,
 * manejar conversiones de moneda (PEN/USD) y obtener saldos pendientes.
 */
class PagosOC
{
    /**
     * Instancia de conexion a la base de datos
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor del modelo
     * 
     * Inicializa la conexion a la base de datos
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     *  Registra un nuevo pago de orden de compra
     * 
     * Crea un nuevo registro de pago asociado a una orden de compra.
     * El ID del colaborador de logística se obtiene automáticamente de la sesión.
     * Maneja pagos en diferentes monedas (USD/PEN) con tipo de cambio opcional.
     * 
     * Notas:
     * - tipocambio es null cuando el pago es en dólares
     * - observaciones puede ser null
     * 
     * @param array $params Array asociativo con los datos del pago:
     *                      - idorden: int (ID de la orden de compra)
     *                      - identidadpago: int (ID de la entidad de pago/banco)
     *                      - fecharealpago: string (Fecha del pago, formato: YYYY-MM-DD)
     *                      - numtransaccion: string (Número de transacción)
     *                      - moneda: string (Moneda del pago: USD/PEN)
     *                      - tipocambio: float|null (Tipo de cambio, null si es USD)
     *                      - valorUSD: float (Valor equivalente en USD)
     *                      - amortizacion: float (Monto pagado)
     *                      - comprobante: string (Número de comprobante)
     *                      - observaciones: string|null (Observaciones adicionales, opcional)
     * @return int ID del pago creado o -1 en caso de error
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
     * Lista todos los pagos de una orden de compra específica
     * 
     * Retorna el historial completo de pagos de una orden de compra,
     * incluyendo información del colaborador de logística y la entidad de pago.
     * Calcula automáticamente el total amortizado en USD, considerando
     * conversiones de moneda cuando aplica.
     * 
     * @param int $idorden
     * @return array {pagos: array, totalAmortizado: float|int|array{pagos: array, totalAmortizado: int}}
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
    /**
     * Obtiene todas las entidades de pago disponibles
     * 
     * Retorna el listado de bancos o entidades financieras disponibles
     * para registrar pagos, ordenadas alfabéticamente.
     * 
     * @return array Array asociativo con las entidades de pago (identidadpago, entidad)
     *               o array vacío en caso de error
     */
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
     * Obtiene el saldo restante de una orden de compra
     * 
     * Calcula el saldo pendiente de pago de una orden de compra.
     * Si existen pagos previos, retorna el saldo del último pago registrado.
     * Si no hay pagos, calcula el total de la orden (suma de productos + IGV 18%).
     * Todos los valores se manejan en USD considerando conversiones de moneda.
     * 
     * 1. Busca el saldo del último pago registrado
     * 2. Si no existe, calcula el total de la OC (preciocompra * 1.18)
     * 3. Retorna el saldo en USD
     * 
     * @param int $idorden ID de la orden de compra
     * @return float Saldo restante en USD o 0.0 en caso de error
     */
    public function obtenerSaldoRestante(int $idorden): float
    {
        // Obtener el saldo del último pago si existe
        $query_saldo_existente = '
                SELECT saldo
                FROM pagosOC
                WHERE idorden = :idorden
                ORDER BY fecharealpago DESC, idpagooc DESC
                LIMIT 1;
            ';

        try {
            $stmt_saldo = $this->db->prepare($query_saldo_existente);
            $stmt_saldo->execute([':idorden' => $idorden]);
            $saldo_de_pago = $stmt_saldo->fetch(PDO::FETCH_ASSOC);

            // Si se encontró un pago, devolver el saldo de ese pago.
            if ($saldo_de_pago) {
                return (float) $saldo_de_pago['saldo'];
            }
        } catch (PDOException $error) {
            error_log('Error en obtenerSaldoRestante (saldo existente): ' . $error->getMessage());
        }

        //  Si no hay pagos, calcular el saldo total de la OC
        $query_total_oc = '
        SELECT ROUND(IFNULL(SUM(preciocompra * 1.18), 0), 2) AS totalOC
        FROM detordencompra
        WHERE idordencompra = :idorden;
    ';

        try {
            $stmt_total = $this->db->prepare($query_total_oc);
            $stmt_total->execute([':idorden' => $idorden]);
            $total_oc = $stmt_total->fetch(PDO::FETCH_ASSOC);

            return $total_oc ? (float) $total_oc['totalOC'] : 0.0;
        } catch (PDOException $error) {
            error_log('Error en obtenerSaldoRestante (total OC): ' . $error->getMessage());
            return 0.0;
        }
    }
}
