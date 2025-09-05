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
        $this->view('clientes.create');
    }

    public function storePersonaClient(): int
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/clientes/create');
            return 0;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registroPersona = [
            'apellidos' => $data['apellidos'] ?? '',
            'nombres' => $data['nombres'] ?? '',
            'tipodoc' => $data['tipodocumento'] ?? '',
            'nrodoc' => $data['nrodoc'] ?? '',
            'genero' => $data['genero'] ?? '',
            'fechanac' => $data['fechanac'] ?? '',
            'estadocivil' => $data['estadocivil'] ?? '',
            'email' => $data['email'] ?? null,
            'iddistrito' => !empty($data['distrito']) ? (int) $data['distrito'] : null,
            'direccion' => $data['direccion'] ?? null,
            'referencia' => $data['referencia'] ?? null,
            'telprimario' => $data['telprimario'] ?? '',
            'telalternativo' => $data['telalternativo'] ?? null,
            'latitud' => $data['latitud'] ?? null,
            'longitud' => $data['longitud'] ?? null,
        ];

        $errores = Validador::validarPersonaCrear($registroPersona);


        if (!empty($errores)) {
            $this->view('clientes.create', ['error' => implode("<br>", $errores), 'data' => $registroPersona]);
            return -1;
        }

        $idPersona = $this->personaModel->create($registroPersona);

        if ($idPersona > 0) {
            $registroCliente = [
                'idpersona' => $idPersona,
                'idempresa' => null,
                // 'idcolregistra'  => null,
                'idcolactualiza' => null,
                'tipocliente' => 'P',
            ];

            $idCliente = $this->clienteModel->create($registroCliente);

            if ($idCliente > 0) {
                $_SESSION['success'] = '¡Cliente creado exitosamente!';
                $this->redirect('/clientes');
                return $idCliente;
            } else {
                $this->view('clientes.create', ['error' => 'Error al crear el cliente.']);
            }
        } else {
            $this->view('clientes.create', ['error' => 'Error al crear la persona.']);
        }

        return -1;
    }

    public function edit(int $id): void
    {
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'nombres' => $data['nombres'] ?? '',
            'apellidos' => $data['apellidos'] ?? '',
            'email' => $data['email'] ?? '',
            'estadocivil' => $data['estadocivil'] ?? '',
            'telprimario' => $data['telprimario'] ?? '',
            'latitud' => $data['latitud'] ?? null,
            'longitud' => $data['longitud'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'iddistrito' => !empty($data['iddistrito']) ? (int) $data['iddistrito'] : null,
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
    /* public function searchByDNI(): void
    {
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
    } */

}
