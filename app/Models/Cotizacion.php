<?php

/**
 * Modelo de Cotización
 * 
 * Gestiona las operaciones de base de datos relacionadas con cotizaciones
 * de vehículos, incluyendo cálculo de financiamiento, generación de cronogramas
 * de pago, gestión de pagos iniciales, y control de estados. Implementa
 * algoritmos financieros para calcular cuotas y tasas de interés.
 */

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;
use DateTime;
use DateInterval;
use PDOException;

/**
 * Clase Cotizacion
 * 
 * Modelo para la gestión de cotizaciones de venta de vehículos.
 * Proporciona métodos para crear cotizaciones con diferentes opciones
 * de financiamiento, calcular cuotas mensuales con interés compuesto,
 * generar cronogramas de pago, gestionar pagos de inicial, y controlar
 * estados (pendiente, aprobada, vencida). Implementa fórmulas financieras
 * para cálculo de anualidades y manejo de múltiples monedas (PEN/USD)
 */
class Cotizacion
{
    /**
     * Instancia de conexión a la base de datos
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor del modelo
     * 
     * Inicializa la conexión a la base de datos
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todas las cotizaciones por estado
     * 
     * Retorna cotizaciones filtradas por estado mediante vista,
     * ordenadas por fecha de registro descendente.
     * 
     * Estados válidos: 'P' (Pendiente), 'A' (Aprobada), 'S' (Separada),
     * 'V' (Vencida), 'C' (Cancelada)
     * 
     * @param string $estado Estado de las cotizaciones a consultar
     * @return array Array asociativo con las cotizaciones
     */
    public function getAll(string $estado): array
    {

        $query = "SELECT * FROM vwgetallcotizacion WHERE estadocotizacion = :estado 
              ORDER BY fechaRegistro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el precio de venta al contado de un vehículo
     * 
     * Consulta el precio base del vehículo para cálculos de cotización.
     * 
     * @param int $idvehiculo ID del vehículo
     * @return float|null Precio de venta o null si no existe
     */
    public function getPrecioVehiculoAlContado(int $idvehiculo): ?float
    {
        $sql = "SELECT precioventa FROM vehiculos WHERE idvehiculo = :idvehiculo LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':idvehiculo', $idvehiculo, PDO::PARAM_INT);
            $stmt->execute();

            $precio = $stmt->fetchColumn();

            return $precio !== false ? (float) $precio : null;
        } catch (PDOException $e) {
            error_log("Error en getPrecioVehiculoAlContado: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene cotizaciones de un asesor específico por estado
     * 
     * Filtra cotizaciones por asesor y estado, ordenadas por fecha descendente.
     * Útil para dashboards personalizados por vendedor.
     * 
     * @param int $idasesor ID del asesor de ventas
     * @param string $estado Estado de las cotizaciones
     * @return array Array asociativo con las cotizaciones del asesor
     */
    public function getAllByAsesor(int $idasesor, string $estado): array
    {
        $query = "SELECT * FROM vwgetallcotizacion
              WHERE idasesor = :idasesor AND estadocotizacion = :estado 
              ORDER BY fechaRegistro DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idasesor", $idasesor, PDO::PARAM_INT);
        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 
     * Verifica si se completó el pago del inicial
     * 
     * Calcula el total pagado del inicial considerando conversiones de moneda
     * (PEN/USD) y determina si se ha completado el monto requerido para
     * habilitar la generación del contrato.
     *
     * @param int $idcotizacion ID de la cotización
     * @return int 1 si inicial completado y cotización aprobada/separada,
     *             0 si no cumple condiciones, -1 en error
     */
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

    /**
     * Obtiene datos resumidos de una cotización
     * 
     * Retorna información esencial de la cotización para visualización rápida:
     * vehículo, precio, cliente, condiciones de financiamiento.

     * @param string $id ID de la cotización
     * @return array Array asociativo con datos resumidos de la cotización
     */
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
            FROM vwgetallcotizacion
            WHERE idcotizacion = :idcotizacion LIMIT 1;";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idcotizacion", $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getDatosClienteByCotizacion(int $id): ?array
    {
        $query = "SELECT 
                        nombrecliente,
                        documento,
                        direccion,
                        email
                    FROM vwgetallcotizacion
                    WHERE idcotizacion = :idcotizacion LIMIT 1;";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":idcotizacion", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener datos del cliente por cotización ID {$id}: " . $e->getMessage());
            return null;
        }
    }






    /**
     * Obtiene el total pagado y saldo pendiente del inicial
     * 
     * Calcula el monto total pagado de inicial con conversión de moneda automática
     * y determina el saldo restante. Si hay pagos previos, usa el saldo del último;
     * si no, retorna el monto inicial completo.

     * @param mixed $idcotizacion ID de la cotización
     * @return array Array con: idcotizacion, idvehiculo, moneda_cotizacion,
     *               monto_inicial, totalpagado, saldorestante
     */
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

    /**
     * Obtiene el historial de pagos del inicial
     * 
     * Retorna todos los pagos realizados del concepto "Inicial" con información
     * detallada de entidad bancaria, medio de pago, conversiones de moneda,
     * y comprobantes. Ordenado cronológicamente.

     * @param int $idcotizacion ID de la cotización   
     * @return array|null Array de pagos con conversión automática a moneda de cotización,
     *                    o null en caso de error
     */
    public function getHistorialPagosInicial($idcotizacion): ?array
    {
        $query = "
        SELECT 
            c.idcotizacion,
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
            p.enlace_pdf_nubefact,
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
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // return $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Registra un pago del inicial de cotización
     * 
     * Ejecuta procedimiento almacenado que registra un pago de inicial
     * con cálculo automático de saldo restante y soporte para múltiples
     * monedas con tipo de cambio.

     * @param array $params Array asociativo con datos del pago:
     *                      - idconcepto: int (ID del concepto "Inicial")
     *                      - idcotizacion: int
     *                      - idvehiculo: int
     *                      - idcuentapago: int|null (null si es efectivo)
     *                      - idcolcaja: int|null (ID del colaborador)
     *                      - mediopago: string (Efectivo/Transferencia/etc.)
     *                      - numerotransaccion: string
     *                      - fechapago: string (Fecha del pago)
     *                      - amortizacion: float (Monto pagado)
     *                      - saldorestante: float (Saldo después del pago)
     *                      - comprobante: string (Ruta del comprobante)
     *                      - observacion: string
     *                      - moneda: string (Moneda del pago: USD/PEN)
     *                      - montomonedaoriginal: float (Monto en moneda original)
     *                      - tipocambioaplicado: float (Tipo de cambio usado)
     * @return int ID del pago creado o 0 en caso de error
     */
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
        return (int) ($result['idpago'] ?? 0);
    }

    /**
     * Obtiene datos para acta de separación
     * 
     * Ejecuta procedimiento almacenado que retorna toda la información necesaria
     * para generar el acta de separación: datos del cliente, vehículo, condiciones
     * de venta y financiamiento.

     * @param int $idcotizacion ID de la cotización
     */
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

    /**
     * Aprueba una cotización
     * 
     * Cambia el estado de la cotización a 'A' (Aprobada), permitiendo
     * el registro de pagos de inicial y posterior generación de contrato.

     * @param int $id ID de la cotización
     * @return int Número de filas afectadas (1 si exitoso, 0 si no)
     */
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

    /**
     * Busca un cliente por tipo y número de documento
     * 
     * Busca en tablas de personas (DNI) o empresas (RUC) según el tipo
     * de documento proporcionado. Retorna datos del cliente si existe
     * como cliente registrado en el sistema.
     *
     *- DNI: Busca en tabla personas
     * - RUC: Busca en tabla empresas
     *
     * @param string $tipo Tipo de documento: 'DNI' o 'RUC'
     * @param string $doc Número de documento
     */
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
                    e.telprimario, e.telsecundario,e.direccion, e.email
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

    /**
     * Calcula el pago de una anualidad (fórmula financiera)
     * 
     * Método privado que implementa la fórmula estándar de anualidades
     * para calcular el pago periódico de un préstamo con interés compuesto.
     *
     * @param float $tasaInteres Tasa de interés por período (decimal)
     * @param int $numPagos Número total de pagos
     * @param float $montoPrestamo Monto principal del préstamo
     * @return float|int Valor del pago periódico
     */
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

    /**
     * Calcula el pago mensual de un financiamiento
     * 
     * Calcula la cuota mensual aplicando tasa de interés anual convertida
     * a mensual mediante interés compuesto. La tasa por defecto es 65% anual
     * (0.65), típica de financiamiento de vehículos.
     *
     * Conversión de tasa: tasa_mensual = (1 + tasa_anual)^(1/12) - 1
     *
     * @param float $importeTotal Precio total del vehículo
     * @param float $inicial Monto del pago inicial
     * @param int $meses Plazo en meses
     * @param float $tasaAnual Tasa de interés anual (por defecto: 0.65 = 65%)
     * @return float Valor de la cuota mensual redondeada a 2 decimales
     */
    public function calcularPagoMensual($importeTotal, $inicial, $meses, $tasaAnual = 0.65)
    {
        $tasaMensual = pow((1 + $tasaAnual), (1 / 12)) - 1;
        $montoFinanciar = $importeTotal - $inicial;
        $cuota = round($this->Pago($tasaMensual, $meses, $montoFinanciar), 2);
        return $cuota;
    }

    /**
     * Genera cronograma completo de pagos
     * 
     * Crea un cronograma detallado mes a mes mostrando la distribución
     * de cada pago entre interés y capital, con el saldo de capital
     * decreciente. El último pago ajusta el abono a capital para saldar
     * exactamente el préstamo (elimina centavos residuales por redondeo).
     *
     * Características:
     * - Primera cuota: 1 mes después de la fecha actual
     * - Cada cuota incluye: interés calculado, abono a capital, saldo
     * - Ajuste en última cuota para saldo exacto en 0
     * 
     * @param float $importeTotal Precio total del vehículo
     * @param float $inicial Monto del pago inicial
     * @param int $meses Plazo en meses
     * @param float $tasaAnual Tasa de interés anual (por defecto: 0.65)
     * @return array <array|array{abono_capital: float, fecha_pago: string, interes: float, item: int, saldo_capital: float, valor_cuota: float>}
     */
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

    /**
     * Crea una nueva cotización
     * 
     * Registra una cotización con todas sus condiciones de venta y
     * financiamiento. Incluye validaciones de formato, cliente, vehículo
     * y asesor responsable.

     * @param array $d Array asociativo con datos de la cotización:
     *                 - idformato: int (Tipo de formato de cotización)
     *                 - idcliente: int (ID del cliente)
     *                 - idvehiculo: int (ID del vehículo)
     *                 - moneda: string (USD/PEN)
     *                 - precioventa: float (Precio del vehículo)
     *                 - vigenciadias: int (Días de vigencia)
     *                 - inicial: float (Monto inicial)
     *                 - numcuotas: int (Número de cuotas)
     *                 - valorcuota: float (Valor de cada cuota)
     *                 - gastosadministrativos: float (Gastos admin, opcional)
     *                 - tasaanual: float (Tasa anual, opcional, default: 65%)
     *                 - tasamensual: float (Tasa mensual, opcional)
     *                 - idasesor: int (ID del asesor de ventas)
     * @return int ID de la cotización creada
     */
    public function create(array $d): int
    {
        $sql = "INSERT INTO cotizaciones
            (idformato, idcliente, idvehiculo, moneda, precioventa,
            vigenciadias, inicial, numcuotas, valorcuota, gastosadministrativos, 
            tasaanual, tasamensual, idasesor)
            VALUES
            (:idformato, :idcliente, :idvehiculo, :moneda, :precioventa,
            :vigenciadias, :inicial, :numcuotas, :valorcuota, :gastosadministrativos,
            :tasaanual, :tasamensual, :idasesor)";

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
            ':tasaanual' => isset($d['tasaanual']) ? (float) $d['tasaanual'] : 65.00,
            ':tasamensual' => isset($d['tasamensual']) ? (float) $d['tasamensual'] : 0.00,
            ':idasesor' => $d['idasesor']
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Crea una opción de financiamiento adicional
     * 
     * Registra una opción alternativa de financiamiento para una cotización,
     * permitiendo ofrecer múltiples planes de pago al cliente (ej: 12, 24, 36 meses).
     * Las opciones comparten el mismo vehículo y cliente pero difieren en plazo y cuota.
     *
     * @param int $idcotizacion ID de la cotización base
     * @param int $numcuotas Número de cuotas de esta opción
     * @param float $inicial Monto inicial
     * @param float $valorcuota Valor de la cuota mensual
     * @param string $moneda Moneda (USD/PEN)
     * @param float $precioventa Precio de venta del vehículo
     * @return int ID del financiamiento creado
     */
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

    /**
     * Obtiene todas las opciones de financiamiento de una cotización
     * 
     * Retorna todas las cotizaciones generadas en la misma sesión (mismo cliente,
     * vehículo y timestamp) para ofrecer múltiples alternativas de pago al cliente.
     * Permite comparar diferentes plazos y montos de cuota.
     *
     * @param int $idcotizacion ID de cualquier cotización del grupo
     * @return array Array de opciones de financiamiento ordenadas por número de cuotas ascendente
     */
    public function getFinanciamientos(int $idcotizacion): array
    {
        //Obtener la marca de tiempo de la cotización principal, el vehículo Y EL CLIENTE.
        $sqlBase = "SELECT idcliente, idvehiculo, creado, moneda, precioventa, gastosadministrativos 
                FROM cotizaciones 
                WHERE idcotizacion = :idcotizacion LIMIT 1";
        $stmtBase = $this->db->prepare($sqlBase);
        $stmtBase->execute([':idcotizacion' => $idcotizacion]);
        $baseData = $stmtBase->fetch(PDO::FETCH_ASSOC);

        if (!$baseData) {
            return [];
        }

        $idcliente = $baseData['idcliente'];
        $idvehiculo = $baseData['idvehiculo'];
        $fechaBase = $baseData['creado'];
        $sqlOpciones = "
        SELECT 
            c.idcotizacion, 
            c.numcuotas, 
            c.inicial, 
            c.valorcuota, 
            c.moneda, 
            c.precioventa
        FROM cotizaciones c
        WHERE c.idcliente = :idcliente                
          AND c.idvehiculo = :idvehiculo
          AND DATE_FORMAT(c.creado, '%Y-%m-%d %H:%i') = DATE_FORMAT(:fecha_base, '%Y-%m-%d %H:%i')
        ORDER BY c.numcuotas ASC
    ";

        $stmtOpciones = $this->db->prepare($sqlOpciones);
        $stmtOpciones->execute([
            ':idcliente' => $idcliente,
            ':idvehiculo' => $idvehiculo,
            ':fecha_base' => $fechaBase
        ]);


        return $stmtOpciones->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una cotización por ID con sus opciones de financiamiento
     * 
     * Retorna información completa de una cotización mediante vista,
     * incluyendo datos del cliente, vehículo, condiciones de venta,
     * y todas las opciones de financiamiento asociadas.

     * @param int $idcotizacion ID de la cotización
     */
    public function getById(int $idcotizacion): ?array
    {
        $query = "SELECT * FROM vwgetcotizaciondetail WHERE idcotizacion = :id LIMIT 1";
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
     * 
     * Retorna cotizaciones que superaron su período de vigencia sin
     * ser aprobadas o convertidas en venta.
     *
     * @return array Array de cotizaciones vencidas con información completa
     */
    public function getAllVencidas(): array
    {
        $query = "SELECT * FROM vwgetallcotizacionvencidas";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene cotizaciones vencidas de un asesor específico
     * 
     * Filtra cotizaciones vencidas por asesor para seguimiento individual
     * de gestión comercial y recuperación de oportunidades.
     *
     * @param int $idasesor ID del asesor de ventas
     * @return array Array de cotizaciones vencidas del asesor
     */
    public function getAllVencidasByAsesor(int $idasesor): array
    {
        $query = "SELECT * FROM vwgetallcotizacionvencidas WHERE idasesor = :idasesor ORDER BY fechaRegistro DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idasesor', $idasesor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reactiva una cotización vencida
     * 
     * Restablece una cotización vencida a estado 'P' (Pendiente) con nueva
     * fecha de vigencia, permitiendo continuar con el proceso de venta.
     * Registra la fecha de reactivación para auditoría.
     *
     * @param int $idcotizacion ID de la cotización a reactivar
     * @param int $vigenciadias Nueva vigencia en días (por defecto: 7 días)
     * @return bool True si se reactivó exitosamente, false en caso contrario
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


    public function getReporteCotizacionGeneral(): ?array
    {
        $query = "CALL sp_reporte_cotizaciones_general();";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {

            error_log("Error PDO en reporte general: " . $e->getMessage());
            return null;
        }
    }
}
