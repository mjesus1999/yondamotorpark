<?php

$router->add('GET', '/caja', 'CajaController', 'index');
$router->add('GET', '/caja/cronograma/{id}', 'CajaController', 'cronogramaByContrato');
$router->add('GET','/caja/reporte/by/fecha','CajaController','indexReporteByFecha');


$router->add('GET', '/api/reporte/hoy', 'CajaController', 'getReporteIngresosCajaHoy');
$router->add('GET', '/api/reporte/by/fecha', 'CajaController', 'reportePagosByFecha');
