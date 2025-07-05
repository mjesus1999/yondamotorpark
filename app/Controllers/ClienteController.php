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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $result = $this->clienteModel->disabled($id);

        // Obtener tipo cliente para redirección
        $tipoCliente = $this->clienteModel->getTipoClienteById($id);
        
        // Redirige según tipo
        if ($tipoCliente === 'P') {
            $this->redirect('/clientes');  
        } else  {
            $this->redirect('/clientes/empresas');  
        
    }

}
}

}
