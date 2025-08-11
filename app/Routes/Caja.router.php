<?php

$router->add('GET','/caja','CajaController','index');
$router->add('GET','/caja/cronograma/{id}','CajaController','cronogramaByContrato');