<?php


// Ruta para listar clientes (Personas-clientes)
$router->add('GET','/clientes','PersonaController','indexPersonCliente');

// Ruta para mostrar el formulario de creación de persona-cliente
$router->add('GET','/clientes/createpersonclient','PersonaController','createPersonClient');

// Ruta para procesar el formulario de creación de persona y cliente (POST)
// Este formulario envía a PersonaController, que luego crea el Cliente
$router->add('POST', '/storepersonclient/store', 'PersonaController', 'store');

