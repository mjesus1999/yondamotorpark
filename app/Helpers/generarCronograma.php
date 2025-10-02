<!-- 
    private function Pago($tasaInteres, $numPagos, $montoPrestamo)
    {
        // Verificar si la tasa de interés es 0
        if ($tasaInteres == 0) {
            return $montoPrestamo / $numPagos;
        }

        // Calcular el pago utilizando la fórmula de anualidad
        $pago = ($montoPrestamo * $tasaInteres) / (1 - pow(1 + $tasaInteres, -$numPagos));
        return $pago;
    }

    public function calcularPagoMensual($importeTotal, $inicial, $meses, $tasaAnual = 0.65)
    {
        $tasaMensual = pow((1 + $tasaAnual), (1 / 12)) - 1;
        $montoFinanciar = $importeTotal - $inicial;
        $cuota = round($this->Pago($tasaMensual, $meses, $montoFinanciar), 2);
        return $cuota;
    }

    public function generarCronograma(float $importeTotal, float $inicial, int $meses, float $tasaAnual = 0.65): array
    {
        $tasaMensual = pow((1 + $tasaAnual), (1 / 12)) - 1;
        $montoFinanciar = $importeTotal - $inicial;
        $valorCuota = round($this->Pago($tasaMensual, $meses, $montoFinanciar), 2);

        $cronograma = [];
        $saldoCapital = $montoFinanciar;
        $fechaPago = new Datetime('now');

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

            $cronograma[] = [
                'item' => $i,
                'fecha_pago' => $fechaPago->format('d/m/Y'),
                'interes' => $interes,
                'abono_capital' => $abonoCapital,
                'valor_cuota' => $valorCuota,
                'saldo_capital' => $saldoCapital,
            ];
        }

        return $cronograma; -->