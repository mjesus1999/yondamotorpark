<?php

namespace App\Helpers;

use App\Config\Env;

/**
 * Opciones cURL seguras según entorno (Hostinger HTTPS vs desarrollo local).
 */
final class CurlHelper
{
    public static function sslVerifyPeer(): bool
    {
        return Env::getBool('CURL_SSL_VERIFY', Env::isProduction());
    }

    /** @param resource|\CurlHandle $ch */
    public static function applySslOptions($ch): void
    {
        $verify = self::sslVerifyPeer();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verify ? 2 : 0);
    }
}
