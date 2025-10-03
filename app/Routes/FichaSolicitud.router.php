<?php


$router->add('GET', '/fichasolicitud/{id}', 'FichaSolicitudController', 'index');

$router->add('POST', '/storePersonaFicha', 'FichaSolicitudController', 'storePersona');
$router->add('POST', '/storeFicha', 'FichaSolicitudController', 'storeFicha');






$router->add('GET', '/api/fichaSolicitud/searchPersonaByDNI/{dni}', 'FichaSolicitudController', 'searchPersonaByDNI');
$router->add('GET', '/api/fichaSolicitud/searchPersonaByReniec/{dni}', 'FichaSolicitudController', 'searchPersonaByReniec');
