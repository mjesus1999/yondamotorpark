<?php


// RUTAS PARA CLIENTES PERSONAS:

// Ruta para listar clientes (Personas-clientes)
$router->add('GET','/clientes','PersonaController','indexPersonCliente');

// Ruta para mostrar el formulario de creación de persona-cliente
$router->add('GET','/clientes/createpersonclient','PersonaController','createPersonClient');

// Ruta para procesar el formulario de creación de persona y cliente (POST)
// Este formulario envía a PersonaController, que luego crea el Cliente
$router->add('POST', '/storepersonclient/store', 'PersonaController', 'storePersonaClient');

$router->add('GET', '/personaCliente/edit/{id}', 'PersonaController', 'edit'); 
$router->add('POST', '/personaCliente/update/{id}', 'PersonaController', 'update');
$router->add('POST','/personaCliente/delete/{id}','ClienteController','delete');


$router->add('GET','/api/clientes/{dni}','ClienteController','searchCliente');








// RUTAS PARA LOS CLIENTES EMPRESAS:

$router->add('GET', '/clientes/empresas','EmpresaController','indexEmpresaClientes');
$router->add('GET','/clientes/empresas/createempresaclient','EmpresaController','createEmpresaClient');
$router->add('POST','/clientes/empresas/storeempresaclient','EmpresaController','storeEmpresaClient');
$router->add('GET', '/clientes/empresaCliente/edit/{id}', 'EmpresaController', 'edit'); 
$router->add('POST', '/clientes/empresaCliente/update/{id}', 'EmpresaController', 'update');
$router->add('POST','/empresaCliente/delete/{id}','ClienteController','delete');
$router->add('GET', '/clientes/empresas/searchByRUCApi', 'EmpresaController', 'searchByRUCApi');