<?php

$router->add('GET', '/cotizacion', 'CotizacionController', 'index');
$router->add('GET', '/cotizacion/create', 'CotizacionController', 'create');

$router->add('GET', '/cotizaciones/requisitos/(\d+)', 'CotizacionController', 'requisitos');

/* $router->add('GET', '/cotizacion/requisitos/{idformato}', 'CotizacionController', 'getRequisitos');
$router->add('POST', '/cotizacion/requisitos/save', 'CotizacionController', 'saveRequisitos'); */
/* $router->add('GET', '/cotizacion/requisitos', 'CotizacionController', 'requisitos'); */

/* $router->add('POST',   '/cotizacion/store',   'CotizacionController', 'store');
 */
/* $router->add('GET', '/cotizacion/requisitos/{id}','CotizacionController', 'requisitos');

/* $router->add('POST', '/cotizacion/store', 'CotizacionController', 'store'); */