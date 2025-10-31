<?php

/**
 * Modelo de Arqueo de Caja
 * 
 * Gestiona todas las operaciones de base de datos relacionadas con arqueos
 * de caja: registro de cierres diarios con conteo físico vs teórico, consulta
 * de arqueos consolidados, cálculo automático de ingresos/egresos desde último
 * arqueo, registro de entregas de dinero a gerencia o depósitos bancarios con
 * transacciones ACID, generación de reportes por ciclos y sedes, y consultas
 * de catálogos (gerentes, cuentas bancarias). Implementa manejo de valores por
 * defecto para primer arqueo del día, cálculos de diferencias (sobrante/faltante),
 * y actualización automática de estados de arqueos entregados.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase ArqueoCaja
 * 
 * Modelo que representa y gestiona los arqueos de caja en el sistema.
 * Un arqueo de caja es el proceso de conteo y verificación del efectivo
 * disponible en caja al final de un turno o día laboral, comparando el
 * monto teórico (según registros) con el monto físico real contado.
 */
class ArqueoCaja
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
     * Obtiene listado consolidado de todos los arqueos
     * 
     * Ejecuta procedimiento almacenado que retorna un listado completo de
     * arqueos registrados con información resumida: fecha, hora inicio/fin,
     * montos, diferencias, usuario responsable, y estado de entrega.
     * Útil para visualización histórica y auditorías.
     * 
     * Utiliza stored procedure: sp_listar_arqueos_consolidados
     * 
     * @return array Array de arqueos con estructura:
     *   - idarqueo (int): ID del arqueo
     *   - fecha (date): Fecha del arqueo
     *   - hora_inicio (time): Hora de inicio del turno
     *   - hora_fin (time): Hora de cierre del turno
     *   - saldo_inicial (decimal): Efectivo inicial del turno
     *   - ingresos_efectivo (decimal): Total ingresos en efectivo
     *   - ingresos_digital (decimal): Total ingresos digitales
     *   - egresos_dia (decimal): Total egresos del período
     *   - monto_teorico (decimal): Monto esperado en caja
     *   - monto_fisico (decimal): Monto físico contado
     *   - diferencia (decimal): Sobrante (+) o Faltante (-)
     *   - usuario_responsable (string): Nombre del cajero
     *   - entregado (char): 'S' si fue entregado, 'N' si está pendiente
     *   Retorna array vacío si no hay arqueos o hay error
     */
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

    /**
     * Obtiene datos del último arqueo para calcular saldo inicial del siguiente
     * 
     * Ejecuta procedimiento almacenado que retorna información del arqueo más
     * reciente para determinar el saldo inicial del próximo arqueo. Si no hay
     * arqueos previos (primer arqueo del día/sistema), retorna valores por
     * defecto seguros: saldo inicial 0, hora 00:00:00, fecha de ayer.
     * 
     * Utiliza stored procedure: sp_obtener_datos_ultimo_arqueo
     * 
     * @return array {fecha_ultimo_arqueo: mixed, hora_ultimo_arqueo: mixed, saldo_inicial_para_hoy: float|array{fecha_ultimo_arqueo: string, hora_ultimo_arqueo: string, saldo_inicial_para_hoy: float}}
     */
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
     * Obtiene ingresos (efectivo y digital) desde fecha/hora específica
     * 
     * Ejecuta procedimiento almacenado que calcula la suma de ingresos
     * registrados desde una fecha y hora determinadas hasta el momento actual.
     * Separa ingresos en efectivo (dinero físico) e ingresos digitales
     * (transferencias, tarjetas, Yape, etc.). Usado para calcular ingresos
     * del turno actual desde el último arqueo.
     * 
     * Utiliza stored procedure: sp_obtener_ingresos_desde
     *
     * @param string $fechaReferencia Fecha desde la cual contar ingresos
     * @param string $horaReferencia Hora desde la cual contar ingresos
     * @return array {ingresos_digital_nuevos: float, ingresos_efectivo_nuevos: float}
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
     * Obtiene total de egresos desde fecha/hora específica
     * 
     * Ejecuta procedimiento almacenado que calcula la suma de todos los
     * egresos (salidas de efectivo) registrados desde una fecha y hora
     * determinadas hasta el momento actual. Incluye compras, gastos,
     * devoluciones y otras salidas de dinero. Usado para calcular egresos
     * del turno actual desde el último arqueo.
     * 
     * Utiliza stored procedure: sp_obtener_egresos_desde
     *
     * @param string $fechaReferencia Fecha desde la cual contar egresos
     * @param string $horaReferencia Hora desde la cual contar egresos
     * @return float Total de egresos del período
     *   Retorna 0.00 si no hay egresos o hay error
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

    /**
     * Registra un nuevo arqueo de caja
     * 
     * Ejecuta procedimiento almacenado que registra un arqueo completo con
     * todos sus componentes: saldo inicial, ingresos (efectivo/digital),
     * egresos, monto teórico calculado, monto físico contado, diferencia,
     * y observaciones. El usuario responsable se obtiene automáticamente de
     * la sesión para trazabilidad.
     * 
     * Utiliza stored procedure: spu_arqueo_caja_insert
     *
     * @param array $data Datos del arqueo con estructura:
     *   - hora_inicio (string, requerido): Hora inicio del turno (HH:MM:SS)
     *   - hora_fin (string, requerido): Hora fin del turno (HH:MM:SS)
     *   - saldo_inicial (decimal, requerido): Efectivo inicial del turno
     *   - ingresos_efectivo (decimal, requerido): Total ingresos en efectivo
     *   - ingresos_digital (decimal, requerido): Total ingresos digitales
     *   - egresos_dia (decimal, requerido): Total egresos del turno
     *   - monto_teorico (decimal, requerido): Monto esperado según cálculos
     *   - monto_fisico (decimal, requerido): Efectivo real contado
     *   - diferencia (decimal, requerido): Físico - Teórico
     *   - observaciones (string, opcional): Notas sobre diferencias o incidencias
     * @return bool true si el registro fue exitoso, false en caso de error
     */
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

    /**
     * Registra entrega de dinero con transacción ACID
     * 
     * Registra una entrega de efectivo a gerencia o depósito bancario mediante
     * transacción ACID que garantiza atomicidad. Soporta entregas a un gerente
     * o múltiples depósitos bancarios. Actualiza automáticamente el estado de
     * los arqueos asociados a 'entregado'. Rollback automático en errores.
     *
     * Proceso transaccionaL:
     * 1. INSERT en entregasdinero (registro principal)
     * 2. INSERT en entregasdinero_destinos (uno o múltiples)
     * 3. INSERT en entregasdineroarqueos (relación N:N)
     * 4. UPDATE arqueocaja SET entregado='S' (marca arqueos)
     *
     * @param array $data Datos de la entrega con estructura:
     *   - tipodestino (string, requerido): 'Gerente' o 'Deposito'
     *   - montoentregado (decimal, requerido): Monto total entregado
     *   - observaciones (string, opcional): Notas sobre la entrega
     *   - ids_arqueos (array|string, requerido): IDs de arqueos a marcar como entregados
     *   
     *   Si tipodestino = 'Gerente':
     *   - iddestino (int, requerido): ID del gerente receptor
     *   
     *   Si tipodestino = 'Deposito':
     *   - destinos_multiples (array, requerido): Array de destinos con estructura:
     *     * iddestino (int): ID de la cuenta bancaria
     *     * monto (decimal): Monto depositado en esa cuenta
     * @return bool true si la entrega fue exitosa, false en caso de error (con rollback)
     */
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

    /**
     * Obtiene listado de gerentes disponibles para recibir entregas
     * 
     * Consulta que retorna todos los colaboradores que trabajan en el área
     * de Gerencia y están activos (con contrato laboral vigente). Utilizado
     * para poblar selectores en formularios de entrega de dinero.
     *
     * @return array Array de gerentes con estructura:
     *   - id (int): ID de la persona (idpersona)
     *   - nombre (string): Nombre completo (nombres + apellidos)
     *   Retorna array vacío si no hay gerentes o hay error
     */
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

    /**
     * Obtiene listado de cuentas bancarias disponibles para depósitos
     * 
     * Consulta que retorna todas las cuentas de pago activas con información
     * de la entidad bancaria, número de cuenta y moneda. Utilizado para poblar
     * selectores en formularios de entrega de dinero cuando el destino es
     * depósito bancario.
     *
     * @return array Array de cuentas bancarias con estructura:
     *   - id (int): ID de la cuenta de pago (idcuentapago)
     *   - nombre (string): Descripción formateada "Banco - NumCuenta (Moneda)"
     *   Retorna array vacío si no hay cuentas o hay error
     */
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

    /**
     * Obtiene reporte detallado de arqueo agrupado por ciclo
     * 
     * Ejecuta procedimiento almacenado que genera un reporte completo de uno
     * o más arqueos agrupando la información por ciclos académicos o períodos.
     * Retorna tres conjuntos de datos: resumen del arqueo, detalle de egresos,
     * y detalle de ingresos digitales.
     * 
     * Utiliza stored procedure: sp_reporte_arqueo_por_fecha_por_ciclo
     *
     * @param string $ids_arqueo IDs de arqueos separados por comas
     * @return array Array con estructura de tres niveles:
     *   - arqueo (array): Datos consolidados del arqueo
     *   - egresos (array): Listado detallado de egresos por categoría
     *   - ingresos_digitales (array): Listado de ingresos no efectivo
     *   Retorna array vacío si no se encuentran datos o hay error
     */
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

    /**
     * Obtiene reporte detallado de arqueo filtrado por sede específica
     * 
     * Ejecuta procedimiento almacenado que genera un reporte de uno o más
     * arqueos filtrado específicamente para una sede/local particular.
     * Retorna datos consolidados (resumen) y detalle de ingresos digitales
     * de esa sede.
     * 
     * Utiliza stored procedure: sp_reporte_arqueo_por_fecha_y_sede
     *
     * @param string $ids_arqueo IDs de arqueos separados por comas
     * @param int $sede_id ID de la sede/local a filtrar
     * @return array Array con dos componentes:
     *   - resumen (array): Datos consolidados del arqueo para la sede específica
     *   - ingresos_digitales (array): Detalle de ingresos digitales de la sede
     *   Retorna array vacío si no se encuentran datos o hay error
     */
    public function getReporteArqueoBySede(string $ids_arqueo, int $sede_id): array
    {
        $resultado = [
            'resumen' => [],
            'ingresos_digitales' => []
        ];

        try {

            $stmt = $this->db->prepare("CALL sp_reporte_arqueo_por_fecha_y_sede(:ids_arqueo, :idlocal)");
            $stmt->bindParam(':ids_arqueo', $ids_arqueo, PDO::PARAM_STR);
            $stmt->bindParam(':idlocal', $sede_id, PDO::PARAM_INT);

            $stmt->execute();


            $resultado['resumen'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($stmt->nextRowset()) {
                $resultado['ingresos_digitales'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt->closeCursor();
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en getReporteArqueoBySede: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene la hora de la última entrega de dinero realizada hoy
     * 
     * Consulta que retorna la hora (formato HH:MM) de la última entrega de
     * dinero registrada en el día actual. Útil para determinar el punto de
     * corte temporal para el próximo arqueo o entrega, evitando solapamientos
     * y asegurando continuidad en el control de caja.
     *
     * @return string Hora de la última entrega en formato HH:MM (ej: "14:30").
     *                Retorna string vacío si no hay entregas hoy o hay error
     */
    public function getHoraUltimaEntregaHoy(): string
    {
        $query = "SELECT DATE_FORMAT(fechaentrega,'%H:%i') AS ultima_hora_entrega
              FROM entregasdinero
              WHERE DATE(fechaentrega) = CURDATE()
              ORDER BY identrega DESC
              LIMIT 1;";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($resultado) {
                return $resultado['ultima_hora_entrega'];
            } else {

                return "";
            }
        } catch (PDOException $e) {
            error_log("Error al obtener la última hora de entrega: " . $e->getMessage());
            return "";
        }
    }

}
