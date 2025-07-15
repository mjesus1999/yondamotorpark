<?php

$router->add('GET', '/marcas', 'MarcaController', 'index');
$router->add('GET', '/marcas/lista', 'MarcaController', 'getAll');