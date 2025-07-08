<?php

//USUARIO

// MOSTRAR
$router->add('GET', '/usuarios', 'UsuarioController', 'index');
$router->add('GET', '/usuarios/create', 'UsuarioController', 'create');
$router->add('GET',  '/usuarios/cargos',   'UsuarioController', 'getCargosByArea');

// REGISTRAR
$router->add('POST', '/usuarios/store', 'UsuarioController', 'store');

// BUSCAR
$router->add('GET', '/usuarios/searchByDNI', 'UsuarioController', 'searchByDNI');

// ACTUALIZAR
$router->add('POST', '/usuarios/changePassword', 'UsuarioController', 'changePassword');

// ELIMINAR
$router->add('POST', '/usuarios/delete/{id}', 'UsuarioController', 'delete');

// PROFILE
$router->add('POST', '/usuarios/profile/avatar', 'UsuarioController', 'uploadAvatar');
$router->add('GET', '/usuarios/profile/{id}', 'UsuarioController', 'profile');