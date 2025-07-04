<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Empresa;
use App\Models\Cliente;

class EmpresaController extends Controller
{
    private Empresa $empresaModel;
    private Cliente $clienteModel;


    public function __construct()
    {
        $this->empresaModel = new Empresa();
        $this->clienteModel = new Cliente();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function indexEmpresaClientes(): void
    {
        $empresaClientes = $this->empresaModel->getAllEmpresasCliente();

        $this->view('/clientes/empresas.index', ['empresasClientes' => $empresaClientes]);
    }

    public function createEmpresaClient(): void
    {
        $this->view('/clientes/empresas.create');
    }

    public function storeEmpresaClient(): void
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->redirect('/clientes/createempresaclient');
            return;
        }

        $iddistrito_val = trim($_POST['iddistrito'] ?? '');
        $razonsocial_val = trim($_POST['razonsocial'] ?? '');
        $nombrecomercial_val = trim($_POST['nombrecomercial'] ?? '');
        $ruc_val = trim($_POST['ruc'] ?? '');
        $representante_val = trim($_POST['representante'] ?? '');
        $email_val = trim($_POST['email'] ?? '');
        $direccion_val = trim($_POST['direccion'] ?? '');
        $referencia_val = trim($_POST['referencia'] ?? '');
        $latitud_val = trim($_POST['latitud'] ?? '');
        $longitud_val = trim($_POST['longitud'] ?? '');
        $telprimario_val = trim($_POST['telprimario'] ?? '');
        $telsecundario_val = trim($_POST['telsecundario'] ?? '');

        $registroEmpresa = [
            'iddistrito'     => ($iddistrito_val !== '') ? (int)$iddistrito_val : null,
            'razonsocial'    => ($razonsocial_val !== '') ? $razonsocial_val : null,
            'nombrecomercial' => ($nombrecomercial_val !== '') ? $nombrecomercial_val : null,
            'ruc'            => ($ruc_val !== '') ? $ruc_val : null,
            'representante'  => ($representante_val !== '') ? $representante_val : null,
            'email'          => ($email_val !== '') ? $email_val : null,
            'direccion'      => ($direccion_val !== '') ? $direccion_val : null,
            'referencia'     => ($referencia_val !== '') ? $referencia_val : null,
            'latitud'        => ($latitud_val !== '') ? $latitud_val : null,
            'longitud'       => ($longitud_val !== '') ? $longitud_val : null,
            'telprimario'    => $telprimario_val,
            'telsecundario' => ($telsecundario_val !== '') ? $telsecundario_val : null,
        ];

        $errores = [];
        foreach (['iddistrito', 'razonsocial', 'nombrecomercial', 'ruc', 'representante', 'direccion', 'telprimario'] as $campo) {
            if (empty($registroEmpresa[$campo])) {
                $errores[] = "El campo '" . $campo . "' es obligatorio.";
            }
        }

        if (count($errores) > 0) {

            $this->view('/clientes/empresas.create', ['error' => implode('<br>', $errores)]);
            return;
        }

        $idEmpresa = $this->empresaModel->create($registroEmpresa);

        if ($idEmpresa > 0) {
            $registroCliente = [
                'idpersona'      => null,
                'idempresa'      => $idEmpresa,
                'idcolregistra'  => null,
                'idcolactualiza' => null,
                'tipocliente'    => 'E',
            ];

            $idCliente = $this->clienteModel->create($registroCliente);

            if ($idCliente > 0) {
                $_SESSION['message'] = 'Se registro el cliente exitosamente';
                $_SESSION['message_type'] = 'SUCCESS';
                // $this->view('clientes/empresas.create');
                $this->redirect('/clientes/empresas');
            } else {
                $_SESSION['message'] = 'No se pudo crear el cliente.';
                $_SESSION['message_type'] = 'ERROR';

                $this->view('clientes/empresas.create');
            }
        } else {
            $_SESSION['message'] = 'No se ha podido crear la empresa';
            $_SESSION['message_type'] = 'ERROR';
            $this->view('clientes/empresas.create');
        }
        return;
    }


    public function edit(int $id): void
    {
        $empresaCliente = $this->empresaModel->getById($id);
        if ($empresaCliente) {
            $this->view('clientes/empresas.edit', ['empresaCliente' => $empresaCliente]);
        } else {
            http_response_code(404);
            $this->view('errors.404');
        }
    }


    public function update(int $id): void
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $registro = [
                'razonsocial' =>   trim($_POST['razonsocial']) ?? '',
                'nombrecomercial' =>   trim($_POST['nombrecomercial']) ?? '',
                'ruc' =>   trim($_POST['ruc']) ?? '',
                'representante' =>   trim($_POST['representante']) ?? '',
                'email' =>   trim($_POST['email']) ?? '',
                'telprimario' =>   trim($_POST['telprimario']) ?? '',
                'idempresa' => $id

            ];

            if ($registro['razonsocial'] && $registro['nombrecomercial'] && $registro['ruc'] && $registro['representante'] &&$registro['telprimario'] !== false) {
                if ($this->empresaModel->update($registro)) {
                    $this->redirect('/clientes/empresas');
                } else {
                    $empresaCliente = $this->empresaModel->getById($id);
                    $this->view('clientes/empresas.edit', ['empresaCliente' => $empresaCliente, 'error' => 'Error al actualizar el cliente.']);
                }
            } else {
                $empresaCliente = $this->empresaModel->getById($id);
                $this->view('clientes/empresas.edit', ['empresaCliente' => $empresaCliente, 'error' => 'Todos los campos son obligatorios.']);
            }
        }
    }
}
