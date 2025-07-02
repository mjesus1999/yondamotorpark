<?php
// public/index.php

// Define el directorio raíz de la aplicación para mayor claridad
define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/app/Core/Autoloader.php';
require APP_ROOT . '/vendor/autoload.php';

//Variable de entorno desde .env
$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

// Registra el autocargador
//App\Core\Autoloader::register();
Autoloader::register();

use App\Core\Router;

$router = new Router();

//RUTAS POR MÓDULO
require APP_ROOT . '/app/Routes/Home.router.php';
require APP_ROOT . '/app/Routes/Marca.router.php';
require APP_ROOT . '/app/Routes/Vehiculo.router.php';
require APP_ROOT . '/app/Routes/Local.router.php';
require APP_ROOT . '/app/Routes/Ubigeo.router.php';
require APP_ROOT . '/app/Routes/Motorpark.router.php';

// Un controlador básico para la página de inicio
class HomeController extends App\Core\Controller
{
  public function index()
  {
    $this->view('home.index');
  }
}

$router->dispatch();