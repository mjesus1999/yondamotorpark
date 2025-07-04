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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function delete($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->clienteModel->disabled($id);

            // Asignar mensaje basado en el resultado
            if ($result > 0) {
                $_SESSION['message'] = 'Se eliminó el cliente exitosamente';
                $_SESSION['message_type'] = 'SUCCESS';
            } else {
                $_SESSION['message'] = 'No se pudo eliminar el cliente. Inténtelo nuevamente.';
                $_SESSION['message_type'] = 'ERROR';
            }

            
            $this->redirect('/clientes');
        }
    }
}
