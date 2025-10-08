<?php

// RUTAS PRINCIPALES PARA MOSTRAR
$router->add('GET', '/Cobranza', 'CobranzaController', 'index');
$router->add('GET', '/Cobranza/getEstadisticas', 'CobranzaController', 'getEstadisticas');
$router->add('GET', '/Cobranza/getTarjetas', 'CobranzaController', 'getTarjetas');
$router->add('GET', '/Cobranza/getClientesNotificar', 'CobranzaController', 'getClientesNotificar');

// RUTAS PARA OBTENER RESULTADO EN EL MODAL DE DETALLE EN TARJETAS
$router->add('GET', '/Cobranza/getInfoCliente/{id}', 'CobranzaController', 'getInfoCliente');
$router->add('GET', '/Cobranza/getDetalleContrato/{id}', 'CobranzaController', 'getDetalleContrato');
$router->add('GET', '/Cobranza/getCronogramaPagos/{id}', 'CobranzaController', 'getCronogramaPagos');
$router->add('GET', '/Cobranza/getResumenFinanciero/{id}', 'CobranzaController', 'getResumenFinanciero');
$router->add('GET', '/Cobranza/getHistorialPagos/{id}/{limite}', 'CobranzaController', 'getHistorialPagos');

// RUTA PARA ENVIAR SMS
$router->add('POST', '/Cobranza/enviarSmsNotificacion', 'CobranzaController', 'enviarSmsNotificacion');

// RUTA PAR ACTUALIZAR TELEFONO
$router->add('POST', '/Cobranza/actualizarTelefono', 'CobranzaController', 'actualizarTelefono');

// RUTAS PARA LA VISTA DE NOTIFICAR Y VENCIDOS
$router->add('GET', '/Recordatorios', 'CobranzaController', 'indexNotificar');
$router->add('GET', '/Vencidos', 'CobranzaController', 'indexVencidos');

//REPORTES PDF
/* $router->add('GET', '/reportes', 'CobranzaController', 'reporteCobranza'); */
$router->add('GET', '/reportesAtrasado', 'CobranzaController', 'reporteCobranzaAtrasado');
//CARGAR DATOS AL PDF
$router->add('GET', '/Cobranza/getDatosReporteNotificacion', 'CobranzaController', 'getDatosReporteNotificacion');


$router->add('GET', '/reportesAtrasado2', 'CobranzaController', 'reporteCobranzaAtrasado2');
$router->add('GET', '/reportesRecojo', 'CobranzaController', 'reporteRecojoVehicular');


//EJEMPLOS
// En tu router o CobranzaController
/* $router->add('POST', '/api/enviar-sms', 'CobranzaController', 'enviarSms'); */
/* $router->get('/Cobranza/getResumenFinanciero/{id}', 'CobranzaController@getResumenFinanciero');
$router->get('/Cobranza/getHistorialPagos/{id}/{limite}', 'CobranzaController@getHistorialPagos'); */
