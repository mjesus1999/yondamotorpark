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

    public function storeEmpresaClient(): int
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->redirect('/clientes/createempresaclient');
            return 0;
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
        $nombresCampos = [
            'iddistrito' => 'Distrito',
            'razonsocial' => 'Razón Social',
            'nombrecomercial' => 'Nombre Comercial',
            'ruc' => 'RUC',
            'representante' => 'Representante',
            'telprimario' => 'Teléfono',
        ];
        foreach (['iddistrito', 'razonsocial', 'nombrecomercial', 'ruc', 'representante', 'telprimario'] as $campo) {
            if (empty($registroEmpresa[$campo])) {
                $nombreAmigable = $nombresCampos[$campo] ?? $campo;
                $errores[] = "El campo '$nombreAmigable' es obligatorio.";
            }
        }

        if (count($errores) > 0) {
            $this->view('clientes/empresas.create', ['error' => implode('\n', $errores)]);
            return -1;
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

                $success = 'Se agrego el cliente';
                $this->view('clientes/empresas.create',['success' => $success]);
                
            } else {
                $this->view('clientes/empresas.create', ['error' => 'Error al crear el registro del cliente.']);
            }
        } else {
            $this->view('clientes.create', ['error' => 'Error al crear la empresa.']);
        }
        return -1;
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
                'razonsocial' => trim($_POST['razonsocial']) ?? '',
                'nombrecomercial' => trim($_POST['nombrecomercial']) ?? '',
                'ruc' => trim($_POST['ruc']) ?? '',
                'representante' => trim($_POST['representante']) ?? '',
                'email' => trim($_POST['email']) ?? '',
                'telprimario' => trim($_POST['telprimario']) ?? '',
                'idempresa' => $id
            ];

            // Validación amigable
            $errores = [];
            $nombresCampos = [
                'razonsocial' => 'Razón Social',
                'nombrecomercial' => 'Nombre Comercial',
                'ruc' => 'RUC',
                'representante' => 'Representante',
                'telprimario' => 'Teléfono'
            ];
            foreach (['razonsocial', 'nombrecomercial', 'ruc', 'representante', 'telprimario'] as $campo) {
                if (empty($registro[$campo])) {
                    $nombreAmigable = $nombresCampos[$campo] ?? $campo;
                    $errores[] = "El campo '$nombreAmigable' es obligatorio.";
                }
            }

            if (count($errores) > 0) {
                $empresaCliente = $this->empresaModel->getById($id);
                $this->view('clientes/empresas.edit', [
                    'empresaCliente' => $empresaCliente,
                    'error' => implode('\n', $errores)
                ]);
                return;
            }

            // Si la actualización fue exitosa
            $resultado = $this->empresaModel->update($registro);
            //var_dump($resultado); exit;

            if ($resultado >= 0) {
                // Actualización exitosa
                $empresaCliente = $this->empresaModel->getById($id);
                $success = '¡Cliente actualizado correctamente!';
                $this->view('clientes/empresas.edit', [
                    'empresaCliente' => $empresaCliente,
                    'success' => $success
                ]);
            } /*elseif ($resultado === 0) {
                // No hubo cambios
                $empresaCliente = $this->empresaModel->getById($id);
                $this->view('clientes/empresas.edit', [
                    'empresaCliente' => $empresaCliente,
                    'error' => 'No se realizaron cambios en el cliente.'
                ]);*/
            else {
                // Error real
                $empresaCliente = $this->empresaModel->getById($id);
                $this->view('clientes/empresas.edit', [
                    'empresaCliente' => $empresaCliente,
                    'error' => 'Error al actualizar el cliente.'
                ]);
            }
        }
    }
}
