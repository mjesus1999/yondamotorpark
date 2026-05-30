<?php

namespace App\Config;

class Credencialesnubefact
{
    /**
     * Importante: no commitear credenciales reales.
     * Configurar en el servidor con variables de entorno (.env):
     * - NUBEFACT_RUTA (recomendado) o NUBEFACT_API_URL (alias usado en algunos despliegues)
     * - NUBEFACT_TOKEN
     */
    private const DEFAULT_NUBEFACT_RUTA = '';
    private const DEFAULT_NUBEFACT_TOKEN = '';

    /**
     * Lee variable de entorno compatible con hosting donde putenv/getenv fallan
     * (phpdotenv solo llena $_ENV / $_SERVER).
     */
    private static function readEnv(string $key): string
    {
        $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($v === false || $v === null) {
            return '';
        }
        $v = is_string($v) ? trim($v) : '';
        // Quitar comillas tipográficas por copiar/pegar desde panel o .env mal formado
        $v = trim($v, " \t\n\r\0\x0B\"'");
        return $v;
    }

    public static function getRuta(): string
    {
        $ruta = self::readEnv('NUBEFACT_RUTA');
        if ($ruta === '') {
            $ruta = self::readEnv('NUBEFACT_API_URL');
        }
        return $ruta !== '' ? $ruta : self::DEFAULT_NUBEFACT_RUTA;
    }

    public static function getToken(): string
    {
        $token = self::readEnv('NUBEFACT_TOKEN');
        return $token !== '' ? $token : self::DEFAULT_NUBEFACT_TOKEN;
    }

    /** Serie nota de crédito vinculada a boletas (configurar también en panel Nubefact). */
    public static function getSerieNcBoleta(): string
    {
        $s = self::readEnv('NUBEFACT_SERIE_NC_BOLETA');
        return $s !== '' ? $s : 'BCN1';
    }

    /** Serie nota de crédito vinculada a facturas (configurar también en panel Nubefact). */
    public static function getSerieNcFactura(): string
    {
        $s = self::readEnv('NUBEFACT_SERIE_NC_FACTURA');
        return $s !== '' ? $s : 'FCN1';
    }
}
