<?php

//LOGIN

$router->add('GET',  '/login',  'AuthController', 'showLogin');
$router->add('POST', '/login',  'AuthController', 'login');
$router->add('GET',  '/logout', 'AuthController', 'logout');

//RECUPERAR CONTRASEÑA
$router->add('GET', '/recoverAccount', 'AuthController', 'showRecoverForm');
$router->add('POST','/recoverAccount','AuthController','handleRecover');


// CREAR ACCOUNT
$router->add('GET',  '/createAccount',     'UsuarioController', 'showCreateFromContracts');
$router->add('POST', '/createFromContract','UsuarioController', 'createFromContract');
