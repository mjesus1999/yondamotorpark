<?php

$router->add('GET', '/cobranza', 'CobranzaController', 'index');
$router->add('GET', '/Recordatorios', 'CobranzaController', 'indexNotificar');
$router->add('GET', '/Vencidos', 'CobranzaController', 'indexVencidos');


//PDF
/* $router->add('GET', '/reportes', 'CobranzaController', 'reporteCobranza'); */
$router->add('GET', '/reportesAtrasado', 'CobranzaController', 'reporteCobranzaAtrasado');
$router->add('GET', '/reportesAtrasado2', 'CobranzaController', 'reporteCobranzaAtrasado2');

$router->add('GET', '/reportesRecojo', 'CobranzaController', 'reporteRecojoVehicular');



//reporteCobranzaAtrasado.php