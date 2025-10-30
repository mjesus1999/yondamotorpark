<?php

/**
 * Controlador de Local
 * 
 * app/Controllers/LocalController.php
 * 
 * Gestiona todas las operaciones CRUD relacionadas con los locales/sucursales
 * de la empresa, incluyendo validaciones de datos, gestión de estados y
 * endpoints API para consumo AJAX.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Local;
use App\Helpers\Validador;

/**
 * Clase LocalController
 * 
 * Controlador para la gestion de locales/sucusales.
 * Proporciona funcionalidades completas de CRUD con validaciones robustas y
 * endpoints tanto para vistas como para operaciones AJAX/API.
 */
class LocalController extends Controller
{

    /**
     * Modelo de Local
     * @var Local
     */
    private Local $localModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa el modelo de Local necesario para las operaciones del controlador.
     */
    public function __construct()
    {
        $this->localModel = new Local();
    }

    /**
     * Muestra el listado de todos los locales
     * 
     * Renderiza la vista principal con todos los locales activos del sistema.
     * requiere autenticacion previa.
     * 
     * @return void
     */
    public function index(): void
    {
        $this->authRequired();
        $locales = $this->localModel->getAll();
        $this->view('locales.index', ['locales' => $locales]);
    }

    /**
     * Muestra el formulario de creacion de local
     * 
     * Renderiza la vista con el formulario para registrar un nuevo local.
     * 
     * @return void
     */
    public function create(): void
    {
        $this->view('locales.create');
    }

    /**
     * Registra un nuevo local.
     * 
     * Procesa el formulario de creacion de local, validando todo los campos
     * requeridos incluyendo formato de telefono y email.
     * Realiza validaciones:
     *      - campos obligatorios: Tienda, distrito, motorpark, principal, responsable, telefono
     *      - Formato de telefono valido
     *      - Formato de email valido (si se proporciona)
     * @return int ID del Localcreado si existe 
     */
    public function store(): int
    {
        $this->authRequired();
        // 1. Validar que la petición sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return 0; // Errror en el método
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'tienda' => $data['tienda'] ?? '',
            'iddistrito' => (int) ($data['iddistrito'] ?? 0),
            'idmotorpark' => (int) ($data['idmotorpark'] ?? 0),
            'principal' => $data['principal'] ?? '',
            'responsable' => $data['responsable'] ?? '',
            'correo' => !empty($data['correo']) ? $data['correo'] : null,
            'direccion' => !empty($data['direccion']) ? $data['direccion'] : null,
            'telefono' => $data['telefono'] ?? ''
        ];


        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['tienda'], 'Local');
        $errores[] = Validador::campoObligatorio($registro['iddistrito'], 'Distrito');
        $errores[] = Validador::campoObligatorio($registro['idmotorpark'], 'Tienda');
        $errores[] = Validador::campoObligatorio($registro['principal'], 'Es Principal');
        $errores[] = Validador::campoObligatorio($registro['responsable'], 'Responsable');

        $errorTel = Validador::campoObligatorio($registro['telefono'], 'Teléfono');
        if ($errorTel) {
            $errores[] = $errorTel;
        } else {
            $errores[] = Validador::telefonoValido($registro['telefono'], 'Teléfono');
        }

        if (!empty($registro['correo'])) {
            $errores[] = Validador::emailValido($registro['correo']);
        }

        $errores = array_filter($errores);

        if (!empty($errores)) {
            $this->view('locales.create', ['error' => implode('<br>', $errores), 'data' => $registro]);
            return -1;
        }

        $lastInsertId = $this->localModel->create($registro);

        if ($lastInsertId > 0) {
            $_SESSION['success'] = 'Local agregado correctamente';
            $this->redirect('/locales');
            return $lastInsertId; // Retorna el ID insertado

        } else {
            $this->view('locales.create', ['error' => 'Error al crear el local.']);
            return $lastInsertId;
        }
    }

    /**
     * Obtiene los datos de un local para edicion 
     * 
     * Enpoint AJAX que retorna los datos especifico en formato JSON.
     * Requiere autenticacion y responde con codigo HTTP 404 si no existe.
     * 
     * @param int $id ID del local a consultar 
     * @return void
     */
    public function edit(int $id): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $local = $this->localModel->getById($id);
        if ($local) {
            echo json_encode(['success' => true, 'local' => $local]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Local no encontrado.']);
        }
        exit();
    }

    /**
     * Actualiza los datos de un local existente
     * 
     * Endpoint AJAX que procesa la actualiza de responsable y telefono
     * de un local. Valida campos obligatorios y responde en formato JSON.
     * Solo acepta peticiones POST.
     * 
     * Respuesta HTTP:
     * - 200: Actualiza exitosa
     * - 400: Errores de validacion
     * - 405: Metodo no permitido
     * - 500: Error en la actualizacion
     * 
     * @param int $id ID del local a actualizar
     * @return void
     */
    public function update(int $id): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $responsable = trim($_POST['responsable'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            $registro = [
                'responsable' => $responsable,
                'telefono' => $telefono,
                'idlocal' => $id
            ];

            $errores = [];
            if (empty($registro['responsable'])) {
                $errores[] = "El campo 'Responsable' es obligatorio.";
            }
            if (empty($registro['telefono'])) {
                $errores[] = "El campo 'Teléfono' es obligatorio.";
            }

            if (count($errores) > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Errores de validación: ' . implode('<br>', $errores)]);
                exit();
            }

            $rowsAffected = $this->localModel->update($registro);

            if ($rowsAffected > 0) {
                echo json_encode(['success' => true, 'message' => '¡Local actualizado exitosamente!']);
            } else {

                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el local o no se realizaron cambios.']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
        }
        exit();
    }

    /**
     * Deshabilita un local.
     * 
     * Marca el local como inactivo sin eliminarlo fisicamente de la base de datos.
     * Solo procesa peticiones POST y requiere autenticacion.
     * Redirige al listado de locales despues de la operacion.
     * 
     * @param int $id ID del local a deshabilitar
     * @return void
     */
    public function delete(int $id): void
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->localModel->disable($id) > 0) {
                $this->redirect('/locales');
            } else {

                $this->redirect('/products?error=delete_failed');
            }
        } else {

            http_response_code(405);
            $this->view('errors.405');
        }
    }

    /**
     * Devuelve la lista de locales activos en formato JSON.
     *
     * Este endpoint actúa como API y retorna todos los locales activos,
     * incluyendo sus campos principales (ID, nombre y dirección).
     *
     * Respuestas:
     * - 200 OK: Retorna un array de locales en formato JSON.
     * - 404 Not Found: Si no se encuentran locales, retorna un JSON vacío.
     *
     * @return void Imprime un JSON con la lista de locales o un array vacío.
     */
    public function apiGetLocales()
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $locales = $this->localModel->getAllLocales();

        if ($locales) {
            echo json_encode($locales);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}
