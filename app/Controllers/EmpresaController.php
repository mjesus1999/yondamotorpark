<?php

/**
 * Controlador de Empresa
 * 
 * app/Controllers/EmpresaController.php
 * 
 * Gestiona todas las operaciones relacionadas con empresas cliente,
 * Incluyendo validaciones fiscales (RUC), integracion con API externa de SUNAT,
 * y sincronizacion con la base de datos local.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Helpers\Validador;
use Exception;

/**
 * Clase EmpresaController
 * 
 * Controlador para la gestion de empresas cliente,
 * Porporciona funcionalidades CRUD completas con validaciones robustas,
 * verificacion de RUC mediante API externa y gestion dual de cliente empresa.
 */
class EmpresaController extends Controller
{

    /**
     * Modelo de empresa
     * @var Empresa
     */
    private Empresa $empresaModel;
    /**
     * Modelo de cleinte
     * @var Cliente
     */
    private Cliente $clienteModel;

    /**
     * Contructor del controlador
     * 
     * Inicializa los modelos de Empresa y cliente necesarios para las operaciones del controlador.
     */
    public function __construct()
    {
        $this->empresaModel = new Empresa();
        $this->clienteModel = new Cliente();
    }

    /**
     * Muestra el listado de empresas cliente
     * 
     * Renderiza la vista principal con todas las empresas registradas como clientes activos del sistema.
     * Requiere autenticacion previa
     * 
     * @return void
     */
    public function indexEmpresaClientes(): void
    {
        $empresas = $this->empresaModel->getAllEmpresasCliente();
        $this->authRequired();
        $this->view('clientes.empresas.index', ['empresasClientes' => $empresas]);
    }

    /**
     * Muestra el formulario de creacion de empresa cliente
     * 
     * Renderiza la vista con el formulario para registrar una nueva empresa como cliente.
     * 
     * @return void
     */
    public function createEmpresaClient(): void
    {
        $this->authRequired();
        $this->view('clientes.empresas.create');
    }

    /**
     * Registra una nueva empresa cliente en el sistema
     * 
     * Procesa el formulario de creacion, validando todos los campos requeridos incluyendo RUC unico,
     * formato de telefono y email. Crea tanto el registro de una empresa como el de cliente asociado
     * en una operacion transaccional. 
     * 
     * Validaciones realizadas:
     * - Campos obligatorios: DIST, RAZON SOCIAL, NOMBRE COMERCIAL, RUC, REPRESENTANTE, TELEFONO.
     * - RUC: solo numeros y unicidad en el sistema
     * - Formato de telefono valido
     * - Formato de email valido (si se proporciona)
     * 
     * @return int ID del cliente creado si exitoso, -1 en error de validacion o registro
     *              0 si el metodo HTTP no es POST
     */
    public function storeEmpresaClient(): int
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/clientes/createempresaclient');
            return 0; // Error en el método
        }

        // Sanitización
        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $empresa = [
            'iddistrito' => (int) ($data['distrito'] ?? 0),
            'razonsocial' => html_entity_decode($data['razonsocial'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'nombrecomercial' => html_entity_decode($data['nombrecomercial'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'ruc' => $data['ruc'] ?? '',
            'representante' => html_entity_decode($data['representante'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'email' => !empty($data['email']) ? $data['email'] : null,
            'direccion' => !empty($data['direccion']) ? html_entity_decode($data['direccion'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : null,
            'referencia' => !empty($data['referencia']) ? html_entity_decode($data['referencia'], ENT_QUOTES | ENT_HTML5, 'UTF-8') : null,
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
            $this->view('clientes.empresas.create', ['error' => implode("<br>", $errores), 'data' => $empresa]);
            return -1;
        }

        if ($this->empresaModel->rucExiste($empresa['ruc'])) {
            $this->view('clientes.empresas.create', [
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
                'idcolactualiza' => null,
                'tipocliente' => 'E'
            ];

            $idCliente = $this->clienteModel->create($cliente);

            if ($idCliente > 0) {

                $_SESSION['success'] = 'Cliente creado correctamente';
                $this->redirect('/clientes/empresas');
                return $idCliente; // Se pudo agregar
            } else {
                $this->view('clientes.empresas.create', ['error' => 'Error al crear el cliente.']);
            }
        } else {
            $this->view('clientes.empresas.create', ['error' => 'Error al registrar la empresa.']);
        }

        return -1; // No se pudo agregar a la DB
    }

    /**
     * Muestra el formulario de edicion de Empresa cliente
     * 
     * Carga los datos de una empresa especifica para su edicion.
     * Requiere autenticacion y muestra error 404 si no existe.
     * 
     * @param int $id ID de la empresa a editar
     * @return void
     */
    public function edit(int $id): void
    {
        $this->authRequired();
        $empresa = $this->empresaModel->getById($id);
        if ($empresa) {
            $this->view('clientes.empresas.edit', ['empresaCliente' => $empresa]);
        } else {
            http_response_code(404);
            $this->view('errors.404');
        }
    }

    /**
     * Actualiza los datos de una empresa cliente existente
     * 
     * Procesa el formulario de actualizacion, validando todos los campos.
     * Realiza las mismas validaciones que en la creacion (RUC, TELEFONO, EMAIL).
     * Solo procesa peticiones POST y requiere autenticacion.
     * 
     * @param int $id ID de la empresa a actualizar
     * @return void
     */
    public function update(int $id): void
    {
        $this->authRequired();
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
            $this->view('clientes.empresas.edit', [
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
            $this->view('clientes.empresas.edit', [
                'empresaCliente' => $empresaCliente,
                'error' => 'No se realizaron cambios.'
            ]);
        } else {
            $this->view('clientes.empresas.edit', [
                'empresaCliente' => $empresaCliente,
                'error' => 'Error al actualizar el cliente.'
            ]);
        }
    }

    /**
     * Busca una empresa por RUC usando base de datos local y API externa
     * 
     * Endpoint AJAX que realiza una búsqueda en dos niveles:
     * 1. Primero busca en la base de datos local
     * 2. Si no existe localmente, consulta la API externa de SUNAT
     * 
     * Acepta peticiones GET y POST. Responde en formato JSON con información
     * completa de la empresa incluyendo razón social, nombre comercial,
     * representante y datos de contacto.
     * 
     * Respuesta para empresa local:
     * - success: true
     * - source: 'local'
     * - datos de la empresa (idempresa, razonsocial, nombrecomercial, etc.)
     * 
     * Respuesta para API externa:
     * - Formato definido por el helper Api_ruc.php
     *
     * @return void
     */
    public function searchByRUCApi(): void
    {
        $this->authRequired();
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
