<?php
// app/Controllers/ClienteController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;

class ClienteController extends Controller
{
    private Cliente $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Cliente();
    }

    /**
     * GET /clientes/lista
     * Devuelve JSON con todos los clientes para el AJAX.
     */
    public function getCliente(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $lista = $this->model->getAll();  // tu método en App\Models\Cliente
        echo json_encode([
            'success'  => true,
            'clientes' => $lista
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
