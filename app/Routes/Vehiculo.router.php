<?php


// $router->add('GET', '/vehiculos', 'VehiculoController', 'index');
$router->add('POST','/vehiculosOC/store','VehiculoController','storeVehiculoOC');



$router->add('GET', '/vehiculos', 'VehiculoController', 'index');
$router->add('GET', '/vehiculos/create', 'VehiculoController', 'create');
// $router->add('GET', '/vehiculos/edit/{id}','VehiculoController', 'edit');

//Modelos de vehiculo
$router->add('GET', '/modelos/lista', 'ModeloController', 'getByMarcaYTipo');

//registro del vehiculo:
$router->add('POST', '/vehiculos/store', 'VehiculoController', 'store');
//Eliminar vehiculo
$router->add('POST', '/vehiculos/delete', 'VehiculoController', 'delete');

$router->add('GET', '/vehiculos/edit/{id}','VehiculoController', 'edit');
$router->add('POST', '/vehiculos/update', 'VehiculoController', 'update');