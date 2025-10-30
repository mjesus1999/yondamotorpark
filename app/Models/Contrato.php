<?php

/**
 * Modelo de Contrato de Venta de Vehículos
 * 
 * Gestiona las operaciones de base de datos relacionadas con los contratos
 * de venta de vehículos, incluyendo creación de contratos con generación
 * automática de cronogramas de pago, cálculos financieros con interés compuesto,
 * actualización de estados de cotizaciones y vehículos, y consultas para
 * generación de documentos PDF.
 * 
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;
use DateTime;
use DateInterval;
use PDOException;

/**
 * Clase Contrato
 * 
 * Modelo que representa y gestiona los contratos de venta de vehículos en el sistema.
 * Implementa transacciones para garantizar integridad en la creación de contratos,
 * genera cronogramas de pago con cálculos de interés compuesto, y mantiene
 * sincronizados los estados de cotizaciones y vehículos.
 * 
 */
class Contrato
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
     * Obtiene el listado completo de contratos activos
     * 
     * Recupera todos los contratos en estado activo ('ACT') con cotizaciones
     * contratadas ('CONT'). Incluye información relacionada de cliente (persona
     * o empresa), vehículo, asesor, local y términos financieros.
     * 
     * @return array Array de contratos con todos sus datos relacionados, vacío en caso de error
     */
    public function getAll(): array
    {
        $query = "
            SELECT
                c.idcontrato,
                DATE_FORMAT(c.fechainicio, '%d-%m-%Y') AS fechainicio,
                c.diapago,
                c.observaciones,
                CONCAT(dep.departamento, ' / ', pro.provincia, ' / ', dist.distrito) AS tienda,
                CONCAT(ma.marca, ' / ', m.modelo, ' / ', v.version, ' / ', v.color, ' / ', m.anio) AS vehiculo,
                CASE
                    WHEN cli.tipocliente = 'P' THEN CONCAT(p.apellidos, ', ', p.nombres)
                    ELSE e.razonsocial
                END AS cliente,
                CASE
                    WHEN cli.tipocliente = 'P' THEN p.nrodoc
                    ELSE e.ruc
                END AS doc_cliente,
                CONCAT(per.apellidos, ', ', per.nombres) AS asesor,
                cot.moneda,
                cot.precioventa,
                cot.inicial,
                cot.valorcuota,
                cot.numcuotas
            FROM contratos AS c
                INNER JOIN cotizaciones AS cot ON c.idcotizacion = cot.idcotizacion
                INNER JOIN locales AS l ON c.idlocal = l.idlocal
                INNER JOIN distritos AS dist ON l.iddistrito = dist.iddistrito
                INNER JOIN provincias AS pro ON dist.idprovincia = pro.idprovincia
                INNER JOIN departamentos AS dep ON pro.iddepartamento = dep.iddepartamento
                INNER JOIN clientes AS cli ON cot.idcliente = cli.idcliente
                LEFT JOIN personas AS p ON cli.idpersona = p.idpersona
                LEFT JOIN empresas AS e ON cli.idempresa = e.idempresa
                LEFT JOIN colaboradores AS col ON cot.idasesor = col.idcolaborador
                LEFT JOIN contratoslaborales AS clab ON col.idcontratolaboral = clab.idcontratolaboral
                LEFT JOIN personas AS per ON clab.idpersona = per.idpersona
                LEFT JOIN vehiculos AS v ON cot.idvehiculo = v.idvehiculo
                LEFT JOIN modelos AS m ON v.idmodelo = m.idmodelo
                LEFT JOIN marcas AS ma ON m.idmarca = ma.idmarca
           WHERE c.estado = 'ACT' AND cot.estadocotizacion IN ('CONT')
            ORDER BY c.idcontrato DESC;

        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene datos completos de un contrato para generación de PDF
     * 
     * Ejecuta procedimiento almacenado que recupera toda la información necesaria
     * para generar documentos PDF del contrato, incluyendo datos estructurados
     * de cliente, cónyuge, aval (con su cónyuge), vehículo y financiamiento.
     * 
     * Utiliza stored procedure: sp_contrato_pdf
     * 
     * @param int $idcontrato ID del contrato a consultar
     * @return array|bool Array con datos del contrato, array vacío si no existe, false en caso de error de conexión
     */
    public function getDataPDFContrato(int $idcontrato): array|false
    {
        $query = "CALL sp_contrato_pdf(:idcontrato)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idcontrato', $idcontrato, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si no hay filas, devolver array vacío (no error)
            return $result ?: [];
        } catch (PDOException $e) {
            error_log("Error en getDataPDFContrato: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Desactiva (elimina lógicamente) un contrato
     * 
     * Realiza eliminación lógica cambiando el estado del contrato a 'INACT'.
     * No elimina físicamente el registro, permitiendo mantener historial
     * y auditoría de contratos cancelados o anulados.
     *
     * @param int $id ID del contrato a desactivar
     * @return int Número de filas afectadas (1 si exitoso, 0 si no existe o error)
     */
    public function disabledContrato(int $id): int
    {
        try {
            $stmt = $this->db->prepare("
            UPDATE contratos
            SET estado = 'INACT'
            WHERE idcontrato = :idcontrato
        ");
            $stmt->bindValue(":idcontrato", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error al desactivar contrato: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Crea un nuevo registro de contrato en la base de datos
     * 
     * Inserta el registro principal del contrato vinculando la cotización aprobada
     * con un local específico. Asigna automáticamente el usuario logístico desde
     * la sesión actual para control de responsable del proceso.
     *
     * @param array $data Datos del contrato
     *                          - idlocal (int): ID del local/sede
     *                          - idcotizacion (int): ID de la cotización base
     *                          - fechainicio (string): Fecha de inicio del contrato (Y-m-d)
     *                          - diapago (int): Día del mes para pago de cuotas
     *                               - fecharevision (string|null): Fecha de revisión del contrato
     *                          - observaciones (string|null): Notas adicionales
     * @return int ID del contrato creado
     */
    private function createContrato(array $data): int
    {
        $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

        $query = "INSERT INTO contratos (idlocal, idlogistica, idcotizacion, fechainicio, diapago, fecharevision, observaciones) 
                  VALUES (:idlocal, :idlogistica, :idcotizacion, :fechainicio, :diapago, :fecharevision, :observaciones)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idlocal', $data['idlocal'], PDO::PARAM_INT);
        $stmt->bindParam(':idlogistica', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idcotizacion', $data['idcotizacion'], PDO::PARAM_INT);
        $stmt->bindParam(':fechainicio', $data['fechainicio']);
        $stmt->bindParam(':diapago', $data['diapago'], PDO::PARAM_INT);
        $stmt->bindParam(':fecharevision', $data['fecharevision']);
        $stmt->bindParam(':observaciones', $data['observaciones']);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    /**
     * Inserta el cronograma de pagos de un contrato
     * 
     * Registra todas las cuotas del cronograma generado en la tabla cronogramas.
     * Cada cuota incluye fecha de pago, interés, abono a capital, número de cuota
     * y saldo pendiente. Convierte fechas de formato d/m/Y a Y-m-d para BD.
     *
     * @param int $idcontrato ID del contrato al que pertenece el cronograma
     * @param array $cronograma Array de cuotas generado por generarCronograma()
     * @return bool true si la inserción fue exitosa
     */
    private function insertCronograma(int $idcontrato, array $cronograma): bool
    {
        $query = "INSERT INTO cronogramas (idcontrato, fechapago, interes, abonocapital, numcuota, saldocapital) 
                  VALUES (:idcontrato, :fechapago, :interes, :abonocapital, :numcuota, :saldocapital)";
        $stmt = $this->db->prepare($query);

        foreach ($cronograma as $cuota) {
            $stmt->bindValue(':idcontrato', $idcontrato, PDO::PARAM_INT);
            $fechaDB = DateTime::createFromFormat('d/m/Y', $cuota['fecha_pago'])->format('Y-m-d');
            $stmt->bindValue(':fechapago', $fechaDB);
            $stmt->bindValue(':interes', $cuota['interes']);
            $stmt->bindValue(':abonocapital', $cuota['abono_capital']);
            $stmt->bindValue(':numcuota', $cuota['item'], PDO::PARAM_INT);
            $stmt->bindValue(':saldocapital', $cuota['saldo_capital']);
            $stmt->execute();
        }
        return true;
    }

    /**
     * Actualiza el estado de una cotización a 'CONT' (Contratado)
     * 
     * Marca la cotización como contratada una vez que se ha creado exitosamente
     * el contrato correspondiente. Previene que la misma cotización sea usada
     * para crear múltiples contratos.
     *
     * @param int $idcotizacion ID de la cotización a actualizar
     * @return void
     */
    private function updateCotizacionEstado(int $idcotizacion): void
    {
        $sql = "UPDATE cotizaciones SET estadocotizacion = 'CONT' WHERE idcotizacion = :idcotizacion";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idcotizacion', $idcotizacion, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Actualiza el estado de disponibilidad del vehículo a 'vendido'
     * 
     * Marca el vehículo asociado a una cotización como vendido, actualizando
     * también la fecha de modificación. Previene que el mismo vehículo sea
     * vendido o cotizado múltiples veces.
     *
     * @param int $idcotizacion ID de la cotización que contiene el vehículo
     * @return void
     */
    public function updateVehiculoEstadoVendido(int $idcotizacion): void
    {

        $sql = "UPDATE vehiculos
                SET disponibilidad = 'vendido', modificado = NOW()
                WHERE idvehiculo = (
                    SELECT idvehiculo 
                    FROM cotizaciones 
                    WHERE idcotizacion = :idcotizacion
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idcotizacion', $idcotizacion, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Crea un contrato completo con cronograma de pagos en una transacción
     * 
     * Método principal que ejecuta el proceso completo de creación de contrato
     * mediante una transacción de base de datos. Garantiza integridad mediante
     * rollback automático si falla cualquier paso.
     *
     * @param array $contractData Datos del contrato (ver createContrato())
     * @param array $cotizacionData Datos de la cotización para cálculos
     *                                  - precioventa (float): Precio total del vehículo
     *                                  - inicial (float): Monto del pago inicial
     *                                  - numcuotas (int): Número de cuotas del financiamiento
     * @return int ID del contrato creado, 0 en caso de error
     */
    public function createContratoYCronograma(array $contractData, array $cotizacionData): int
    {
        $this->db->beginTransaction();
        try {
            // Crear Contrato
            $idcontrato = $this->createContrato($contractData);

            //  Generar Cronograma
            $cronograma = $this->generarCronograma(
                $cotizacionData['precioventa'],
                $cotizacionData['inicial'],
                $cotizacionData['numcuotas'],
                $contractData['diapago'],
                $contractData['fechainicio']
            );

            // Insertar Cronograma
            $this->insertCronograma($idcontrato, $cronograma);

            // Actualizar Cotización → estado = 'CONT'
            $this->updateCotizacionEstado($contractData['idcotizacion']);

            // Confirmar
            $this->db->commit();
            return $idcontrato;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error de transacción al crear contrato: " . $e->getMessage());
            return 0;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error de lógica al crear contrato: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Genera el cronograma de pagos con interés compuesto
     * 
     * Calcula el cronograma completo de pagos aplicando fórmula de anualidad
     * con interés compuesto. Cada cuota tiene valor fijo, pero la distribución
     * entre interés y capital varía: al inicio se paga más interés, al final
     * más capital.
     *
     * @param float $importeTotal Precio total del vehículo
     * @param float $inicial Monto del pago inicial
     * @param int $meses Número de cuotas del financiamiento
     * @param int $diaPago Día del mes para fecha de pago (1-31)
     * @param string $fechaInicio Fecha de inicio del contrato (Y-m-d)
     * @return array <array|array{abono_capital: float, fecha_pago: string, interes: float, item: int, saldo_capital: float, valor_cuota: float>}
     */
    private function generarCronograma(float $importeTotal, float $inicial, int $meses, int $diaPago, string $fechaInicio): array
    {
        $tasaAnual = 0.65;
        $tasaMensual = pow((1 + $tasaAnual), (1 / 12)) - 1;
        $montoFinanciar = $importeTotal - $inicial;
        $valorCuota = round($this->Pago($tasaMensual, $meses, $montoFinanciar), 2);

        $cronograma = [];
        $saldoCapital = $montoFinanciar;
        $fechaPago = new DateTime($fechaInicio);

        for ($i = 1; $i <= $meses; $i++) {
            $interesExacto = $saldoCapital * $tasaMensual;
            $interes = round($interesExacto, 2);
            $abonoCapital = $valorCuota - $interes;

            if ($i === $meses) {
                $abonoCapital = $saldoCapital;
                $interes = $valorCuota - $abonoCapital;
            }

            $saldoCapital -= $abonoCapital;
            $saldoCapital = round($saldoCapital, 2);

            $fechaPago->add(new DateInterval('P1M'));
            $fechaPago->setDate(
                (int) $fechaPago->format('Y'),
                (int) $fechaPago->format('m'),
                $diaPago
            );

            $cronograma[] = [
                'item' => $i,
                'fecha_pago' => $fechaPago->format('d/m/Y'),
                'interes' => $interes,
                'abono_capital' => $abonoCapital,
                'valor_cuota' => $valorCuota,
                'saldo_capital' => $saldoCapital,
            ];
        }

        return $cronograma;
    }

    /**
     * Calcula el valor de la cuota fija usando la fórmula de anualidad
     * 
     * Implementa la fórmula financiera de anualidad para
     * calcular el pago periódico fijo de un préstamo con interés compuesto.
     *
     * @param mixed $tasaInteres Tasa de interés mensual
     * @param mixed $numPagos Número total de pagos
     * @param mixed $montoPrestamo Monto total a financiar
     * @return float|int Valor de la cuota fija mensual
     */
    private function Pago($tasaInteres, $numPagos, $montoPrestamo)
    {
        if ($tasaInteres == 0) {
            return $montoPrestamo / $numPagos;
        }
        $pago = ($montoPrestamo * $tasaInteres) / (1 - pow(1 + $tasaInteres, -$numPagos));
        return $pago;
    }

    /**
     * Obtiene los detalles de una cotización para creación de contrato
     * 
     * Recupera la información básica de una cotización necesaria para generar
     * el contrato y calcular el cronograma de pagos. Incluye datos financieros
     * como precios, cuotas, inicial y moneda.
     *
     * @param int $idcotizacion ID de la cotización a consultar
     */
    public function getCotizacionDetails(int $idcotizacion): ?array
    {
        $query = "SELECT idcotizacion, precioventa, inicial, numcuotas, valorcuota, moneda 
                  FROM cotizaciones
                  WHERE idcotizacion = :idcotizacion";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idcotizacion', $idcotizacion, PDO::PARAM_INT);
        $stmt->execute();
        $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cotizacion ?: null;
    }
}
