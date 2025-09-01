<?php

$router->add('GET', '/cotizacion', 'CotizacionController', 'index');
$router->add('GET', '/cotizacion/create', 'CotizacionController', 'create');

$router->add('GET', '/cotizacion/reporte/(\d+)', 'CotizacionController', 'html2pdfReport');
$router->add('GET', '/api/cotizacion/(\d+)', 'CotizacionController', 'apiShow');


$router->add('GET', '/cotizaciones/requisitos/(\d+)', 'CotizacionController', 'requisitos');
$router->add('GET', '/cotizacion/buscarCliente', 'CotizacionController', 'buscarCliente');
$router->add('POST', '/cotizaciones', 'CotizacionController', 'store');

// TIPO DE CAMBIO SEGUN LA MONEDA (NOTITA
$router->add('GET', '/cotizacion/tipo-cambio', 'CotizacionController', 'tipoCambio');

$router->add('GET', '/api/cotizacion/calcularpagomensual/{importeTotal}/{inicial}/{meses}', 'CotizacionController', 'calcularPagoMensual');

$router->add('GET','/api/cotizacion/generar-cronograma/{importeTotal}/{inicial}/{meses}','CotizacionController','generarCronograma');