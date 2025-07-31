<?php

$router->add('GET','/compras','CompraController','index');
$router->add('GET','/compras/create','CompraController','create');
$router->add('POST','/compras/store','CompraController','store');




// APIS

$router->add('GET','/api/detOCConcesionario/{id}','CompraController','searchDetOCByConcesionario');