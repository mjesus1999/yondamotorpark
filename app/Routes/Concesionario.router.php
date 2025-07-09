<?php

use App\Core\Router;

$router->add('GET','/concesionarios','ConcesionarioController','index');
$router->add('GET', '/concesionarios/create', 'ConcesionarioController','create');
$router->add('GET', '/api/concesionariosSunat/{ruc}','ConcesionarioController','searchRucSunat');