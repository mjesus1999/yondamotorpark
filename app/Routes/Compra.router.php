<?php

$router->add('GET','/compras','CompraController','index');
$router->add('GET','/compras/create','CompraController','create');




// APIS

$router->add('GET','/api/detOCConcesionario/{id}','CompraController','searchDetOCByConcesionario');