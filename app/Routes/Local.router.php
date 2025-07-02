<?php


$router->add('GET','/locales','LocalController','index');
$router->add('GET', '/locales/create', 'LocalController', 'create');
$router->add('POST', '/locales/store', 'LocalController', 'store');





// API UBIGEO



