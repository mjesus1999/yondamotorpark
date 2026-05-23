<?php

/**
 * Rutas legacy: evitan 404 cuando un enlace antiguo sigue en favoritos o en el home.
 */
$router->add('GET', '/ordencompra/emitido', 'RedirectController', 'ordenCompraEmitido');
$router->add('GET', '/ordencompra/{estado}', 'RedirectController', 'ordenCompraEstado');
$router->add('GET', '/egresos/{estado}', 'RedirectController', 'egresosEstado');
