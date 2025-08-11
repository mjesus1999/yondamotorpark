<?php

$router->add('GET','/caja','CajaController','index');
$router->add('GET','/caja/cronograma','CajaController','cronogramaByContrato');