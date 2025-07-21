<?php

//FORMATO COTIZACION 
/* $router->add('GET', '/formato', 'ForCotController', 'getAllFormato'); */

// VISTA DE REQUISITOS
$router->add('GET', '/formatoCotizacion', 'ForCotController', 'index');
$router->add('GET', '/formatoCotizacion/create', 'ForCotController', 'create');
$router->add('GET', '/formatoCotizacion/requisitos/{idformato}', 'ForCotController', 'getRequisitos');
/* $router->add('POST', '/formatoCotizacion/requisitos/save', 'ForCotController', 'saveRequisitos'); */
$router->add('POST', '/formatoCotizacion/store', 'ForCotController', 'store');
$router->add('GET', '/formatoCotizacion/detalle/{id}', 'ForCotController', 'details');
$router->add('POST', '/formatoCotizacion/delete/{id}', 'ForCotController', 'delete');

$router->add('POST', '/formatoCotizacion/requisitos/storeRequisitos', 'ForCotController', 'storeRequisitos');

