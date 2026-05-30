<?php

$router->add('GET', '/cotizacion', 'CotizacionController', 'index');

$router->add('GET', '/cotizacion/create', 'CotizacionController', 'create');
$router->add('GET', '/cotizacion/historial', 'CotizacionController', 'historial');
$router->add('GET', '/cotizacion/buscarCliente', 'CotizacionController', 'buscarCliente');
$router->add('GET', '/cotizacion/tipo-cambio', 'CotizacionController', 'tipoCambio');
$router->add('GET', '/cotizacion/pagoInicial/{id}', 'CotizacionController', 'indexPagoInicial');
$router->add('GET', '/cotizacion/reporte/{id}', 'CotizacionController', 'html2pdfReport');
$router->add('GET', '/cotizacion/reporteCot/{id}', 'CotizacionController', 'html2pdfReport');

// Filtro por estado: /cotizacion/P, /cotizacion/S, /cotizacion/A (un carácter)
$router->add('GET', '/cotizacion/{estado}', 'CotizacionController', 'index');

$router->add('POST', '/cotizacion/aprobar/{id}', 'CotizacionController', 'aprobarCotizacion');

$router->add('GET', '/api/cotizacion/{id}', 'CotizacionController', 'apiShow');

$router->add('GET', '/api/ultimo-cliente-registrado', 'CotizacionController', 'ultimoClienteRegistrado');
$router->add('POST', '/api/limpiar-ultimo-cliente', 'CotizacionController', 'limpiarUltimoCliente');

$router->add('GET', '/cotizaciones/requisitos/{id}', 'CotizacionController', 'requisitos');
$router->add('POST', '/cotizaciones', 'CotizacionController', 'store');
$router->add('POST', '/cotizaciones/storePagoInicial', 'CotizacionController', 'storePagoInicial');

$router->add('GET', '/api/cotizacion/calcularpagomensual/{importeTotal}/{inicial}/{meses}', 'CotizacionController', 'calcularPagoMensual');
$router->add('GET', '/api/cotizacion/generar-cronograma/{importeTotal}/{inicial}/{meses}', 'CotizacionController', 'generarCronograma');

$router->add('POST', '/cotizacion/reactivar/{id}', 'CotizacionController', 'reactivar');

$router->add('GET', '/api/actaSeparacion/{id}', 'CotizacionController', 'getDataSeparacionVehicular');
$router->add('GET', '/api/pagosCotizacion/{id}', 'CotizacionController', 'getPagosCliente');
$router->add('GET', '/api/cotizacion/reporte-general', 'CotizacionController', 'getReporteGeneral');
