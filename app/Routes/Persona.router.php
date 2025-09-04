<?php
// PERSONA

// REGISTRAR PERSONA NATURAL
$router->add('POST', '/persona/store', 'PersonaController', 'store');

//API - BUSCAR DNI
$router->add('GET', '/api/persona/searchByDNI', 'PersonaController', 'searchByDNI');

$router->add('GET', '/persona/searchByDNIApi', 'PersonaController', 'searchByDNIApi');