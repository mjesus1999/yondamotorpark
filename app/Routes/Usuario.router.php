<?php

$router->add('GET', '/usuarios', 'UsuarioController', 'index');
$router->add('GET', '/usuarios/create', 'UsuarioController', 'create');
$router->add('GET',  '/usuarios/cargos',   'UsuarioController', 'getCargosByArea');
$router->add('POST', '/usuarios/storePersona', 'UsuarioController', 'storePersona');
$router->add('POST', '/usuarios/store', 'UsuarioController', 'store');
$router->add('GET', '/usuarios/searchByDNI', 'UsuarioController', 'searchByDNI');