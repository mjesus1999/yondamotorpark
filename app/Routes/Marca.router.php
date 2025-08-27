<?php

$router->add('GET', '/marcas', 'MarcaController', 'index');

// API PARA TRAER LAS MARCAS DE LA DB
$router->add('GET','/api/marcas','MarcaController','getMarcasDB');