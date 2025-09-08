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

    // Algunos ejmplos para validar la persona y no repetir tanto código de validacióm el Controller.

    public static function validarPersonaCrear(array $data): array
    {

        $errores = [];

        $errores[] = self::campoObligatorio($data['apellidos'], 'Apellidos');
        $errores[] = self::campoObligatorio($data['nombres'], 'Nombres');
        $errores[] = self::campoObligatorio($data['tipodoc'], 'Tipo de documento');
        $errores[] = self::campoObligatorio($data['nrodoc'], 'Número de documento');
        $errores[] = self::campoObligatorio($data['genero'], 'Género');
        // $errores[] = self::campoObligatorio($data['fechanac'], 'Fecha de nacimiento');
        // $errores[] = self::campoObligatorio($data['estadocivil'], 'Estado civil');
        $errores[] = self::campoObligatorio($data['iddistrito'], 'Distrito');
        // $errores[] = self::campoObligatorio($data['fechanac'], 'Fecha de nacimiento');
        // $errores[] = self::validarFechaNacimiento($data['fechanac']);

        $errorTel = self::campoObligatorio($data['telprimario'], 'Teléfono');

        $errores[] = $errorTel ?: self::telefonoValido($data['telprimario'], 'Télefono');


        if (!empty($data['email'])) {
            $errores[] = self::emailValido($data['email']);
        }

        return array_filter($errores);
    }

    public static function validarPersonaUpdate(array $data): array
    {
        $errores = [];

        $errores[] = self::campoObligatorio($data['apellidos'] ?? '', 'Apellidos');
        $errores[] = self::campoObligatorio($data['nombres'] ?? '', 'Nombres');
        // $errores[] = self::campoObligatorio($data['estadocivil'] ?? '', 'Estado civil');

        $errorTel = self::campoObligatorio($data['telprimario'] ?? '', 'Teléfono');
        $errores[] = $errorTel ?: self::telefonoValido($data['telprimario'], 'Teléfono');

        if (!empty($data['email'])) {
            $errores[] = self::emailValido($data['email']);
        }

        return array_filter($errores);
    }



    // public static function validarFechaNacimiento(string $fecha): ?string
    // {
    //     try {
    //         $nacimiento = new DateTime($fecha);
    //         $hoy = new DateTime();
    //         $mayorEdad = (clone $hoy)->modify('-18 years');

    //         // Comparamos solo fechas 
    //         $fechaNacimientoStr = $nacimiento->format('Y-m-d');
    //         $fechaHoyStr = $hoy->format('Y-m-d');
    //         $fechaLimiteStr = $mayorEdad->format('Y-m-d');

    //         // var_dump("FECHA HOY:" . $fechaHoyStr);

    //         if ($fechaNacimientoStr > $fechaHoyStr) {
    //             return "La fecha de nacimiento no puede ser en el futuro.";
    //         }

    //         if ($fechaNacimientoStr === $fechaHoyStr) {
    //             return "La fecha de nacimiento no puede ser la actual.";
    //         }

    //         if ($fechaNacimientoStr > $fechaLimiteStr) {
    //             return "Debe tener al menos 18 años.";
    //         }

    //         return null;
    //     } catch (Exception $e) {
    //         return "Fecha inválida. Formato esperado: YYYY-MM-DD.";
    //     }
    // }

}


// $fechaNacimiento = '2025-07-08';

// var_dump(Validador::validarFechaNacimiento($fechaNacimiento));
