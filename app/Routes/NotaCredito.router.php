<?php

$router->add('GET', '/api/nota-credito/pago/{id}', 'NotaCreditoController', 'apiInfoPago');
$router->add('POST', '/api/nota-credito/emitir', 'NotaCreditoController', 'apiEmitir');
