<?php

$router->add('GET', '/', 'HomeController', 'index'); // Ruta para la página de inicio

// URLs antiguas del home / favoritos → rutas canónicas (evita 404)
if (class_exists(\App\Controllers\RedirectController::class)) {
    $router->add('GET', '/ordencompra/emitido', 'RedirectController', 'ordenCompraEmitido');
    $router->add('GET', '/ordencompra/{estado}', 'RedirectController', 'ordenCompraEstado');
    $router->add('GET', '/egresos/{estado}', 'RedirectController', 'egresosEstado');
}