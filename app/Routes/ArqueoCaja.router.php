<?php

$router->add('GET', '/arqueoCaja', 'ArqueoCajaController', 'index');
$router->add('GET', '/arqueoCaja/listar/{estado}', 'ArqueoCajaController', 'index'); // LISTAR POR ESTADO
$router->add('GET', '/arqueoCaja/create', 'ArqueoCajaController', 'createForm');

$router->add('GET', '/arqueo/entregados','EntregaDineroController', 'index');


$router->add('POST', '/arqueocaja/store', 'ArqueoCajaController', 'store');
// En routes.php

$router->add('POST', '/arqueocaja/entregar', 'ArqueoCajaController', 'entregar');



$router->add('GET', '/arqueocaja/destinos/{desitno}', 'ArqueoCajaController', 'destinos');
// Corrección en el enrutador
$router->add('GET', '/api/arqueo/reporte-ciclo/{ids}', 'ArqueoCajaController', 'getReporteArqueoPorCiclo');

$router->add('GET', '/api/arqueo/entregas/{id}', 'EntregaDineroController', 'detalle');

$router->add('POST', '/api/arqueo/reporte/sede', 'ArqueoCajaController', 'reportePorSede');



$router->add('GET', '/api/arqueo/reporte/sede', 'ArqueoCajaController', 'reportePorSede');
