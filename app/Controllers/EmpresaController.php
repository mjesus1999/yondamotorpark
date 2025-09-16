<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Helpers\Validador;
use Exception;

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
        $this->authRequired();
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
            return 0; // Error en el método
        }

        // Sanitización
        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $empresa = [
            'iddistrito' => (int) ($data['distrito'] ?? 0),
            'razonsocial' => $data['razonsocial'] ?? '',
            'nombrecomercial' => $data['nombrecomercial'] ?? '',
            'ruc' => $data['ruc'] ?? '',
            'representante' => $data['representante'] ?? '',
            'email' => !empty($data['email']) ? $data['email'] : null,
            'direccion' => !empty($data['direccion']) ? $data['direccion'] : null,
            'referencia' => !empty($data['referencia']) ? $data['referencia'] : null,
            'latitud' => !empty($data['latitud']) ? $data['latitud'] : null,
            'longitud' => !empty($data['longitud']) ? $data['longitud'] : null,
            'telprimario' => $data['telprimario'] ?? '',
            'telsecundario' => !empty($data['telsecundario']) ? $data['telsecundario'] : null,
        ];

        $errores = [];

        // Validaciones básicas 
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
            $this->view('/clientes/empresas.create', ['error' => implode("<br>", $errores), 'data' => $empresa]);
            return -1;
        }

        if ($this->empresaModel->rucExiste($empresa['ruc'])) {
            $this->view('/clientes/empresas.create', [
                'error' => 'El RUC ingresado ya se encuentra registrado',
                'data' => $empresa
            ]);

            return -1;
        }

        $idEmpresa = $this->empresaModel->create($empresa);

        if ($idEmpresa > 0) {
            $cliente = [
                'idpersona' => null,
                'idempresa' => $idEmpresa,
                // 'idcolregistra' => null,
                'idcolactualiza' => null,
                'tipocliente' => 'E'
            ];

            $idCliente = $this->clienteModel->create($cliente);

            if ($idCliente > 0) {

                $_SESSION['success'] = 'Cliente creado correctamente';
                $this->redirect('/clientes/empresas');
                return $idCliente; // Se pudo agregar
            } else {
                $this->view('clientes/empresas.create', ['error' => 'Error al crear el cliente.']);
            }
        } else {
            $this->view('clientes/empresas.create', ['error' => 'Error al registrar la empresa.']);
        }

        return -1; // No se pudo agregar a la DB
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

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

    /**
     * Buscar empresa por RUC usando API externa
     * @return void
     */
    public function searchByRUCApi(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $ruc = '';
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $ruc = trim($_GET['ruc'] ?? '');
        } else {
            $ruc = trim($_POST['ruc'] ?? '');
        }

        if ($ruc === '') {
            echo json_encode(['success' => false, 'message' => 'RUC es requerido']);
            return;
        }

        // Primero buscar en la base de datos local
        $empresaLocal = $this->empresaModel->searchByRUC($ruc);
        if ($empresaLocal) {
            echo json_encode([
                'success' => true,
                'source' => 'local',
                'message' => 'Empresa encontrada en base de datos local',
                'idempresa' => $empresaLocal['idempresa'],
                'razonsocial' => $empresaLocal['razonsocial'],
                'nombrecomercial' => $empresaLocal['nombrecomercial'],
                'representante' => $empresaLocal['representante'],
                'email' => $empresaLocal['email'],
                'telprimario' => $empresaLocal['telprimario']
            ]);
            return;
        }

        // Si no existe localmente, buscar en API externa
        try {
            require_once __DIR__ . '/../Helpers/Api_ruc.php';

            ob_start();
            searchByRUC($ruc);
            $apiResponse = ob_get_clean();

            $responseData = json_decode($apiResponse, true);

            if ($responseData && $responseData['success']) {
                echo $apiResponse;
            } else {
                echo $apiResponse;
            }

        } catch (Exception $e) {
            error_log('Error en búsqueda por API RUC: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }
}
