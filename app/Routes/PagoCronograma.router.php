<?php


$router->add('GET','/caja/historial/pagos/{id}','PagoCronogramaController','indexHistorialPagos');
$router->add('POST', '/pago/cronograma', 'PagoCronogramaController', 'store');



// APi

$router->add('GET', '/api/numcuentaspagos', 'PagoCronogramaController', 'searchNumCuentasPagos');
$router->add('GET', '/api/numcuenta/{id}', 'PagoCronogramaController', 'getCuenta');
