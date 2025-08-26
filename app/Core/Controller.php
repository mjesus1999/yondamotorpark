<?php
// app/Core/Controller.php

namespace App\Core;

class Controller
{

  public function __construct()
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    // Leer timeout desde .env
    $rawTimeout = getenv('SESSION_TIMEOUT');
    $timeoutSeconds = ($rawTimeout !== false && $rawTimeout !== '') ? (int) $rawTimeout : 60;

    // Si existe last_activity, comprobar inactividad y destruir sesion si corresponde
    if (isset($_SESSION['last_activity'])) {
      $inactive = time() - (int) $_SESSION['last_activity'];
      if ($inactive > $timeoutSeconds) {
        // Cerrar sesión por inactividad
        $_SESSION = [];  // Limpiar la sesión

        // Borrar la cookie de la sesión
        if (ini_get("session.use_cookies")) {
          $params = session_get_cookie_params();
          setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
          );
        }
        session_destroy();

        session_start();
        $_SESSION['error_message'] = 'Sesión cerrada por inactividad.';

        header('Location: /login');
        exit;
      }
    }

    // Actualizar last_activity solo si hay un usuario (para evitar crear last_activity en páginas públicas)
    if (!empty($_SESSION['user'])) {
      $_SESSION['last_activity'] = time();
    }
  }
  
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
      if (empty($_SESSION['user'])) {
        header('Location: /login');
        exit;
      }
  
      // evita cache del navegador
      header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
    }

}