<?php

$router->add('GET', '/usuarios', 'UsuarioController', 'index');
$router->add('GET', '/usuarios/create', 'UsuarioController', 'create');
$router->add('GET',  '/usuarios/cargos',   'UsuarioController', 'getCargosByArea');


