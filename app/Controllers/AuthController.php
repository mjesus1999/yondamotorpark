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

        // buscar colaborador (devuelve al menos idcolaborador y userpassword)
        $user = $this->usuarioModel->searchByUsernick($usernick);

        if (!$user || ($user['habilitado'] ?? 'N') !== 'S') {
            $this->view('auth.login', ['error' => 'Usuario no encontrado o inactivo.']);
            return;
        }

        // verificar contraseña
        if (!password_verify($password, $user['userpassword'])) {
            $this->view('auth.login', ['error' => 'Contraseña incorrecta.', 'old' => ['usernick' => $usernick]]);
            return;
        }

        // obtener fila completa (incluye restriccionhoraria ahora)
        $full = $this->usuarioModel->getById((int) $user['idcolaborador']);
        if (!$full) {
            $this->view('auth.login', ['error' => 'No se pudo cargar el usuario.']);
            return;
        }

        // comprobación de restricción horaria
        $restr = $full['restriccionhoraria'] ?? 'N';
        if ($restr === 'S') {
            $norm = function (string $t) {
                $t = trim($t);
                if ($t === '')
                    return '00:00';
                if (strpos($t, ':') === false)
                    $t .= ':00';
                $parts = explode(':', $t);
                $h = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $m = isset($parts[1]) ? str_pad(substr($parts[1], 0, 2), 2, '0', STR_PAD_LEFT) : '00';
                return "$h:$m";
            };

            $start = $norm(getenv('STARTIME') ?: '07:30');
            $end = $norm(getenv('ENDTIME') ?: '19:30');

            $tz = new \DateTimeZone('America/Lima');
            $now = new \DateTime('now', $tz);
            $dow = (int) $now->format('N'); // 1..7

            if ($dow === 7) {
                $this->view('auth.login', ['error' => "Acceso restringido los domingos. Acceso permitido sólo Lunes a Sábado entre $start y $end."]);
                return;
            }

            $today = $now->format('Y-m-d');
            $startDT = \DateTime::createFromFormat('Y-m-d H:i', "$today $start", $tz);
            $endDT = \DateTime::createFromFormat('Y-m-d H:i', "$today $end", $tz);

            if ($startDT === false || $endDT === false) {
                $this->view('auth.login', ['error' => "Configuración de horario inválida. Contacta al administrador."]);
                return;
            }

            if ($now < $startDT || $now > $endDT) {
                $this->view('auth.login', ['error' => "Acceso permitido sólo Lunes a Sábado entre $start y $end."]);
                return;
            }
        }

        // Login OK
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $full['idcolaborador'],
            'usernick' => $full['usernick'],
            'nombres' => $full['nombres'] ?? '',
            'apellidos' => $full['apellidos'] ?? '',
            'avatar' => $full['avatar'] ?? '/assets/images/profile.jpg',
            'idcargo' => $full['idcargo'] ?? null,
            'cargo' => $full['cargo'] ?? null,
        ];

        // actualizar ultimo acceso
        $this->usuarioModel->updateLastAccess((int) $full['idcolaborador']);

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
