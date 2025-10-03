<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;
use DateTime;
use DateInterval;
use PDOException;

class Contrato
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

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

        return (int)$this->db->lastInsertId();
    }

   
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

    private function updateCotizacionEstado(int $idcotizacion): void
    {
        $sql = "UPDATE cotizaciones SET estadocotizacion = 'CONT' WHERE idcotizacion = :idcotizacion";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idcotizacion', $idcotizacion, PDO::PARAM_INT);
        $stmt->execute();
    }

    
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

    // Lógica de Cronograma
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
                (int)$fechaPago->format('Y'),
                (int)$fechaPago->format('m'),
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

    // Función de pago
    private function Pago($tasaInteres, $numPagos, $montoPrestamo)
    {
        if ($tasaInteres == 0) {
            return $montoPrestamo / $numPagos;
        }
        $pago = ($montoPrestamo * $tasaInteres) / (1 - pow(1 + $tasaInteres, -$numPagos));
        return $pago;
    }

    //  Obtener cotización
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
