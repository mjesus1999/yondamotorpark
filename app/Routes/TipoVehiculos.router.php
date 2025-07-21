<?php

// Tipos de vehículo por marca
$router->add('GET', '/tipovehiculos/lista', 'TipoVehiculoController', 'getTipoVehiculoByMarca');

