<?php

$router->add('GET', '/api/conceptospagos', 'ConceptoPagoController', 'getConceptosPago');
$router->add('POST', '/store/conceptoPago', 'ConceptoPagoController', 'store');
