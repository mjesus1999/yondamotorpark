<?php

/**
 * Modelo de Egreso
 * 
 * app/Models/Egreso.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los egresos
 * del sistema (salidas de dinero). Controla el registro de gastos operativos,
 * compras, pagos a proveedores y otros desembolsos, incluyendo validación
 * de comprobantes (facturas, boletas), carga de documentos digitalizados,
 * y generación de reportes financieros. Implementa flujo completo:
 * solicitud → aprobación → comprobante → validación contable.
 */
namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

/**
 * Clase Egreso
 * 
 * Modelo para la gestión de egresos del sistema.
 * Proporciona métodos para CRUD de egresos, gestión de comprobantes
 * asociados (facturas, boletas), validación contable, consulta de
 * catálogos relacionados (conceptos, colaboradores, proveedores),
 * y generación de reportes financieros por periodo. Cada egreso
 * puede requerir o no comprobante según el concepto y política
 * de la empresa.
 */
class Egreso
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
     * Obtiene egresos filtrados por estado
     * 
     * Ejecuta un procedimiento almacenado que retorna los egresos
     * según su estado actual (pendiente, aprobado, rechazado, etc.).
     * Permite segmentar la vista de egresos para diferentes roles:
     * solicitantes ven pendientes, contabilidad ve aprobados, etc.
     * 
     * @param string $estado Estado del egreso a filtrar
     * @return array Array asociativo con los egresos filtrados.
     *               Estructura determinada por sp_egresos_por_estado.
     *               Retorna array vacío en caso de error
     */
    public function getAllEgresosByEstado(string $estado): array
    {
        try {
            $stmt = $this->db->prepare("CALL sp_egresos_por_estado(:estado)");
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener los egresos por estado: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el catálogo de conceptos de egreso
     * 
     * Retorna la lista completa de conceptos de egreso disponibles
     * (combustible, viáticos, servicios públicos, compras, etc.).
     * Cada concepto incluye su descripción detallada para facilitar
     * la selección correcta al registrar un egreso.
     * Ordenado alfabéticamente por descripción.
     * 
     * @return array Array asociativo con los conceptos. Cada elemento contiene:
     *               - idconceptoegreso: int (Identificador único del concepto)
     *               - concepto: string (Nombre corto del concepto)
     *               - descripcion: string (Descripción detallada del concepto)
     *               Ordenado alfabéticamente por descripción.
     *               Retorna array vacío en caso de error
     * @throws Exception Si ocurre un error en la consulta
     */
    public function getConceptosEgreso(): array
    {
        try {
            $stmt = $this->db->prepare("SELECT idconceptoegreso,concepto, descripcion FROM conceptoegreso ORDER BY descripcion");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener los conceptos de egreso: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene la lista de colaboradores activos
     * 
     * Retorna todos los colaboradores con contratos laborales vigentes,
     * excluyendo practicantes. Incluye información de cargo y área
     * para identificación completa. Utilizado para seleccionar
     * solicitantes de egresos o responsables de aprobación.
     * 
     * @return array Array asociativo con los colaboradores. Cada elemento contiene:
     *               - idcolaborador: int (ID del colaborador)
     *               - idpersona: int (ID de la persona asociada)
     *               - colaborador: string (Nombre completo: "Apellidos Nombres")
     *               - cargo: string (Cargo del colaborador)
     *               - area: string (Área a la que pertenece)
     *               Retorna array vacío en caso de error
     */
    public function getColaboradores(): array
    {
        $query = "
                SELECT
                col.idcolaborador,
                per.idpersona, CONCAT(per.apellidos, ' ', per.nombres) AS colaborador,
                car.cargo,
                ar.area
                    FROM
                        colaboradores col
                    JOIN
                        contratoslaborales cl ON cl.idcontratolaboral = col.idcontratolaboral
                    JOIN
                        personas per ON per.idpersona = cl.idpersona
                    JOIN
                        cargos car ON car.idcargo = cl.idcargo
                    JOIN
                        areas ar ON ar.idarea = car.idarea
                    WHERE
                        car.cargo != 'Practicante'
                AND (cl.fechafin IS NULL OR cl.fechafin > CURDATE());";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Error al obtener los colaboradores: " . $error->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el catálogo de proveedores
     * 
     * Retorna la lista completa de proveedores registrados en el sistema,
     * ordenados alfabéticamente por razón social. Incluye nombre comercial
     * y RUC para identificación fiscal. Utilizado principalmente en el
     * registro de comprobantes de egreso.
     * 
     * @return array Array asociativo con los proveedores. Cada elemento contiene:
     *               - idproovedor: int (Identificador único del proveedor)
     *               - nombrecomercial: string (Nombre comercial del proveedor)
     *               - ruc: string (RUC del proveedor, 11 dígitos)
     *               Ordenado alfabéticamente por razón social.
     *               Retorna array vacío en caso de error
     */
    public function getProovedores(): array
    {
        $query = "SELECT idproovedor, nombrecomercial, ruc FROM proovedores ORDER BY razonsocial";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log("Error al obtener los proovedores: " . $error->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el detalle de un egreso con su comprobante
     * 
     * Retorna información completa de un egreso que requiere comprobante,
     * incluyendo datos del comprobante asociado (factura/boleta), proveedor,
     * y ruta del archivo digitalizado. Utilizado para visualizar y validar
     * comprobantes en el módulo de contabilidad.
     * 
     * @param int $id ID del egreso a consultar
     */
    public function getDetalleEgresoById(int $id)
    {
        $query = "
                SELECT
                    c.idcomprobante,
                    c.idegreso,
                    c.idproovedor,
                    c.tipodoc,
                    c.serie,
                    c.numdocumento,
                    c.monto AS montocomprobante,
                    c.cargadocontabilidad,
                    c.rutacomprobante,
                    e.monto AS montoegreso,
                    CONCAT(p.nombrecomercial, ' - ', p.ruc ) AS proovedor
                    FROM comprobantes c
                    JOIN egresos e ON c.idegreso = e.idegreso
                    JOIN proovedores p ON c.idproovedor = p.idproovedor
                    WHERE c.idegreso = $id AND e.requierecomprobante = 'S';
                        
        
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener el detalle del egreso: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene egresos con comprobantes validados contablemente
     * 
     * Retorna el listado de egresos que tienen comprobantes cargados
     * y ya han sido validados por el área de contabilidad
     * (cargadocontabilidad = 'S'). Incluye información completa del
     * comprobante, proveedor, solicitante y fechas. Ordenado por
     * fecha de validación descendente
     * 
     * @return array Array asociativo con los egresos validados. Cada elemento contiene:
     *               - idegreso: int (ID del egreso)
     *               - idcomprobante: int (ID del comprobante)
     *               - tipodoc: string (Tipo de comprobante)
     *               - fecha: string (Fecha creación formato DD/MM/YYYY)
     *               - concepto: string (Concepto del egreso)
     *               - proovedor: string (Razón social del proveedor)
     *               - solicitante: string (Nombre completo del solicitante)
     *               - monto: decimal (Monto del egreso)
     *               - comentario: string (Observaciones del egreso)
     *               - numdocumento: string (Número del comprobante)
     *               - monto_validado: decimal (Monto del comprobante validado)
     *               - rutacomprobante: string (Ruta del archivo)
     *               - modificado: string (Fecha validación formato DD-MM-YYYY HH:MM)
     *               Ordenado por fecha de validación descendente.
     *               Retorna array vacío en caso de error
     */
    public function getEgresoWithComprobantesValidados(): array
    {
        $query = "SELECT 
                        eg.idegreso,
                        cm.idcomprobante,
                        cm.tipodoc,
                        DATE_FORMAT(eg.creado, '%d/%m/%Y') AS fecha,
                        ce.concepto,
                        CONCAT(pro.razonsocial ) AS proovedor,
                        CONCAT(p.apellidos,' ',p.nombres) AS solicitante,
                        eg.monto,
                        eg.comentario,
                        cm.numdocumento,
                        cm.monto AS monto_validado,
                        cm.rutacomprobante,
                        DATE_FORMAT(cm.modificado, '%d-%m-%Y %H:%i') AS modificado
                    FROM egresos eg
                    JOIN conceptoegreso ce ON eg.idconceptoegreso = ce.idconceptoegreso
                    JOIN colaboradores c ON eg.idcolsolicitante = c.idcolaborador
                    JOIN contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
                    JOIN personas p ON cl.idpersona = p.idpersona
                    JOIN comprobantes cm ON eg.idegreso = cm.idegreso
                    JOIN proovedores pro ON pro.idproovedor = cm.idproovedor
                    WHERE eg.requierecomprobante = 'S' AND cm.cargadocontabilidad = 'S'
                    ORDER BY cm.modificado DESC;";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Registra un nuevo egreso
     * 
     * Crea un egreso mediante procedimiento almacenado que valida
     * permisos, presupuesto disponible y reglas de negocio.
     * El ID del colaborador de caja se obtiene automáticamente de
     * la sesión del usuario autenticado.
     * 
     * @param array $params Array asociativo con los datos del egreso:
     *                      - idconceptoegreso: int (ID del concepto)
     *                      - idsolicitante: int (ID del colaborador solicitante)
     *                      - monto: decimal (Monto del egreso)
     *                      - comentario: string (Observaciones/justificación)
     *                      - requierecomprobante: string (S: requiere, N: no requiere)
     * @return int ID del egreso creado, o 0 en caso de error
     */
    public function add($params = []): int
    {

        $query = "CALL sp_egresos_add(:idconceptoegreso, :idcolacaja, :idsolicitante, :monto, :comentario, :requierecomprobante)";
        try {
            $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            // error_log("User ID in Egreso add: " . var_export($idUsuario, true));
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idconceptoegreso' => $params['idconceptoegreso'],
                ':idcolacaja' => $idUsuario,
                ':idsolicitante' => $params['idsolicitante'],
                ':monto' => $params['monto'],
                ':comentario' => $params['comentario'],
                ':requierecomprobante' => $params['requierecomprobante']
            ));

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['last_insert_id'] ?? 0;
        } catch (Exception $e) {
            error_log("Error al agregar el egreso: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Registra un comprobante asociado a un egreso
     * 
     * Crea un registro de comprobante (factura, boleta, nota) para
     * sustentar un egreso previamente registrado. Ejecuta procedimiento
     * almacenado que valida coherencia entre monto del egreso y
     * monto del comprobante.
     * 
     * @param mixed $params Array asociativo con los datos del comprobante:
     *                      - idegreso: int (ID del egreso a sustentar)
     *                      - idproovedor: int (ID del proveedor emisor)
     *                      - tipodoc: string (Factura/Boleta/Nota)
     *                      - serie: string (Serie del comprobante: F001, B001)
     *                      - numdocumento: string (Número del comprobante)
     *                      - monto: decimal (Monto del comprobante)
     *                      - rutacomprobante: string (Ruta del archivo digitalizado)
     * @return int ID del comprobante creado, o 0 en caso de error
     */
    public function addComprobante($params = []): int
    {
        $query = "CALL sp_egresos_add_comprobante(:idegreso,:idproovedor, :tipodoc,:serie, :numdocumento, :monto,:rutacomprobante)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idegreso' => $params['idegreso'],
                ':idproovedor' => $params['idproovedor'],
                ':tipodoc' => $params['tipodoc'],
                ':serie' => $params['serie'],
                ':numdocumento' => $params['numdocumento'],
                ':monto' => $params['monto'],
                ':rutacomprobante' => $params['rutacomprobante']
            ));

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['last_insert_id'] ?? 0;
        } catch (Exception $e) {
            error_log("Error al agregar el comprobante: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Valida un comprobante contablemente
     * 
     * Marca un comprobante como validado por el área de contabilidad,
     * actualizando el campo cargadocontabilidad a 'S' y registrando
     * la fecha/hora de validación. Este proceso confirma que el
     * comprobante cumple con requisitos fiscales y contables.
     * 
     * @param int $idComprobante ID del comprobante a validar
     * @return bool true si la validación fue exitosa, false en caso de error
     */
    public function validarComprobante(int $idComprobante): bool
    {
        $query = "UPDATE comprobantes SET cargadocontabilidad = 'S', modificado = NOW() WHERE idcomprobante = :idcomprobante";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idcomprobante', $idComprobante, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            return true;
        } catch (Exception $e) {
            error_log("Error al validar el comprobante: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un egreso
     * 
     * Borra un egreso de la base de datos. La eliminación en cascada
     * puede eliminar también comprobantes asociados según configuración
     * de restricciones de integridad referencial.
     * 
     * @param int $id ID del egreso a eliminar
     * @return bool true si la eliminación fue exitosa, false en caso de error
     */
    public function deleteEgreso($id): bool
    {
        $query = "DELETE FROM egresos WHERE idegreso = :idegreso";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idegreso', $id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            return true;
        } catch (Exception $e) {
            error_log("Error al eliminar el egreso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Genera reporte completo de egresos por periodo
     * 
     * Ejecuta un procedimiento almacenado que retorna múltiples conjuntos
     * de resultados (result sets) con diferentes vistas del reporte:
     * resumen ejecutivo, detalle por concepto, detalle por colaborador,
     * egresos pendientes de comprobante, entre otros.
     * 
     * @param mixed $fechaInicio Fecha inicio del periodo
     * @param mixed $fechaFin Fecha fin del periodo
     * @return array[] Array multidimensional con todos los result sets,
     *               o array vacío en caso de error
     */
    public function getReporteByFecha($fechaInicio, $fechaFin): array
    {
        $query = "CALL sp_obtener_reporte_egresos_completo(:fechainicio, :fechafin);";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fechainicio', $fechaInicio);
            $stmt->bindParam(':fechafin', $fechaFin);
            $stmt->execute();

            $allResults = [];
            $resultCount = 0;

            do {
                // Siempre se guarda el resultado en el índice actual, incluso si es un array vacío
                $allResults[$resultCount] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $resultCount++;
            } while ($stmt->nextRowset());


            $stmt->closeCursor();

            return $allResults;
        } catch (PDOException $error) {
            error_log("Error al llamar al procedimiento almacenado: " . $error->getMessage());
            return [];
        }
    }
}
