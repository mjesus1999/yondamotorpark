<?php

/**
 * Controlador de Autenticación
 * 
 * app/Controllers/AuthController.php
 * 
 * Gestiona todas las operaciones de autenticación y recuperación de cuentas:
 * inicio de sesión con restricciones horarias configurables, cierre de sesión,
 * recuperación de contraseñas mediante validación de email, mantención de
 * sesión activa (keep-alive), y validación de usuarios activos. Implementa
 * control de acceso por horarios laborales (Lunes a Sábado), verificación
 * de contraseñas hasheadas con password_verify, regeneración de IDs de sesión
 * para seguridad, y actualización de último acceso para auditoría. Todas las
 * operaciones críticas validan el estado del usuario (habilitado/inactivo).
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

/**
 * Clase AuthController
 * 
 * Controlador principal para el módulo de autenticación. Maneja todas las
 * peticiones relacionadas con acceso al sistema: visualización de formularios
 * de login y recuperación, procesamiento de credenciales con validaciones
 * de seguridad, restricciones horarias para usuarios específicos, gestión
 * de sesiones con regeneración de IDs, recuperación de cuentas mediante
 * validación de email, y endpoint keep-alive para mantener sesiones activas.
 */
class AuthController extends Controller
{
    /** @var Usuario|null */
    private $usuarioModel = null;

    /**
     * Conexión a BD solo cuando hace falta (login/recuperar), no al abrir /login.
     */
    private function getUsuarioModel(): Usuario
    {
        if ($this->usuarioModel === null) {
            $this->usuarioModel = new Usuario();
        }
        return $this->usuarioModel;
    }

