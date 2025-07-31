<?php

$router->add('GET', '/cotizacion', 'CotizacionController', 'index');
$router->add('GET', '/cotizacion/create', 'CotizacionController', 'create');

$router->add('GET', '/cotizaciones/requisitos/(\d+)', 'CotizacionController', 'requisitos');
$router->add('GET', '/cotizacion/buscarCliente', 'CotizacionController', 'buscarCliente');
$router->add('POST', '/cotizaciones', 'CotizacionController', 'store');

// TIPO DE CAMBIO SEGUN LA MONEDA (NOTITA
$router->add('GET', '/cotizacion/tipo-cambio', 'CotizacionController', 'tipoCambio');

$router->add('GET', '/api/cotizacion/calcularpagomensual/{importeTotal}/{inicial}/{meses}', 'CotizacionController', 'calcularPagoMensual');