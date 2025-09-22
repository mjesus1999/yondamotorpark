<?php

$router->add('GET', '/egreso', 'EgresoController', 'index');
$router->add('GET', '/egreso/listar/{estado}', 'EgresoController', 'index'); // LISTAR POR ESTADO
$router->add('GET', '/egreso/create', 'EgresoController', 'create');
$router->add('GET', '/egreso/reporteByFecha','EgresoController', 'indexReporteByFecha');

$router->add('POST', '/egreso/store', 'EgresoController', 'store');
$router->add('POST', '/egreso/store/comprobante', 'EgresoController', 'storeComprobante');
$router->add('POST', '/egreso/validarComprobante/{id}', 'EgresoController', 'ValidarComprobante');








$router->add('GET', '/api/egreso/conceptos', 'EgresoController', 'getConceptosEgreso');
$router->add('GET', '/api/egreso/colaboradores', 'EgresoController', 'getColaboradores');
$router->add('GET', '/api/egreso/proovedores', 'EgresoController', 'getProovedores');
$router->add('GET', '/api/egreso/detalle/{id}', 'EgresoController', 'getDetalleEgreso');
$router->add('GET', '/api/egreso/reporteByFecha', 'EgresoController','getReporteEgresos');



