<?php


$router->add('GET','/oc','OrdenCompraController','index');
$router->add('GET', '/oc/create','OrdenCompraController','create');

$router->add('POST', '/oc/store','OrdenCompraController','store');

$router->add('GET','/oc/reporte/{id}','OrdenCompraController','indexReport');

// RUTAS DE LAS APIS
    //MOSTRAR LOS DETAALLES EN LA VISTA INDEX

$router->add('GET','/api/oc/{id}','OrdenCompraController','searchtDetOCByIdOc');

