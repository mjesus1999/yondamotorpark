<?php

$router->add('GET', '/usuarios', 'UsuarioController', 'index');
$router->add('GET', '/usuarios/create', 'UsuarioController', 'create');
$router->add('GET',  '/usuarios/cargos',   'UsuarioController', 'getCargosByArea');
$router->add('POST', '/usuarios/storePersona', 'UsuarioController', 'storePersona');

