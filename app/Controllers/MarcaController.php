<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Marca;

class MarcaController extends Controller
{
    private Marca $marcaModel;

    public function __construct()
    {
        $this->marcaModel = new Marca();
    }

    public function index(): void
    {
        $this->authRequired();
        $data = $this->marcaModel->getAll();
        $this->view('marcas.index', ['marcas' => $data]);
    }

  

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
     * Actualiza una marca existente.
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