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
use Error;
use League\Csv\Serializer\CastToArray;
use PDO;
use PDOException;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use Twig\Node\Expression\FunctionExpression;

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
    public function getAllContratosDatos(): ?array
    {
        $query = "CALL sp_getAll_contratos_caja()";
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

            error_log('Error en getMorososResumen: ' . $e->getMessage());
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


    // 2. Actualizar datos de Nubefact tras éxito
     public function actualizarDatosFacturacion(int $idPago, string $pdf, string $xml, ?string $cdr, int $numeroBoleta): bool
     {
         $sql = "UPDATE pagos SET 
                     enlace_pdf_nubefact = :pdf,
                     enlace_xml_nubefact = :xml,
                     enlace_del_cdr = :cdr,
                     numero_boleta_sunat = :num,
                     declarado = 'S'
                 WHERE idpago = :idpago";

         $stmt = $this->db->prepare($sql);
         return $stmt->execute([
             ':pdf' => $pdf,
             ':xml' => $xml,
             ':cdr' => $cdr,
             ':num' => $numeroBoleta,
             ':idpago' => $idPago
         ]);
     }



    public function getDatosCliente(int $idCliente): ?array
    {

        $sql = "
    
            SELECT
                cli.idcliente,
                p.nrodoc,
                CONCAT(p.nombres, ' ', p.apellidos) AS razon_social,
                p.direccion,
                p.email
            FROM clientes cli
            JOIN personas p ON cli.idpersona = p.idpersona
            WHERE cli.idcliente = :id AND cli.tipocliente = 'P' AND cli.estado = 'ACT'; 
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idCliente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getNumCuentaPagoById(int $id): ?array

    {
        $query = " SELECT 
                        cp.idcuentapago,
                        CONCAT(ep.entidad, ' - ', cp.numcuenta) AS nombrecuenta
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
}
