<?php
namespace App\Helpers;

class Validador
{
    public static function limpiar(string $valor): string
    {
        return htmlspecialchars(trim($valor));
    }

    public static function campoObligatorio(string $valor, string $nombre): ?string
    {
        return empty($valor) ? "El campo '$nombre' es obligatorio." : null;
    }

    public static function emailValido(?string $email): ?string
    {
        if (empty($email)) return null;
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? null : "El correo no es válido.";
    }

    public static function telefonoValido(string $telefono, string $campo): ?string
    {
        if (!preg_match('/^\d{7,12}$/', $telefono)) {
            return "El $campo debe tener entre 7 y 12 dígitos.";
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
}
