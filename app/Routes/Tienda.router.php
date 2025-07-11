<?php






// APIS

$router->add('GET', '/api/tiendasConcesionario/{id}','TiendaController','searchTiendaByConcesionario');

$router->add('POST','/tiendas/store', 'TiendaController','store');