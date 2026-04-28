<?php

namespace App\Helpers;

use DateTime;
use Exception;

class Validador
{
    public static function limpiar(string $valor): string
    {
        return htmlspecialchars(trim($valor));
    }

    public static function campoObligatorio(?string $valor, string $nombre): ?string
    {
        return ($valor === null || trim($valor) === '')
            ? "El campo '$nombre' es obligatorio."
            : null;
    }

    public static function emailValido(?string $email): ?string
    {
        if (empty($email)) return null;
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? null : 'El correo no es válido.';
    }

    public static function telefonoValido(string $telefono, string $campo): ?string
    {
        if (!preg_match('/^\d{9,12}$/', $telefono)) {
            return "El $campo debe tener entre 9 y 12 dígitos.";
        }
        return null;
    }

    public static function soloNumeros(string $valor, string $campo, int $longitud = 11): ?string
    {
        if (!preg_match('/^\d{' . $longitud . '}$/', $valor)) {
            return "El $campo debe tener exactamente $longitud dígitos.";
        }
        return null;
    }

    public static function validarPersonaCrear(array $data): array
    {
        $errores = [];

        $errores[] = self::campoObligatorio($data['apellidos'] ?? null, 'Apellidos');
        $errores[] = self::campoObligatorio($data['nombres'] ?? null, 'Nombres');
        $errores[] = self::campoObligatorio($data['tipodoc'] ?? null, 'Tipo de documento');
        $errores[] = self::campoObligatorio($data['nrodoc'] ?? null, 'Número de documento');
        $errores[] = self::campoObligatorio($data['genero'] ?? null, 'Género');
        $errores[] = self::campoObligatorio($data['iddistrito'] ?? null, 'Distrito');

        $tel = $data['telprimario'] ?? null;
        $errorTel = self::campoObligatorio($tel, 'Teléfono');
        if ($errorTel) {
            $errores[] = $errorTel;
        } elseif (is_string($tel)) {
            $errTelFmt = self::telefonoValido($tel, 'teléfono');
            if ($errTelFmt) $errores[] = $errTelFmt;
        }

        return array_values(array_filter($errores));
    }

    public static function validarFechaNacimiento(?string $fecha): ?string
    {
        if (empty($fecha)) return null;
        try {
            new DateTime($fecha);
            return null;
        } catch (Exception $e) {
            return 'La fecha de nacimiento no es válida.';
        }
    }
}

