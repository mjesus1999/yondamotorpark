<?php
// app/Core/Controller.php

namespace App\Core;

/**
 * Clase base Controller
 * 
 * Controlador base del patrón MVC que proporciona funcionalidades comunes
 * para todos los controladores de la aplicación. Gestiona el ciclo de vida
 * de las sesiones, incluyendo timeout por inactividad, control de acceso,
 * y métodos auxiliares para renderizado de vistas y redirecciones.
 * 
 * - Gestión automática de sesiones con timeout configurable vía .env
 * - Control de acceso mediante autenticación
 * - Prevención de caché para páginas protegidas
 * - Métodos helper para vistas y navegación
 */
class Controller
{

  /**
   * Constructor del controlador
   * 
   * Inicializa y gestiona el ciclo de vida de las sesiones. Realiza las
   * siguientes operaciones automáticamente en cada petición:
   * 
   * 1. Inicia la sesión PHP si no está activa
   * 2. Lee el timeout de sesión desde variable de entorno SESSION_TIMEOUT
   * 3. Valida el tiempo de inactividad del usuario
   * 4. Destruye la sesión y redirige a login si excede el timeout
   * 5. Actualiza el timestamp de última actividad para usuarios autenticados
   */
  public function __construct()
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }

    $timeoutSeconds = 600;
    if (class_exists(\App\Config\Env::class)) {
      $timeoutSeconds = (int) \App\Config\Env::get('SESSION_TIMEOUT', '600');
    } else {
      $rawTimeout = getenv('SESSION_TIMEOUT');
      $timeoutSeconds = ($rawTimeout !== false && $rawTimeout !== '') ? (int) $rawTimeout : 600;
    }
    if ($timeoutSeconds < 60) {
      $timeoutSeconds = 600;
    }

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

  /**
   * Renderiza una vista con datos opcionales
   * 
   * Carga y muestra un archivo de vista PHP ubicado en el directorio Views.
   * Extrae el array de datos como variables locales disponibles en la vista
   * mediante la función extract().
   * 
   * @param string $path Ruta de la vista usando notación de punto
   * @param array $data Datos asociativos que serán extraídos como variables
   * @return void
   */
  protected function view(string $path, array $data = []): void
  {
    extract($data); // Extrae los datos para que estén disponibles como variables en la vista
    require __DIR__ . '/../Views/' . str_replace('.', '/', $path) . '.php';
  }

  /**
   * Redirige a una ruta específica
   * 
   * Realiza una redirección HTTP mediante el header Location y termina
   * inmediatamente la ejecución del script con exit().
   * 
   * @param string $path Ruta de destino (absoluta o relativa)
   * @return void Nunca retorna, termina la ejecución con exit()
   */
  protected function redirect(string $path): void
  {
    header("Location: " . $path);
    exit();
  }

  /**
   * Valida que el usuario esté autenticado
   * 
   * Verifica la existencia de datos de usuario en la sesión ($_SESSION['user']).
   * Si no existe, redirige automáticamente a la página de login. Además,
   * establece headers HTTP para prevenir el cacheo de páginas protegidas
   * en el navegador del cliente, evitando acceso a contenido sensible
   * mediante el botón "Atrás" después del logout.
   * 
   * Este método debe ser llamado al inicio de cualquier método del controlador
   * que requiera autenticación del usuario.
   * 
   * @return void Redirige a /login si no hay usuario autenticado
   */
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