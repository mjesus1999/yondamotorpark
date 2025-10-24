<?php


$router->add('GET', '/marcas', 'MarcaController', 'index');
$router->add('POST', '/marcas/store', 'MarcaController', 'store');
$router->add('GET', '/marcas/show', 'MarcaController', 'show'); 
$router->add('POST', '/marcas/update', 'MarcaController', 'update');
$router->add('POST', '/marcas/destroy', 'MarcaController', 'destroy');
$router->add('GET', '/modelos/getall', 'ModeloController', 'getall'); 
$router->add('GET', '/modelos/show', 'ModeloController', 'show');
$router->add('POST', '/modelos/store', 'ModeloController', 'store');
$router->add('POST', '/modelos/update', 'ModeloController', 'update');
$router->add('POST', '/modelos/destroy', 'ModeloController', 'destroy');
$router->add('GET', '/tipos/getall', 'TipoVehiculoController', 'getall');
$router->add('GET','/api/marcas','MarcaController','getMarcasDB');

