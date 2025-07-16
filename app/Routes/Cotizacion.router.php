<?php

$router->add('GET', '/cotizacion', 'CotizacionController', 'index');
$router->add('GET', '/cotizacion/create', 'CotizacionController', 'create');
$router->add('GET', '/cotizacion/requisitos', 'CotizacionController', 'requisitos');
$router->add('POST',   '/cotizacion/store',   'CotizacionController', 'store');

/* $router->add('POST', '/cotizacion/store', 'CotizacionController', 'store'); */