    /**
     * Muestra el formulario de inicio de sesión
     * 
     * Renderiza la vista del formulario de login. Si el usuario ya tiene sesión
     * activa, redirecciona automáticamente al dashboard. Maneja mensajes flash
     * de error y éxito almacenados en sesión (validaciones fallidas, recuperación
     * exitosa) y los limpia después de mostrarlos.
     * 
     * Variables flash manejadas:
     * - login_error: Mensajes de error de autenticación
     * - login_success: Mensajes de éxito (recuperación de contraseña)
     * - login_old: Datos del formulario anterior (username) para repoblar
     * 
     * @return void Renderiza vista auth.login o redirecciona a dashboard
     */
    public function showLogin(): void
    {
        if (!empty($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
        $error = $_SESSION['login_error'] ?? null;
        $message = $_SESSION['login_success'] ?? null;
        $old = $_SESSION['login_old'] ?? null;

        // Eliminar después de usarlos
        unset($_SESSION['login_error'], $_SESSION['login_success'], $_SESSION['login_old']);

        $this->view('auth.login', [
            'error' => $error,
            'message' => $message,
            'old' => $old
        ]);
    }

    /**
     * Muestra el formulario de recuperación de cuenta
     * 
     * Renderiza la vista para recuperar contraseña. Si se proporciona un
     * usernick en la URL, busca el usuario y precarga sus datos (email,
     * teléfono) para facilitar la validación. Esto permite que el usuario
     * llegue desde el login con su username ya ingresado.
     * 
     * Flujo de precarga:
     * 1. Recibe usernick de query string (?usernick=username)
     * 2. Busca usuario en base de datos
     * 3. Si existe, obtiene datos completos (email, teléfono)
     * 4. Precarga formulario con username y muestra email para validación
     * 
     * @return void Renderiza vista auth.recoverAccount con datos precargados o vacía
     */
    public function showRecoverForm(): void
    {
        $usernick = trim($_GET['usernick'] ?? '');
        $data = [];

        if ($usernick !== '') {
            // Buscar colaborador por usernick
            $user = $this->getUsuarioModel()->searchByUsernick($usernick);
            if ($user) {
                $full = $this->getUsuarioModel()->getById((int) $user['idcolaborador']);
                $data['usernick'] = $usernick;
                $data['email'] = $full['email'] ?? '';
                $data['telprimario'] = $full['telprimario'] ?? '';
            } else {
                $data['usernick'] = $usernick;
            }
        }

        $this->view('auth.recoverAccount', $data);
    }

    /**
     * Procesa el inicio de sesión con restricciones horarias
     * 
     * Autentica usuarios mediante validación de credenciales, verificación de
     * estado activo, y opcionalmente restricciones horarias configurables.
     * Actualiza última conexión para auditoría y regenera session_id por seguridad.
     *
     *Restricciones horarias (si habilitado):
     * - Solo Lunes a Sábado (Domingo bloqueado)
     * - Horario configurable vía ENV: STARTIME y ENDTIME
     * - Formato 24h: "08:00" a "20:00" por ejemplo
     * - Soporte para intervalos que cruzan medianoche
     * - Simulación de fecha/hora con SIMULATE_NOW (testing)
     * 
     * Variables de entorno requeridas para restricciones:
     * - STARTIME: Hora de inicio permitida (formato HH:MM)
     * - ENDTIME: Hora de fin permitida (formato HH:MM)
     * - SIMULATE_NOW: Fecha/hora simulada para testing (opcional)
     * 
     * Datos almacenados en sesión:
     * - user['id']: ID del colaborador
     * - user['usernick']: Nombre de usuario
     * - user['nombres']: Nombres del usuario
     * - user['apellidos']: Apellidos del usuario
     * - user['avatar']: Ruta de foto de perfil
     * - user['idcargo']: ID del cargo asignado
     * - user['cargo']: Nombre del cargo
     * - last_activity: Timestamp de última actividad
     *
     * @return void Redirecciona a dashboard o muestra error en login
     */
    public function login(): void
    {
        $usernick = trim($_POST['usernick'] ?? '');
        $password = $_POST['userpassword'] ?? '';

        // Buscar usuario
        $user = $this->getUsuarioModel()->searchByUsernick($usernick);

        if (!$user || ($user['habilitado'] ?? 'N') !== 'S') {
            $_SESSION['login_error'] = 'Usuario no encontrado o inactivo.';
            header('Location: /login');
            exit;
        }

        // Verificar contraseña
        if (!password_verify($password, $user['userpassword'])) {
            $_SESSION['login_error'] = 'Contraseña incorrecta.';
            $_SESSION['login_old'] = ['usernick' => $usernick];
            header('Location: /login');
            exit;
        }

        // Restricción horaria
        $restr = $user['restriccionhoraria'] ?? 'N';
        if ($restr === 'S') {
            $norm = function (string $t) {
                $t = trim($t);
                if ($t === '')
                    return null;
                if (strpos($t, ':') === false)
                    $t .= ':00';
                $parts = explode(':', $t);
                $h = (int) $parts[0];
                $m = isset($parts[1]) ? (int) $parts[1] : 0;
                if ($h < 0 || $h > 23 || $m < 0 || $m > 59)
                    return null;
                return sprintf('%02d:%02d', $h, $m);
            };

            $start = $norm(getenv('STARTIME'));
            $end = $norm(getenv('ENDTIME'));

            if ($start === null || $end === null) {
                $this->view('auth.login', ['error' => 'Configuración de horario inválida. Contacta al administrador.']);
                return;
            }

            $tz = new \DateTimeZone('America/Lima');
            $simulate = getenv('SIMULATE_NOW') ?: null;
            $now = $simulate ? new \DateTime($simulate, $tz) : new \DateTime('now', $tz);
            $dow = (int) $now->format('N'); // 1=Lun a 7=Dom

            if ($dow === 7) {
                $this->view('auth.login', ['error' => "Acceso restringido los domingos. Acceso permitido sólo Lunes a Sábado entre $start y $end."]);
                return;
            }

            $today = $now->format('Y-m-d');
            $startDT = \DateTime::createFromFormat('Y-m-d H:i', "$today $start", $tz);
            $endDT = \DateTime::createFromFormat('Y-m-d H:i', "$today $end", $tz);

            // Manejar intervalos que cruzan medianoche
            $isInside = false;
            if ($startDT <= $endDT) {
                $isInside = ($now >= $startDT && $now <= $endDT);
            } else {
                $endNext = clone $endDT;
                $endNext->modify('+1 day');
                if ($now >= $startDT || $now <= $endNext) {
                    $isInside = true;
                }
            }

            if (!$isInside) {
                $_SESSION['login_error'] = "Acceso permitido sólo Lunes a Sábado entre $start y $end.";
                $_SESSION['login_old'] = ['usernick' => $usernick];
                header('Location: /login');
                exit;
            }
        }

        // Regenerar ID y poblar sesión
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['idcolaborador'],
            'usernick' => $user['usernick'],
            'nombres' => $user['nombres'] ?? '',
            'apellidos' => $user['apellidos'] ?? '',
            'avatar' => $user['avatar'],
            'idcargo' => $user['idcargo'] ?? null,
            'cargo' => $user['cargo'] ?? null,
        ];

        $_SESSION['last_activity'] = time();
        $this->getUsuarioModel()->updateLastAccess((int) $user['idcolaborador']);

        header('Location: /');
        exit;
    }

    /**
     * Cierra la sesión del usuario
     * 
     * Destruye completamente la sesión del usuario: limpia todas las variables
     * de sesión, destruye la sesión en el servidor, y redirecciona al formulario
     * de login. Es el método seguro para cerrar sesión eliminando todos los
     * datos almacenados.
     *
     * Proceso de cierre de sesión:
     * 1. Limpiar array $_SESSION (eliminar todas las variables)
     * 2. Destruir sesión en servidor (session_destroy)
     * 3. Redireccionar a página de login
     *
     * @return void Redirecciona a /login después de destruir sesión
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'] ?? '/',
                    $params['domain'] ?? '',
                    (bool) ($params['secure'] ?? false),
                    (bool) ($params['httponly'] ?? true)
                );
            }
            session_destroy();
        }
        header('Location: /login');
        exit;
    }

    /**
     * Procesa la recuperación de contraseña con validación de email
     * 
     * Permite a usuarios recuperar acceso a su cuenta mediante validación de
     * email registrado. Valida múltiples condiciones de seguridad, verifica
     * que el email proporcionado coincida con el registrado en el sistema,
     * y actualiza la contraseña con hash seguro.
     *
     * @return void Renderiza vista con mensaje de éxito o error
     */
    public function handleRecover(): void
    {
        $action = $_POST['action'] ?? 'send_code';

        if ($action === 'send_code') {
            $this->sendVerificationCode();
        } elseif ($action === 'verify_code') {
            $this->verifyCode();
        } elseif ($action === 'change_password') {
            $this->changePassword();
        }
    }


    // RECUPERACION DE CONTRASEÑA CON CODIGO

    private function sendVerificationCode(): void
    {
        $usernick = trim($_POST['usernick'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telprimario = trim($_POST['telprimario'] ?? '');

        if (empty($usernick) || empty($email) || empty($telprimario)) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Completa todos los campos.',
                'step'          => 1,
                'usernick'      => $usernick,
                'email'         => $email,
                'telprimario'   => $telprimario
            ]);
            return;
        }

        $user = $this->getUsuarioModel()->searchByUsernick($usernick);
        if (!$user) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Usuario no encontrado.',
                'step'          => 1,
                'usernick'      => $usernick
            ]);
            return;
        }

        $full = $this->getUsuarioModel()->getById((int) $user['idcolaborador']);
        $emailStored = $full['email'] ?? '';
        $telStored = $full['telprimario'] ?? '';

        if (mb_strtolower(trim($emailStored)) !== mb_strtolower(trim($email))) {
            $this->view('auth.recoverAccount', [
                'error'         => 'El email no coincide con el registrado.',
                'step'          => 1,
                'usernick'      => $usernick,
                'email'         => $email,
                'telprimario'   => $telprimario
            ]);
            return;
        }

        if (trim($telStored) !== trim($telprimario)) {
            $this->view('auth.recoverAccount', [
                'error'         => 'El teléfono no coincide con el registrado.',
                'step'          => 1,
                'usernick'      => $usernick,
                'email'         => $email,
                'telprimario'   => $telprimario
            ]);
            return;
        }

        // Generar código de 6 dígitos
        $code = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar en sesión con timestamp
        $_SESSION['recovery_code'] = $code;
        $_SESSION['recovery_user'] = $usernick;
        $_SESSION['recovery_time'] = time();
        $_SESSION['recovery_attempts'] = 0;

        require_once __DIR__ . '/../Helpers/ApiSms.php';
        $apiSms = new \ApiSms();
        $message = "Motorpark Yonda - Tu codigo de recuperacion es: $code. Valido por 10 minutos.";

        $sent = $apiSms->sendMessage($telprimario, $message);

        if (!$sent) {
            $this->view('auth.recoverAccount', [
                'error'         => 'No se pudo enviar el código SMS. Intenta más tarde.',
                'step'          => 1,
                'usernick'      => $usernick,
                'email'         => $email,
                'telprimario'   => $telprimario
            ]);
            return;
        }

        // Ir a paso 2
        $this->view('auth.recoverAccount', [
            'message'           => 'Código enviado a tu teléfono. Verifica e ingresa el código.',
            'step'              => 2,
            'usernick'          => $usernick,
            'telprimario'       => $telprimario
        ]);
    }

    private function verifyCode(): void
    {
        $usernick = trim($_POST['usernick'] ?? '');
        $code = trim($_POST['code'] ?? '');

        if (empty($code)) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Ingresa el código recibido.',
                'step'          => 2,
                'usernick'      => $usernick
            ]);
            return;
        }

        // Verificar sesión
        if (empty($_SESSION['recovery_code']) || empty($_SESSION['recovery_user'])) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Sesión expirada. Solicita un nuevo código.',
                'step'          => 1
            ]);
            return;
        }

        if ($_SESSION['recovery_user'] !== $usernick) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Usuario no coincide con la solicitud.',
                'step'          => 1
            ]);
            return;
        }

        // Verificar tiempo (10 minutos)
        $elapsed = time() - ($_SESSION['recovery_time'] ?? 0);
        if ($elapsed > 600) {
            unset($_SESSION['recovery_code'], $_SESSION['recovery_user'], $_SESSION['recovery_time']);
            $this->view('auth.recoverAccount', [
                'error'         => 'Código expirado. Solicita uno nuevo.',
                'step'          => 1
            ]);
            return;
        }

        // Verificar intentos
        $_SESSION['recovery_attempts'] = ($_SESSION['recovery_attempts'] ?? 0) + 1;
        if ($_SESSION['recovery_attempts'] > 3) {
            unset($_SESSION['recovery_code'], $_SESSION['recovery_user'], $_SESSION['recovery_time']);
            $this->view('auth.recoverAccount', [
                'error'         => 'Demasiados intentos. Solicita un nuevo código.',
                'step'          => 1
            ]);
            return;
        }

        // Verificar código
        if ($code !== $_SESSION['recovery_code']) {
            $remaining = 3 - $_SESSION['recovery_attempts'];
            $this->view('auth.recoverAccount', [
                'error'         => "Código incorrecto. Te quedan $remaining intentos.",
                'step'          => 2,
                'usernick'      => $usernick
            ]);
            return;
        }

        // Código correcto - ir a paso 3
        $_SESSION['recovery_verified'] = true;
        $this->view('auth.recoverAccount', [
            'message'           => 'Código verificado. Ahora cambia tu contraseña.',
            'step'              => 3,
            'usernick'          => $usernick
        ]);
    }

    private function changePassword(): void
    {
        $usernick = trim($_POST['usernick'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Verificar que pasó la verificación
        if (empty($_SESSION['recovery_verified']) || $_SESSION['recovery_user'] !== $usernick) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Debes verificar el código primero.',
                'step'          => 1
            ]);
            return;
        }

        if (empty($password) || empty($password_confirm)) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Completa ambos campos de contraseña.',
                'step'          => 3,
                'usernick'      => $usernick
            ]);
            return;
        }

        if (strlen($password) < 8) {
            $this->view('auth.recoverAccount', [
                'error'         => 'La contraseña debe tener al menos 8 caracteres.',
                'step'          => 3,
                'usernick'      => $usernick
            ]);
            return;
        }

        if ($password !== $password_confirm) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Las contraseñas no coinciden.',
                'step'          => 3,
                'usernick'      => $usernick
            ]);
            return;
        }

        $user = $this->getUsuarioModel()->searchByUsernick($usernick);
        if (!$user) {
            $this->view('auth.recoverAccount', [
                'error'         => 'Usuario no encontrado.',
                'step'          => 1
            ]);
            return;
        }

        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $ok = $this->getUsuarioModel()->updatePassword((int) $user['idcolaborador'], $newHash);

        if ($ok) {
            // Limpiar sesión de recuperación
            unset(
                $_SESSION['recovery_code'],
                $_SESSION['recovery_user'],
                $_SESSION['recovery_time'],
                $_SESSION['recovery_attempts'],
                $_SESSION['recovery_verified']
            );

            $_SESSION['login_success'] = 'Contraseña actualizada exitosamente. Ya puedes iniciar sesión.';
            $_SESSION['login_old'] = ['usernick' => $usernick];
            header('Location: /login');
            exit;
        }

        $this->view('auth.recoverAccount', [
            'error'             => 'No se pudo actualizar la contraseña. Intenta más tarde.',
            'step'              => 3,
            'usernick'          => $usernick
        ]);
    }

    /**
     * Endpoint para mantener sesión activa (keep-alive)
     * 
     * API REST que actualiza el timestamp de última actividad del usuario
     * para prevenir timeout de sesión. Utilizado por JavaScript frontend
     * para mantener sesiones activas mediante polling periódico mientras
     * el usuario está en el sistema.
     *
     * @return void Respuesta JSON con estado de sesión
     */
    public function keepAlive(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'No session']);
            return;
        }

        $_SESSION['last_activity'] = time();
        echo json_encode(['ok' => true, 'last_activity' => $_SESSION['last_activity']]);
    }

}
