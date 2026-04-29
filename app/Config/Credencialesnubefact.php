<?php

namespace App\Config;

class Credencialesnubefact
{
    /**
     * Importante: no commitear credenciales reales.
     * Configurar en el servidor con variables de entorno (.env):
     * - NUBEFACT_RUTA
     * - NUBEFACT_TOKEN
     */
    private const DEFAULT_NUBEFACT_RUTA = '';
    private const DEFAULT_NUBEFACT_TOKEN = '';

    public static function getRuta(): string
    {
        $ruta = getenv('NUBEFACT_RUTA');
        if (is_string($ruta) && trim($ruta) !== '') {
            return trim($ruta);
        }
        return self::DEFAULT_NUBEFACT_RUTA;
    }

    public static function getToken(): string
    {
        $token = getenv('NUBEFACT_TOKEN');
        if (is_string($token) && trim($token) !== '') {
            return trim($token);
        }
        return self::DEFAULT_NUBEFACT_TOKEN;
    }
}
