<?php

use App\Core\Router;

$router->add('GET', '/concesionarios', 'ConcesionarioController', 'index');
$router->add('GET', '/concesionarios/create', 'ConcesionarioController', 'create');
$router->add('GET', '/concesionarios/gestionar/{ruc}', 'ConcesionarioController', 'gestionar');

$router->add('POST', '/concesionarios/store', 'ConcesionarioController', 'store');  // Mandar los datos del concesionario.
$router->add('POST', '/concesionarios/update/{id}', 'ConcesionarioController', 'update');
$router->add('POST', '/concesionarios/delete/{id}', 'ConcesionarioController', 'delete');

// APIS
$router->add('GET', '/api/concesionarioSunat/{ruc}', 'ConcesionarioController', 'searchRucSunat');
$router->add('GET', '/api/concesionarioDB/{ruc}', 'ConcesionarioController', 'searchRucDB');

// API PARA OBTENER CONCESIONARIO A LA VISTA DE ORDEN DE COMPRA CREAR:
$router->add('GET','/api/concesionariosDB','ConcesionarioController','getConcesionariosDB');
