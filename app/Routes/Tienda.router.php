<?php

// APIS

$router->add('GET', '/api/tiendasConcesionario/{id}', 'TiendaController', 'searchTiendaByConcesionario');


$router->add('POST', '/tiendas/store', 'TiendaController', 'store');
$router->add('GET', '/tiendas/edit/{id}', 'TiendaController', 'edit');
$router->add('POST', '/tiendas/update/{id}', 'TiendaController', 'update');

$router->add('POST', '/tiendas/delete/{id}', 'TiendaController', 'delete');



