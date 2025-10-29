<?php

/**
 * Controlador de Marcas
 * 
 * Gestiona todas las operaciones CRUD relacionadas con las marcas de vehiculos o productos, 
 * incluyendo validaciones y respuestas JSON para operaciones AJAX
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Marca;

/**
 * Clase MarcaController
 * 
 * Controlador para la gestion de marcas.
 * Porporciona endPoints para listar, crear, actualizar y eliminar marcas,
 * con manejo de restricciones de integridad y validaciones
 */
class MarcaController extends Controller
{
    /**
     * Modelo de Marca
     * @var Marca
     */
    private Marca $marcaModel;

    /**
     * Inicializa el modelo de Marca necesario para las operaciones del controlador.
     */
    public function __construct()
    {
        $this->marcaModel = new Marca();
    }

    /**
     * Muestra el listado de todas las marcas
     * Renderiza la vista principal con todas las marcas registradas,
     * requiere autenticacion previa.
     * 
     * @return void
     */
    public function index(): void
    {
        $this->authRequired();
        $data = $this->marcaModel->getAll();
        $this->view('marcas.index', ['marcas' => $data]);
    }

    /**
     * Obtiene una marca especifica por ID
     * 
     * EndPoint Ajax que retorna los datos de una marca en formato JSON.
     * Valida que el ID sea valido antes de realizar la busqueda.
     * 
     * @return void
     */
    public function show(): void
    {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de marca no válido']);
            return;
        }

        $marca = $this->marcaModel->getById($id);

        if ($marca) {
            echo json_encode(['success' => true, 'data' => $marca]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Marca no encontrada']);
        }
    }

    /**
     * Registra una nueva marca
     * 
     * EndPoint AJAX que procesa el formulario de registro de una marca.
     * Valida que el nombre no este vacio y maneja duplicados mediante
     * restriccion (UNIQUE), responde en formato JSON.
     * 
     * @return void
     */
    public function store()
    {
        header('Content-Type: application/json');
        $marca = $_POST['marca'] ?? '';

        if (trim($marca) === '') {
            echo json_encode(['success' => false, 'message' => 'La marca no puede estar vacía']);
            return;
        }

        $id = $this->marcaModel->create($marca);

        if ($id > 0) {
            $nuevaMarca = $this->marcaModel->getById($id);
            echo json_encode([
                'success' => true,
                'message' => 'Marca registrada correctamente',
                'data' => $nuevaMarca
            ]);
        } elseif ($id === -2) {
            echo json_encode(['success' => false, 'message' => 'Esta marca ya existe.', 'type' => 'WARNING']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se ha podido registrar la marca']);
        }
    }

    /**
     * Atualiza una marca exitente
     * 
     * Endpoint AJAX que procesa la actualizacion de una marca.
     * Valida que los datos esten completos y maneja duplicados.
     * Retorna los datos actualizacion en formato JSON.
     * @return void
     */
    public function update()
    {
        header('Content-Type: application/json');
        $id = $_POST['idmarca'] ?? 0;
        $marca = $_POST['marca'] ?? '';

        if ($id <= 0 || trim($marca) === '') {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        $filasAfectadas = $this->marcaModel->update($id, $marca);

        if ($filasAfectadas > 0) {
            $marcaActualizada = $this->marcaModel->getById($id);
            echo json_encode([
                'success' => true,
                'message' => 'Marca actualizada correctamente',
                'data' => $marcaActualizada
            ]);
        } elseif ($filasAfectadas === -2) {
            echo json_encode(['success' => false, 'message' => 'Esta marca ya existe.', 'type' => 'WARNING']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la marca o no hubo cambios.']);
        }
    }

    /**
     * Elimina una marca
     *
     * Endpoint AJAX que procesa la eliminacion de una marca.
     * Valida que el ID sea correcto y maneja rectricciones de integridad
     * referencial cuando la marca tiene modelos asociados.
     * Responde en formato JSON con el resultado de la operacion.
     *
     * @return void
     */
    public function destroy()
    {
        header('Content-Type: application/json');
        $id = $_POST['idmarca'] ?? 0;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID no válido.']);
            return;
        }

        $filasAfectadas = $this->marcaModel->delete($id);

        if ($filasAfectadas > 0) {
            echo json_encode(['success' => true, 'message' => 'Marca eliminada correctamente']);
        } elseif ($filasAfectadas === -2) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar la marca porque tiene modelos asociados.', 'type' => 'ERROR']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la marca.']);
        }
    }

    /**
     * Obtiene todas las marcas para uso en select/combos
     *
     * Endpoint AJAX que retorna todas las marcas en formato JSON,
     * tipicamente utilizado para poblar elementos select en formularios.
     * Retorna codigo HTTP 404 si no hay marcas disponibles.
     *
     * @return never
     */
    public function getMarcasDB(): void
    {
        header('Content-Type: application/json');
        $marcas = $this->marcaModel->getAll();
        if ($marcas) {
            echo json_encode($marcas);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}