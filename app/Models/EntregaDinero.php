<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class EntregaDinero
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
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
