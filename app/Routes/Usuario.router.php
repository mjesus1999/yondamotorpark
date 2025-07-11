<?php

//USUARIO

// MOSTRAR
$router->add('GET', '/usuarios', 'UsuarioController', 'index');
$router->add('GET', '/usuarios/create', 'UsuarioController', 'create');

// REGISTRAR
$router->add('POST', '/usuarios/store', 'UsuarioController', 'store');

// ELIMINAR
$router->add('POST', '/usuarios/disabled/{id}', 'UsuarioController', 'disabled');
/* $router->add('POST', '/usuarios/delete/{id}', 'UsuarioController', 'delete'); */

// PROFILE
$router->add('GET', '/usuarios/profile/{id}', 'UsuarioController', 'profile');


//API

// MOSTRAR
$router->add('GET',  '/api/usuarios/cargos', 'UsuarioController', 'getCargosByArea');

// ACTUALIZAR
$router->add('POST', '/api/usuarios/changePassword',  'UsuarioController', 'changePassword');

// PROFILE
$router->add('POST', '/api/usuarios/profile/avatar',  'UsuarioController', 'uploadAvatar');
