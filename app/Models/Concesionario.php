<?php

/**
 * Modelo de Concesionario
 * 
 * Gestiona las operaciones de base de datos relacionadas con los concesionarios
 * proveedores de vehículos, incluyendo registro, consulta, actualización y
 * eliminación. Maneja validaciones para evitar eliminación de concesionarios
 * con órdenes de compra asociadas, consultas de concesionarios con procesos
 * activos, y generación de reportes detallados con información ejecutiva,
 * pagos y vehículos.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Concesionario
 * 
 * Modelo que representa y gestiona los concesionarios en el sistema.
 * Un concesionario es una empresa proveedora de vehículos que mantiene
 * relación comercial con el sistema mediante órdenes de compra.
 */
class Concesionario
{

    /**
     * Instancia de conexión a la base de datos
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor del modelo
     * 
     * Inicializa la conexión a la base de datos mediante el patrón Singleton,
     * garantizando una única instancia de conexión compartida.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene el listado completo de concesionarios
     * 
     * Recupera todos los concesionarios registrados en el sistema ordenados
     * por fecha de creación descendente (más recientes primero).
     * 
     * @return array Array de concesionarios con estructura:
     *   - idconcesionario (int): ID del concesionario
     *   - ruc (string): RUC del concesionario
     *   - razonsocial (string): Razón social registrada
     *   - nombrecomercial (string): Nombre comercial
     *   Retorna array vacío en caso de error
     */
    public function getAll(): ?array
    {
        $query = 'SELECT idconcesionario, ruc, razonsocial, nombrecomercial FROM concesionarios ORDER BY creado DESC';
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

    /**
     * Busca un concesionario por su RUC
     * 
     * Realiza búsqueda exacta de concesionario utilizando el número de RUC.
     * Útil para validar existencia antes de crear duplicados o para
     * autocompletar datos en formularios.
     * 
     * @param string $ruc Número de RUC del concesionario a buscar
     * @return array Array con datos del concesionario si existe:
     *   - idconcesionario (int): ID del concesionario
     *   - ruc (string): RUC del concesionario
     *   - razonsocial (string): Razón social
     *   - nombrecomercial (string): Nombre comercial
     *   Retorna array vacío si no se encuentra o hay error
     */
    public function getConcesionarioByRUC($ruc = ''): ?array
    {
        $query = 'SELECT idconcesionario, ruc, razonsocial, nombrecomercial FROM concesionarios WHERE ruc = ?';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($ruc));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Registra un nuevo concesionario en el sistema
     * 
     * Crea un nuevo registro de concesionario con sus datos básicos.
     * El RUC debe ser único en el sistema. La razón social y nombre
     * comercial son obligatorios.
     *
     * @param array $params Datos del concesionario
     *   - ruc (string, requerido): RUC del concesionario (11 dígitos)
     *   - razonsocial (string, requerido): Razón social oficial
     *   - nombrecomercial (string, requerido): Nombre comercial
     * @return int ID del concesionario creado, -1 en caso de error
     */
    public function create($params = []): int
    {
        $query = 'INSERT INTO concesionarios (ruc, razonsocial, nombrecomercial) VALUES (:ruc,:razonsocial,:nombrecomercial)';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(
                array(
                    ':ruc' => $params['ruc'],
                    ':razonsocial' => $params['razonsocial'],
                    ':nombrecomercial' => $params['nombrecomercial']
                )
            );
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Actualiza el nombre comercial de un concesionario
     * 
     * Modifica únicamente el nombre comercial del concesionario y actualiza
     * la fecha de modificación. No permite cambiar RUC ni razón social por
     * ser datos tributarios que no deben modificarse.
     *
     * @param array $params Datos para actualización
     *   - idconcesionario (int, requerido): ID del concesionario
     *   - nombrecomercial (string, requerido): Nuevo nombre comercial
     * @return int Número de filas afectadas (1 si exitoso, 0 si no existe), -1 en caso de error
     */
    public function update($params): int
    {
        try {
            $query = 'UPDATE concesionarios SET nombrecomercial = :nombrecomercial, modificado = NOW() WHERE idconcesionario = :idconcesionario';
            $stmt = $this->db->prepare($query);
            $stmt->execute(
                array(
                    ':nombrecomercial' => $params['nombrecomercial'],
                    ':idconcesionario' => $params['idconcesionario']
                )
            );
            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Verifica si un concesionario tiene órdenes de compra registradas
     * 
     * Ejecuta procedimiento almacenado que cuenta el número de órdenes de compra
     * asociadas al concesionario. Usado para validar si es seguro eliminar
     * el registro sin perder integridad referencial.
     * 
     * Utiliza stored procedure: spu_concesionarios_obtener_oc
     *
     * @param int $idconcesionario ID del concesionario a verificar (default: -1)
     * @return int Número de órdenes de compra registradas:
     *      - 0: No tiene órdenes de compra (se puede eliminar)
     *      - >0: Tiene órdenes de compra (NO se puede eliminar)
     *      - -1: Error en la consulta
     */
    public function getOC($idconcesionario = -1): int
    {
        try {
            $stmt = $this->db->prepare('call spu_concesionarios_obtener_oc(?,@registros)');
            $stmt->execute(
                array($idconcesionario)
            );
            $response = $this->db->query('SELECT @registros AS registros')->fetch(PDO::FETCH_ASSOC);
            return (int) $response['registros'];
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Elimina un concesionario y todos sus registros relacionados
     * 
     * Ejecuta procedimiento almacenado que elimina el concesionario y opcionalmente
     * sus registros dependientes (tiendas, contactos, etc.) según la lógica
     * implementada en el stored procedure.
     *
     * @param int $idconcesionario ID del concesionario a eliminar (default: -1)
     * @return int Número de filas afectadas, -1 en caso de error
     */
    public function delete($idconcesionario = -1): int
    {
        try {
            $stmt = $this->db->prepare('call spu_concesionarios_eliminar_todo(?)');
            $stmt->execute(array($idconcesionario));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Obtiene concesionarios con órdenes de compra en proceso
     * 
     * Consulta que retorna únicamente los concesionarios que tienen al menos
     * una orden de compra en estado "proceso". Útil para filtrar proveedores
     * con operaciones activas o pendientes.
     *
     * @return array Array de concesionarios con estructura:
     *   - idconcesionario (int): ID del concesionario
     *   - ruc (string): RUC del concesionario
     *   - razonsocial (string): Razón social
     *   - nombrecomercial (string): Nombre comercial
     *   Ordenado alfabéticamente por razón social
     *   Retorna array vacío si no hay concesionarios con OC en proceso o hay error
     */
    public function getConcesionariosWhitOCProceso(): array
    {
        try {
            $query = 'SELECT DISTINCT c.idconcesionario, c.ruc, c.razonsocial, c.nombrecomercial 
                      FROM tiendas ti
                      JOIN ordenescompra oc ON ti.idtienda = oc.idtienda
                      JOIN concesionarios c ON ti.idconcesionario = c.idconcesionario
                      WHERE oc.estado = "proceso"
                      ORDER BY c.razonsocial ASC;';
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Genera reporte detallado completo de un concesionario
     * 
     * Ejecuta procedimiento almacenado que retorna tres conjuntos de resultados
     * diferentes para un reporte completo del concesionario:
     * 
     * 1. Resumen Ejecutivo: Información agregada (totales, estadísticas generales)
     * 2. Detalle de Pagos: Historial de pagos realizados al concesionario
     * 3. Detalle de Vehículos: Inventario de vehículos comprados al concesionario
     * 
     * Utiliza stored procedure: sp_reporte_concesionario_detallado
     *
     * @param int $id ID del concesionario para el reporte
     * @return array {detallePagos: array, detalleVehiculos: array, resumenEjecutivo: mixed|null}
     */
    public function getReporteConcesionarioDetallado(int $id): ?array
    {
        // Asegúrate de que el nombre de tu procedimiento sea correcto y que espera un parámetro.
        $query = "CALL sp_reporte_concesionario_detallado(?)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            // 1. Obtener el Resumen Ejecutivo (primer conjunto de resultados)
            $resumenEjecutivo = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Avanzar al siguiente conjunto para obtener el Detalle de Pagos
            $stmt->nextRowset();
            $detallePagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Avanzar al siguiente conjunto para obtener el Detalle de Vehículos
            $stmt->nextRowset();
            $detalleVehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Opcional: Para depuración, puedes seguir usando error_log
            // error_log(print_r($resumenEjecutivo, true));
            // error_log(print_r($detallePagos, true));
            // error_log(print_r($detalleVehiculos, true));

            // Cierra el cursor para liberar la conexión a la base de datos
            $stmt->closeCursor();

            // Retornar los tres conjuntos de resultados en un solo array
            return [
                'resumenEjecutivo' => $resumenEjecutivo,
                'detallePagos' => $detallePagos,
                'detalleVehiculos' => $detalleVehiculos
            ];
        } catch (PDOException $e) {
            error_log("Error en el procedimiento almacenado: " . $e->getMessage());
            return null;
        }
    }

}
