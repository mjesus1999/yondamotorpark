<?php
// app/Helpers/Validador.php

namespace App\Helpers;

use App\Models\Usuario;

class Validador
{
    /**
     * Valida los datos básicos de la persona.
     * @param array $data
     * @return array Lista de mensajes de error (vacía si ok)
     */
    public static function validatePersona(array $data): array
    {
        $errors = [];

        $required = [
            'apellidos' => 'Apellidos',
            'nombres' => 'Nombres',
            'tipodoc' => 'Tipo de documento',
            'nrodoc' => 'Nro. de documento',
            'genero' => 'Género',
            'telprimario' => 'Teléfono primario',
        ];

        foreach ($required as $key => $label) {
            if (empty($data[$key]) && $data[$key] !== '0') {
                $errors[] = "El campo «{$label}» es obligatorio.";
            }
        }

        // Validar nrodoc (solo dígitos y longitud razonable 6..12)
        if (!empty($data['nrodoc']) && !preg_match('/^\d{6,12}$/', $data['nrodoc'])) {
            $errors[] = 'El número de documento debe contener sólo dígitos (6-12).';
        }

        // Email válido si viene
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        }

        // telprimario longitud y dígitos
        if (!empty($data['telprimario']) && !preg_match('/^\d{6,15}$/', $data['telprimario'])) {
            $errors[] = 'Teléfono primario inválido. Debe contener sólo dígitos (6-15).';
        }

        // telalternativo si existe
        if (!empty($data['telalternativo']) && !preg_match('/^\d{6,15}$/', $data['telalternativo'])) {
            $errors[] = 'Teléfono alternativo inválido. Debe contener sólo dígitos (6-15).';
        }

        return $errors;
    }

    /**
     * Valida datos de contrato / cargo / fechas.
     * @param array $data
     * @return array
     */
    public static function validateContrato(array $data): array
    {
        $errors = [];

        if (empty($data['idcargo']) || (int) $data['idcargo'] <= 0) {
            $errors[] = 'Debe seleccionar un cargo.';
        }

        // fecha_inicio obligatoria y formato YYYY-MM-DD
        if (empty($data['fecha_inicio'])) {
            $errors[] = 'La fecha de inicio es obligatoria.';
        } elseif (!self::isValidDate($data['fecha_inicio'])) {
            $errors[] = 'La fecha de inicio no tiene formato válido (YYYY-MM-DD).';
        }

        // si fecha_fin viene, validar formato
        if (!empty($data['fecha_fin']) && !self::isValidDate($data['fecha_fin'])) {
            $errors[] = 'La fecha fin no tiene formato válido (YYYY-MM-DD).';
        }

        return $errors;
    }

    /**
     * Valida usernick y contraseñas. Si pasas $usuarioModel, valida unicidad.
     * @param array $data keys: usuario, password1, password2
     * @param Usuario|null $usuarioModel
     * @return array
     */
    public static function validateUsuario(array $data, ?Usuario $usuarioModel = null): array
    {
        $errors = [];

        $user = trim($data['usuario'] ?? '');
        $p1 = $data['password1'] ?? '';
        $p2 = $data['password2'] ?? '';

        if ($user === '') {
            $errors[] = 'Introduce un nombre de usuario.';
        } elseif (!preg_match('/^[a-zA-Z0-9_\-\.]{3,50}$/', $user)) {
            $errors[] = 'Nombre de usuario inválido. Usa 3-50 caracteres (letras, números, _ - .).';
        }

        // Comprobar unicidad si modelo disponible
        if ($usuarioModel !== null && $user !== '') {
            try {
                if ($usuarioModel->searchByUsernick($user)) {
                    $errors[] = 'El usernick ya existe.';
                }
            } catch (\Throwable $e) {
                // no bloquear, solo informar (el controlador puede decidir)
                $errors[] = 'No se pudo comprobar la disponibilidad del usernick.';
            }
        }

        // Contraseña
        if ($p1 === '') {
            $errors[] = 'Introduce la contraseña.';
        } else {
            // fuerza mínima: al menos 8, una letra, un dígito y un símbolo
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/', $p1)) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres, incluir letra, número y símbolo.';
            }
            if ($p1 !== $p2) {
                $errors[] = 'Las contraseñas no coinciden.';
            }
        }

        return $errors;
    }

    /**
     * Comprueba formato YYYY-MM-DD
     * @param string $date
     * @return bool
     */
    protected static function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
