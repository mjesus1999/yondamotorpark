<?php
// public/index.php

// Define el directorio raíz de la aplicación para mayor claridad
define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/app/Core/Autoloader.php';
require APP_ROOT . '/vendor/autoload.php';

//Variable de entorno desde .env
$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
//$dotenv->safeLoad();
$dotenv->load();

//para leer .ENV
//fijar timezone (tmp)
if (!ini_get('date.timezone')) {
  date_default_timezone_set('America/Lima');
}

/**
 * 2) Asegurar que las variables cargadas por phpdotenv estén también
 * como variables de entorno accesibles por getenv() y en $_SERVER.
 */

foreach ($_ENV as $key => $value) {
  //solo strings (evita arrays/objetos)
  if (!is_string($value))
    continue;
  // setear en entorno C-level si no existe
  if (getenv($key) === false) {
    putenv(sprintf('%s=%s', $key, $value));
  }
  // mantener también en $_SERVER si no está
  if (!isset($_SERVER[$key])) {
    $_SERVER[$key] = $value;
  }
}

/**
 * SESSION: leer timeout desde .env y aplicar antes de session_start()
 * Alinear para que no borre sesiones antes del timeout
 */

$timeout = (int) (getenv('SESSION_TIMEOUT') ?: 60);
// opcional: ajustar gc_maxlifetime (asegúrate >= $timeout)
ini_set('session.gc_maxlifetime', (string) max(1440, $timeout));

// usar nombre de sesión propio y cookie params
session_name('YONDASESSID');
session_set_cookie_params([
    'lifetime' => 0,    // 0 = expira al cerrar navegador (recomendado)
    'path' => '/',
    'domain' => '',
    'secure' => false,  // poner true en producción con HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}


// Registra el autocargador
//App\Core\Autoloader::register();
Autoloader::register();

use App\Core\Router;

$router = new Router();

//RUTAS POR MÓDULO
require APP_ROOT . '/app/Routes/Home.router.php';
require APP_ROOT . '/app/Routes/Marca.router.php';
require APP_ROOT . '/app/Routes/Vehiculo.router.php';
require APP_ROOT . '/app/Routes/Usuario.router.php';
require APP_ROOT . '/app/Routes/Ubigeo.router.php';
require APP_ROOT . '/app/Routes/Persona.router.php';
require APP_ROOT . '/app/Routes/Auth.router.php';
require APP_ROOT . '/app/Routes/ForCotizacion.router.php';
require APP_ROOT . '/app/Routes/TipoVehiculos.router.php';
require APP_ROOT . '/app/Routes/Cotizacion.router.php';

// Un controlador básico para la página de inicio
class HomeController extends App\Core\Controller
{
  public function index()
  {
    $this->view('home.index');
  }
}

$router->dispatch();