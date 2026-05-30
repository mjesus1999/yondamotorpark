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

    public static function getBool(string $key, bool $default = false): bool
    {
        $raw = strtolower(trim(self::get($key, $default ? 'true' : 'false')));
        return in_array($raw, ['1', 'true', 'yes', 'on'], true);
    }

    public static function isProduction(): bool
    {
        return strtolower(self::get('APP_ENV', 'development')) === 'production';
    }

    public static function isHttpsRequest(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
            return true;
        }
        return false;
    }

    public static function sessionCookieSecure(): bool
    {
        $configured = trim(self::get('SESSION_SECURE', ''));
        if ($configured !== '') {
            return self::getBool('SESSION_SECURE', false);
        }
        if (self::isHttpsRequest()) {
            return true;
        }
        return self::isProduction();
    }
}
