<?php

//VEHICULOS

// Endpoint para búsqueda con AJAX
$router->add('GET', '/vehiculos', 'VehiculoController', 'index');
$router->add('GET', '/vehiculos/create', 'VehiculoController', 'create');

//Modelos de vehiculo
$router->add('GET', '/modelos/lista', 'ModeloController', 'getByMarcaYTipo');

//registro del vehiculo:
$router->add('POST', '/vehiculos/store', 'VehiculoController', 'store');

$router->add('POST', '/vehiculos/delete', 'VehiculoController', 'delete');

//CLIENTE DENTRO DE VEHICULO
/* $router->add('GET', '/clientes/lista', 'ClienteController', 'getAllCliente'); */
/* $router->add('GET', '/vehiculos/disponibles', 'VehiculoController', 'getVehiculosDisponibles');  */