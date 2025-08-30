<?php

// Rutas principales de crédito
$router->add('GET', '/creditos', 'CreditoController', 'index');
$router->add('POST', '/creditos/seguimiento', 'CreditoController', 'registrarSeguimiento');
$router->add('GET', '/creditos/historial/{id}', 'CreditoController', 'verHistorial');

// APIs para el sistema de crédito
$router->add('GET', '/api/creditos/estadisticas', 'CreditoController', 'getEstadisticas');
$router->add('GET', '/api/creditos/morosos', 'CreditoController', 'getMorosos');

// routes.php 
$router->add('GET', '/archivos/{tipo}/{nombre}', 'ComprobantesController', 'verArchivo');