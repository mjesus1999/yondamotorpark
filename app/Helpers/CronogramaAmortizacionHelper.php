<?php

namespace App\Helpers;

/**
 * Cronograma francés: interés sobre saldo + abono a capital + cuota fija (como Excel Yonda).
 */
class CronogramaAmortizacionHelper
{
    /**
     * @param array{
     *   monto_financiar: float,
     *   cuota_mensual: float,
     *   plazo_meses: int,
     *   fecha_inicio: string|null,
     *   cuotas_pagadas?: int,
     *   gps_monto?: float,
     *   fechas_pago?: array<int, string|null>
     * } $params
     * @return array{meta: array<string, mixed>, filas: list<array<string, mixed>>}
     */
    public static function generar(array $params): array
    {
        $principal = round(max(0, (float) ($params['monto_financiar'] ?? 0)), 2);
        $cuota = round(max(0, (float) ($params['cuota_mensual'] ?? 0)), 2);
        $plazo = max(0, (int) ($params['plazo_meses'] ?? 0));
        $cuotasPagadas = max(0, (int) ($params['cuotas_pagadas'] ?? 0));
        $gps = round(max(0, (float) ($params['gps_monto'] ?? 0)), 2);
        $fechasPago = is_array($params['fechas_pago'] ?? null) ? $params['fechas_pago'] : [];

        $fechaInicio = self::parseFecha($params['fecha_inicio'] ?? null) ?? new \DateTimeImmutable('today');

        if ($principal <= 0 || $plazo <= 0 || $cuota <= 0) {
            return [
                'meta' => self::buildMeta($principal, $cuota, $plazo, 0, $cuotasPagadas, $gps, $principal),
                'filas' => [],
            ];
        }

        $tasaMensual = self::resolverTasaMensual($principal, $cuota, $plazo);
        $filas = [];
        $saldo = $principal;
        $totalInteres = 0.0;
        $totalCapital = 0.0;
        $today = new \DateTimeImmutable('today');

        for ($i = 1; $i <= $plazo; $i++) {
            $interes = round($saldo * $tasaMensual, 2);
            $abono = round($cuota - $interes, 2);

            if ($i === $plazo) {
                $abono = round($saldo, 2);
                $interes = round($cuota - $abono, 2);
            }

            $saldo = round($saldo - $abono, 2);
            if ($i === $plazo && abs($saldo) < 0.02) {
                $saldo = 0.0;
            }

            $totalInteres += $interes;
            $totalCapital += $abono;

            $fechaCronograma = self::fechaCronograma($fechaInicio, $i);
            $fechaPagoCliente = self::fechaPagoCliente($i, $fechasPago);
            $estado = self::resolverEstado($i, $cuotasPagadas, $fechaCronograma, $today);

            $filas[] = [
                'numcuota' => $i,
                'fechapago' => $fechaCronograma,
                'fecha_cronograma' => $fechaCronograma,
                'fecha_pago_cliente' => $fechaPagoCliente,
                'interes' => $interes,
                'abonocapital' => $abono,
                'valorcuota' => $cuota,
                'saldocapital' => $saldo,
                'gps' => $gps,
                'total_con_gps' => round($cuota + $gps, 2),
                'estado' => $estado,
            ];
        }

        $saldoActual = $principal;
        if ($cuotasPagadas > 0) {
            foreach ($filas as $f) {
                if ((int) $f['numcuota'] === $cuotasPagadas) {
                    $saldoActual = (float) $f['saldocapital'];
                    break;
                }
            }
        }

        $tasaAnual = (pow(1 + $tasaMensual, 12) - 1) * 100;
        $fechaFinCronograma = $plazo > 0 ? self::fechaCronograma($fechaInicio, $plazo) : null;

        return [
            'meta' => self::buildMeta(
                $principal,
                $cuota,
                $plazo,
                $tasaMensual,
                $cuotasPagadas,
                $gps,
                $saldoActual,
                $tasaAnual,
                round($totalInteres, 2),
                round($totalCapital, 2),
                $fechaInicio->format('Y-m-d'),
                $fechaFinCronograma
            ),
            'filas' => $filas,
        ];
    }

    /**
     * Fecha programada del cronograma: cuota 1 = fecha de inicio, cuota 2 = +1 mes, etc.
     */
    private static function fechaCronograma(\DateTimeImmutable $inicio, int $numCuota): string
    {
        $meses = max(0, $numCuota - 1);

        return $inicio->modify('+' . $meses . ' month')->format('Y-m-d');
    }

    /**
     * Fecha real en que pagó el cliente (Excel/import), si está registrada.
     */
    private static function fechaPagoCliente(int $numCuota, array $fechasImport): ?string
    {
        if (empty($fechasImport[$numCuota])) {
            return null;
        }
        $parsed = self::parseFecha($fechasImport[$numCuota]);

        return $parsed ? $parsed->format('Y-m-d') : null;
    }

