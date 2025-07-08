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

    public function login(): void
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
            $this->view('auth.login', ['error' => 'Contraseña incorrecta.']);
            return;
        }

        //confirmacion
        $_SESSION['user'] = [
            'id'         => $user['idcolaborador'],
            'usernick'   => $user['usernick'],
            'nombres'    => $user['nombres'],
            'apellidos'  => $user['apellidos'],
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
}
