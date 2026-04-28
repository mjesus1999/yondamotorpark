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
        return (is_null($valor) || trim($valor) === '')
            ? "El campo '$nombre' es obligatorio."
            : null;
    }

    public static function emailValido(?string $email): ?string
    {
        if (empty($email)) {
            return null;
        }
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
            return "La fecha de nacimiento no es válida.";
        }
    }
}

<?php

namespace App\Helpers;

use DateTime;
use Exception;

/**
 * Clase Validador
 *
 * Proporciona métodos estáticos para validar y sanitizar datos de entrada
 * en formularios y operaciones de la aplicación.
 */
class Validador
{

    /**
     * Limpia y sanitiza una cadena de texto.
     *
     * Elimina espacios en blanco al inicio y final, y convierte caracteres
     * especiales a entidades HTML para prevenir ataques XSS.
     *
     * @param string $valor El valor a limpiar
     * @return string El valor limpio y sanitizado
     */
    public static function limpiar(string $valor): string
    {
        return htmlspecialchars(trim($valor));
    }

    /**
     * Valida que un campo no esté vacío.
     *
     * @param string|null $valor El valor a validar
     * @param string $nombre El nombre del campo para el mensaje de error
     * @return string|null Mensaje de error si el campo está vacío, null si es válido
     */
    public static function campoObligatorio(?string $valor, string $nombre): ?string
    {
        return (is_null($valor) || trim($valor) === '')
            ? "El campo '$nombre' es obligatorio."
            : null;
    }

    /**
     * Valida el formato de una dirección de correo electrónico.
     */
    public static function emailValido(?string $email): ?string
    {
        if (empty($email))
            return null;
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? null : "El correo no es válido.";
    }

    /**
     * Valida el formato de un número de teléfono.
     */
    public static function telefonoValido(string $telefono, string $campo): ?string
    {
        if (!preg_match('/^\d{9,12}$/', $telefono)) {
            return "El $campo debe tener entre 9 y 12 dígitos.";
        }
        return null;
    }

    /**
     * Valida que un valor contenga solo números con una longitud específica.
     */
    public static function soloNumeros(string $valor, string $campo, int $longitud = 11): ?string
    {
        if (!preg_match('/^\d{' . $longitud . '}$/', $valor)) {
            return "El $campo debe tener exactamente $longitud dígitos.";
        }
        return null;
    }

    /**
     * Valida todos los campos requeridos para crear una nueva Persona.
     */
    public static function validarPersonaCrear(array $data): array
    {
        $errores = [];

        $errores[] = self::campoObligatorio($data['apellidos'], 'Apellidos');
        $errores[] = self::campoObligatorio($data['nombres'], 'Nombres');
        $errores[] = self::campoObligatorio($data['tipodoc'], 'Tipo de documento');
        $errores[] = self::campoObligatorio($data['nrodoc'], 'Número de documento');
        $errores[] = self::campoObligatorio($data['genero'], 'Género');
        $errores[] = self::campoObligatorio($data['iddistrito'], 'Distrito');

        $errorTel = self::campoObligatorio($data['telprimario'], 'Teléfono');
        if ($errorTel) {
            $errores[] = $errorTel;
        } else {
            $errTelFmt = self::telefonoValido($data['telprimario'], 'teléfono');
            if ($errTelFmt) $errores[] = $errTelFmt;
        }

        return array_values(array_filter($errores));
    }

    /**
     * Valida una fecha de nacimiento.
     */
    public static function validarFechaNacimiento(?string $fecha): ?string
    {
        if (empty($fecha)) return null;
        try {
            new DateTime($fecha);
            return null;
        } catch (Exception $e) {
            return "La fecha de nacimiento no es válida.";
        }
    }
}

<?php

namespace App\Helpers;

use DateTime;
use Exception;

/**
 * Clase Validador
 * 
 * Proporciona métodos estáticos para validar y sanitizar datos de entrada
 * en formularios y operaciones de la aplicación.
 */
class Validador
{

    /**
     * Limpia y sanitiza una cadena de texto.
     * 
     * Elimina espacios en blanco al inicio y final, y convierte caracteres
     * especiales a entidades HTML para prevenir ataques XSS.
     * 
     * @param string $valor El valor a limpiar
     * @return string El valor limpio y sanitizado
     */
    public static function limpiar(string $valor): string
    {
        return htmlspecialchars(trim($valor));
    }

    /**
     * Valida que un campo no esté vacío.
     * 
     * Verifica si el valor es nulo o está vacío después de eliminar espacios.
     * Retorna un mensaje de error personalizado si el campo está vacío.
     * 
     * @param string $valor El valor a validar
     * @param string $nombre El nombre del campo para el mensaje de error
     * @return string|null Mensaje de error si el campo está vacío, null si es válido
     */
    public static function campoObligatorio(?string $valor, string $nombre): ?string
    {
        return (is_null($valor) || trim($valor) === '')
            ? "El campo '$nombre' es obligatorio."
            : null;
    }

    /**
     * Valida el formato de una dirección de correo electrónico.
     * 
     * Utiliza el filtro nativo de PHP para validar el formato del email.
     * Si el email está vacío, retorna null (no es obligatorio por defecto).
     * 
     * @param string $email El email a validar
     * @return string|null Mensaje de error si el email es inválido, null si es válido o vacío
     */
    public static function emailValido(?string $email): ?string
    {
        if (empty($email))
            return null;
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? null : "El correo no es válido.";
    }

    /**
     * Valida el formato de un número de teléfono.
     * 
     * Verifica que el teléfono contenga únicamente dígitos y tenga
     * una longitud entre 9 y 12 caracteres.
     * 
     * @param string $telefono El número de teléfono a validar
     * @param string $campo El nombre del campo para el mensaje de error
     * @return string|null Mensaje de error si el teléfono es inválido, null si es válido
     */
    public static function telefonoValido(string $telefono, string $campo): ?string
    {
        if (!preg_match('/^\d{9,12}$/', $telefono)) {
            return "El $campo debe tener entre 9 y 12 dígitos.";
        }
        return null;
    }

    /**
     * Valida que un valor contenga solo números con una longitud específica.
     * 
     * @param string $valor El valor a validar
     * @param string $campo El nombre del campo para el mensaje de error
     * @param int $longitud La longitud exacta requerida
     * @return string|null Mensaje de error si el valor es inválido, null si es válido
     */
    public static function soloNumeros(string $valor, string $campo, int $longitud = 11): ?string
    {
        if (!preg_match('/^\d{' . $longitud . '}$/', $valor)) {
            return "El $campo debe tener exactamente $longitud dígitos.";
        }
        return null;
    }

    /**
     * Valida todos los campos requeridos para crear una nueva Persona.
     * 
     * @param array $data Array asociativo con los datos de la persona a validar
     * @return array Array con los mensajes de error encontrados
     */
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

    /**
     * Valida los campos requeridos para actualizar una Persona existente.
     * 
     * @param array $data Array asociativo con los datos de la persona a validar
     * @return array Array con los mensajes de error encontrados
     */
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
