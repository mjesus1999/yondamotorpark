<?php

//WEB 
$router->add('GET', '/products', 'ProductController', 'index');
$router->add('GET', '/products/create', 'ProductController', 'create');
$router->add('POST', '/products/store', 'ProductController', 'store');
$router->add('GET', '/products/edit/{id}', 'ProductController', 'edit'); // {id} para capturar el ID
$router->add('POST', '/products/update/{id}', 'ProductController', 'update');
$router->add('POST', '/products/delete/{id}', 'ProductController', 'delete');
$router->add('GET', '/products/search', 'ProductController', 'search');

//API
