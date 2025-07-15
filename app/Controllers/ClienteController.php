<?php
// app/Controllers/ClienteController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;

class ClienteController extends Controller
{
    private Cliente $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    /**
     * GET /clientes/lista
     * Devuelve JSON con todos los clientes.
     */
    public function getAllCliente(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $lista = $this->clienteModel->getAll();
        echo json_encode([
            'success'  => true,
            'clientes' => $lista
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
