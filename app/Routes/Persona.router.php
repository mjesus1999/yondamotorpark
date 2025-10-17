<?php
// PERSONA

// REGISTRAR PERSONA NATURAL
$router->add('POST', '/persona/store', 'PersonaController', 'store');

//API - BUSCAR DNI BD
$router->add('GET', '/api/persona/searchByDNI', 'PersonaController', 'searchByDNI');

//API RENIEC
$router->add('GET', '/persona/searchByDNIApi', 'PersonaController', 'searchByDNIApi');