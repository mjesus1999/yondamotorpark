<?php
// app/Core/Controller.php

namespace App\Core;

class Controller
{
  protected function view(string $path, array $data = []): void
  {
    extract($data); // Extrae los datos para que estén disponibles como variables en la vista
    require __DIR__ . '/../Views/' . str_replace('.', '/', $path) . '.php';
  }

  protected function redirect(string $path): void
  {
    header("Location: " . $path);
    exit();
  }

  protected function authRequired(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    if (empty($_SESSION['user'])) {
      header('Location: /login'); //accede
      exit;
    }

    // Tiempo máximo de inactividad en segundos
    $timeoutSeconds = 60; // 1 minuto

    // Si existe last_activity, comprobar inactividad
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - (int) $_SESSION['last_activity'];
        if ($inactive > $timeoutSeconds) {
            // cerrar sesión por inactividad
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();

            // opcional: mensaje flash (se puede leer en /login)
            session_start();
            $_SESSION['error_message'] = 'Sesión cerrada por inactividad.';
            header('Location: /login');
            exit;
        }
    }

    // Actualizar last_activity para esta petición
    $_SESSION['last_activity'] = time();

    //evita cache del navegador
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
  }

}