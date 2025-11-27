<?php

/**
 * Modelo de Compra
 * 
 * app/Models/Compra.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las compras
 * de vehículos realizadas a concesionarios mediante órdenes de compra.
 * Incluye registro de compras con documentación fiscal (facturas, boletas),
 * consultas de órdenes de compra por estado, listado de compras con información
 * de concesionarios, y filtros especializados para órdenes en proceso o pagadas.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Compra
 * 
 * Modelo que representa y gestiona las compras en el sistema.
 * Una compra es la materialización de una orden de compra, registrando
 * la transacción efectiva con su documentación fiscal correspondiente
 * (factura o boleta), fecha de compra, fecha de recepción y ruta del
 * documento escaneado.
 * 
 */
class Compra
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
     * Obtiene el listado completo de compras para la vista principal
     * 
     * Recupera todas las compras registradas con información
     * relacionada del concesionario. Los datos se presentan ordenados por
     * fecha de creación descendente
     * 
     * @return array Array de compras con estructura:
     *   - idcompra (int): ID de la compra
     *   - idorden (int): ID de la orden de compra asociada
     *   - fechacompra (string): Fecha de compra formateada (dd-mm-yyyy)
     *   - fecharecepcion (string|null): Fecha de recepción de vehículos
     *   - tipodoc (string): Tipo de documento (Factura/Boleta)
     *   - serie (string): Serie del comprobante
     *   - numdocumento (string): Número del comprobante
     *   - rutadoc (string|null): Ruta del documento escaneado
     *   - razon_concesionario (string): Nombre comercial del concesionario
     *   Retorna array vacío si no hay compras o hay error

     */
    public function getAll(): array
    {
        $query = " SELECT 
            c.idcompra,
            c.idorden,
            DATE_FORMAT(c.fechacompra, '%d-%m-%Y') AS fechacompra,
            c.fecharecepcion,
            c.tipodoc,
            c.serie,
            c.numdocumento,
            c.rutadoc,
            con.razonsocial AS razon_concesionario
            
        FROM compras c
        INNER JOIN ordenescompra o ON c.idorden = o.idordencompra
        INNER JOIN tiendas t ON o.idtienda = t.idtienda
        INNER JOIN concesionarios con ON t.idconcesionario = con.idconcesionario
        ORDER BY c.creado DESC;";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Obtiene concesionarios con órdenes de compra en proceso o pagadas
     * 
     * Consulta que retorna únicamente los concesionarios que tienen al menos
     * una orden de compra en estado "proceso" o "pagado". Estos son los
     * concesionarios con operaciones pendientes que pueden registrarse como
     * compras efectivas.
     *
     * @return array Array de concesionarios con estructura:
     *   - idconcesionario (int): ID del concesionario
     *   - razonsocial (string): Razón social oficial
     *   - nombrecomercial (string): Nombre comercial
     *   Retorna array vacío si no hay concesionarios o hay error
     */
    public function getConcesionariosConOCEnProcesoOPagado(): ?array
    {
        $query = "SELECT DISTINCT c.idconcesionario, c.razonsocial, c.nombrecomercial
                    FROM concesionarios c
                    JOIN tiendas t ON c.idconcesionario = t.idconcesionario
                    JOIN ordenescompra oc ON t.idtienda = oc.idtienda
                    WHERE oc.estado IN ('proceso', 'pagado');
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

    /**
     * Obtiene el detalle de órdenes de compra de un concesionario específico
     * 
     * Ejecuta procedimiento almacenado que retorna información detallada de
     * todas las órdenes de compra de un concesionario que estén en estado
     * "proceso" o "pagado". Incluye información completa de cada orden:
     * número, fecha, montos, estado, vehículos asociados, etc.
     * 
     * Utiliza stored procedure: sp_detalle_oc_por_concesionario
     *
     * @param int $id ID del concesionario
     * @return array Array con detalles de órdenes de compra.
     *   La estructura exacta depende del stored procedure, típicamente incluye:
     *   - idordencompra (int): ID de la orden
     *   - numero_orden (string): Número de orden
     *   - fecha (string): Fecha de creación
     *   - monto_total (decimal): Monto total de la orden
     *   - estado (string): Estado actual
     *   - cantidad_vehiculos (int): Número de vehículos en la orden
     *   - Otros campos definidos en el procedimiento almacenado
     *   Retorna array vacío si no hay órdenes o hay error
     */
    public function getDetOCByConcesionario(int $id): ?array
    {
        $query = "CALL sp_detalle_oc_por_concesionario(:idconcesionario);";

        try {

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idconcesionario' => $id));
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Registra una nueva compra en el sistema
     * 
     * Ejecuta procedimiento almacenado que registra una compra asociada a una
     * orden de compra, incluyendo la documentación fiscal completa (factura o
     * boleta). El procedimiento actualiza el estado de la orden de compra y
     * puede realizar otras operaciones relacionadas como actualizar inventario
     * o generar registros contables.
     * 
     * Utiliza stored procedure: sp_compra_registrar
     * 
     * @param array $params Datos de la compra con estructura:
     *   - idorden (int, requerido): ID de la orden de compra
     *   - fechacompra (string, requerido): Fecha de compra (formato: YYYY-MM-DD)
     *   - tipodoc (string, requerido): Tipo de documento ("Factura" o "Boleta")
     *   - serie (string, requerido): Serie del comprobante (ej: "F001")
     *   - numdocumento (string, requerido): Número del comprobante
     *   - rutadoc (string, opcional): Ruta del archivo escaneado del comprobante
     * 
     * @return int ID de la compra creada (positivo) si es exitoso,
     *             0 si no se pudo obtener el ID,
     *             -1 en caso de error
     */
    public function create($params = []): int
    {

        $query = "CALL  sp_compra_registrar(:idorden,:idlogistica,:fechacompra,:tipodoc,:serie,:numdocumento,:rutadoc)";

        try {
            $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(
                ':idorden' => $params['idorden'],
                ':idlogistica' => $idUsuario,
                ':fechacompra' => $params['fechacompra'],
                ':tipodoc' => $params['tipodoc'],
                ':serie' => $params['serie'],
                ':numdocumento' => $params['numdocumento'],
                'rutadoc' => $params['rutadoc']
            ));

            $id = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return isset($id['last_id']) ? (int) $id['last_id'] : 0;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

}
