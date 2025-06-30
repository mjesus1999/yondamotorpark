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

// Rutas para los productos
$router->add('GET', '/', 'HomeController', 'index'); // Ruta para la página de inicio
$router->add('GET', '/products', 'ProductController', 'index');
$router->add('GET', '/products/create', 'ProductController', 'create');
$router->add('POST', '/products/store', 'ProductController', 'store');
$router->add('GET', '/products/edit/{id}', 'ProductController', 'edit'); // {id} para capturar el ID
$router->add('POST', '/products/update/{id}', 'ProductController', 'update');
$router->add('POST', '/products/delete/{id}', 'ProductController', 'delete');

$router->add('GET', '/products/search', 'ProductController', 'search');

// Nueva ruta - endpoint para búsqueda con AJAX
$router->add('GET', '/api/products/{id}', 'ProductController', 'searchById');


// Un controlador básico para la página de inicio
class HomeController extends App\Core\Controller
{
  public function index()
  {
    $this->view('home.index');
  }
}

$router->dispatch();