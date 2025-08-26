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
        $this->usuarioModel = new Usuario();
    }

    public function showLogin(): void
    {
        if (!empty($_SESSION['user'])) {
            header('Location: /');
            exit;
        }
        $error = $_SESSION['login_error'] ?? null;
        $message = $_SESSION['login_success'] ?? null;
        $old = $_SESSION['login_old'] ?? null;

        // Eliminar después de usarlos (para que no persistan)
        unset($_SESSION['login_error'], $_SESSION['login_success'], $_SESSION['login_old']);

        $this->view('auth.login', [
            'error' => $error,
            'message' => $message,
            'old' => $old
        ]);
    }

    public function showRecoverForm(): void
    {
        $usernick = trim($_GET['usernick'] ?? '');
        $data = [];

        if ($usernick !== '') {
            // Buscar colaborador por usernick
            $user = $this->usuarioModel->searchByUsernick($usernick);
            if ($user) {
                $full = $this->usuarioModel->getById((int) $user['idcolaborador']);
                $data['usernick'] = $usernick;
                $data['email'] = $full['email'] ?? '';
                $data['telprimario'] = $full['telprimario'] ?? '';
            } else {
                $data['usernick'] = $usernick;
            }
        }

        $this->view('auth.recoverAccount', $data);
    }

    public function login(): void
    {
        $usernick = trim($_POST['usernick'] ?? '');
        $password = $_POST['userpassword'] ?? '';

        // Buscar usuario
        $user = $this->usuarioModel->searchByUsernick($usernick);

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
        $this->usuarioModel->updateLastAccess((int) $user['idcolaborador']);

        header('Location: /');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function handleRecover(): void
    {
        $usernick = trim($_POST['usernick'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validaciones
        if (empty($usernick) || empty($email) || empty($password) || empty($password_confirm)) {
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

        // Buscar usuario por usernick
        $user = $this->usuarioModel->searchByUsernick($usernick);
        if (!$user) {
            $this->view('auth.recoverAccount', ['error' => 'Usuario no encontrado.', 'usernick' => $usernick, 'email' => $email]);
            return;
        }

        // Obtener email real desde getById
        $full = $this->usuarioModel->getById((int) $user['idcolaborador']);
        $emailStored = $full['email'] ?? '';

        if ($emailStored === '') {
            $this->view('auth.recoverAccount', ['error' => 'El usuario no tiene email registrado.', 'usernick' => $usernick]);
            return;
        }

        // Comparar emails
        if (mb_strtolower(trim($emailStored)) !== mb_strtolower(trim($email))) {
            $this->view('auth.recoverAccount', ['error' => 'El email no coincide con el usuario.', 'usernick' => $usernick, 'email' => $email]);
            return;
        }

        // Actualizar contraseña
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $ok = $this->usuarioModel->updatePassword((int) $user['idcolaborador'], $newHash);

        if ($ok) {
            $this->view('auth.login', ['message' => 'Contraseña actualizada. Ya puedes iniciar sesión.', 'old' => ['usernick' => $usernick]]);
            return;
        }

        $this->view('auth.recoverAccount', ['error' => 'No se pudo actualizar la contraseña. Intenta más tarde.', 'usernick' => $usernick, 'email' => $email]);
    }

    // Mantener la sesión activa
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
