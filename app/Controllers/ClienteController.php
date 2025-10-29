<?php

/**
 * Controlador de Clientes
 * app/Controllers/ClienteController.php
 * 
 * Gestiona las operaciones relacionadas con clientes, 
 * incluyendo busqueda, eliminacion y diferenciacion entre personas naturales y empresas
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cliente;

/**
 * Clase ClienteController
 * 
 * Controlador para la gestion de clientes.
 * Maneja operaciones de busqueda y deshabilitacion de clientes,
 * diferenciando entre clientes tipo persona y tipo empresa.
 */
class ClienteController extends Controller
{

    /**
     * Modelo de cliente
     * @var Cliente
     */
    private Cliente $clienteModel;

    /**
     * Constructor del controlador 
     * 
     * Inicializa el modelo de cliente necesario para las operaciones del controlador
     */
    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    /**
     * Deshabilita el cliente
     * 
     * Marca un cliente como inactivo y redirige a la vista correspondiente segun su tipo:
     * - Persona 
     * - Empresa
     * Solo procesa peticiones POST y requiere autenticacion previa
     * 
     * @param int $id ID del cliente a deshabilitar
     * @return void
     */
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

    /**
     * Busca un cliente por DNI y retorna sus datos en JSON 
     * 
     * End point Ajax que busca un lciente persona por su numero de documento
     * y retorna la informacion en formato JSON. 
     * Requiere autenticacion.
     * 
     * @param string $id Numero de documento (DNI) del cliente a buscar
     * @return void
     */
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
