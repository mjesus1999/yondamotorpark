<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Persona;
use App\Models\Cliente;
use App\Helpers\Validador;
use PDOException;

class PersonaController extends Controller
{
    private Persona $personaModel;
    private Cliente $clienteModel;

    public function __construct()
    {
        $this->personaModel = new Persona();
        $this->clienteModel = new Cliente();
    }

    public function indexPersonCliente(): void
    {
        $this->authRequired();
        $personClientes = $this->personaModel->getAllPersonasCliente();
        $this->view('clientes.index', ['personClientes' => $personClientes]);
    }

    public function createPersonClient(): void
    {
        $this->authRequired();
        $this->view('clientes.create');
    }

    public function storePersonaClient(): void
    {
        $this->authRequired();
        // Detectar si la petición es AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            } else {
                $this->redirect('/clientes/create');
            }
            return;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registroPersona = [
            'apellidos' => $data['apellidos'] ?? '',
            'nombres' => $data['nombres'] ?? '',
            'tipodoc' => $data['tipodocumento'] ?? '',
            'nrodoc' => $data['nrodoc'] ?? '',
            'genero' => $data['genero'] ?? '',
            'iddistrito' => !empty($data['distrito']) ? (int) $data['distrito'] : null,
            'direccion' => empty($data['direccion']) ? null :$data['direccion'],
            'referencia' => $data['referencia'] ?? null,
            'telprimario' => $data['telprimario'] ?? null,
            'telalternativo' => empty($data['telalternativo']) ? null : $data['telalternativo'],
            'latitud' => $data['latitud'] ?? null,
            'longitud' => $data['longitud'] ?? null,
        ];

        $errores = Validador::validarPersonaCrear($registroPersona);

