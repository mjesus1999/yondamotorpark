<?php


// $router->add('GET', '/vehiculos', 'VehiculoController', 'index');
$router->add('POST', '/vehiculosOC/store', 'VehiculoController', 'storeVehiculoOC');



$router->add('GET', '/vehiculos', 'VehiculoController', 'index');
$router->add('GET', '/vehiculosAlContado','VehiculoController', 'indexVehiculosAlContado');

$router->add('GET', '/vehiculos/create', 'VehiculoController', 'create');
// $router->add('GET', '/vehiculos/edit/{id}','VehiculoController', 'edit');

//Modelos de vehiculo
$router->add('GET', '/modelos/lista', 'ModeloController', 'getByMarcaYTipo');
$router->add('GET', '/vehiculos/edit/{id}', 'VehiculoController', 'edit');

// Ruta para ir a la vista de recepción de vehículos

$router->add('GET', '/recepcionVehiculos', 'VehiculoController', 'indexRecepcionVehiculos');


$router->add('GET', '/recepcionVehiculos/edit/{idcompra}', 'VehiculoController', 'recepcionEdit');


//registro del vehiculo:
$router->add('POST', '/vehiculos/store', 'VehiculoController', 'store');
//Eliminar vehiculo
$router->add('POST', '/vehiculos/delete', 'VehiculoController', 'delete');
$router->add('POST', '/vehiculo/store/PagoAlContado','VehiculoController', 'storePagoALContado');

$router->add('POST', '/vehiculos/update', 'VehiculoController', 'update');

// listar tipos por marca (GET)
$router->add('GET', '/api/getTipoVehiculoByMarca/{id}', 'TipovehiculoController', 'getByMarca');

// agregar año (POST)
$router->add('POST', '/vehiculos/agregarAnio', 'VehiculoController', 'agregarAnio');

// Actualizar datos del vehículo recepcionado :
$router->add('POST', '/update/vehiculoRecepcionado', 'VehiculoController', 'updateVehiculoRecepcionOC');


$router->add('GET', '/api/vehiculo/searchVehiculo','VehiculoController', 'searchVehiculo');
$router->add('GET', '/vehiculosVendidosAlContado','VehiculoController','getVehiculosVendidosAlContado');