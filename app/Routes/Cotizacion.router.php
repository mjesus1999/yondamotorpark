<?php

$router->add('GET', '/cotizacion', 'CotizacionController', 'index');

$router->add('GET', '/cotizacion/([A-Za-z])', 'CotizacionController', 'index');
$router->add('GET', '/cotizacion/pagoInicial/{id}','CotizacionController', 'indexPagoInicial');

$router->add('POST', '/cotizacion/aprobar/{id}', 'CotizacionController', 'aprobarCotizacion');




$router->add('GET', '/cotizacion/create', 'CotizacionController', 'create');

$router->add('GET', '/cotizacion/reporte/(\d+)', 'CotizacionController', 'html2pdfReport');
$router->add('GET', '/api/cotizacion/(\d+)', 'CotizacionController', 'apiShow');

//GET Y POST PARA EL DNI DEL ULTIMO CLIENTE
$router->add('GET', '/api/ultimo-cliente-registrado', 'CotizacionController', 'ultimoClienteRegistrado');
$router->add('POST', '/api/limpiar-ultimo-cliente', 'CotizacionController', 'limpiarUltimoCliente');

$router->add('GET', '/cotizaciones/requisitos/(\d+)', 'CotizacionController', 'requisitos');
$router->add('POST', '/cotizaciones', 'CotizacionController', 'store');

$router->add('POST', '/cotizaciones/storePagoInicial', 'CotizacionController', 'storePagoInicial');


// TIPO DE CAMBIO SEGUN LA MONEDA (NOTITA
$router->add('GET', '/cotizacion/buscarCliente', 'CotizacionController', 'buscarCliente');

$router->add('GET', '/cotizacion/tipo-cambio', 'CotizacionController', 'tipoCambio');

$router->add('GET', '/api/cotizacion/calcularpagomensual/{importeTotal}/{inicial}/{meses}', 'CotizacionController', 'calcularPagoMensual');

$router->add('GET', '/api/cotizacion/generar-cronograma/{importeTotal}/{inicial}/{meses}', 'CotizacionController', 'generarCronograma');

//HISTORIAL
$router->add('GET', '/cotizacion/historial', 'CotizacionController', 'historial');

//FECHA DE REACTIVACION
$router->add('POST', '/cotizacion/reactivar/(\d+)', 'CotizacionController', 'reactivar');

$router->add('GET', '/cotizacion/reporteCot/(\d+)', 'CotizacionController', 'reporte-cotizacion');
$router->add('GET', '/api/actaSeparacion/{id}', 'CotizacionController', 'getDataSeparacionVehicular');