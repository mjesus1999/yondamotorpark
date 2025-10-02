<?php

$router->add('GET', '/Cobranza', 'CobranzaController', 'index');
$router->add('GET', '/Cobranza/getEstadisticas', 'CobranzaController', 'getEstadisticas');
$router->add('GET', '/Cobranza/getTarjetas', 'CobranzaController', 'getTarjetas');

// RUTAS PARA EL MODAL DE DETALLE
$router->add('GET', '/Cobranza/getInfoCliente/{id}', 'CobranzaController', 'getInfoCliente');
$router->add('GET', '/Cobranza/getDetalleContrato/{id}', 'CobranzaController', 'getDetalleContrato');
$router->add('GET', '/Cobranza/getCronogramaPagos/{id}', 'CobranzaController', 'getCronogramaPagos');
$router->add('GET', '/Cobranza/getResumenFinanciero/{id}', 'CobranzaController', 'getResumenFinanciero');
$router->add('GET', '/Cobranza/getHistorialPagos/{id}/{limite}', 'CobranzaController', 'getHistorialPagos');
$router->add('GET', '', 'CobranzaController', '');


/* $router->get('/Cobranza/getResumenFinanciero/{id}', 'CobranzaController@getResumenFinanciero');
$router->get('/Cobranza/getHistorialPagos/{id}/{limite}', 'CobranzaController@getHistorialPagos'); */

// VISTAS SECUNDARIAS
$router->add('GET', '/Recordatorios', 'CobranzaController', 'indexNotificar');
$router->add('GET', '/Vencidos', 'CobranzaController', 'indexVencidos');


//PDF
/* $router->add('GET', '/reportes', 'CobranzaController', 'reporteCobranza'); */
$router->add('GET', '/reportesAtrasado', 'CobranzaController', 'reporteCobranzaAtrasado');
$router->add('GET', '/reportesAtrasado2', 'CobranzaController', 'reporteCobranzaAtrasado2');

$router->add('GET', '/reportesRecojo', 'CobranzaController', 'reporteRecojoVehicular');



//reporteCobranzaAtrasado.php