<?php

/**
 * Modelo de Orden de Compra
 * 
 * app/Models/OrdenCompra.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las órdenes
 * de compra de vehículos, incluyendo creación, seguimiento de estados,
 * recepción de mercadería, pagos y generación de reportes.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase OrdenCompra
 * 
 * Modelo para la gestión de órdenes de compra de vehículos.
 * Proporciona métodos para CRUD, control de estados (emitido, proceso,
 * recepcionado, anulado), validación de recepción correcta y generación
 * de reportes ejecutivos.
 */
class OrdenCompra
{
    /**
     * Instancia de conexion a la base de datos
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
     * Obtiene órdenes de compra por estado
     * 
     * Ejecuta un procedimiento almacenado que retorna las órdenes de compra
     * filtradas por su estado actual (emitido, proceso, recepcionado, anulado).
     * 
     * @param string $estado Estado de la orden a filtrar (por defecto: 'emitido')
     * @return array Array asociativo con las órdenes de compra
     *                    o array vacío en caso de error
     */
    public function getByEstado(string $estado = 'emitido'): ?array
    {
        $query = "CALL sp_oc_por_estado(:estado)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene información del concesionario por ID de orden
     * 
     * Retorna datos del concesionario asociado a una orden de compra,
     * incluyendo identificador formateado de OC y ubicación completa
     * (departamento/provincia/distrito).
     * 
     * @param int $idorden ID de la orden de compra
     */
    public function obtenerConcesionarioById(int $idorden): ?array
    {
        $query = "SELECT 
                oc.idordencompra,
                c.nombrecomercial AS concesionario,
                fn_format_oc_display_id(oc.idordencompra, oc.serie) AS numeroOCIdentificador,
                CONCAT(dep.departamento, ' / ', p.provincia, ' / ', d.distrito) AS ubicacion
            FROM ordenescompra oc
            INNER JOIN tiendas t ON oc.idtienda = t.idtienda
            INNER JOIN concesionarios c ON t.idconcesionario = c.idconcesionario
            INNER JOIN distritos d ON t.iddistrito = d.iddistrito
            INNER JOIN provincias p ON d.idprovincia = p.idprovincia
            INNER JOIN departamentos dep ON p.iddepartamento = dep.iddepartamento
            WHERE oc.idordencompra = :idorden";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idorden' => $idorden]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            // error_log(print_r($data, true) . '|');
            return $data ?: null;
        } catch (PDOException $error) {
            error_log('Error en obtenerPorId OrdenCompra: ' . $error->getMessage());
            return null;
        }
    }

