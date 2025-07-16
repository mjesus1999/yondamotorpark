<?php

// Endpoint para búsqueda con AJAX
$router->add('GET', '/vehiculos', 'VehiculoController', 'index');
$router->add('POST','/vehiculosOC/store','VehiculoController','storeVehiculoOC');
