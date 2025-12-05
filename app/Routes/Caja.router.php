<?php

use App\Controllers\CajaController;

$router->add('GET', '/caja', 'CajaController', 'index');
$router->add('GET', '/caja/cronograma/{id}', 'CajaController', 'cronogramaByContrato');
$router->add('GET','/caja/reporte/by/fecha','CajaController','indexReporteByFecha');


$router->add('GET', '/api/reporte/hoy', 'CajaController', 'getReporteIngresosCajaHoy');
$router->add('GET', '/api/reporte/by/fecha', 'CajaController', 'reportePagosByFecha');
$router->add('GET','/caja/pagos/denominacion','CajaController','indexPagosDenominacion');

$router->add('GET', '/api/reporte/hoy', 'CajaController', 'getReporteIngresosCajaHoy');

$router->add('GET', '/api/conceptoPagos', 'CajaController', 'searchConceptosPagos');
$router->add('POST','/api/storePagoCompuesto','CajaController','storePagoCompuesto');




$router->add('GET','/api/clienteByDNI/{dni}','CajaController','searchClienteByDNI');



