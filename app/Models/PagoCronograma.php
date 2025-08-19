<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class PagoCronograma
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Registra múltiples pagos (cuota y penalidad) en una transacción y actualiza el estado del cronograma.
     *
     * @param array $pagoCuota
     * @param array|null $pagoPenalidad
     * @return array
     */

    public function addMultiplePagos(array $pagoCuota, ?array $pagoPenalidad = null): array
    {
        try {
            $this->db->beginTransaction();
            $ids = [];

            // Procesar el pago de la cuota si el monto es mayor que 0
            if ($pagoCuota['amortizacion'] > 0) {
                $idPagoCuota = $this->add($pagoCuota);
                if ($idPagoCuota <= 0) {
                    $this->db->rollBack();
                    return [];
                }
                $ids[] = $idPagoCuota;
            }

            // Procesar el pago de la penalidad si existe y el monto es mayor que 0
            if ($pagoPenalidad && $pagoPenalidad['amortizacion'] > 0) {
                $idPagoPenalidad = $this->add($pagoPenalidad);
                if ($idPagoPenalidad <= 0) {
                    $this->db->rollBack();
                    return [];
                }
                $ids[] = $idPagoPenalidad;
            }

            // Actualizar el estado del cronograma si se han registrado pagos
            if (!empty($ids)) {
                $this->checkCuotaPagada($pagoCuota['idcronograma']);
            }

            $this->db->commit();
            return $ids;
        } catch (PDOException $error) {
            $this->db->rollBack();
            error_log("Error en la transacción de pagos: " . $error->getMessage());
            return [];
        }
    }

    /**
     * Inserta un único pago en la tabla `pagos` usando el SP.
     *
     * @param array $params
     * @return int
     */
    protected function add(array $params): int
    {
        $query = "CALL sp_addPagoCronograma(:idcronograma, :idcuentapago, :idcolcaja, :mediopago, :numerotransaccion, :fechapago, :amortizacion, :comprobante, :observacion, :tipo)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idcronograma'      => $params['idcronograma'],
                ':idcuentapago'      => $params['idcuentapago'],
                ':idcolcaja'         => $params['idcolcaja'],
                ':mediopago'         => $params['mediopago'],
                ':numerotransaccion' => $params['numerotransaccion'],
                ':fechapago'         => $params['fechapago'],
                ':amortizacion'      => $params['amortizacion'],
                ':comprobante'       => $params['comprobante'],
                ':observacion'       => $params['observacion'],
                ':tipo'              => $params['tipo']
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
     * Obtiene el valor de la cuota y penalidad y el total amortizado para verificar si se ha pagado completamente.
     *
     * @param int $idCronograma
     * @return void
     */
    protected function checkCuotaPagada(int $idCronograma): void
    {
        $queryAmortizado = "SELECT tipo, COALESCE(SUM(amortizacion), 0) as total_amortizado FROM pagos WHERE idcronograma = :idcronograma GROUP BY tipo";
        $stmtAmortizado = $this->db->prepare($queryAmortizado);
        $stmtAmortizado->execute([':idcronograma' => $idCronograma]);
        $pagosAmortizados = $stmtAmortizado->fetchAll(PDO::FETCH_KEY_PAIR);

        $totalAmortizadoCuota = (float)($pagosAmortizados['Cuota'] ?? 0);
        $totalAmortizadoPenalidad = (float)($pagosAmortizados['Penalidad'] ?? 0);

        $queryCronograma = "SELECT coti.valorcuota, cro.penalidad FROM cronogramas cro JOIN contratos cont ON cro.idcontrato = cont.idcontrato JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion WHERE cro.idcronograma = :idcronograma";
        $stmtCronograma = $this->db->prepare($queryCronograma);
        $stmtCronograma->execute([':idcronograma' => $idCronograma]);
        $cronograma = $stmtCronograma->fetch(PDO::FETCH_ASSOC);

        $cuotaNecesaria = (float)$cronograma['valorcuota'];
        $penalidadNecesaria = (float)$cronograma['penalidad'];

        $pagadoCuota = $totalAmortizadoCuota >= $cuotaNecesaria;
        $pagadoPenalidad = $penalidadNecesaria <= 0 || $totalAmortizadoPenalidad >= $penalidadNecesaria;

        if ($pagadoCuota && $pagadoPenalidad) {
            $queryUpdate = "UPDATE cronogramas SET estado = 'Pagado' WHERE idcronograma = :idcronograma";
            $stmtUpdate = $this->db->prepare($queryUpdate);
            $stmtUpdate->execute([':idcronograma' => $idCronograma]);
        }
    }


    /**
     * Obtiene los datos de un cronograma para validaciones en el controlador.
     *
     * @param int $idCronograma
     * @return array|false
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



    // METODO PARA TRAER LOS NUEMROS DE CUENTAS PAGOS

    public function getNumCuentasPagos(): ?array
    {
        $query = " SELECT 
                        cp.idcuentapago,
                        CONCAT(ep.entidad, ' - ', cp.numcuenta) AS nombrecuenta
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
