<?php

$router->add('GET', '/egreso', 'EgresoController', 'index');
$router->add('GET','/egreso/listar/{estado}', 'EgresoController', 'index'); // LISTAR POR ESTADO
$router->add('GET', '/egreso/create', 'EgresoController', 'create');

$router->add('POST', '/egreso/store', 'EgresoController', 'store');








$router->add('GET', '/api/egreso/conceptos', 'EgresoController', 'getConceptosEgreso');
$router->add('GET', '/api/egreso/colaboradores', 'EgresoController', 'getColaboradores');