    private static function resolverEstado(int $numCuota, int $pagadas, string $fechaCuota, \DateTimeImmutable $today): string
    {
        if ($pagadas > 0 && $numCuota <= $pagadas) {
            return 'Pagado';
        }

        $primeraPendiente = $pagadas + 1;
        if ($numCuota !== $primeraPendiente) {
            return 'Por abonar';
        }

        try {
            $venc = new \DateTimeImmutable($fechaCuota);
            if ($today > $venc->modify('+3 days')) {
                return 'Por saldar (MORA)';
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return 'Por saldar';
    }

    private static function buildMeta(
        float $principal,
        float $cuota,
        int $plazo,
        float $tasaMensual,
        int $cuotasPagadas,
        float $gps,
        float $saldoActual,
        float $tasaAnual = 0,
        float $totalInteres = 0,
        float $totalCapital = 0,
        ?string $fechaInicioCronograma = null,
        ?string $fechaFinCronograma = null
    ): array {
        $pendientes = max(0, $plazo - $cuotasPagadas);

        return [
            'monto_financiar' => round($principal, 2),
            'lo_que_debe_pagar_cliente' => round($principal, 2),
            'cuota_por_mes' => round($cuota, 2),
            'duracion_meses' => $plazo,
            'cuotas_pagadas' => $cuotasPagadas,
            'cuotas_pendientes' => $pendientes,
            'saldo_capital_actual' => round($saldoActual, 2),
            'tasa_mensual_pct' => round($tasaMensual * 100, 2),
            'tasa_anual_pct' => round($tasaAnual > 0 ? $tasaAnual : ((pow(1 + $tasaMensual, 12) - 1) * 100), 2),
            'total_interes_proyectado' => $totalInteres,
            'total_capital_proyectado' => $totalCapital,
            'gps_mensual' => $gps,
            'fecha_inicio_cronograma' => $fechaInicioCronograma,
            'fecha_fin_cronograma' => $fechaFinCronograma,
        ];
    }

    private static function resolverTasaMensual(float $principal, float $cuota, int $plazo): float
    {
        if ($principal <= 0 || $cuota <= 0 || $plazo <= 0) {
            return 0.0;
        }

        if (abs($cuota * $plazo - $principal) < 0.01) {
            return 0.0;
        }

        $hi = min(0.25, ($cuota / $principal) * 0.999);
        $lo = 0.0001;
        $r = 0.05;

        // Newton: encontrar r tal que PMT(r) ≈ cuota
        for ($i = 0; $i < 80; $i++) {
            $pmt = self::calcularPmt($r, $plazo, $principal);
            $diff = $pmt - $cuota;
            if (abs($diff) < 0.001) {
                return $r;
            }
            $dr = 0.00001;
            $deriv = (self::calcularPmt($r + $dr, $plazo, $principal) - $pmt) / $dr;
            if (abs($deriv) < 1e-12) {
                break;
            }
            $r = $r - ($diff / $deriv);
            if ($r < $lo) {
                $r = $lo;
            }
            if ($r > $hi) {
                $r = $hi;
            }
        }

        for ($i = 0; $i < 80; $i++) {
            $mid = ($lo + $hi) / 2;
            $pmt = self::calcularPmt($mid, $plazo, $principal);
            if ($pmt > $cuota) {
                $hi = $mid;
            } else {
                $lo = $mid;
            }
        }

        return ($lo + $hi) / 2;
    }

    private static function calcularPmt(float $tasaMensual, int $plazo, float $principal): float
    {
        if ($tasaMensual <= 0) {
            return $principal / $plazo;
        }
        $pow = pow(1 + $tasaMensual, $plazo);

        return $principal * $tasaMensual * $pow / ($pow - 1);
    }

    private static function simularSaldoFinal(float $principal, float $cuota, int $plazo, float $tasaMensual): float
    {
        $saldo = $principal;
        for ($i = 1; $i <= $plazo; $i++) {
            $interes = round($saldo * $tasaMensual, 2);
            $abono = round($cuota - $interes, 2);
            if ($abono < 0) {
                $abono = 0;
            }
            if ($i === $plazo) {
                $abono = round($saldo, 2);
            }
            $saldo = round($saldo - $abono, 2);
        }

        return $saldo;
    }

    private static function parseFecha(?string $raw): ?\DateTimeImmutable
    {
        $raw = trim((string) $raw);
        if ($raw === '' || $raw === '0000-00-00') {
            return null;
        }
        try {
            return new \DateTimeImmutable($raw);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $registro Fila de import / registro ventas
     * @return array<int, string|null>
     */
    public static function fechasPagoDesdeImport(array $registro): array
    {
        $out = [];
        $fechas = $registro['fechas_cuota_import'] ?? null;
        if (is_array($fechas)) {
            foreach ($fechas as $n => $f) {
                $out[(int) $n] = $f;
            }
            return $out;
        }

        for ($n = 1; $n <= 5; $n++) {
            $key = 'fecha_pago_' . $n;
            if (!empty($registro[$key])) {
                $out[$n] = (string) $registro[$key];
            }
        }

        return $out;
    }
}
