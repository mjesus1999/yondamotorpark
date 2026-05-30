<?php
// app/Core/Controller.php

namespace App\Core;

use App\Config\Env;
use App\Models\Permisos;

/**
 * Clase base Controller (compatible PHP 7.4+ / Hostinger).
 */
class Controller
{
  /** @var array<string, string|string[]|null>|null */
  private static $controllerModulos = null;

  public function __construct()
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
  }

  protected function view(string $path, array $data = []): void
  {
    extract($data);
    require __DIR__ . '/../Views/' . str_replace('.', '/', $path) . '.php';
  }

  protected function redirect(string $path): void
  {
    header("Location: " . $path);
    exit();
  }

  /**
   * @param string|string[]|null $modulos Módulo(s) requeridos.
   *        Omitir argumento = mapa automático por controlador.
   *        null = solo autenticación.
   */
  protected function authRequired($modulos = '__auto__'): void
  {
    $this->ensureSessionActive();

    if (empty($_SESSION['user'])) {
      header('Location: /login');
      exit;
    }

    $this->enforceSessionTimeout();

    if ($modulos === '__auto__') {
      $modulos = $this->resolveModulosFromMap();
    }

    if ($modulos !== null && $modulos !== '' && $modulos !== []) {
      if (!$this->usuarioTienePermiso($modulos)) {
        $this->forbidden();
      }
    }

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
  }

  private function ensureSessionActive(): void
  {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
  }

  private function enforceSessionTimeout(): void
  {
    $timeoutSeconds = (int) Env::get('SESSION_TIMEOUT', '600');
    if ($timeoutSeconds < 60) {
      $timeoutSeconds = 600;
    }

    if (!isset($_SESSION['last_activity'])) {
      $_SESSION['last_activity'] = time();
      return;
    }

    $inactive = time() - (int) $_SESSION['last_activity'];
    if ($inactive <= $timeoutSeconds) {
      $_SESSION['last_activity'] = time();
      return;
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        isset($params['path']) ? $params['path'] : '/',
        isset($params['domain']) ? $params['domain'] : '',
        !empty($params['secure']),
        !empty($params['httponly'])
      );
    }
    session_destroy();
    session_start();
    $_SESSION['error_message'] = 'Sesión cerrada por inactividad.';
    header('Location: /login');
    exit;
  }

  /** @return string|string[]|null */
  private function resolveModulosFromMap()
  {
    if (self::$controllerModulos === null) {
      $file = __DIR__ . '/../Config/ControllerModulos.php';
      self::$controllerModulos = is_readable($file) ? require $file : [];
    }
    $class = static::class;
    return isset(self::$controllerModulos[$class]) ? self::$controllerModulos[$class] : null;
  }

  /** @param string|string[] $modulos */
  private function usuarioTienePermiso($modulos): bool
  {
    if (!Env::getBool('PERMISOS_ENFORCED', true)) {
      return true;
    }

    $idCargo = (int) (isset($_SESSION['user']['idcargo']) ? $_SESSION['user']['idcargo'] : 0);
    if ($idCargo <= 0) {
      return false;
    }

    try {
      $permisosModel = new Permisos();
      $permitidos = $permisosModel->getPermisosByCargo($idCargo);
    } catch (\Throwable $e) {
      error_log('Permisos no disponibles: ' . $e->getMessage());
      return false;
    }

    $requeridos = is_array($modulos) ? $modulos : [$modulos];
    foreach ($requeridos as $modulo) {
      if (in_array($modulo, $permitidos, true)) {
        return true;
      }
    }
    return false;
  }

  protected function forbidden(): void
  {
    http_response_code(403);
    require __DIR__ . '/../Views/errors/403.php';
    exit;
  }
}
