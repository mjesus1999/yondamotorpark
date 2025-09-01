<?php

$router->add('GET', '/marcas', 'MarcaController', 'index');

$router->add('POST', '/marcas/store', 'MarcaController', 'store');

// API PARA TRAER LAS MARCAS DE LA DB
$router->add('GET','/api/marcas','MarcaController','getMarcasDB');