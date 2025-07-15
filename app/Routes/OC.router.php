<?php


$router->add('GET','/oc','OrdenCompraController','index');
$router->add('GET', '/oc/create','OrdenCompraController','create');

$router->add('POST', '/oc/store','OrdenCompraController','store');




// RUTAS DE LAS APIS

