<?php

//VEHICULOS COTIZACION

// Endpoint para búsqueda con AJAX
$router->add('GET', '/vehiculos', 'VehiculoController', 'index');
$router->add('GET', '/vehiculos/create', 'VehiculoController', 'create');

//CLIENTE DENTRO DE VEHICULO
$router->add('GET', '/clientes/lista', 'ClienteController', 'getAllCliente');
/* $router->add('GET', '/clientes/lista', 'ClienteController', 'getAllCliente'); */

//Modelos de vehiculo
$router->add('GET', '/modelos/lista', 'ModeloController', 'getByMarcaYTipo');