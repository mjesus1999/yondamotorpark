<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Helpers\Validador;

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
        $empresas = $this->empresaModel->getAllEmpresasCliente();
        $this->view('/clientes/empresas.index', ['empresasClientes' => $empresas]);
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

        // Sanitización
        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $empresa = [
            'iddistrito'       => (int)($data['iddistrito'] ?? 0),
            'razonsocial'      => $data['razonsocial'] ?? '',
            'nombrecomercial'  => $data['nombrecomercial'] ?? '',
            'ruc'              => $data['ruc'] ?? '',
            'representante'    => $data['representante'] ?? '',
            'email'            => !empty($data['email']) ? $data['email'] : null,
            'direccion'        => $data['direccion'] ?? '',
            'referencia'       => !empty($data['referencia']) ? $data['referencia'] : null,
            'latitud'          => !empty($data['latitud']) ? $data['latitud'] : null,
            'longitud'         => !empty($data['longitud']) ? $data['longitud'] : null,
            'telprimario'      => $data['telprimario'] ?? '',
            'telsecundario'    => !empty($data['telsecundario']) ? $data['telsecundario'] : null,
        ];

        $errores = [];

        // Validaciones básicas - 
        $errores[] = Validador::campoObligatorio($empresa['iddistrito'], 'Distrito');
        $errores[] = Validador::campoObligatorio($empresa['razonsocial'], 'Razón Social');
        $errores[] = Validador::campoObligatorio($empresa['nombrecomercial'], 'Nombre Comercial');

        // Validación de RUC 
        $errorRuc = Validador::campoObligatorio($empresa['ruc'], 'RUC');
        if ($errorRuc) {
            $errores[] = $errorRuc;
        } else {
            $errores[] = Validador::soloNumeros($empresa['ruc'], 'RUC');
        }

        $errores[] = Validador::campoObligatorio($empresa['representante'], 'Representante');

        // Validación de teléfono corregida
        $errorTel = Validador::campoObligatorio($empresa['telprimario'], 'Teléfono');
        if ($errorTel) {
            $errores[] = $errorTel;
        } else {
            $errores[] = Validador::telefonoValido($empresa['telprimario'], 'teléfono');
        }

        // Email solo si no está vacío
        if (!empty($empresa['email'])) {
            $errores[] = Validador::emailValido($empresa['email']);
        }

        $errores = array_filter($errores); 

        if (!empty($errores)) {
            $this->view('/clientes/empresas.create',['error' => implode("<br>",$errores),'data' => $empresa]); 
            return -1;
        }

        // // Debug: Log para verificar los datos antes de insertar
        // error_log("Datos a insertar: " . print_r($empresa, true));

        $idEmpresa = $this->empresaModel->create($empresa);

        // Debug: Log del resultado
        // error_log("ID Empresa creado: " . $idEmpresa);

        if ($idEmpresa > 0) {
            $cliente = [
                'idpersona' => null,
                'idempresa' => $idEmpresa,
                'idcolregistra' => null,
                'idcolactualiza' => null,
                'tipocliente' => 'E'
            ];

            $idCliente = $this->clienteModel->create($cliente);

            if ($idCliente > 0) {

                $_SESSION['success'] = 'Cliente creado correctamente';
                $this->redirect('/clientes/empresas');
                //  $this->view('clientes/empresas.create', ['success' => 'Se agregó el cliente correctamente.']);
                 return $idCliente;
            } else {
                $this->view('clientes/empresas.create', ['error' => 'Error al crear el cliente.']);
            }
        } else {
            $this->view('clientes/empresas.create', ['error' => 'Error al registrar la empresa.']);
        }

        return -1;
    }

    public function edit(int $id): void
    {
        $empresa = $this->empresaModel->getById($id);
        if ($empresa) {
            $this->view('clientes/empresas.edit', ['empresaCliente' => $empresa]);
        } else {
            http_response_code(404);
            $this->view('errors.404');
        }
    }
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $empresa = [
            'razonsocial' => $data['razonsocial'] ?? '',
            'nombrecomercial' => $data['nombrecomercial'] ?? '',
            'ruc' => $data['ruc'] ?? '',
            'representante' => $data['representante'] ?? '',
            'email' => !empty($data['email']) ? $data['email'] : null,
            'telprimario' => $data['telprimario'] ?? '',
            'idempresa' => $id
        ];

        $errores = [];

        // Validaciones corregidas
        $errores[] = Validador::campoObligatorio($empresa['razonsocial'], 'Razón Social');
        $errores[] = Validador::campoObligatorio($empresa['nombrecomercial'], 'Nombre Comercial');

        // RUC
        $errorRuc = Validador::campoObligatorio($empresa['ruc'], 'RUC');
        if ($errorRuc) {
            $errores[] = $errorRuc;
        } else {
            $errores[] = Validador::soloNumeros($empresa['ruc'], 'RUC');
        }

        $errores[] = Validador::campoObligatorio($empresa['representante'], 'Representante');

        // Teléfono
        $errorTel = Validador::campoObligatorio($empresa['telprimario'], 'Teléfono');
        if ($errorTel) {
            $errores[] = $errorTel;
        } else {
            $errores[] = Validador::telefonoValido($empresa['telprimario'], 'teléfono');
        }

        // Email solo si no está vacío
        if (!empty($empresa['email'])) {
            $errores[] = Validador::emailValido($empresa['email']);
        }

        $errores = array_filter($errores);

        if (!empty($errores)) {
            $empresaCliente = $this->empresaModel->getById($id);
            $this->view('clientes/empresas.edit', [
                'empresaCliente' => $empresaCliente,
                'error' => implode("<br>", $errores)
            ]);
            return;
        }

        $resultado = $this->empresaModel->update($empresa);

        $empresaCliente = $this->empresaModel->getById($id);

        if ($resultado > 0) {
            $_SESSION['success'] = 'Cliente actualizado correctamente';
            $this->redirect('/clientes/empresas');
        } elseif ($resultado === 0) {
            $this->view('clientes/empresas.edit', [
                'empresaCliente' => $empresaCliente,
                'error' => 'No se realizaron cambios.'
            ]);
        } else {
            $this->view('clientes/empresas.edit', [
                'empresaCliente' => $empresaCliente,
                'error' => 'Error al actualizar el cliente.'
            ]);
        }
    }
}
