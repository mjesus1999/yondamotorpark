<?php

namespace App\Config;

/**
 * Lee variables de entorno (compatible Hostinger: $_ENV sin depender de putenv).
 */
final class Env
{
    public static function get(string $key, string $default = ''): string
    {
        if (isset($_ENV[$key]) && is_string($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }
        if (isset($_SERVER[$key]) && is_string($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return $_SERVER[$key];
        }
        $fromGetenv = getenv($key);
        if ($fromGetenv !== false && $fromGetenv !== '') {
            return (string) $fromGetenv;
        }
        return $default;
    }
}
