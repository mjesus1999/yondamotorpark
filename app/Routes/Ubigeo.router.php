<?php

// Rutas para Ubigeo (departamentos, provincias y distritos)
$router->add('GET', '/ubigeo/departamentos', 'UbigeoController', 'getAllDepartamentos');
$router->add('GET', '/ubigeo/provincias',    'UbigeoController', 'getAllProvincias');
$router->add('GET', '/ubigeo/distritos',     'UbigeoController', 'getAllDistritos');
$router->add('GET', '/ubigeo/distritos/all', 'UbigeoController', 'getAllDistritosAll');