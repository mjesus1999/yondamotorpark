<?php
// public/index.php



// Define el directorio raíz de la aplicación para mayor claridad
define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/app/Core/Autoloader.php';
require APP_ROOT . '/vendor/autoload.php';

// Registrar autoload propio (namespace App\*)
Autoloader::register();

//Variable de entorno desde .env
$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->load();


if (!ini_get('date.timezone')) {
  date_default_timezone_set('America/Lima');
}

/**
 * Asegurar que las variables cargadas por phpdotenv estén también
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
  // mantener $_SERVER
  if (!isset($_SERVER[$key])) {
    $_SERVER[$key] = $value;
  }
}

/**
 * SESSION: leer timeout desde .env y aplicar antes de session_start()
 * Alinear para que no borre sesiones antes del timeout
 */

$timeout = (int) (getenv('SESSION_TIMEOUT') ?: 60);
ini_set('session.gc_maxlifetime', (string) max(1440, $timeout));

//sesion y cookie params
session_name('YONDASESSID');
session_set_cookie_params([
  'lifetime' => 0,    // 0 = expira al cerrar navegador
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

// echo "Intentando cargar: " . APP_ROOT . '/app/Routes/ConceptoPagos.router.php' . "<br>";
//RUTAS POR MÓDULO
require APP_ROOT . '/app/Routes/Home.router.php';
require APP_ROOT . '/app/Routes/Legacy.router.php';
require APP_ROOT . '/app/Routes/Marca.router.php';
require APP_ROOT . '/app/Routes/Local.router.php';
require APP_ROOT . '/app/Routes/Ubigeo.router.php';
require APP_ROOT . '/app/Routes/Motorpark.router.php';
require APP_ROOT . '/app/Routes/Persona.router.php';
require APP_ROOT . '/app/Routes/Cliente.router.php';
require APP_ROOT . '/app/Routes/Tienda.router.php';
require APP_ROOT . '/app/Routes/Concesionario.router.php';
require APP_ROOT . '/app/Routes/TipoVehiculo.router.php';
require APP_ROOT . '/app/Routes/Modelo.router.php';
require APP_ROOT . '/app/Routes/Vehiculo.router.php';
require APP_ROOT . '/app/Routes/DetalleOC.router.php';
require APP_ROOT . '/app/Routes/OC.router.php';
require APP_ROOT . '/app/Routes/PagosOC.router.php';
require APP_ROOT . '/app/Routes/Compra.router.php';
require APP_ROOT . '/app/Routes/Caja.router.php';
require APP_ROOT . '/app/Routes/PagoCronograma.router.php';
require APP_ROOT . '/app/Routes/Comprobantes.router.php';
require APP_ROOT . '/app/Routes/Egreso.router.php';
require APP_ROOT . '/app/Routes/ArqueoCaja.router.php';
require APP_ROOT . '/app/Routes/Contrato.router.php';
require APP_ROOT . '/app/Routes/FichaSolicitud.router.php';
require APP_ROOT .'/app/Routes/ConceptoPagos.router.php';
require APP_ROOT . '/app/Routes/Usuario.router.php';
require APP_ROOT . '/app/Routes/Auth.router.php';
require APP_ROOT . '/app/Routes/ForCotizacion.router.php';
require APP_ROOT . '/app/Routes/Cotizacion.router.php';
require APP_ROOT .'/app/Routes/Credito.router.php';
require APP_ROOT .'/app/Routes/Cobranza.router.php';
require APP_ROOT . '/app/Routes/Nubefact.router.php';
require APP_ROOT . '/app/Routes/Producto.router.php';




$router->dispatch();