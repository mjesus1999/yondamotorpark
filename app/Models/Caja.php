<?php

/**
 * Modelo de Caja
 * 
 * app/Models/Caja.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con el módulo de caja
 * y tesorería: consulta de contratos activos con datos financieros, cronogramas
 * de pago por contrato, generación de reportes de ingresos diarios, reportes
 * por rangos de fechas personalizados, y resúmenes ejecutivos de cartera morosa.
 * Todos los métodos utilizan procedimientos almacenados para garantizar lógica
 * de negocio consistente y optimización de consultas complejas.
 */

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Caja
 * 
 * Modelo que representa y gestiona las operaciones de caja en el sistema.
 * Proporciona acceso a información financiera de contratos, cronogramas de
 * cuotas, reportes de ingresos por períodos, y análisis de cartera morosa.
 * Todas las consultas se realizan mediante stored procedures para asegurar
 * integridad de datos y rendimiento óptimo.
 */
class Caja
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
     * Convierte una fila de `import_contratos_enero_2026` al mismo shape que `registro_ventas_vehiculares`
     * para reutilizar sincronización de cliente y cronograma estimado en CAJA.
     */
    private function mapImportContratoEneroRowToRegistroVentas(array $r): array
    {
        $dir = trim((string) ($r['direccion'] ?? ''));
        $ciu = trim((string) ($r['ciudad'] ?? ''));
        if ($ciu !== '') {
            $dir = $dir === '' ? $ciu : ($dir . ', ' . $ciu);
        }
        $car = trim((string) ($r['caracteristicas'] ?? ''));
        $placaRaw = trim((string) ($r['placa'] ?? ''));
        $enTramite = $placaRaw !== '' && (bool) preg_match('/TR[ÁA]MITE/ui', $placaRaw);
        $placa = $enTramite ? null : ($placaRaw === '' ? null : $placaRaw);
        $pct = (float) ($r['pct_mora'] ?? 10);
        if ($pct > 0 && $pct < 1) {
            $pct *= 100.0;
        }
        if ($pct <= 0) {
            $pct = 10.0;
        }
        $cuotaMens = isset($r['cuota_mensual']) ? (float) $r['cuota_mensual'] : 0.0;
        $cuotaBase = $cuotaMens;
        $montoInteres = round($cuotaBase * ($pct / 100.0), 2);

        $det = [];
        for ($n = 1; $n <= 5; $n++) {
            $cuota = trim((string) ($r['cuota_' . $n] ?? ''));
            $fecha = trim((string) ($r['fecha_pago_' . $n] ?? ''));
            $gps = trim((string) ($r['gps_' . $n] ?? $r['gps_cuota' . $n] ?? ''));
            $mora = trim((string) ($r['mora_' . $n] ?? ''));
            $deudas = trim((string) ($r['deudas_' . $n] ?? ''));
            if ($cuota === '' && $fecha === '' && $gps === '' && $mora === '' && $deudas === '') {
                continue;
            }
            $chunk = 'Cuota' . $n . ': ' . ($cuota !== '' ? $cuota : '-');
            if ($fecha !== '') {
                $chunk .= ' | Pago: ' . $fecha;
            }
            if ($gps !== '') {
                $chunk .= ' | GPS: ' . $gps;
            }
            if ($mora !== '' && strtoupper($mora) !== 'NULL') {
                $chunk .= ' | Mora: ' . $mora;
            }
            if ($deudas !== '' && strtoupper($deudas) !== 'NULL') {
                $chunk .= ' | Deuda: ' . $deudas;
            }
            $det[] = $chunk;
        }
        $masDeudas = trim((string) ($r['mas_deudas_pendientes'] ?? ''));
        if ($masDeudas !== '' && strtoupper($masDeudas) !== 'NULL') {
            $det[] = 'Mas deudas: ' . $masDeudas;
        }
        $detalle = $det !== [] ? implode(' · ', $det) : null;

        $numPagada = null;
        for ($n = 5; $n >= 1; $n--) {
            $fp = trim((string) ($r['fecha_pago_' . $n] ?? ''));
            if ($fp !== '' && $fp !== '0000-00-00') {
                $numPagada = $n;
                break;
            }
        }

        $tel = preg_replace('/\D+/', '', (string) ($r['celular'] ?? ''));
        if (strlen($tel) > 15) {
            $tel = substr($tel, -15);
        }
        $telDisplay = trim((string) ($r['celular'] ?? ''));

        return [
            'id' => (int) ($r['id'] ?? 0),
            'fecha_venta' => $r['fecha_firma'] ?? null,
            'dni_cliente' => $r['dni'] ?? $r['dni_cliente'] ?? null,
            'nombre_cliente' => trim((string) ($r['cliente_nombre'] ?? '')),
            'aval' => null,
            'dni_aval' => null,
            'direccion' => $dir !== '' ? $dir : 'SIN DIRECCION',
            'modelo' => $car !== '' ? $car : '-',
            'marca' => '-',
            'chasis' => trim((string) ($r['chasis'] ?? '')) ?: null,
            'motor' => trim((string) ($r['motor'] ?? '')) ?: null,
            'color' => trim((string) ($r['color'] ?? '')) ?: null,
            'estado_tramite' => $enTramite ? 'En tramite' : (trim((string) ($r['estado'] ?? '')) ?: null),
            'placa' => $placa,
            'telefono_1' => $telDisplay !== '' ? $telDisplay : ($tel !== '' ? $tel : null),
            'telefono_2' => null,
            'precio_total' => isset($r['monto_valor']) ? (float) $r['monto_valor'] : 0.0,
            'pago_inicial' => isset($r['monto_inicial']) ? (float) $r['monto_inicial'] : 0.0,
            'deudas_pendientes' => null,
            'deudas_pendientes_detalle' => $detalle,
            'fecha_inicio_credito' => $r['fecha_comienzo'] ?? null,
            'plazo_meses' => isset($r['duracion_meses']) ? (int) $r['duracion_meses'] : 0,
            'fecha_fin_credito' => $r['fecha_vencimiento'] ?? null,
            'cuota_base' => round($cuotaBase, 2),
            'tasa_interes' => round($pct, 2),
            'monto_interes' => $montoInteres,
            'cuota_total_mensual' => round($cuotaMens, 2),
            'monto_financiar' => round(max(0, (float) ($r['monto_valor'] ?? 0) - (float) ($r['monto_inicial'] ?? 0)), 2),
            'fechas_cuota_import' => self::fechasCuotaDesdeFilaImport($r),
            'mora_3_dias' => isset($r['mora_monto']) ? (float) $r['mora_monto'] : null,
            'total_con_mora' => isset($r['total_con_mora']) ? (float) $r['total_con_mora'] : null,
            'numero_cuota_pagada' => $numPagada,
            'id_contrato' => trim((string) ($r['id_contrato'] ?? '')) ?: null,
            'fuente' => 'import_excel',
        ];
    }

    /**
     * @return array<int, string|null>
     */
    private static function fechasCuotaDesdeFilaImport(array $r): array
    {
        $out = [];
        for ($n = 1; $n <= 5; $n++) {
            $fp = trim((string) ($r['fecha_pago_' . $n] ?? ''));
            if ($fp !== '' && $fp !== '0000-00-00') {
                $out[$n] = $fp;
            }
        }

        return $out;
    }

    /**
     * Clave para no duplicar la misma venta entre registro oficial e import mensual.
     */
    private function registroVentasRowKey(array $row): string
    {
        $chasis = trim((string) ($row['chasis'] ?? ''));
        if ($chasis !== '') {
            return 'chasis:' . strtoupper($chasis);
        }
        $motor = trim((string) ($row['motor'] ?? ''));
        if ($motor !== '') {
            return 'motor:' . strtoupper($motor);
        }
        $idContrato = trim((string) ($row['id_contrato'] ?? ''));
        if ($idContrato !== '') {
            return 'id:' . strtoupper($idContrato);
        }
        $doc = trim((string) ($row['dni_cliente'] ?? ''));
        $fecha = trim((string) ($row['fecha_venta'] ?? $row['fecha_inicio_credito'] ?? ''));

        return 'doc:' . $doc . '|' . $fecha;
    }

    /**
     * @return list<string> DNI/RUC normalizado y, si es RUC de 11 dígitos, también los últimos 8 (como en el import Excel).
     */
    private function variantesDocumentoBusqueda(string $documento): array
    {
        $documento = preg_replace('/\D+/', '', $documento);
        if ($documento === '') {
            return [];
        }
        $out = [$documento];
        if (strlen($documento) === 11) {
            $last8 = substr($documento, -8);
            if ($last8 !== '' && !in_array($last8, $out, true)) {
                $out[] = $last8;
            }
        }

        return $out;
    }

    /**
     * Obtiene registro(s) de ventas vehiculares por DNI del cliente.
     * Puede retornar múltiples filas si el DNI tiene varias ventas.
     * Combina `registro_ventas_vehiculares` y tablas import_* (sin duplicar por chasis/motor).
     */
    private const IMPORT_CONTRATOS_TABLAS = [
        'import_contratos_enero_2026',
        'import_contratos_febrero_2026',
        'import_contratos_marzo_2026',
        'import_contratos_abril_2026',
        'import_contratos_mayo_2026',
    ];

    public function getRegistroVentasVehicularesByDni(string $dni): array
    {
        $variantes = $this->variantesDocumentoBusqueda($dni);
        if ($variantes === []) {
            return [];
        }

        $sql = "SELECT
                    id,
                    fecha_venta,
                    dni_cliente,
                    nombre_cliente,
                    aval,
                    dni_aval,
                    direccion,
                    modelo,
                    marca,
                    chasis,
                    motor,
                    color,
                    estado_tramite,
                    placa,
                    telefono_1,
                    telefono_2,
                    precio_total,
                    pago_inicial,
                    deudas_pendientes,
                    deudas_pendientes_detalle,
                    fecha_inicio_credito,
                    plazo_meses,
                    fecha_fin_credito,
                    cuota_base,
                    tasa_interes,
                    monto_interes,
                    cuota_total_mensual,
                    mora_3_dias,
                    total_con_mora,
                    numero_cuota_pagada
                FROM registro_ventas_vehiculares
                WHERE dni_cliente = :dni
                ORDER BY fecha_venta DESC, id DESC";

        try {
            $byKey = [];

            foreach ($variantes as $doc) {
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':dni' => $doc]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!is_array($rows)) {
                    continue;
                }
                foreach ($rows as $row) {
                    $row['fuente'] = 'registro_ventas_vehiculares';
                    $byKey[$this->registroVentasRowKey($row)] = $row;
                }
            }

            foreach (self::IMPORT_CONTRATOS_TABLAS as $tabla) {
                try {
                    foreach ($variantes as $doc) {
                        $sqlImp = "SELECT * FROM {$tabla} WHERE dni = :dni ORDER BY fecha_firma DESC, id DESC";
                        $stImp = $this->db->prepare($sqlImp);
                        $stImp->execute([':dni' => $doc]);
                        $imp = $stImp->fetchAll(PDO::FETCH_ASSOC);
                        if (!is_array($imp)) {
                            continue;
                        }
                        foreach ($imp as $row) {
                            $mapped = $this->mapImportContratoEneroRowToRegistroVentas($row);
                            $byKey[$this->registroVentasRowKey($mapped)] = $mapped;
                        }
                    }
                } catch (PDOException $e) {
                    // Tabla del mes aún no creada en este entorno
                    continue;
                }
            }

            if ($byKey === []) {
                return [];
            }

            $merged = array_values($byKey);
            usort($merged, static function (array $a, array $b): int {
                $fa = $a['fecha_venta'] ?? '';
                $fb = $b['fecha_venta'] ?? '';
                return strcmp((string) $fb, (string) $fa);
            });

            return $merged;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los contratos con sus datos financieros para caja
     * 
     * Ejecuta procedimiento almacenado que retorna el listado completo de
     * contratos activos con información detallada necesaria para operaciones
     * de caja: datos del cliente, vehículo, montos, saldos pendientes,
     * estado de pagos, y fechas relevantes.
     * 
     * Utiliza stored procedure: sp_getAll_contratos_caja
     * 
     * @return array Array de contratos con estructura definida por el SP:
     *   Estructura típica esperada:
     *   - idcontrato (int): ID del contrato
     *   - numero_contrato (string): Número de contrato
     *   - cliente_nombre (string): Nombre completo del cliente
     *   - cliente_documento (string): DNI/RUC del cliente
     *   - vehiculo_descripcion (string): Descripción del vehículo
     *   - monto_total (decimal): Monto total del contrato
     *   - saldo_pendiente (decimal): Saldo por cobrar
     *   - cuotas_pagadas (int): Número de cuotas pagadas
     *   - cuotas_totales (int): Total de cuotas del contrato
     *   - estado_pago (string): Estado actual (Al Día, Moroso, etc.)
     *   Retorna array vacío si no hay contratos o hay error
     */
    public function getAllContratosDatos(): array
    {
        $query = 'CALL sp_getAll_contratos_caja()';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            do {
                // vaciar result sets adicionales del CALL (evita lista vacía en algunos entornos MySQL/PDO)
            } while ($stmt->nextRowset());
            $stmt->closeCursor();

            return is_array($results) ? $results : [];
        } catch (PDOException $error) {
            error_log('getAllContratosDatos: ' . $error->getMessage());
            return [];
        }
    }

    public function getContratosCompletados(): ?array
    {
        $query = "CALL sp_get_contratos_completados()";
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





    public function getDatosClientePorCronograma(int $idCronograma): ?array
    {

        $sql = "
        SELECT 
            p.nrodoc, 
            CONCAT(p.nombres, ' ', p.apellidos) as razon_social,
            p.direccion,
            p.email,
            cli.idcliente
        FROM cronogramas cr
        INNER JOIN contratos c ON cr.idcontrato = c.idcontrato
        INNER JOIN cotizaciones cot ON c.idcotizacion = cot.idcotizacion
        INNER JOIN clientes cli ON cot.idcliente = cli.idcliente
        INNER JOIN personas p ON cli.idpersona = p.idpersona
        WHERE cr.idcronograma = :id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idCronograma]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve el cliente o null
    }


    public function obtenerNuevoCorrelativo(string $serie): int
    {

        $this->db->beginTransaction();

        try {


            $sqlSelectLock = "SELECT ultimo_numero FROM series_nubefact WHERE serie = :serie FOR UPDATE";
            $stmtSelectLock = $this->db->prepare($sqlSelectLock);
            $stmtSelectLock->execute([':serie' => $serie]);
            $resultado = $stmtSelectLock->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {

                $this->db->rollBack();
                return 1;
            }

            $nuevoNumero = (int)$resultado['ultimo_numero'] + 1;


            $sqlUpdate = "UPDATE series_nubefact SET ultimo_numero = :nuevoNumero WHERE serie = :serie";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $stmtUpdate->execute([':nuevoNumero' => $nuevoNumero, ':serie' => $serie]);
            $this->db->commit();

            return $nuevoNumero;
        } catch (\Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error al obtener correlativo: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Inicia una transacción y bloquea la serie para obtener
     * el siguiente correlativo SIN consumirlo todavía.
     */
    public function obtenerSiguienteCorrelativoBloqueado(string $serie): int
    {
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
        }

        $sqlSelect = "SELECT ultimo_numero FROM series_nubefact WHERE serie = :serie FOR UPDATE";
        $stmt = $this->db->prepare($sqlSelect);
        $stmt->execute([':serie' => $serie]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $sqlInsert = "INSERT INTO series_nubefact (serie, ultimo_numero) VALUES (:serie, 0)";
            $ins = $this->db->prepare($sqlInsert);
            $ins->execute([':serie' => $serie]);
            return 1;
        }

        return ((int) $row['ultimo_numero']) + 1;
    }

    /**
     * Confirma el correlativo reservado y cierra la transacción.
     */
    public function confirmarCorrelativoBloqueado(string $serie, int $numero): void
    {
        $sql = "UPDATE series_nubefact SET ultimo_numero = GREATEST(ultimo_numero, :num) WHERE serie = :serie";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':num' => $numero,
            ':serie' => $serie
        ]);

        if ($this->db->inTransaction()) {
            $this->db->commit();
        }
    }

    /**
     * Cancela reserva de correlativo si hubo error.
     */
    public function cancelarCorrelativoBloqueado(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }





    /**
     * Obtiene el cronograma de pagos completo de un contrato específico
     * 
     * Ejecuta procedimiento almacenado que retorna todas las cuotas programadas
     * de un contrato con su estado actual: número de cuota, fecha de vencimiento,
     * monto de la cuota, monto pagado, saldo pendiente, fecha de pago real,
     * días de atraso, y estado (Pendiente, Pagada, Vencida).
     * 
     * Utiliza stored procedure: sp_get_cronogramas_by_idcontrato
     * 
     * @param int $id ID del contrato
     * @return array Array de cuotas del cronograma con estructura:
     *   - numero_cuota (int): Número secuencial de cuota
     *   - fecha_vencimiento (date): Fecha programada de vencimiento
     *   - monto_cuota (decimal): Monto original de la cuota
     *   - monto_pagado (decimal): Monto efectivamente pagado
     *   - saldo_pendiente (decimal): Saldo que falta por pagar de la cuota
     *   - fecha_pago (date|null): Fecha real del pago (NULL si pendiente)
     *   - dias_atraso (int): Días transcurridos desde vencimiento
     *   - estado (string): Pendiente, Pagada, Vencida, Pagada con Mora
     *   - monto_mora (decimal): Monto adicional por mora (si aplica)
     *   Retorna array vacío si el contrato no existe o hay error
     */
    public function getCronogramaByIdContrato(int $id): array
    {
        $query = "CALL sp_get_cronogramas_by_idcontrato(:idcontrato)";

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

    /**
     * Obtiene el reporte completo de ingresos del día actual
     * 
     * Ejecuta procedimiento almacenado que genera un reporte detallado de todos
     * los ingresos registrados en caja durante el día actual (fecha del servidor).
     * Incluye pagos de cuotas, iniciales, gastos administrativos, y otros conceptos,
     * con información del cliente, contrato, método de pago, y monto.
     * 
     * Utiliza stored procedure: spu_caja_reporte_completo_hoy
     *
     * @return array
     */
    public function getReporteIngresosHoy(): array
    {
        $query = "CALL spu_caja_reporte_completo_hoy()";
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
     * Obtiene reporte de pagos por rango de fechas personalizado
     * 
     * Ejecuta procedimiento almacenado que genera un reporte detallado de todos
     * los pagos registrados entre dos fechas específicas. Permite análisis
     * periódicos (semanal, mensual, trimestral) con información completa de
     * cada transacción: cliente, contrato, concepto, método de pago, y montos.
     * 
     * Utiliza stored procedure: ObtenerReportePagosPorFechas
     *
     * @param string $fechaInicio Fecha de inicio del reporte
     * @param string $fechaFin Fecha de fin del reporte
     * @return array Array de pagos del período con estructura:
     *   - fecha_pago (date): Fecha del pago
     *   - hora_pago (time): Hora del pago
     *   - numero_recibo (string): Número de comprobante
     *   - cliente_nombre (string): Nombre del cliente
     *   - cliente_documento (string): DNI/RUC
     *   - numero_contrato (string): Referencia al contrato
     *   - concepto (string): Tipo de pago
     *   - metodo_pago (string): Forma de pago
     *   - monto (decimal): Monto del ingreso
     *   - usuario_caja (string): Usuario que registró
     *   - total_periodo (decimal): Suma total del período
     *   Retorna array vacío si no hay pagos en el período o hay error
     */
    public function getReporteByFecha($fechaInicio, $fechaFin)
    {
        $query = "CALL ObtenerReportePagosPorFechas(:fechainicio, :fechafin)";

        try {

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':fechainicio', $fechaInicio);
            $stmt->bindParam(':fechafin', $fechaFin);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultados;
        } catch (PDOException $error) {

            error_log("Error al llamar al procedimiento almacenado: " . $error->getMessage());
            return [];
        }
    }

    /**
     * Obtiene resumen ejecutivo de cartera morosa
     * 
     * Ejecuta procedimiento almacenado que genera un resumen consolidado de
     * la cartera morosa con información agregada por cliente: cantidad de
     * cuotas vencidas, días de atraso promedio, monto total adeudado,
     * última fecha de pago, y datos de contacto para gestión de cobranza.
     * 
     * Utiliza stored procedure: getMorososResumen
     *
     * @return array Array de clientes morosos con estructura:
     *   - idcliente (int): ID del cliente
     *   - cliente_nombre (string): Nombre completo del cliente
     *   - cliente_documento (string): DNI/RUC del cliente
     *   - cliente_telefono (string): Teléfono de contacto
     *   - cliente_email (string): Email del cliente
     *   - numero_contrato (string): Número de contrato
     *   - cuotas_vencidas (int): Cantidad de cuotas en mora
     *   - monto_total_mora (decimal): Total adeudado en mora
     *   - dias_atraso_maximo (int): Mayor atraso en días
     *   - dias_atraso_promedio (decimal): Promedio de días de atraso
     *   - ultimo_pago_fecha (date): Fecha del último pago registrado
     *   - ultimo_pago_monto (decimal): Monto del último pago
     *   - asesor_nombre (string): Asesor responsable
     *   Retorna array vacío si no hay morosos o hay error
     */
    public function getMorososResumen(): array
    {
        $sql = "CALL getMorososResumen()";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getMorososResumen: ' . $e->getMessage());
            return [];  // En caso de error, devolvemos un array vacío
        }
    }


    public function getConceptosPagos(): array
    {

        $sql = "SELECT * FROM conceptospago WHERE concepto NOT IN ('Contado', 'Inicial')";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            error_log('Error en getConceptosPagos: ' . $e->getMessage());
            return [];
        }
    }

    public function getClienteByDni(string $dni): ?array
    {
        $sql = "CALL sp_getClienteBy_DNI(:dni)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':dni', $dni);
            $stmt->execute();

            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $cliente ?: null;
        } catch (PDOException $e) {
            error_log("Error al buscar cliente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crea/activa cliente desde registro_ventas_vehiculares para evitar cargas manuales DNI por DNI.
     * Devuelve true si pudo crear/activar o si ya existía.
     */
    public function sincronizarClienteDesdeRegistroVentasByDni(string $dni): bool
    {
        try {
            $rows = $this->getRegistroVentasVehicularesByDni($dni);
            if (empty($rows)) {
                return false;
            }

            $r = $rows[0];
            $nombreCompleto = trim((string) ($r['nombre_cliente'] ?? ''));
            if ($nombreCompleto === '') {
                $nombreCompleto = 'CLIENTE SIN NOMBRE';
            }

            $partes = preg_split('/\s+/', $nombreCompleto) ?: [];
            $nombres = '';
            $apellidos = '';
            if (count($partes) >= 3) {
                $apellidos = trim($partes[count($partes) - 2] . ' ' . $partes[count($partes) - 1]);
                $nombres = trim(implode(' ', array_slice($partes, 0, -2)));
            } elseif (count($partes) === 2) {
                $nombres = trim($partes[0]);
                $apellidos = trim($partes[1]);
            } elseif (count($partes) === 1) {
                $nombres = trim($partes[0]);
                $apellidos = '-';
            } else {
                $nombres = 'SIN NOMBRE';
                $apellidos = '-';
            }
            if ($nombres === '') $nombres = 'SIN NOMBRE';
            if ($apellidos === '') $apellidos = '-';

            $telefonoRaw = (string) ($r['telefono_1'] ?? '');
            $telefono = preg_replace('/\D+/', '', $telefonoRaw ?? '');
            if (!is_string($telefono) || strlen($telefono) < 9) {
                $telefono = '999999999';
            } elseif (strlen($telefono) > 9) {
                $telefono = substr($telefono, -9);
            }

            $direccion = trim((string) ($r['direccion'] ?? ''));
            if ($direccion === '') {
                $direccion = 'SIN DIRECCION';
            }

            $this->db->beginTransaction();

            $sqlPersona = "SELECT idpersona FROM personas WHERE tipodoc = 'DNI' AND nrodoc = :dni LIMIT 1";
            $stmtP = $this->db->prepare($sqlPersona);
            $stmtP->execute([':dni' => $dni]);
            $persona = $stmtP->fetch(PDO::FETCH_ASSOC);

            if (!$persona) {
                $sqlInsertPersona = "INSERT INTO personas (iddistrito, apellidos, nombres, tipodoc, nrodoc, genero, fechanac, estadocivil, email, direccion, referencia, latitud, longitud, telprimario, telalternativo)
                                    VALUES (NULL, :apellidos, :nombres, 'DNI', :dni, 'M', NULL, NULL, NULL, :direccion, NULL, NULL, NULL, :telprimario, NULL)";
                $insP = $this->db->prepare($sqlInsertPersona);
                $insP->execute([
                    ':apellidos' => mb_substr($apellidos, 0, 70),
                    ':nombres' => mb_substr($nombres, 0, 70),
                    ':dni' => $dni,
                    ':direccion' => mb_substr($direccion, 0, 200),
                    ':telprimario' => $telefono
                ]);
                $idPersona = (int) $this->db->lastInsertId();
            } else {
                $idPersona = (int) ($persona['idpersona'] ?? 0);
                $sqlUpdatePersona = "UPDATE personas
                                     SET apellidos = COALESCE(NULLIF(apellidos, ''), :apellidos),
                                         nombres = COALESCE(NULLIF(nombres, ''), :nombres),
                                         direccion = COALESCE(NULLIF(direccion, ''), :direccion),
                                         telprimario = CASE WHEN telprimario IS NULL OR telprimario = '' THEN :telprimario ELSE telprimario END
                                     WHERE idpersona = :idpersona";
                $upP = $this->db->prepare($sqlUpdatePersona);
                $upP->execute([
                    ':apellidos' => mb_substr($apellidos, 0, 70),
                    ':nombres' => mb_substr($nombres, 0, 70),
                    ':direccion' => mb_substr($direccion, 0, 200),
                    ':telprimario' => $telefono,
                    ':idpersona' => $idPersona
                ]);
            }

            if ($idPersona <= 0) {
                throw new \RuntimeException('No se pudo obtener idpersona para sincronización.');
            }

            $sqlCliente = "SELECT idcliente, estado FROM clientes WHERE idpersona = :idpersona LIMIT 1";
            $stmtC = $this->db->prepare($sqlCliente);
            $stmtC->execute([':idpersona' => $idPersona]);
            $cliente = $stmtC->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                $sqlInsertCliente = "INSERT INTO clientes (idpersona, idempresa, idcolregistra, idcolactualiza, tipocliente, estado)
                                     VALUES (:idpersona, NULL, NULL, NULL, 'P', 'ACT')";
                $insC = $this->db->prepare($sqlInsertCliente);
                $insC->execute([':idpersona' => $idPersona]);
            } else {
                if (($cliente['estado'] ?? '') !== 'ACT') {
                    $sqlAct = "UPDATE clientes SET estado = 'ACT', idempresa = NULL, tipocliente = 'P' WHERE idcliente = :idcliente";
                    $upC = $this->db->prepare($sqlAct);
                    $upC->execute([':idcliente' => (int) $cliente['idcliente']]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('sincronizarClienteDesdeRegistroVentasByDni: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ID del concepto reservado para líneas manuales en Caja (evita forzar idconcepto=1).
     * Requiere fila creada con datos-inserts/caja_concepto_varios.sql
     */
    public function getIdConceptoVariosCaja(): int
    {
        $sql = "SELECT idconcepto FROM conceptospago WHERE concepto = 'Varios caja (manual)' LIMIT 1";
        try {
            $stmt = $this->db->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int) $row['idconcepto'] : 0;
        } catch (PDOException $e) {
            error_log('getIdConceptoVariosCaja: ' . $e->getMessage());
            return 0;
        }
    }










    public function registrarPagoCompuesto(int $idCliente, int $idColCaja, string $medioPago, ?string $numTransaccion, ?int $idCuentaPago, float $amortizacion, string $detallesJson): int
    {


        $query = "CALL sp_registrar_pago_compuesto(:idcliente, :idcolcaja, :mediopago, :numerotransaccion, :idcuentapago, :amortizacion, :detalles)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idcliente' => $idCliente,
                ':idcolcaja' => $idColCaja,
                ':mediopago' => $medioPago,
                ':numerotransaccion' => $numTransaccion,
                ':idcuentapago' => $idCuentaPago,
                ':amortizacion' => $amortizacion,
                ':detalles' => $detallesJson
            ]);

            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return (int) ($res['id_pago_generado'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error DB: " . $e->getMessage());
            return 0;
        }
    }

    public function getPagoById(int $idPago): ?array
    {
        $sql = "SELECT idpago, enlace_pdf_nubefact, enlace_xml_nubefact, enlace_del_cdr, numero_boleta_sunat FROM pagos WHERE idpago = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $idPago]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('getPagoById: ' . $e->getMessage());
            return null;
        }
    }

    public function getIdempotenciaByRequestId(string $requestId): ?array
    {
        $sql = "SELECT request_id, estado, idpago FROM pagos_idempotencia WHERE request_id = :request_id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':request_id' => $requestId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('getIdempotenciaByRequestId: ' . $e->getMessage());
            return null;
        }
    }

    public function crearIdempotenciaEnProceso(string $requestId, int $idCliente, float $montoTotal, string $payloadHash): bool
    {
        $sql = "INSERT INTO pagos_idempotencia (request_id, idcliente, monto_total, payload_hash, estado) VALUES (:request_id, :idcliente, :monto_total, :payload_hash, 'PROCESSING')";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':request_id' => $requestId,
                ':idcliente' => $idCliente,
                ':monto_total' => $montoTotal,
                ':payload_hash' => $payloadHash
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function completarIdempotencia(string $requestId, int $idPago): void
    {
        $sql = "UPDATE pagos_idempotencia SET estado = 'COMPLETED', idpago = :idpago, modificado = NOW() WHERE request_id = :request_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':idpago' => $idPago,
                ':request_id' => $requestId
            ]);
        } catch (PDOException $e) {
            error_log('completarIdempotencia: ' . $e->getMessage());
        }
    }

    public function fallarIdempotencia(string $requestId, string $mensaje): void
    {
        $sql = "UPDATE pagos_idempotencia SET estado = 'FAILED', mensaje = :mensaje, modificado = NOW() WHERE request_id = :request_id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':mensaje' => $mensaje,
                ':request_id' => $requestId
            ]);
        } catch (PDOException $e) {
            error_log('fallarIdempotencia: ' . $e->getMessage());
        }
    }


    // 2. Actualizar datos de Nubefact tras éxito
     public function actualizarDatosFacturacion(
         int $idPago,
         string $pdf,
         string $xml,
         ?string $cdr,
         int $numeroBoleta,
         ?string $serieComprobante = null,
         ?int $tipoNubefact = null
     ): bool {
         $sql = "UPDATE pagos SET 
                     enlace_pdf_nubefact = :pdf,
                     enlace_xml_nubefact = :xml,
                     enlace_del_cdr = :cdr,
                     numero_boleta_sunat = :num,
                     declarado = 'S'";

         $params = [
             ':pdf' => $pdf,
             ':xml' => $xml,
             ':cdr' => $cdr,
             ':num' => $numeroBoleta,
             ':idpago' => $idPago,
         ];

         if ($serieComprobante !== null && $serieComprobante !== '') {
             $sql .= ", comprobante_serie = :serie";
             $params[':serie'] = $serieComprobante;
         }
         if ($tipoNubefact !== null && ($tipoNubefact === 1 || $tipoNubefact === 2)) {
             $sql .= ", comprobante_tipo_nubefact = :tipo_nubefact";
             $params[':tipo_nubefact'] = $tipoNubefact;
         }

         $sql .= " WHERE idpago = :idpago";

         try {
             $stmt = $this->db->prepare($sql);
             return $stmt->execute($params);
         } catch (PDOException $e) {
             // Columnas nuevas aún no aplicadas en Hostinger: actualizar sin serie/tipo
             if ($serieComprobante !== null || $tipoNubefact !== null) {
                 return $this->actualizarDatosFacturacion($idPago, $pdf, $xml, $cdr, $numeroBoleta);
             }
             error_log('actualizarDatosFacturacion: ' . $e->getMessage());
             return false;
         }
     }

     public function actualizarCdrFacturacion(int $idPago, ?string $cdr): bool
     {
         $sql = "UPDATE pagos SET enlace_del_cdr = :cdr WHERE idpago = :idpago";
         try {
             $stmt = $this->db->prepare($sql);
             return $stmt->execute([
                 ':cdr' => $cdr,
                 ':idpago' => $idPago,
             ]);
         } catch (PDOException $e) {
             error_log('actualizarCdrFacturacion: ' . $e->getMessage());
             return false;
         }
     }



    public function getDatosCliente(int $idCliente): ?array
    {
        $sql = "
            SELECT
                cli.idcliente,
                CASE
                    WHEN cli.tipocliente = 'P' THEN p.nrodoc
                    ELSE e.ruc
                END AS nrodoc,
                CASE
                    WHEN cli.tipocliente = 'P' THEN CONCAT(p.nombres, ' ', p.apellidos)
                    ELSE e.razonsocial
                END AS razon_social,
                CASE
                    WHEN cli.tipocliente = 'P' THEN p.direccion
                    ELSE e.direccion
                END AS direccion,
                CASE
                    WHEN cli.tipocliente = 'P' THEN p.email
                    ELSE e.email
                END AS email
            FROM clientes cli
            LEFT JOIN personas p ON cli.idpersona = p.idpersona AND cli.tipocliente = 'P'
            LEFT JOIN empresas e ON cli.idempresa = e.idempresa AND cli.tipocliente = 'E'
            WHERE cli.idcliente = :id AND cli.estado = 'ACT'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idCliente]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }


    public function getNumCuentaPagoById(int $id): ?array

    {
        $query = " SELECT 
                        cp.idcuentapago,
                        cp.tipo_cuenta,
                        cp.numcuenta,
                        cp.cuenta_corriente,
                        ep.entidad,
                        CONCAT(
                            ep.entidad,
                            IF(cp.tipo_cuenta = 'CCI', ' - CCI ', ' - '),
                            cp.numcuenta,
                            IF(cp.cuenta_corriente IS NOT NULL AND cp.cuenta_corriente != '',
                               CONCAT(' (CTA ', cp.cuenta_corriente, ')'), '')
                        ) AS nombrecuenta
                    FROM 
                        cuentaspago cp
                    JOIN 
                        entidadespago ep ON cp.identidadpago = ep.identidadpago
                        WHERE cp.idcuentapago = :id
                    ";
        try {

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Contratos ACT de un cliente (para caja: localizar cuotas sin depender solo del listado general).
     */
    public function getContratosActivosPorIdCliente(int $idcliente): array
    {
        $sql = "
            SELECT
                c.idcontrato,
                cot.idcotizacion,
                TRIM(CONCAT(
                    IFNULL(ma.marca, ''), ' / ',
                    IFNULL(mo.modelo, ''),
                    IF(mo.anio IS NOT NULL AND mo.anio > 0, CONCAT(' / ', mo.anio), '')
                )) AS vehiculo_resumen
            FROM contratos c
            INNER JOIN cotizaciones cot ON cot.idcotizacion = c.idcotizacion
            LEFT JOIN vehiculos v ON v.idvehiculo = cot.idvehiculo
            LEFT JOIN modelos mo ON mo.idmodelo = v.idmodelo
            LEFT JOIN marcas ma ON ma.idmarca = mo.idmarca
            WHERE cot.idcliente = :idcliente AND c.estado = 'ACT'
            ORDER BY c.idcontrato DESC
        ";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idcliente' => $idcliente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getContratosActivosPorIdCliente: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el monto sugerido de un concepto por nombre (ej: "GPS").
     */
    public function getMontoConceptoPagoByNombre(string $nombreConcepto): float
    {
        $sql = "SELECT montosugerido FROM conceptospago WHERE UPPER(concepto) = UPPER(:c) LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':c' => $nombreConcepto]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $m = $row ? (float) ($row['montosugerido'] ?? 0) : 0.0;
            return $m > 0 ? $m : 0.0;
        } catch (PDOException $e) {
            error_log('getMontoConceptoPagoByNombre: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Contratos de un cliente (sin filtrar por estado).
     * Útil para mostrar cronograma incluso si el contrato está FIN/INACT.
     */
    public function getContratosPorIdCliente(int $idcliente): array
    {
        $sql = "
            SELECT
                c.idcontrato,
                c.estado,
                cot.idcotizacion,
                TRIM(CONCAT(
                    IFNULL(ma.marca, ''), ' / ',
                    IFNULL(mo.modelo, ''),
                    IF(mo.anio IS NOT NULL AND mo.anio > 0, CONCAT(' / ', mo.anio), '')
                )) AS vehiculo_resumen
            FROM contratos c
            INNER JOIN cotizaciones cot ON cot.idcotizacion = c.idcotizacion
            LEFT JOIN vehiculos v ON v.idvehiculo = cot.idvehiculo
            LEFT JOIN modelos mo ON mo.idmodelo = v.idmodelo
            LEFT JOIN marcas ma ON ma.idmarca = mo.idmarca
            WHERE cot.idcliente = :idcliente
            ORDER BY c.idcontrato DESC
        ";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idcliente' => $idcliente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getContratosPorIdCliente: ' . $e->getMessage());
            return [];
        }
    }

    public function getIdsContratosPorCliente(int $idcliente): array
    {
        $sql = "SELECT idcontrato FROM contratos c INNER JOIN cotizaciones cot ON cot.idcotizacion = c.idcotizacion WHERE cot.idcliente = :idcliente";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idcliente' => $idcliente]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(static fn($r) => (int) ($r['idcontrato'] ?? 0), $rows ?: []);
        } catch (PDOException $e) {
            error_log('getIdsContratosPorCliente: ' . $e->getMessage());
            return [];
        }
    }

    public function marcarCuotaPagadaRegistroVentas(string $dni, int $numeroCuota, int $idPago): bool
    {
        if ($numeroCuota < 1) {
            return false;
        }

        $sql = "UPDATE registro_ventas_vehiculares
                SET numero_cuota_pagada = GREATEST(COALESCE(numero_cuota_pagada, 0), :cuota),
                    deudas_pendientes_detalle = CONCAT_WS(' | ',
                        NULLIF(TRIM(COALESCE(deudas_pendientes_detalle, '')), ''),
                        CONCAT('Cuota ', :cuota, ' registrada en caja (pago #', :idpago, ')')
                    )
                WHERE dni_cliente = :dni
                ORDER BY fecha_venta DESC, id DESC
                LIMIT 1";

        $ok = false;
        foreach ($this->variantesDocumentoBusqueda($dni) as $doc) {
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':cuota' => $numeroCuota,
                    ':idpago' => $idPago,
                    ':dni' => $doc,
                ]);
                if ($stmt->rowCount() > 0) {
                    $ok = true;
                }
            } catch (PDOException $e) {
                error_log('marcarCuotaPagadaRegistroVentas: ' . $e->getMessage());
            }
        }

        return $ok;
    }

    /**
     * Marca fecha de pago en tablas import_contratos_* (cuotas 1-5 del Excel).
     */
    public function marcarCuotaPagadaImportContratos(string $documento, int $numeroCuota, int $idPago, ?string $chasis = null): bool
    {
        if ($numeroCuota < 1 || $numeroCuota > 5) {
            return false;
        }

        $target = $this->resolverFilaImportContrato($documento, $chasis);
        if ($target === null) {
            return false;
        }

        $tabla = $target['tabla'];
        $id = (int) $target['id'];
        $fechaCol = 'fecha_pago_' . $numeroCuota;
        $deudasCol = 'deudas_' . $numeroCuota;
        $nota = "Cuota {$numeroCuota} registrada en caja (pago #{$idPago})";

        $sql = "UPDATE {$tabla}
                SET {$fechaCol} = COALESCE({$fechaCol}, CURDATE()),
                    {$deudasCol} = CONCAT_WS(' | ',
                        NULLIF(TRIM(COALESCE({$deudasCol}, '')), ''),
                        :nota
                    )
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nota' => $nota,
                ':id' => $id,
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('marcarCuotaPagadaImportContratos: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza registro oficial y/o import mensual tras un cobro de cuota en caja.
     */
    public function marcarCuotaPagadaVentasPorDocumento(
        string $documento,
        int $numeroCuota,
        int $idPago,
        ?string $chasis = null
    ): bool {
        $okReg = $this->marcarCuotaPagadaRegistroVentas($documento, $numeroCuota, $idPago);
        $okImp = $this->marcarCuotaPagadaImportContratos($documento, $numeroCuota, $idPago, $chasis);

        return $okReg || $okImp;
    }

    /**
     * @return array{tabla: string, id: int}|null
     */
    private function resolverFilaImportContrato(string $documento, ?string $chasis = null): ?array
    {
        $variantes = $this->variantesDocumentoBusqueda($documento);
        if ($variantes === []) {
            return null;
        }

        $chasisKey = $chasis !== null && trim($chasis) !== ''
            ? strtoupper(trim($chasis))
            : null;

        $candidatos = [];
        foreach (self::IMPORT_CONTRATOS_TABLAS as $tabla) {
            try {
                foreach ($variantes as $doc) {
                    $sql = "SELECT id, chasis, fecha_firma FROM {$tabla} WHERE dni = :dni ORDER BY fecha_firma DESC, id DESC";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([':dni' => $doc]);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!is_array($rows)) {
                        continue;
                    }
                    foreach ($rows as $row) {
                        $candidatos[] = [
                            'tabla' => $tabla,
                            'id' => (int) ($row['id'] ?? 0),
                            'chasis' => strtoupper(trim((string) ($row['chasis'] ?? ''))),
                            'fecha_firma' => (string) ($row['fecha_firma'] ?? ''),
                        ];
                    }
                }
            } catch (PDOException $e) {
                continue;
            }
        }

        if ($candidatos === []) {
            return null;
        }

        if ($chasisKey !== null) {
            foreach ($candidatos as $c) {
                if ($c['chasis'] !== '' && $c['chasis'] === $chasisKey) {
                    return ['tabla' => $c['tabla'], 'id' => $c['id']];
                }
            }
        }

        usort($candidatos, static function (array $a, array $b): int {
            return strcmp((string) ($b['fecha_firma'] ?? ''), (string) ($a['fecha_firma'] ?? ''));
        });

        $first = $candidatos[0];

        return $first['id'] > 0 ? ['tabla' => $first['tabla'], 'id' => $first['id']] : null;
    }
}