        if (!empty($errores)) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => implode("\n", $errores)], 422);
            } else {
                $this->view('clientes.create', ['error' => implode("<br>", $errores), 'data' => $registroPersona]);
            }
            return;
        }

        $idPersona = $this->personaModel->create($registroPersona);

        if ($idPersona > 0) {
            $registroCliente = [
                'idpersona' => $idPersona,
                'idempresa' => null,
                'idcolactualiza' => null,
                'tipocliente' => 'P',
            ];

            $idCliente = $this->clienteModel->create($registroCliente);

            if ($idCliente > 0) {

                $_SESSION['ultimo_cliente_registrado'] = [
                    'idcliente' => $idCliente,
                    'nrodoc' => $registroPersona['nrodoc'],
                    'tipodoc' => $registroPersona['tipodoc'],
                    'nombres' => $registroPersona['nombres'],
                    'apellidos' => $registroPersona['apellidos'],
                    'telprimario' => $registroPersona['telprimario'],
                    'telalternativo' => $registroPersona['telalternativo'],
                    'direccion'      => $registroPersona['direccion'] ?? '',
                    'timestamp' => time()
                ];
            

                // Preparamos los datos para la respuesta
                $nuevoCliente = [
                    'idcliente' => $idCliente,
                    'cliente' => $registroPersona['nombres'] . ' ' . $registroPersona['apellidos'],
                    'nrodoc' => $registroPersona['nrodoc'],
                    'telprimario' => $registroPersona['telprimario'],
                ];

                if ($isAjax) {
                    // Si es AJAX, devolvemos la respuesta JSON
                    $this->jsonResponse(['success' => true, 'message' => '¡Cliente creado exitosamente!', 'cliente' => $nuevoCliente]);
                } else {
                    // Si es un formulario normal, aplicamos la lógica de redirección original
                    $_SESSION['success'] = '¡Cliente creado exitosamente!';
                    $returnTo = $_GET['return_to'] ?? '/clientes';
                    $this->redirect($returnTo === 'cotizacion' ? '/cotizacion/create' : '/clientes');
                }
                return;
            }
        }


        $errorMsg = 'Error al guardar en la base de datos.';
        if ($isAjax) {
            $this->jsonResponse(['success' => false, 'message' => $errorMsg], 500);
        } else {
            $this->view('clientes.create', ['error' => $errorMsg]);
        }
    }

    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }


    public function edit(int $id): void
    {
        $this->authRequired();
        $personaCliente = $this->personaModel->getById($id);
        if ($personaCliente) {
            $this->view('clientes.edit', ['personaCliente' => $personaCliente]);
        } else {
            http_response_code(404);
            $this->view('errors.404');
        }
    }

    public function update(int $id): void
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'nombres' => $data['nombres'] ?? '',
            'apellidos' => $data['apellidos'] ?? '',
            'email' => !empty($data['email']) ? $data['email'] : null,
            'estadocivil' => !empty($data['estadocivil']) ? $data['estadocivil'] : null,
            'telprimario' => !empty($data['telprimario']) ? $data['telprimario'] : null,
            'latitud' => !empty($data['latitud']) ? $data['latitud'] : null,
            'longitud' => !empty($data['longitud']) ? $data['longitud'] : null,
            'direccion' => !empty($data['direccion']) ? $data['direccion'] : null,
            'iddistrito' => !empty($data['iddistrito']) ? (int) $data['iddistrito'] : null,
            'fechanac' => !empty($data['fechanac']) ? $data['fechanac'] : null,
            'idpersona' => $id
        ];

        $errores = Validador::validarPersonaUpdate($registro);
        $personaCliente = $this->personaModel->getById($id);

        if (!empty($errores)) {
            $this->view('clientes.edit', [
                'personaCliente' => $personaCliente,
                'error' => implode("<br>", $errores)
            ]);
            return;
        }

        $resultado = $this->personaModel->update($registro);

        if ($resultado > 0) {
            $_SESSION['success'] = '¡Cliente actualizado correctamente!';
            $this->redirect('/clientes');
        } elseif ($resultado === 0) {
            $this->view('clientes.edit', [
                'personaCliente' => $personaCliente,
                'error' => 'No se realizaron cambios en el cliente.'
            ]);
        } else {
            $this->view('clientes.edit', [
                'personaCliente' => $personaCliente,
                'error' => 'Error al actualizar el cliente.'
            ]);
        }
    }

    public function store(): void
    {
        $this->authRequired();
        // Solo aceptamos POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios/create');
            return;
        }

        // 1) Recoger y sanear datos
        $apellidos = trim($_POST['apellidos'] ?? '');
        $nombres = trim($_POST['nombres'] ?? '');
        $tipodoc = trim($_POST['tipodoc'] ?? '');
        $nrodoc = trim($_POST['nrodoc'] ?? '');
        $genero = trim($_POST['genero'] ?? '');
        $fechanac = trim($_POST['fechanac'] ?? '');
        $estadocivil = trim($_POST['estadocivil'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $iddistrito = trim($_POST['iddistrito'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $referencia = trim($_POST['referencia'] ?? '');
        $telprimario = trim($_POST['telprimario'] ?? '');
        $telalternativo = trim($_POST['telalternativo'] ?? '');

        // 2) Preparar array para el modelo
        $data = [
            'apellidos' => $apellidos,
            'nombres' => $nombres,
            'tipodoc' => $tipodoc,
            'nrodoc' => $nrodoc,
            'genero' => $genero,
            'fechanac' => $fechanac ?: null,
            'estadocivil' => $estadocivil ?: null,
            'email' => $email ?: null,
            'iddistrito' => $iddistrito !== '' ? (int) $iddistrito : null,
            'direccion' => $direccion ?: null,
            'referencia' => $referencia ?: null,
            'telprimario' => $telprimario,
            'telalternativo' => $telalternativo ?: null,
        ];

        // 3) Validaciones mínimas
        $required = ['apellidos', 'nombres', 'tipodoc', 'nrodoc', 'genero', 'telprimario'];
        $errors = [];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "El campo «{$field}» es obligatorio.";
            }
        }

        if (!empty($errors)) {
            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
            ) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'errors' => $errors,
                ]);
                exit;
            }
            // si no es AJAX, renderizamos la vista normal
            $this->view('usuarios.create', [
                'error' => implode('<br>', $errors),
                'old' => $data
            ]);
            return;
        }

        // 4) Insertar y obtener nuevo ID
        $newId = $this->personaModel->create($data);

        // Respuesta AJAX
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
        ) {
            header('Content-Type: application/json; charset=utf-8');

            if ($newId > 0) {
                $_SESSION['success_message'] = 'Persona registrada correctamente';

                $response = [
                    'success' => true,
                    'message' => 'Persona registrada correctamente',
                    'idpersona' => $newId,
                    'nrodoc' => $data['nrodoc'],
                    'apellidos' => $data['apellidos'],
                    'nombres' => $data['nombres'],
                ];
            } else {
                $response = ['success' => false, 'errors' => ['Error al crear la persona.']];
            }

            echo json_encode($response);
            exit;
        }

        // Respuesta normal en flujo síncrono
        /* if ($newId > 0) {
            $_SESSION['success_message'] = 'Persona registrada con ID ' . $newId;
            $this->redirect('/usuarios');
        } else {
            $this->view('usuarios.create', [
                'error' => 'Error al crear la persona. Intente de nuevo.',
                'old' => $data
            ]);
        } */
    }

    /**
     * Buscar persona por DNI usando API externa
     * @return void
     */
    public function searchByDNIApi(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        // Verificar que sea una petición GET o POST
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Obtener el DNI desde GET o POST
        $dni = '';
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $dni = trim($_GET['dni'] ?? '');
        } else {
            $dni = trim($_POST['dni'] ?? '');
        }

        if ($dni === '') {
            echo json_encode(['success' => false, 'message' => 'DNI es requerido']);
            return;
        }

        // Primero buscar en la base de datos local
        /* $personaLocal = $this->personaModel->searchByDNI($dni);
        if ($personaLocal) {
            echo json_encode([
                'success' => true,
                'source' => 'local',
                'message' => 'Persona encontrada en base de datos local',
                'idpersona' => $personaLocal['idpersona'],
                'apellidos' => $personaLocal['apellidos'],
                'nombres' => $personaLocal['nombres']
            ]);
            return;
        } */

        try {
            require_once __DIR__ . '/../Helpers/Api_dni.php';

            // Capturar la salida de la función
            ob_start();
            searchByDNI($dni);
            $apiResponse = ob_get_clean();

            $responseData = json_decode($apiResponse, true);

            if ($responseData && $responseData['success']) {
                echo json_encode([
                    'success' => true,
                    'source' => 'api',
                    'message' => 'Persona encontrada en RENIEC',
                    'apellidos' => trim($responseData['apepaterno'] . ' ' . $responseData['apematerno']),
                    'nombres' => $responseData['nombres']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $responseData['message'] ?? 'No se encontró la persona'
                ]);
            }
        } catch (PDOException $e) {
            error_log('Error en búsqueda por API DNI: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    /**
     * Buscar Persona por DNI
     * @return void
     */
    public function searchByDNI(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');
        $dni = trim($_GET['dni'] ?? '');
        if ($dni === '') {
            echo json_encode(['success' => false, 'message' => 'Falta DNI']);
            return;
        }
        $persona = $this->personaModel->searchByDNI($dni);
        if ($persona) {
            $response = ['success' => true];
            foreach ($persona as $key => $value) {
                $response[$key] = $value;
            }
        } else {
            $response = ['success' => false, 'message' => 'No encontrado'];
        }
        echo json_encode($response);
    }
}
