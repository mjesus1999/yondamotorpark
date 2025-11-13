<?php
// app/Helpers/Validation.php

/**
 * Helper de Validación
 * 
 * app/Helpers/Validation.php
 * 
 * Proporciona métodos estáticos para validar datos de formularios relacionados
 * con usuarios del sistema: creación, edición, cambio de contraseñas, subida
 * de avatares, y creación desde contratos. Implementa validaciones exhaustivas
 * de formato, longitud, unicidad de usuarios, tipos MIME de imágenes, y rangos
 * de fechas. Retorna arrays de mensajes de error descriptivos para feedback
 * al usuario. Todas las validaciones son reutilizables y centralizadas para
 * mantener consistencia en todo el sistema.
 * 
 */
namespace App\Helpers;

use App\Models\Usuario;
use DateTime;
use finfo;

/**
 * Clase Validation
 * 
 * Helper que centraliza todas las validaciones de datos de usuario.
 * Proporciona métodos estáticos para validar formularios de creación,
 * edición, cambio de contraseña, y subida de archivos. Cada método
 * retorna un array de errores descriptivos para mostrar al usuario.
 * 
 */
class Validation
{

    // VALIDACIONES DE USUARIOS

    /**
     * Valida datos del formulario de creación de usuario
     * 
     * Realiza validación completa de todos los campos requeridos para crear
     * un nuevo usuario en el sistema. Verifica formato de datos, longitud
     * de contraseña, coincidencia de confirmación, y unicidad del nombre de
     * usuario mediante consulta a base de datos.
     * 
     * @param array $data Datos del formulario con claves:
     *   - idpersona (int): ID de la persona base
     *   - idcargo (int): ID del cargo asignado
     *   - fecha_inicio (string): Fecha inicio formato YYYY-MM-DD
     *   - usuario (string): Nombre de usuario (usernick)
     *   - password1 (string): Contraseña
     *   - password2 (string): Confirmación de contraseña
     * @param \App\Models\Usuario $usuarioModel Instancia del modelo para validar unicidad
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
     * Valida cambio de contraseña de usuario
     * 
     * Valida los datos para actualizar la contraseña de un usuario existente.
     * Verifica que el ID sea válido, las contraseñas no estén vacías, coincidan
     * entre sí, y cumplan con la longitud mínima de seguridad.
     * 
     * @param array $data Datos del formulario con claves:
     *   - idcolaborador (int): ID del usuario a actualizar
     *   - password1 (string): Nueva contraseña
     *   - password2 (string): Confirmación de contraseña
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
     * Valida subida de archivo de avatar de usuario
     * 
     * Realiza validación exhaustiva de archivos de imagen subidos como avatares
     * de usuario. Verifica tipo MIME real del archivo (no solo extensión) para
     * prevenir subida de archivos maliciosos, valida tamaño máximo, y confirma
     * que el archivo fue subido correctamente por PHP.
     * 
     * @param array $file Array del archivo ($_FILES['avatar']) o null
     *   Estructura esperada de $file:
     *   - error (int): Código de error de subida
     *   - size (int): Tamaño en bytes
     *   - tmp_name (string): Ruta temporal del archivo
     *   - name (string): Nombre original del archivo
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

        // Tamaño máximo: 2 MB
        $maxBytes = 2 * 1024 * 1024;
        if (isset($file['size']) && $file['size'] > $maxBytes) {
            $errors[] = 'El archivo supera el límite de 2 MB.';
        }

        // Verificar que el archivo fue subido por PHP
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'El archivo no es válido.';
            return $errors;
        }

        // Validación por tipo MIME (no solo extensión)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        // Agregamos image/webp para aceptar imágenes editadas por apps móviles
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mimeType, $allowedTypes, true)) {
            $errors[] = 'Formato de imagen no soportado. Usa JPG, PNG, GIF o WEBP.';
        }

        return $errors;
    }

    /**
     * Valida creación de usuario a partir de contrato laboral existente
     * 
     * Valida los datos para crear un usuario (colaborador) basándose en un
     * contrato laboral ya registrado. Útil para dar acceso al sistema a
     * empleados contratados. Verifica unicidad del username y seguridad
     * de contraseña.
     * 
     * @param array $data Datos del formulario con claves:
     *   - idcontrato (int): ID del contrato laboral base
     *   - usernick (string): Nombre de usuario deseado
     *   - password1 (string): Contraseña
     *   - password2 (string): Confirmación de contraseña
     * @param \App\Models\Usuario $usuarioModel Instancia del modelo para validar unicidad
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
     * Valida datos del formulario de edición de usuario
     * 
     * Realiza validación completa de todos los campos para actualizar datos
     * de un usuario existente.
     * 
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
