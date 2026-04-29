<?php

use App\Controllers\CajaController;

$router->add('GET', '/caja', 'CajaController', 'index');
$router->add('GET', '/caja/cronograma/{id}', 'CajaController', 'cronogramaByContrato');
$router->add('GET','/caja/reporte/by/fecha','CajaController','indexReporteByFecha');
$router->add('GET','/caja/contratos/completados','CajaController','indexContratosCompletados');


$router->add('GET', '/api/reporte/hoy', 'CajaController', 'getReporteIngresosCajaHoy');
$router->add('GET', '/api/reporte/by/fecha', 'CajaController', 'reportePagosByFecha');
$router->add('GET','/caja/pagos/denominacion','CajaController','indexPagosDenominacion');
$router->add('GET', '/caja/buscar-cliente', 'CajaController', 'indexBuscarCliente');
$router->add('GET', '/api/caja/contratos-por-cliente/{id}', 'CajaController', 'apiContratosPorCliente');
$router->add('GET', '/api/caja/cronograma-por-cliente/{id}', 'CajaController', 'apiCronogramaPorCliente');
$router->add('GET', '/api/caja/registro-ventas-vehiculares/{dni}', 'CajaController', 'apiRegistroVentasVehicularesByDNI');
$router->add('POST', '/api/nubefact/consultar-estado-sunat', 'CajaController', 'apiConsultarEstadoSunatNubefact');

$router->add('GET', '/api/reporte/hoy', 'CajaController', 'getReporteIngresosCajaHoy');

$router->add('GET', '/api/conceptoPagos', 'CajaController', 'searchConceptosPagos');
$router->add('POST','/api/storePagoCompuesto','CajaController','storePagoCompuesto');




$router->add('GET','/api/clienteByDNI/{dni}','CajaController','searchClienteByDNI');
$router->add('GET','/api/contratos/completados','CajaController','getContratosCompletados');



