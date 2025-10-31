<?php

/**
 * Modelo de Entrega de Dinero
 * 
 * app/Models/EntregaDinero.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las entregas
 * de dinero del sistema. Controla el flujo de efectivo desde los arqueos
 * de caja hacia diferentes destinos (gerentes, depósitos bancarios),
 * permitiendo trazabilidad completa del dinero recaudado y su distribución.
 * Proporciona vistas resumidas y detalladas de cada entrega con información
 * de arqueos cubiertos y destinos asignados.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase EntregaDinero
 * 
 * Modelo para la gestión de entregas de dinero del sistema.
 * Proporciona métodos para consultar el listado de entregas con resumen
 * ejecutivo, obtener el detalle de arqueos asociados a cada entrega,
 * y consultar los destinos específicos (personas o cuentas bancarias)
 * donde fue distribuido el dinero. Facilita auditoría y control financiero.
 */
class EntregaDinero
{

    /**
     * Instancia de conexión a la base de datos
     * @var 
     */
    private $db;

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
     * Obtiene el resumen ejecutivo de todas las entregas
     * 
     * Recupera un listado completo de entregas de dinero con información
     * consolidada: fecha, monto total entregado, colaborador responsable,
     * cantidad de arqueos cubiertos, y tipos de destino utilizados.
     * Ordenado por fecha descendente (más recientes primero).
     * 
     * @return array Array asociativo con las entregas. Cada elemento contiene:
     *               - identrega: int (Identificador único de la entrega)
     *               - fechaentrega: string (Fecha y hora formato DD-MM-YYYY HH:MM)
     *               - montoentregado: decimal (Monto total entregado)
     *               - obs_entrega: string|null (Observaciones de la entrega)
     *               - nombre_colaborador: string (Nombre completo del colaborador)
     *               - total_arqueos_cubiertos: int (Cantidad de arqueos asociados)
     *               - tipos_destino_resumen: string|null (Lista de tipos: "Gerente, Deposito")
     *               Retorna array vacío en caso de error
     */
    public function listarResumen(): array
    {
        $query = "
            SELECT 
                e.identrega,
                DATE_FORMAT(e.fechaentrega,'%d-%m-%Y %H:%i') AS fechaentrega,
                e.montoentregado,
                e.observaciones AS obs_entrega,
                CONCAT(p.nombres, ' ', p.apellidos) AS nombre_colaborador,
                (
                    SELECT COUNT(ida.idarqueo)
                    FROM entregasdineroarqueos ida
                    WHERE ida.identrega = e.identrega
                ) AS total_arqueos_cubiertos,
                (
                    SELECT GROUP_CONCAT(DISTINCT edd.tipodestino SEPARATOR ', ')
                    FROM entregasdinero_destinos edd
                    WHERE edd.identrega = e.identrega
                ) AS tipos_destino_resumen
            FROM 
                entregasdinero e
            JOIN 
                colaboradores c ON e.idcolentrega = c.idcolaborador
            JOIN
                contratoslaborales cl ON c.idcontratolaboral = cl.idcontratolaboral
            JOIN
                personas p ON cl.idpersona = p.idpersona
            ORDER BY 
                e.fechaentrega DESC;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el detalle de arqueos asociados a una entrega
     * 
     * Retorna la lista completa de arqueos de caja que fueron cubiertos
     * en una entrega específica. Incluye información financiera detallada
     * de cada arqueo: ingresos en efectivo, ingresos digitales, egresos
     * y el total resultante.
     * 
     * @param int $identrega ID de la entrega a consultar
     * @return array Array asociativo con los arqueos. Cada elemento contiene:
     *               - idarqueo: int (Identificador del arqueo)
     *               - ingresos_efectivo: decimal (Total ingresos en efectivo)
     *               - ingresos_digital: decimal (Total ingresos digitales/tarjeta)
     *               - egresos_dia: decimal (Total egresos del día)
     *               - total: decimal (Efectivo disponible: ingresos_efectivo - egresos_dia)
     *               - estado_arqueo: string (Estado del arqueo)
     *               Ordenado por ID de arqueo ascendente.
     *               Retorna array vacío si no hay arqueos o hay error
     */
    public function obtenerDetalleArqueos(int $identrega): array
    {
        $query = "
                        SELECT 
                            a.idarqueo,
                            a.ingresos_efectivo,
                            a.ingresos_digital,
                            a.egresos_dia,
                            (a.ingresos_efectivo - a.egresos_dia) AS total,
                            a.estado AS estado_arqueo
                        FROM 
                            entregasdineroarqueos eda
                        JOIN 
                            arqueocaja a ON eda.idarqueo = a.idarqueo
                        WHERE 
                            eda.identrega = :identrega
                        ORDER BY
                            a.idarqueo ASC;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':identrega', $identrega, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }

    }

    /**
     * Obtiene el detalle de destinos de una entrega
     * 
     * Retorna la lista completa de destinos donde fue distribuido el dinero
     * de una entrega específica. Cada destino incluye el tipo (Gerente o
     * Depósito bancario), el monto asignado, y la información específica
     * del destinatario:
     * - Si es Gerente: nombre completo de la persona
     * - Si es Depósito: entidad bancaria y número de cuenta
     * 
     * @param int $identrega ID de la entrega a consultar
     * @return array Array asociativo con los destinos. Cada elemento contiene:
     *               - tipodestino: string (Tipo: "Gerente" o "Deposito")
     *               - monto: decimal (Monto asignado a este destino)
     *               - destino: string (Información del destinatario:
     *                   - Para Gerente: "Nombres Apellidos"
     *                   - Para Deposito: "Entidad Bancaria - Número de Cuenta"
     *                   - Si no existe: "Destino no especificado")
     *               Retorna array vacío si no hay destinos o hay error
     */
    public function obtenerDetalleDestinos(int $identrega): array
    {
        $query = "
            SELECT
                edd.tipodestino,
                edd.monto,
                CASE edd.tipodestino
                    WHEN 'Gerente' THEN CONCAT(p.nombres, ' ', p.apellidos)
                    WHEN 'Deposito' THEN CONCAT(ent.entidad, ' - ', cp.numcuenta)
                    ELSE 'Destino no especificado'
                END AS destino
            FROM
                entregasdinero_destinos edd
            LEFT JOIN
                personas p 
                ON edd.iddestino = p.idpersona AND edd.tipodestino = 'Gerente'
            LEFT JOIN
                cuentaspago cp 
                ON edd.iddestino = cp.idcuentapago AND edd.tipodestino = 'Deposito'
            LEFT JOIN
                entidadespago ent 
                ON cp.identidadpago = ent.identidadpago
            WHERE
                edd.identrega = :identrega;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':identrega', $identrega, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
    
}
