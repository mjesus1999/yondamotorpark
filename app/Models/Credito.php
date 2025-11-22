<?php

/**
 * Modelo de Crédito
 * 
 * app/Models/Credito.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con el seguimiento
 * y control de créditos morosos del sistema. Proporciona estadísticas
 * ejecutivas de morosidad, clasificación de clientes por días de atraso,
 * registro de seguimientos (llamadas, visitas, acuerdos de pago), y
 * consulta de historiales de gestión. Implementa flujo de actualización
 * automática de cuotas vencidas antes de consultar datos para garantizar
 * información actualizada en tiempo real.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Credito
 * 
 * Modelo para la gestión de créditos y control de morosidad.
 * Proporciona métodos para obtener estadísticas de morosos, clasificarlos
 * por nivel de atraso (5 días, 2 semanas, 1+ mes), registrar acciones
 * de seguimiento con evidencias, y consultar historiales completos de
 * gestión de cobranza. Ejecuta múltiples procedimientos almacenados
 * con manejo correcto de cursores para evitar conflictos en operaciones
 * secuenciales.
 */
class Credito
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
     * Obtiene estadísticas ejecutivas de morosidad
     * 
     * Ejecuta dos procedimientos almacenados separados y combina sus
     * resultados para generar un dashboard de indicadores clave:
     * total de clientes morosos, deuda acumulada, promedio de días
     * de atraso, y seguimientos realizados el día actual.
     * 
     * Procedimientos ejecutados:
     * 1. sp_get_estadisticas_morosos_base: Totales y promedios generales
     * 2. sp_get_seguimientos_hoy: Conteo de seguimientos del día
     * 
     * @return array {deuda_total: float, dias_promedio: float, seguimientos_hoy: int, total_morosos: int|array{deuda_total: int, dias_promedio: int, seguimientos_hoy: int, total_morosos: int}}
     */
    public function getEstadisticasMorosos(): array
    {
        try {
           
            $stmt1 = $this->db->prepare("CALL sp_get_estadisticas_morosos_base()");
            $stmt1->execute();
            $estadisticas = $stmt1->fetch(PDO::FETCH_ASSOC);
            $stmt1->closeCursor(); 

     
            $stmt2 = $this->db->prepare("CALL sp_get_seguimientos_hoy()");
            $stmt2->execute();
            $seguimientos = $stmt2->fetch(PDO::FETCH_ASSOC);
            $stmt2->closeCursor();

            return [
                'total_morosos' => (int) ($estadisticas['total_morosos'] ?? 0),
                'deuda_total' => (float) ($estadisticas['deuda_total'] ?? 0),
                'dias_promedio' => (float) ($estadisticas['dias_promedio'] ?? 0),
                'seguimientos_hoy' => (int) ($seguimientos['seguimientos_hoy'] ?? 0)
            ];

        } catch (PDOException $error) {
            error_log("Error en estadísticas morosos: " . $error->getMessage());
            return [
                'total_morosos' => 0,
                'deuda_total' => 0,
                'seguimientos_hoy' => 0,
                'dias_promedio' => 0
            ];
        }
    }

    /**
     * Obtiene morosos clasificados por nivel de atraso
     * 
     * Actualiza primero el estado de todas las cuotas vencidas ejecutando
     * sp_actualizar_cuotas_vencidas, luego obtiene la lista completa de
     * morosos con sp_get_morosos_clasificados, y finalmente los clasifica
     * en tres categorías según días de atraso:
     * 
     * Categorías de clasificación:
     * - 5-dias: 1 a 5 días de atraso
     * - 2-semanas: 6 a 14 días de atraso
     * - 1-mes: 15+ días de atraso
     * 
     * @return array|array {1-mes: array, 2-semanas: array, 5-dias: array}
     */
    public function getMorososClasificados(): array
    {
        try {
            // 1) PRIMERO: Actualizar cuotas vencidas
            $stmtUpdate = $this->db->prepare("CALL sp_actualizar_cuotas_vencidas()");
            $stmtUpdate->execute();
            $stmtUpdate->closeCursor(); // Cerrar cursor

            // 2) LUEGO: Obtener lista de morosos actualizada
            $stmtSelect = $this->db->prepare("CALL sp_get_morosos_clasificados()");
            $stmtSelect->execute();
            $results = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);
            $stmtSelect->closeCursor();

    
            // 3) Clasificar por días de atraso
            return $this->clasificarMorosos($results);


        } catch (PDOException $error) {
            error_log("Error en morosos clasificados: " . $error->getMessage());
            return [
                '5-dias' => [],
                '2-semanas' => [],
                '1-mes' => []
            ];
        }
    }

    /**
     * Clasifica morosos en categorías según días de atraso
     * 
     * Método privado que recibe un array de morosos y los distribuye
     * en tres categorías según la cantidad de días de atraso. Esta
     * clasificación permite priorizar la gestión de cobranza:
     * contactos tempranos para atrasos leves, seguimiento activo
     * para atrasos medios, y gestión intensiva/legal para atrasos
     * prolongados.
     * 
     * Criterios de clasificación:
     * - 1-5 días: Etapa de recordatorio amigable
     * - 6-14 días: Etapa de seguimiento formal
     * - 15+ días: Etapa crítica/gestión legal
     * 
     * @param array $morosos Array de morosos con campo 'dias_atraso'
     * @return array[]|array {1-mes: array, 2-semanas: array, 5-dias: array}
     */
    private function clasificarMorosos(array $morosos): array
    {
        $clasificados = [
            '5-dias' => [],
            '2-semanas' => [],
            '1-mes' => []
        ];

        foreach ($morosos as $moroso) {
            $diasAtraso = (int) $moroso['dias_atraso'];

            if ($diasAtraso >= 1 && $diasAtraso <= 5) {
                $clasificados['5-dias'][] = $moroso;
            } elseif ($diasAtraso >= 6 && $diasAtraso <= 14) {
                $clasificados['2-semanas'][] = $moroso;
            } elseif ($diasAtraso >= 15) {
                $clasificados['1-mes'][] = $moroso;
            }

            /* if ($diasAtraso <= 7) {
                $clasificados['5-dias'][] = $moroso;
            } elseif ($diasAtraso <= 30) {
                $clasificados['2-semanas'][] = $moroso;
            } else {
                $clasificados['1-mes'][] = $moroso;
            } */
        }

        return $clasificados;
    }

    /**
     * Registra un seguimiento de gestión de cobranza
     * 
     * Crea un registro de seguimiento documentando las acciones realizadas
     * para recuperar la cartera morosa. Cada seguimiento incluye tipo de
     * contacto, observaciones detalladas, evidencias (grabaciones, fotos,
     * acuerdos firmados), y fecha/hora de la gestión.
     * 
     * @param array $data Array asociativo con datos del seguimiento:
     *                    - idcontrato: int (ID del contrato moroso)
     *                    - tipo: string (Tipo de seguimiento realizado)
     *                    - observaciones: string (Descripción detallada de la gestión)
     *                    - evidencia: string|null (Ruta del archivo de evidencia)
     *                    - fecha_seguimiento: string (Fecha/hora de la gestión)
     *                    - usuario_registro: int (ID del colaborador que realizó la gestión)
     * @return int ID del seguimiento creado, o 0 en caso de error
     */
    public function registrarSeguimiento(array $data): int
    {
        $query = "CALL sp_registrar_seguimiento_moroso(:idcontrato, :tipo, :observaciones, :evidencia, :fecha_seguimiento, :usuario_registro)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idcontrato' => $data['idcontrato'],
                ':tipo' => $data['tipo'],
                ':observaciones' => $data['observaciones'],
                ':evidencia' => $data['evidencia'],
                ':fecha_seguimiento' => $data['fecha_seguimiento'],
                ':usuario_registro' => $data['usuario_registro']
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return (int) ($result['last_insert_id'] ?? 0);
        } catch (PDOException $error) {
            error_log("Error al registrar seguimiento: " . $error->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene el historial completo de seguimientos de un contrato
     * 
     * Retorna todos los seguimientos de gestión de cobranza realizados
     * sobre un contrato específico, ordenados cronológicamente (más
     * recientes primero). Permite auditar todas las acciones de cobranza,
     * evaluar efectividad de estrategias, y tener trazabilidad completa
     * de la gestión realizada.
     * 
     * @param int $idContrato ID del contrato a consultar
     * @return array Array de seguimientos ordenados cronológicamente.
     *               Cada elemento contiene el detalle completo del seguimiento.
     *               Retorna array vacío si no hay seguimientos o hay error
     */
    public function getHistorialSeguimientos(int $idContrato): array
    {
        try {
            $query = "CALL sp_get_historial_seguimientos(:idcontrato)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idcontrato' => $idContrato]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $results ?: [];
        } catch (PDOException $e) {
            error_log("Error getHistorialSeguimientos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene información del cliente asociado a un contrato
     * 
     * Retorna datos completos del cliente titular de un contrato:
     * nombres, documento, teléfono, valor de cuota, y estado del contrato.
     * Utilizado para mostrar información del cliente en módulos de
     * gestión de cobranza y seguimiento.
     * 
     * @param int $idContrato ID del contrato a consultar
     */
    public function getClienteByContrato(int $idContrato): ?array
    {
        $query = "SELECT 
                    con.idcontrato,
                    CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
                    p.nrodoc,
                    p.telprimario AS celular,
                    cot.valorcuota,
                    con.estado
                  FROM contratos con
                  JOIN cotizaciones cot ON con.idcotizacion = cot.idcotizacion
                  JOIN clientes cli ON cot.idcliente = cli.idcliente
                  JOIN personas p ON cli.idpersona = p.idpersona
                  WHERE con.idcontrato = :idcontrato";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idcontrato' => $idContrato]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result ?: null;
        } catch (PDOException $error) {
            error_log("Error al obtener cliente: " . $error->getMessage());
            return null;
        }
    }
    
}