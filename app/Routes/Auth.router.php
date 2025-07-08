<?php

//LOGIN

$router->add('GET',  '/login',  'AuthController', 'showLogin');
$router->add('POST', '/login',  'AuthController', 'login');
$router->add('GET',  '/logout', 'AuthController', 'logout');
