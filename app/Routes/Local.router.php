<?php

// METODOS DE LA WEB

$router->add('GET','/locales','LocalController','index');
$router->add('GET', '/locales/create', 'LocalController', 'create');
$router->add('POST', '/locales/store', 'LocalController', 'store');
$router->add('POST', '/locales/update/{id}', 'LocalController', 'update');
$router->add('POST', '/locales/delete/{id}', 'LocalController', 'delete');



// API
$router->add('GET', '/api/locales/{id}', 'LocalController', 'edit');

$router->add('GET','/api/localesActivos','LocalController','apiGetLocales');



