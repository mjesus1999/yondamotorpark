<?php

/**
 * Modelo de Pago de Cronograma
 * 
 * app/Models/PagoCronograma.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los pagos
 * de cuotas y penalidades de contratos de financiamiento de vehículos.
 * Maneja transacciones, validaciones de pagos completos y actualización
 * automática de estados de cronograma.
 * 
 */

namespace App\Models;

use App\Core\Database;
use League\Csv\Serializer\CastToArray;
use PDO;
use PDOException;

/**
 * Clase PagoCronograma
 * 
 * Modelo para la gestión de pagos de cronogramas de financiamiento.
 * Proporciona métodos para registrar pagos de cuotas y penalidades,
 * validar completitud de pagos y actualizar automáticamente estados.
 * Utiliza transacciones para garantizar integridad de datos.
 */
class PagoCronograma
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
     * Registra múltiples pagos en una transacción atómica
     * 
     * Procesa pagos de cuota y/o penalidad en una única transacción,
     * garantizando la atomicidad de la operación. Actualiza automáticamente
     * el estado del cronograma a 'Pagado' si se completa el pago total.
     * Solo registra pagos con montos mayores a 0.
     * 
     * @param array|null $pagoCuota Array con datos del pago de cuota:
     *                               - idcronograma: int
     *                               - idcuentapago: int
     *                               - mediopago: string
     *                               - numerotransaccion: string
     *                               - fechapago: string
     *                               - amortizacion: float (monto del pago)
     *                               - comprobante: string
     *                               - observacion: string
     *                               - tipo: 'Cuota'
     * @param array|null $pagoPenalidad Array con datos del pago de penalidad (misma estructura)
     * @return int[] Array con IDs de los pagos registrados o array vacío si falla
     */
    public function addMultiplePagos(?array $pagoCuota = null, ?array $pagoPenalidad = null): array
    {
        try {
            $this->db->beginTransaction();
            $ids = [];
            $idCronogramaParaValidar = null;

            // Procesar el pago de la cuota si el monto es mayor que 0
            if ($pagoCuota && $pagoCuota['amortizacion'] > 0) {
                $idPagoCuota = $this->add($pagoCuota);
                if ($idPagoCuota <= 0) {
                    $this->db->rollBack();
                    return [];
                }
                $ids[] = $idPagoCuota;

                $idCronogramaParaValidar = $pagoCuota['idcronograma'];
            }

            //  Procesar el pago de la penalidad si existe y el monto es mayor que 0
            if ($pagoPenalidad && $pagoPenalidad['amortizacion'] > 0) {
                $idPagoPenalidad = $this->add($pagoPenalidad);
                if ($idPagoPenalidad <= 0) {
                    $this->db->rollBack();
                    return [];
                }
                $ids[] = $idPagoPenalidad;
                // Si solo se pagó la penalidad, usar su ID de cronograma
                if ($idCronogramaParaValidar === null) {
                    $idCronogramaParaValidar = $pagoPenalidad['idcronograma'];
                }
            }

            // Actualizar el estado del cronograma si se ha registrado al menos un pago
            if (!empty($ids) && $idCronogramaParaValidar !== null) {
                $this->checkCuotaPagada($idCronogramaParaValidar);
            }

            $this->db->commit();
            return $ids;
        } catch (PDOException $error) {
            $this->db->rollBack();
            error_log("Error en la transacción de pagos: " . $error->getMessage());
            return [];
        }
    }



    public function actualizarEnlaceYDeclarado($idPago, $urlPdf, $urlXml, $urlCdr, $numeroBoleta)
    {
        // Usamos el campo declarado para marcar que la boleta fue enviada
        $sql = "UPDATE pagos SET 
                enlace_pdf_nubefact = :pdf, 
                enlace_xml_nubefact = :xmlUrl,
                numero_boleta_sunat = :num, 
                enlace_del_cdr = :cdr,
                declarado = 'S' 
            WHERE idpago = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':pdf' => $urlPdf,
            ':xmlUrl' => $urlXml,
            ':num' => $numeroBoleta,
            ':cdr' => $urlCdr,
            ':id' => $idPago
        ]);
    }





    /**
     * Inserta un único pago en la base de datos
     * 
     * Método protegido que registra un pago individual mediante procedimiento
     * almacenado. El ID del colaborador de caja se obtiene automáticamente
     * de la sesión.
     * 
     * @param array $params Array asociativo con los datos del pago:
     *                      - idcronograma: int (ID del cronograma)
     *                      - idcuentapago: int (ID de la cuenta de pago)
     *                      - mediopago: string (Efectivo/Transferencia/etc.)
     *                      - numerotransaccion: string (Número de transacción)
     *                      - fechapago: string (Fecha del pago)
     *                      - amortizacion: float (Monto del pago)
     *                      - comprobante: string (Ruta del comprobante)
     *                      - observacion: string (Observaciones adicionales)
     *                      - tipo: string ('Cuota' o 'Penalidad')
     * @return int ID del pago registrado, 0 si no se pudo obtener el ID,
     *             o -1 en caso de error
     */
    protected function add(array $params): int
    {
        $query = "CALL sp_addPagoCronograma(:idcronograma, :idcuentapago, :idcolcaja, :mediopago, :numerotransaccion, :fechapago, :amortizacion, :comprobante, :observacion, :tipo)";
        try {
            $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idcronograma' => $params['idcronograma'],
                ':idcuentapago' => $params['idcuentapago'],
                ':idcolcaja' => $idUsuario,
                ':mediopago' => $params['mediopago'],
                ':numerotransaccion' => $params['numerotransaccion'],
                ':fechapago' => $params['fechapago'],
                ':amortizacion' => $params['amortizacion'],
                ':comprobante' => $params['comprobante'],
                ':observacion' => $params['observacion'],
                ':tipo' => $params['tipo']
            ]);
            $idPago = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return isset($idPago['last_insert_id']) ? (int) $idPago['last_insert_id'] : 0;
        } catch (PDOException $error) {
            error_log("Error al agregar pago: " . $error->getMessage());
            return -1;
        }
    }

    /**
     * Verifica y actualiza el estado del cronograma si está completamente pagado
     * 
     * Método protegido que calcula los totales amortizados de cuota y penalidad,
     * los compara con los montos necesarios y actualiza el estado del cronograma
     * a 'Pagado' si ambos conceptos están completamente cubiertos.
     * 
     * Validacion: 
     * - Suma total pagado por tipo (Cuota/Penalidad)
     * - Obtiene valores requeridos del cronograma
     * - Verifica: cuota pagada >= cuota necesaria
     * - Verifica: penalidad pagada >= penalidad necesaria (o penalidad = 0)
     * - Actualiza a 'Pagado' solo si ambas condiciones se cumplen
     * 
     * @param int $idCronograma ID del cronograma a validar
     * @return void
     */
    protected function checkCuotaPagada(int $idCronograma): void
    {
        $queryAmortizado = "SELECT tipo, COALESCE(SUM(amortizacion), 0) as total_amortizado FROM pagos WHERE idcronograma = :idcronograma GROUP BY tipo";
        $stmtAmortizado = $this->db->prepare($queryAmortizado);
        $stmtAmortizado->execute([':idcronograma' => $idCronograma]);
        $pagosAmortizados = $stmtAmortizado->fetchAll(PDO::FETCH_KEY_PAIR);

        $totalAmortizadoCuota = (float) ($pagosAmortizados['Cuota'] ?? 0);
        $totalAmortizadoPenalidad = (float) ($pagosAmortizados['Penalidad'] ?? 0);

        $queryCronograma = "SELECT coti.valorcuota, cro.penalidad FROM cronogramas cro JOIN contratos cont ON cro.idcontrato = cont.idcontrato JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion WHERE cro.idcronograma = :idcronograma";
        $stmtCronograma = $this->db->prepare($queryCronograma);
        $stmtCronograma->execute([':idcronograma' => $idCronograma]);
        $cronograma = $stmtCronograma->fetch(PDO::FETCH_ASSOC);

        $cuotaNecesaria = (float) $cronograma['valorcuota'];
        $penalidadNecesaria = (float) $cronograma['penalidad'];

        $pagadoCuota = $totalAmortizadoCuota >= $cuotaNecesaria;
        $pagadoPenalidad = $penalidadNecesaria <= 0 || $totalAmortizadoPenalidad >= $penalidadNecesaria;

        if ($pagadoCuota && $pagadoPenalidad) {
            $queryUpdate = "UPDATE cronogramas SET estado = 'Pagado' WHERE idcronograma = :idcronograma";
            $stmtUpdate = $this->db->prepare($queryUpdate);
            $stmtUpdate->execute([':idcronograma' => $idCronograma]);
        }
    }

    /**
     * Obtiene los datos de un cronograma con información de pagos pendientes
     * 
     * Retorna información detallada de un cronograma incluyendo el cálculo
     * de cuota pendiente y penalidad pendiente, considerando los pagos
     * ya realizados agrupados por tipo.
     * 
     * @param int $idCronograma ID del cronograma a consultar
     * @return array|false Array asociativo con los datos del cronograma:
     *                     - idcronograma: ID del cronograma
     *                     - idcontrato: ID del contrato asociado
     *                     - penalidad: Monto total de penalidad
     *                     - cuotapendiente: Monto pendiente de la cuota
     *                     - penalidadpendiente: Monto pendiente de penalidad
     *                     - estado: Estado actual del cronograma
     *                     - numcuota: Número de cuota
     *                     o false si no existe
     */
    public function getCronogramaData(int $idCronograma): array|false
    {
        $query = "SELECT 
                    cro.idcronograma,
                    cont.idcontrato,
                    cro.penalidad,
                    coti.valorcuota - COALESCE((
                                SELECT SUM(pag.amortizacion)
                                FROM pagos pag
                                WHERE pag.idcronograma = cro.idcronograma
                                AND pag.tipo = 'Cuota'
                            ), 0) AS cuotapendiente,

                    cro.penalidad - COALESCE((
                                SELECT SUM(pag.amortizacion)
                                FROM pagos pag
                                WHERE pag.idcronograma = cro.idcronograma
                                AND pag.tipo = 'Penalidad'
                            ), 0) AS penalidadpendiente,
                            cro.penalidad,
                    cro.estado, 
                    cro.numcuota
                    FROM cronogramas cro 
                    JOIN contratos cont ON cro.idcontrato = cont.idcontrato 
                    JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion 
                    WHERE cro.idcronograma = :idcronograma;";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idcronograma', $idCronograma);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las cuentas de pago disponibles
     * 
     * Retorna un listado de cuentas bancarias disponibles para registrar pagos,
     * con formato concatenado que incluye entidad, número de cuenta y moneda.
     * 
     * @return array Array asociativo con las cuentas de pago:
     *                    - idcuentapago: ID de la cuenta
     *                    - nombrecuenta: Formato "Entidad - NumCuenta - Moneda"
     *                    o array vacío en caso de error
     */
    public function getNumCuentasPagos(): ?array
    {
        $query = " SELECT 
                        cp.idcuentapago,
                        CONCAT(ep.entidad, ' - ', cp.numcuenta, ' - ' , cp.moneda) AS nombrecuenta
                    FROM 
                        cuentaspago cp
                    JOIN 
                        entidadespago ep ON cp.identidadpago = ep.identidadpago;
                    ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    // Ayudará a traer el numero de cuenta donde se paga por tranasferencia.

    public function getNumCuentaPagoById(int $id): ?array

    {
        $query = " SELECT 
                        cp.idcuentapago,
                        CONCAT(ep.entidad, ' - ', cp.numcuenta) AS nombrecuenta
                    FROM 
                        cuentaspago cp
                    JOIN 
                        entidadespago ep ON cp.identidadpago = ep.identidadpago

                        WHERE cp.idcuentapago = :id
                    ";
        try {

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el historial completo de pagos de un contrato
     * 
     * Ejecuta un procedimiento almacenado que retorna todos los pagos
     * realizados para un contrato específico, incluyendo cuotas y penalidades.
     * 
     * @param int $id ID del contrato a consultar
     * @return array Array asociativo con el historial de pagos
     *               o array vacío en caso de error
     */
    public function getHistorialPagosByContrato(int $id): array
    {

        $query = "CALL sp_get_pagos_by_contrato(:idcontrato)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idcontrato' => $id));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }
}
