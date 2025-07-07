<?php
// app/Controllers/AuthController.php
namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
  public function showLogin(): void
  {
    // si ya hay sesión, va al dashboard
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!empty($_SESSION['user'])) {
      header('Location: /');
      exit;
    }
    $this->view('auth.login');
  }

  public function login(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    // aquí validas $_POST['usernick'] y $_POST['userpassword']
    // si OK:
    $_SESSION['user'] = [/* tus datos de usuario */];
    header('Location: /');
    exit;

    // si falla, podrías volver a la vista con un mensaje de error…
  }

  public function logout(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $_SESSION = [];
    session_destroy();
    header('Location: /login');
    exit;
  }
}
