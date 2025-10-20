<?php

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

    public function delete($id): void
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $result = $this->clienteModel->disabled($id);

            // Obtener tipo cliente para redirección
            $tipoCliente = $this->clienteModel->getTipoClienteById($id);

            // Redirige según tipo
            if ($tipoCliente === 'P') {
                $this->redirect('/clientes');
            } else {
                $this->redirect('/clientes/empresas');
            }
        }
    }




    public function searchCliente($id): void
    {
        $this->authRequired();

        header('Content-Type: application/json; charset=utf-8');

        $cliente = $this->clienteModel->searchClienteDB($id);

        if (!empty($cliente)) {
            echo json_encode($cliente);
        } else {
            echo json_encode([]);
        }
    }
}
