<?php

$router->add('GET', '/marcas', 'MarcaController', 'index');
$router->add('GET', '/marcas/lista', 'MarcaController', 'getAll');


//VEHICULOS
/* $router->add('GET', '/modelos/lista', 'ModeloController', 'getByMarcaYTipo');
$router->add('GET', '/vehiculos/disponibles', 'VehiculoController', 'getVehiculosDisponibles');
 */
/* $router->add('GET', '/marcas/getAll', 'MarcaController', 'getAll'); */