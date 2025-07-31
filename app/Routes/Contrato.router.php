<?php

$router->add('GET','/contratos','ContratoController','index');
$router->add('GET','/contrato/cronograma','ContratoController','cronogramaByContrato');