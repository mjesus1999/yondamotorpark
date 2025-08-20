<?php
// app/Helpers/Validation.php

namespace App\Helpers;

use App\Models\Usuario;
use DateTime;

class Validation
{

    // VALIDACIONES DE USUARIOS

    /**
     * validateUsuarioData / Valida los datos del formulario de usuario.
     * @param array $data
     * @param \App\Models\Usuario $usuarioModel
     * @return string[]
     */
    public static function validateUsuarioData(array $data, Usuario $usuarioModel): array
    {
        $errors = [];
        $idpersona = (int) ($data['idpersona'] ?? 0);
        $idcargo = (int) ($data['idcargo'] ?? 0);
        $fechaInicio = trim((string) ($data['fecha_inicio'] ?? ''));
        $usuario = trim((string) ($data['usuario'] ?? ''));
        $pass1 = $data['password1'] ?? '';
        $pass2 = $data['password2'] ?? '';

        // Validar ID de la persona
        if ($idpersona <= 0) {
            $errors[] = 'Debe registrar primero la persona.';
        }

        // Validar ID del cargo
        if ($idcargo <= 0) {
            $errors[] = 'Debe seleccionar un cargo.';
        }

        // Validar la fecha de inicio
        if ($fechaInicio === '') {
            $errors[] = 'La fecha de inicio es obligatoria.';
        } else {
            //validar formato YYYY-MM-DD:
            $d = DateTime::createFromFormat('Y-m-d', $fechaInicio);
            if (!($d && $d->format('Y-m-d') === $fechaInicio)) {
                $errors[] = 'Formato de fecha de inicio inválido (esperado YYYY-MM-DD).';
            }
        }

        // Validar usuario
        if ($usuario === '') {
            $errors[] = 'Introduce un nombre de usuario.';
        }

        // Validar contraseñas
        if ($pass1 !== $pass2) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        // Validar longitud de la contraseña
        if ($pass1 !== '' && strlen($pass1) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        // Validar que no exista el mismo usernick
        try {
            if ($usuario !== '' && $usuarioModel->searchByUsernick($usuario)) {
                $errors[] = 'El usernick ya existe.';
            }
        } catch (\Throwable $e) {
            error_log('Error comprobando usernick: ' . $e->getMessage());
            $errors[] = 'No se pudo comprobar la disponibilidad del usernick.';
        }

        return $errors;
    }

    /**
     * validateChangePassword /Valida cambio de contraseña.
     * @param array $data
     * @return string[] / Espera ['idcolaborador'=>int,'password1'=>string,'password2'=>string]
     */
    public function validateChangePassword(array $data): array
    {
        $errors = [];
        $id = (int) ($data['idcolaborador'] ?? 0);
        $p1 = $data['password1'] ?? '';
        $p2 = $data['password2'] ?? '';

        if ($id <= 0) {
            $errors[] = 'Colaborador inválido.';
        }
        if ($p1 === '' || $p2 === '') {
            $errors[] = 'Introduce y confirma la contraseña.';
        }
        if ($p1 !== $p2) {
            $errors[] = 'Las contraseñas no coinciden.';
        }
        if ($p1 !== '' && strlen($p1) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        return $errors;
    }

    /**
     * validateAvatarUpload / Valida subida de avatar. Recibe el array equivalente a $_FILES['avatar'] o null.
     * @param mixed $file
     * @return string[]
     */
    public function validateAvatarUpload(?array $file): array
    {
        $errors = [];

        if (empty($file) || !isset($file['error'])) {
            $errors[] = 'Archivo no recibido.';
            return $errors;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Error al subir el archivo (código ' . (int) $file['error'] . ').';
            return $errors;
        }

        // límites y tipos razonables
        $maxBytes = 2 * 1024 * 1024; // 2 MB
        if (isset($file['size']) && $file['size'] > $maxBytes) {
            $errors[] = 'El archivo supera el límite de 2 MB.';
        }

        $name = $file['name'] ?? '';
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if ($ext === '' || !in_array($ext, $allowed, true)) {
            $errors[] = 'Tipo de archivo no permitido. Solo jpg, jpeg, png, gif.';
        }

        // adicional: verificar que venga de upload tmp
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'El archivo no es válido.';
        }

        return $errors;
    }

    /**
     * validateCreateFromContract / Valida creación de colaborador a partir de contrato.
     * @param array $data
     * @param \App\Models\Usuario $usuarioModel
     * @return string[] / Espera ['idcontrato'=>int,'usernick'=>string,'password1'=>string,'password2'=>string]
     */
    public function validateCreateFromContract(array $data, Usuario $usuarioModel): array
    {
        $errors = [];
        $idContrato = (int) ($data['idcontrato'] ?? 0);
        $usernick = trim((string) ($data['usernick'] ?? ''));
        $p1 = $data['password1'] ?? '';
        $p2 = $data['password2'] ?? '';

        if ($idContrato <= 0) {
            $errors[] = 'Selecciona un contrato.';
        }
        if ($usernick === '') {
            $errors[] = 'Introduce un nombre de usuario.';
        }
        if ($p1 === '' || $p2 === '') {
            $errors[] = 'Introduce y confirma la contraseña.';
        }
        if ($p1 !== $p2) {
            $errors[] = 'Las contraseñas no coinciden.';
        }
        if ($p1 !== '' && strlen($p1) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        try {
            if ($usernick !== '' && $usuarioModel->searchByUsernick($usernick)) {
                $errors[] = 'El usernick ya existe.';
            }
        } catch (\Throwable $e) {
            error_log('Error comprobando usernick: ' . $e->getMessage());
            $errors[] = 'No se pudo comprobar la disponibilidad del usernick.';
        }

        return $errors;
    }

    /**
     * validateUpdateUsuario / Valida los datos del formulario de edición de usuario.
     * @param array $data Espera keys: idcolaborador, nombres, apellidos, idarea, idcargo, nrodoc, fechainicio
     * @return string[] lista de errores
     */
    public function validateUpdateUsuario(array $data): array
    {
        $errors = [];

        $idColab = (int) ($data['idcolaborador'] ?? 0);
        $nombres = trim((string) ($data['nombres'] ?? ''));
        $apellidos = trim((string) ($data['apellidos'] ?? ''));
        $idArea = (int) ($data['idarea'] ?? 0);
        $idCargo = (int) ($data['idcargo'] ?? 0);
        $nrodoc = trim((string) ($data['nrodoc'] ?? ''));
        $fechaInicio = trim((string) ($data['fechainicio'] ?? ''));

        if ($idColab <= 0) {
            $errors[] = 'ID de colaborador inválido.';
        }

        if ($nombres === '' || mb_strlen($nombres) < 2) {
            $errors[] = 'Nombres inválidos o demasiado cortos.';
        }

        if ($apellidos === '' || mb_strlen($apellidos) < 2) {
            $errors[] = 'Apellidos inválidos o demasiado cortos.';
        }

        if ($idArea <= 0) {
            $errors[] = 'Selecciona un área válida.';
        }

        if ($idCargo <= 0) {
            $errors[] = 'Selecciona un cargo válido.';
        }

        // DNI: solo dígitos, longitud razonable (6-12)
        if ($nrodoc === '') {
            $errors[] = 'DNI no puede estar vacío.';
        } else {
            $digits = preg_replace('/\D+/', '', $nrodoc);
            if ($digits === '') {
                $errors[] = 'DNI inválido. Solo números permitidos.';
            } elseif (strlen($digits) < 6 || strlen($digits) > 12) {
                $errors[] = 'DNI inválido (longitud entre 6 y 12 dígitos).';
            }
        }

        // Fecha inicio: formato YYYY-MM-DD y no futura
        if ($fechaInicio === '') {
            $errors[] = 'Fecha de inicio es obligatoria.';
        } else {
            $d = DateTime::createFromFormat('Y-m-d', $fechaInicio);
            if (!($d && $d->format('Y-m-d') === $fechaInicio)) {
                $errors[] = 'Formato de fecha de inicio inválido (esperado YYYY-MM-DD).';
            } else {
                // Opcional: evitar fecha futura
                $hoy = new DateTime('today');
                if ($d > $hoy) {
                    $errors[] = 'La fecha de inicio no puede ser futura.';
                }
            }
        }

        return $errors;
    }


}
