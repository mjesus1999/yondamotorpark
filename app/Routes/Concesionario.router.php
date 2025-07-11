<?php

use App\Core\Router;

$router->add('GET','/concesionarios','ConcesionarioController','index');
$router->add('GET', '/concesionarios/create', 'ConcesionarioController','create');
$router->add('POST', '/concesionarios/store', 'ConcesionarioController','store'); // Mandar los datos del concesionario.







// APIS 
$router->add('GET', '/api/concesionarioSunat/{ruc}','ConcesionarioController','searchRucSunat');
$router->add('GET', '/api/concesionarioDB/{ruc}','ConcesionarioController','searchRucDB');