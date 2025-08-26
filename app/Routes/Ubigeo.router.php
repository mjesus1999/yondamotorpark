<?php

// APIS


$router->add('GET', '/api/ubigeo/departamentos', 'UbigeoController', 'departamentos');
$router->add('GET', '/api/ubigeo/provincias/{iddepartamento}', 'UbigeoController', 'provincias');
$router->add('GET', '/api/ubigeo/distritos/{idprovincia}', 'UbigeoController', 'distritos');


$router->add('GET', '/ubigeo/distritos/all', 'UbigeoController', 'getAllDistritosAll');