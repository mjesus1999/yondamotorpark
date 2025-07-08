<?php
// PERSONA

// REGISTRAR
$router->add('POST', '/persona/store', 'PersonaController', 'store');
$router->add('GET', '/persona/searchByDNI', 'PersonaController', 'searchByDNI');