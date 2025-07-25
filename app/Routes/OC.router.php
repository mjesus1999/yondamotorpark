<?php


$router->add('GET','/oc','OrdenCompraController','index');
$router->add('GET', '/oc/create','OrdenCompraController','create');
$router->add('GET','/oc/reporte/{id}','OrdenCompraController','html2pdfReport');


$router->add('GET','/oc/listar/{estado}', 'OrdenCompraController', 'index'); // LISTAR POR ESTADO
$router->add('GET','/oc/pagos/{id}','OrdenCompraController','indexPagos'); // Llevara a la lista que em regitra pagos.


$router->add('POST', '/oc/store','OrdenCompraController','store');
$router->add('POST','/oc/update/{id}','OrdenCompraController','update');
$router->add('POST','/oc/updateEstado/{estado}/{idOC}','OrdenCompraController','setEstado');







// RUTAS DE LAS APIS
//MOSTRAR LOS DETAALLES EN LA VISTA INDEX

$router->add('GET','/api/oc/{id}','OrdenCompraController','searchtDetOCByIdOc');
$router->add('GET','/api/oc/infoAutos/{id}','OrdenCompraController','searchInfoAutos');

