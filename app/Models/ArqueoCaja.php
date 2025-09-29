<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class ArqueoCaja
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    
    public function listarConsolidado(): array
    {
        try {
            $stmt = $this->db->prepare("CALL sp_listar_arqueos_consolidados()");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (PDOException $e) {
            error_log("Error al listar arqueos consolidados: " . $e->getMessage());
            return [];
        }
    }

    
    public function obtenerDatosUltimoArqueo(): array
    {
        try {
            $stmt = $this->db->prepare("CALL sp_obtener_datos_ultimo_arqueo()");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            // Si no hay arqueos previos, devolver valores por defecto para el primer arqueo del día
            if (!$result) {
                return [
                    'saldo_inicial_para_hoy' => 0.00,
                    'hora_ultimo_arqueo' => '00:00:00',
                    'fecha_ultimo_arqueo' => date('Y-m-d', strtotime('-1 day')) // Ayer para que tome todos los movimientos de hoy
                ];
            }

            return [
                'saldo_inicial_para_hoy' => floatval($result['saldo_inicial_para_hoy'] ?? 0),
                'hora_ultimo_arqueo' => $result['hora_ultimo_arqueo'] ?? '00:00:00',
                'fecha_ultimo_arqueo' => $result['fecha_ultimo_arqueo'] ?? date('Y-m-d')
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener datos del último arqueo: " . $e->getMessage());
            return [
                'saldo_inicial_para_hoy' => 0.00,
                'hora_ultimo_arqueo' => '00:00:00',
                'fecha_ultimo_arqueo' => date('Y-m-d', strtotime('-1 day'))
            ];
        }
    }

    /**
     * Obtiene ingresos (efectivo/digital) desde una hora específica.
     * @param string $fechaReferencia Fecha del último arqueo.
     * @param string $horaReferencia Hora del último arqueo.
     * @return array Ingresos nuevos.
     */
    public function obtenerIngresosDesde(string $fechaReferencia, string $horaReferencia): array
    {
        try {
            $stmt = $this->db->prepare("CALL sp_obtener_ingresos_desde(?, ?)");
            $stmt->bindParam(1, $fechaReferencia, PDO::PARAM_STR);
            $stmt->bindParam(2, $horaReferencia, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return [
                'ingresos_efectivo_nuevos' => floatval($result['ingresos_efectivo_nuevos'] ?? 0),
                'ingresos_digital_nuevos' => floatval($result['ingresos_digital_nuevos'] ?? 0),
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener ingresos desde hora: " . $e->getMessage());
            return ['ingresos_efectivo_nuevos' => 0.00, 'ingresos_digital_nuevos' => 0.00];
        }
    }

    /**
     * Obtiene egresos desde una hora específica.
     * @param string $fechaReferencia Fecha del último arqueo.
     * @param string $horaReferencia Hora del último arqueo.
     * @return float Egresos nuevos.
     */
    public function obtenerEgresosDesde(string $fechaReferencia, string $horaReferencia): float
    {
        try {
            $stmt = $this->db->prepare("CALL sp_obtener_egresos_desde(?, ?)");
            $stmt->bindParam(1, $fechaReferencia, PDO::PARAM_STR);
            $stmt->bindParam(2, $horaReferencia, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return floatval($result['total_egresos_nuevos'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error al obtener egresos desde hora: " . $e->getMessage());
            return 0.00;
        }
    }




    public function registrar(array $data): bool
    {
        try {
            $stmt = $this->db->prepare("CALL spu_arqueo_caja_insert(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $idUsuario = $_SESSION['user']['id'] ?? null;
            $stmt->bindParam(1, $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(2, $data['hora_inicio'], PDO::PARAM_STR);
            $stmt->bindParam(3, $data['hora_fin'], PDO::PARAM_STR);
            $stmt->bindParam(4, $data['saldo_inicial'], PDO::PARAM_STR);
            $stmt->bindParam(5, $data['ingresos_efectivo'], PDO::PARAM_STR);
            $stmt->bindParam(6, $data['ingresos_digital'], PDO::PARAM_STR);
            $stmt->bindParam(7, $data['egresos_dia'], PDO::PARAM_STR);
            $stmt->bindParam(8, $data['monto_teorico'], PDO::PARAM_STR);
            $stmt->bindParam(9, $data['monto_fisico'], PDO::PARAM_STR);
            $stmt->bindParam(10, $data['diferencia'], PDO::PARAM_STR);
            $stmt->bindParam(11, $data['observaciones'], PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al registrar el arqueo: " . $e->getMessage());
            return false;
        }
    }

    public function registrarEntrega(array $data): bool
    {
        try {
            date_default_timezone_set('America/Lima');

            $this->db->beginTransaction();
            $idUsuario = $_SESSION['user']['id'] ?? null;
            $montoTotal = $data['montoentregado'];
            $idUsuario = $_SESSION['user']['id'] ?? null;
            $montoTotal = $data['montoentregado'];

            // 1. Inserta la entrega principal en la tabla 'entregasdinero'
            $stmt = $this->db->prepare("
            INSERT INTO entregasdinero (
                idcolentrega,
                montoentregado,
                observaciones
            ) VALUES (?, ?, ?);
        ");
            $stmt->execute([
                $idUsuario,
                $montoTotal,
                $data['observaciones'] ?? null
            ]);
            $identrega = $this->db->lastInsertId();

            if (!$identrega) {
                $this->db->rollBack();
                return false;
            }

            // 2. Inserta el o los destino(s) de la entrega
            if ($data['tipodestino'] === 'Gerente') {
                $stmt_destino = $this->db->prepare("
                INSERT INTO entregasdinero_destinos (identrega, tipodestino, iddestino, monto) 
                VALUES (?, ?, ?, ?);
            ");
                $stmt_destino->execute([$identrega, 'Gerente', $data['iddestino'], $montoTotal]);
            } elseif ($data['tipodestino'] === 'Deposito' && !empty($data['destinos_multiples'])) {
                $stmt_destino = $this->db->prepare("
                INSERT INTO entregasdinero_destinos (identrega, tipodestino, iddestino, monto) 
                VALUES (?, ?, ?, ?);
            ");
                foreach ($data['destinos_multiples'] as $destino) {
                    $stmt_destino->execute([$identrega, 'Deposito', $destino['iddestino'], $destino['monto']]);
                }
            } else {
                $this->db->rollBack();
                return false;
            }

            // 3. Vincula la entrega con los arqueos y actualiza su estado a 'S'.
            $idsArqueos = is_array($data['ids_arqueos']) ? $data['ids_arqueos'] : explode(',', $data['ids_arqueos']);
            $stmt_relacion = $this->db->prepare("INSERT INTO entregasdineroarqueos (identrega, idarqueo) VALUES (?, ?)");
            foreach ($idsArqueos as $idarqueo) {
                $stmt_relacion->execute([$identrega, (int) $idarqueo]);
            }

            $placeholders = implode(',', array_fill(0, count($idsArqueos), '?'));
            $stmt_update = $this->db->prepare("UPDATE arqueocaja SET entregado = 'S' WHERE idarqueo IN ($placeholders)");
            $stmt_update->execute($idsArqueos);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al registrar la entrega: " . $e->getMessage());
            return false;
        }
    }







    public function obtenerGerentes(): array
    {
        $query = "SELECT 
                    p.idpersona AS id, 
                    CONCAT(p.nombres, ' ', p.apellidos) AS nombre
                  FROM personas p
                  INNER JOIN contratoslaborales cl ON p.idpersona = cl.idpersona
                  INNER JOIN cargos c ON cl.idcargo = c.idcargo
                  INNER JOIN areas a ON c.idarea = a.idarea
                  WHERE a.area = 'Gerencia'";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener gerentes: " . $e->getMessage());
            return [];
        }
    }




    public function obtenerCuentasBancarias(): array
    {
        $query = "SELECT 
                    cp.idcuentapago AS id, 
                    CONCAT(ep.entidad, ' - ', cp.numcuenta, ' (', cp.moneda, ')') AS nombre 
                  FROM cuentaspago cp
                  INNER JOIN entidadespago ep ON cp.identidadpago = ep.identidadpago";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener cuentas bancarias: " . $e->getMessage());
            return [];
        }
    }




    public function getReporteArqueoPorCiclo(string $ids_arqueo): array
{
   
    $query = "CALL sp_reporte_arqueo_por_fecha_por_ciclo(:ids_arqueo_param);";

    try {
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":ids_arqueo_param", $ids_arqueo, PDO::PARAM_STR); 
        $stmt->execute();

        $data = [];

      
        $data['arqueo'] = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data['arqueo']) {
            return [];
        }

      
        $stmt->nextRowset();
        $data['egresos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        $stmt->nextRowset();
        $data['ingresos_digitales'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor(); 
        return $data;
    } catch (PDOException $e) {
        error_log("Error en el modelo getReporteArqueoPorCiclo: " . $e->getMessage());
        return [];
    }
}
}
