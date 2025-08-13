<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;

class AuthController extends Controller
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->usuarioModel = new Usuario();
    }

    public function showLogin(): void
    {
        //redireccion al dashboard si inicia sesion
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        if (!empty($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
        $this->view('auth.login');
    }

    /**
     * MOSTRAR FORMULARIO DE RECUPERAR CUENTA (AL EXTRAVIO DE LA CLAVE)
     * MOSTRANDO EL USERNICK (IDCOLABORADOR)
     * MOSTRANDO EL EMAIL (DE ESE USERNICK = IDCOLABORADOR)
     */

    public function showRecoverForm(): void
    {
        $usernick = trim($_GET['usernick'] ?? '');
        $data = [];

        if ($usernick !== '') {
            // buscar colaborador por usernick
            $user = $this->usuarioModel->searchByUsernick($usernick);
            if ($user) {
                $full = $this->usuarioModel->getById((int) $user['idcolaborador']);
                $data['usernick'] = $usernick;
                $data['email'] = $full['email'] ?? '';
            } else {
                // si no existe, igual enviamos el usernick para mostrarlo
                $data['usernick'] = $usernick;
            }
        }

        $this->view('auth.recoverAccount', $data);
    }

    public function login(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();

        $usernick = trim($_POST['usernick'] ?? '');
        $password = $_POST['userpassword'] ?? '';

        //Buscar usuario
        $user = $this->usuarioModel->searchByUsernick($usernick);

        if (!$user || ($user['habilitado'] ?? 'N') !== 'S') {
            $this->view('auth.login', ['error' => 'Usuario no encontrado o inactivo.']);
            return;
        }

        //Verificar contraseña
        if (!password_verify($password, $user['userpassword'])) {
            $this->view('auth.login', ['error' => 'Contraseña incorrecta.', 'old' => ['usernick' => $usernick]]);
            return;
        }

        //Restricción horaria
        $restr = $user['restriccionhoraria'] ?? 'N';
        if ($restr === 'S') {
            //HH:MM
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

            $start = $norm(getenv('STARTIME') ?: '07:30');
            $end = $norm(getenv('ENDTIME') ?: '19:30');

            if ($start === null || $end === null) {
                $this->view('auth.login', ['error' => 'Configuración de horario inválida. Contacta al administrador.']);
                return;
            }

            $tz = new \DateTimeZone('America/Lima');
            //prueba para permitir forzar hora con env SIMULATE_NOW
            $simulate = getenv('SIMULATE_NOW') ?: null;
            $now = $simulate ? new \DateTime($simulate, $tz) : new \DateTime('now', $tz);
            $dow = (int) $now->format('N'); //1=Lun a 7 =Dom

            if ($dow === 7) {
                $this->view('auth.login', ['error' => "Acceso restringido los domingos. Acceso permitido sólo Lunes a Sábado entre $start y $end."]);
                return;
            }

            $today = $now->format('Y-m-d');
            $startDT = \DateTime::createFromFormat('Y-m-d H:i', "$today $start", $tz);
            $endDT = \DateTime::createFromFormat('Y-m-d H:i', "$today $end", $tz);

            // Manejar intervalos que cruzan medianoche (p. ej. 22:00 - 06:00)
            $isInside = false;
            if ($startDT <= $endDT) {
                //para el horario normal:
                $isInside = ($now >= $startDT && $now <= $endDT);
            } else {
                // cruza medianoche: acceso si ahora >= start o ahora <= end (siguiente día)
                $endNext = clone $endDT;
                $endNext->modify('+1 day');
                if ($now >= $startDT || $now <= $endNext) {
                    $isInside = true;
                }
            }

            if (!$isInside) {
                $this->view('auth.login', ['error' => "Acceso permitido sólo Lunes a Sábado entre $start y $end."]);
                return;
            }
        }

        //regenerar id y poblar sesión => login
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
        //Actualizar acceso
        $this->usuarioModel->updateLastAccess((int) $user['idcolaborador']);

        header('Location: /');
        exit;
    }

    public function login1(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();

        $usernick = trim($_POST['usernick'] ?? '');
        $password = $_POST['userpassword'] ?? '';

        //buscar colaborador
        $user = $this->usuarioModel->searchByUsernick($usernick);

        if (!$user || $user['habilitado'] !== 'S') {
            $this->view('auth.login', ['error' => 'Usuario no encontrado o inactivo.']);
            return;
        }

        //verificar contraseña
        if (!password_verify($password, $user['userpassword'])) {
            $this->view('auth.login', ['error' => 'Contraseña incorrecta.', 'old' => ['usernick' => $usernick]]);
            return;
        }

        //confirmacion
        $_SESSION['user'] = [
            'id' => $user['idcolaborador'],
            'usernick' => $user['usernick'],
            'nombres' => $user['nombres'],
            'apellidos' => $user['apellidos'],
            'avatar' => $user['avatar'],
            /* 'avatar' => $user['avatar'] ?? '/assets/images/profile.jpg', */ //prueba
            'idcargo' => $user['idcargo'],
            'cargo' => $user['cargo'],
        ];

        header('Location: /');
        exit;
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /login');
        exit;
    }

    /**
     * MANEJAR LA RECUPERACION DE CUENTA 
     * AL PERDER LA CLAVE
     */

    public function handleRecover(): void
    {
        $usernick = trim($_POST['usernick'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // validaciones mínimas
        if ($usernick === '' || $email === '' || $password === '' || $password_confirm === '') {
            $this->view('auth.recoverAccount', ['error' => 'Completa todos los campos.', 'usernick' => $usernick, 'email' => $email]);
            return;
        }
        if (strlen($password) < 8) {
            $this->view('auth.recoverAccount', ['error' => 'La contraseña debe tener al menos 8 caracteres.', 'usernick' => $usernick, 'email' => $email]);
            return;
        }
        if ($password !== $password_confirm) {
            $this->view('auth.recoverAccount', ['error' => 'Las contraseñas no coinciden.', 'usernick' => $usernick, 'email' => $email]);
            return;
        }

        // buscar colaborador por usernick
        $user = $this->usuarioModel->searchByUsernick($usernick);
        if (!$user) {
            $this->view('auth.recoverAccount', ['error' => 'Usuario no encontrado.', 'usernick' => $usernick, 'email' => $email]);
            return;
        }

        // obtener email real desde getById
        $full = $this->usuarioModel->getById((int) $user['idcolaborador']);
        $emailStored = $full['email'] ?? '';

        if ($emailStored === '') {
            $this->view('auth.recoverAccount', ['error' => 'El usuario no tiene email registrado.', 'usernick' => $usernick]);
            return;
        }

        // comparar emails (case-insensitive)
        if (mb_strtolower(trim($emailStored)) !== mb_strtolower(trim($email))) {
            $this->view('auth.recoverAccount', ['error' => 'El email no coincide con el usuario.', 'usernick' => $usernick, 'email' => $email]);
            return;
        }

        // actualizar contraseña
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $ok = $this->usuarioModel->updatePassword((int) $user['idcolaborador'], $newHash);

        if ($ok) {
            $this->view('auth.login', ['message' => 'Contraseña actualizada. Ya puedes iniciar sesión.', 'old' => ['usernick' => $usernick]]);
            return;
        }

        $this->view('auth.recoverAccount', ['error' => 'No se pudo actualizar la contraseña. Intenta más tarde.', 'usernick' => $usernick, 'email' => $email]);
    }


}