    /**
     * Obtiene el detalle de una orden de compra
     * 
     * Ejecuta un procedimiento almacenado que retorna todos los ítems
     * (vehículos) asociados a una orden de compra específica.
     * 
     * @param mixed $idOC ID de la orden de compra
     * @return array Array asociativo con el detalle de la OC
     *               o array vacío en caso de error
     */
    public function getDetOCByIdOC($idOC): ?array
    {
        $query = 'CALL sp_detOC_By_IdOC(:idOC)';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idOC' => $idOC));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    // TRAERA LOS DATOS DE LOS VEHICULOS A ACTUALIZAR EN LA TABLA DETALLE_OC, VERIFICAR SI HAN LLEGADO DE MANERA CORRECTA

    /**
     * Obtiene información de vehículos para verificación de recepción
     * 
     * Ejecuta un procedimiento almacenado que retorna los datos de los vehículos
     * de una OC con su estado de verificación (escorrecto) para el proceso
     * de recepción de mercadería.
     * 
     * @param int $idOC
     * @return array Array asociativo con información de los vehículos
     *                    y su estado de verificación, o array vacío en caso de error
     */
    public function getInfoAutosOC($idOC): ?array
    {
        $query = 'CALL sp_det_oc_escorrecto(:idOC)';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idOC' => $idOC));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // error_log(print_r($results, true) . '|');
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Actualiza el estado de verificación del detalle de OC
     * 
     * Marca si los vehículos de una orden de compra fueron recepcionados
     * correctamente mediante el campo 'escorrecto' en la tabla detalle.
     * 
     * @param array $params Array asociativo con:
     *                      - escorrecto: string ('S' si es correcto, 'N' si no)
     *                      - idordencompra: int (ID de la orden de compra)
     * @return int Número de filas afectadas o -1 en caso de error
     */
    public function updateEscorrectoDetOC($params = []): int
    {
        $query = 'CALL sp_check_recepcion_OC(:escorrecto,:idordencompra)';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':escorrecto' => $params['escorrecto'],
                ':idordencompra' => $params['idordencompra']
            ));

            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Actualiza el estado de una orden de compra
     * 
     * Modifica el estado de una OC (emitido, proceso, recepcionado, anulado).
     * Si el estado es 'anulado', ejecuta un procedimiento almacenado especial
     * que realiza acciones adicionales de anulación.
     * 
     * Estados válidos:
     * - emitido: Orden creada
     * - proceso: En proceso de recepción
     * - recepcionado: Mercadería recibida completamente
     * - anulado: Orden cancelada
     * 
     * @param array $params Array asociativo con:
     *                      - estado: string (Nuevo estado de la orden)
     *                      - observaciones: string (Observaciones del cambio de estado)
     *                      - idordencompra: int (ID de la orden de compra)
     * @return int Número de filas afectadas o -1 en caso de error
     */
    public function updateEstado($params = []): int
    {
        try {
            if ($params['estado'] == 'anulado') {
                $stmt = $this->db->prepare("CALL sp_anular_OC(:estado, :observaciones, :idordencompra)");
                $stmt->execute([
                    ':estado' => $params['estado'],
                    ':observaciones' => $params['observaciones'],
                    ':idordencompra' => $params['idordencompra']
                ]);
                return $stmt->rowCount();
            } else {
                $query = 'UPDATE ordenescompra 
                      SET estado = :estado, observaciones = :observaciones 
                      WHERE idordencompra = :idordencompra;';
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':estado' => $params['estado'],
                    ':observaciones' => $params['observaciones'],
                    ':idordencompra' => $params['idordencompra']
                ]);
                return (int) $stmt->rowCount();
            }
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Crea una nueva orden de compra
     * 
     * Registra una nueva orden de compra mediante procedimiento almacenado.
     * El ID del colaborador de logística se obtiene automáticamente de la sesión.
     * 
     * @param array $params Array asociativo con los datos de la OC:
     *                      - idtienda: int (ID de la tienda/concesionario)
     *                      - moneda: string (Moneda de la orden: USD/PEN)
     *                      - serie: string (Serie de la orden)
     *                      - numstock: int (Número de stock)
     *                      - observaciones: string (Observaciones iniciales)
     * @return int ID de la orden creada, 0 si no se pudo obtener el ID,
     *             o -1 en caso de error
     */
    public function create($params = []): int
    {
        $query = 'call spu_oc_registrar(:idtienda,:idlogistica,:moneda,:serie,:numstock,:observaciones)';

        try {
            $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idtienda' => $params['idtienda'],
                ':idlogistica' => $idUsuario,
                ':moneda' => $params['moneda'],
                ':serie' => $params['serie'],
                ':numstock' => $params['numstock'],
                ':observaciones' => $params['observaciones']
            ));

            $idOrdenCompra = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return isset($idOrdenCompra['last_id']) ? (int) $idOrdenCompra['last_id'] : 0;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Genera reporte general de órdenes de compra en proceso
     * 
     * Ejecuta un procedimiento almacenado que retorna un reporte ejecutivo
     * con dos conjuntos de datos: resumen ejecutivo (totales, promedios)
     * y detalle de órdenes en proceso.
     * 
     * @return array {detalleOrdenes: array, resumenEjecutivo: mixed|null} 
     */
    public function getReporteOCProceso(): ?array
    {
        $query = "CALL sp_reporte_general_oc_proceso()";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $resumenEjecutivo = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->nextRowset();
            $detalleOrdenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log(print_r($resumenEjecutivo, true));
            error_log(print_r($detalleOrdenes, true));
            return [
                'resumenEjecutivo' => $resumenEjecutivo,
                'detalleOrdenes' => $detalleOrdenes
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
    
}

//  $orden = new OrdenCompra();

//  $orden->getReporteOCProceso();

// var_dump($orden->getAll());
