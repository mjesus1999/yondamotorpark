<?php

$router->add('GET', '/contratos', 'ContratoController', 'index');
$router->add('POST', '/contrato/store', 'ContratoController', 'store');
$router->add('POST', '/contrato/delete', 'ContratoController', 'disabledContrato');
$router->add('GET', '/api/contratos', 'ContratoController', 'apiGetContratos');