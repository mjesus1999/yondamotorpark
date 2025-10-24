<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Modelo;

class ModeloController extends Controller
{
    private Modelo $modeloModel;
    private string $uploadPath = __DIR__ . '/../../public/assets/images/vehiculos/';


    public function __construct()
    {
        $this->modeloModel = new Modelo();
    }

 
    public function getall(): void
    {
        header('Content-Type: application/json');
        $idmarca = $_GET['idmarca'] ?? 0;

        if ($idmarca <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de marca no válido']);
            return;
        }

        $data = $this->modeloModel->getByMarca($idmarca);
        echo json_encode(['success' => true, 'data' => $data]);
    }

   
    public function show(): void
    {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de modelo no válido']);
            return;
        }

        $modelo = $this->modeloModel->getById($id);

        if ($modelo) {
            echo json_encode(['success' => true, 'data' => $modelo]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Modelo no encontrado']);
        }
    }

   
    public function store()
    {
        header('Content-Type: application/json');

        $data = [
            'idmarca' => $_POST['idmarca'] ?? 0,
            'idtipovehiculo' => $_POST['idtipovehiculo'] ?? 0,
            'modelo' => $_POST['modelo'] ?? '',
            'anio' => $_POST['anio'] ?? '',
            'imagenreferencial' => null
        ];

       
        if ($data['idmarca'] <= 0 || $data['idtipovehiculo'] <= 0 || trim($data['modelo']) === '' || trim($data['anio']) === '') {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }
        
        
        $imagenNombre = $this->handleFileUpload($_FILES['imagen']);
        if ($imagenNombre) {
            $data['imagenreferencial'] = $imagenNombre;
        }

        $id = $this->modeloModel->create($data);

        if ($id > 0) {
            $nuevoConteo = $this->modeloModel->countByMarca($data['idmarca']);
            echo json_encode([
                'success' => true,
                'message' => 'Modelo registrado correctamente',
                'nuevoConteo' => $nuevoConteo 
            ]);
        } elseif ($id === -2) {
            echo json_encode(['success' => false, 'message' => 'Ya existe un modelo con ese nombre, marca y año.', 'type' => 'WARNING']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo registrar el modelo.']);
        }
    }

    public function update()
    {
         header('Content-Type: application/json');

        $data = [
            'idmodelo' => $_POST['idmodelo'] ?? 0,
            'idmarca' => $_POST['idmarca'] ?? 0,
            'idtipovehiculo' => $_POST['idtipovehiculo'] ?? 0,
            'modelo' => $_POST['modelo'] ?? '',
            'anio' => $_POST['anio'] ?? '',
            'imagenreferencial' => null 
        ];
        
  
        if ($data['idmodelo'] <= 0 || $data['idmarca'] <= 0 || $data['idtipovehiculo'] <= 0 || trim($data['modelo']) === '' || trim($data['anio']) === '') {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }
        
      
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
             
             $modeloAnterior = $this->modeloModel->getById($data['idmodelo']);
             if ($modeloAnterior && $modeloAnterior['imagenreferencial']) {
                 @unlink($this->uploadPath . $modeloAnterior['imagenreferencial']);
             }
             
             $imagenNombre = $this->handleFileUpload($_FILES['imagen']);
             if ($imagenNombre) {
                 $data['imagenreferencial'] = $imagenNombre;
             }
        }

        $filas = $this->modeloModel->update($data);

        if ($filas > 0) {
            $nuevoConteo = $this->modeloModel->countByMarca($data['idmarca']);
            echo json_encode([
                'success' => true,
                'message' => 'Modelo actualizado correctamente',
                'nuevoConteo' => $nuevoConteo
            ]);
        } elseif ($filas === -2) {
            echo json_encode(['success' => false, 'message' => 'Ya existe un modelo con ese nombre, marca y año.', 'type' => 'WARNING']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar o no hubo cambios.']);
        }
    }

  
    public function destroy()
    {
        header('Content-Type: application/json');
        $idmodelo = $_POST['idmodelo'] ?? 0;
        $idmarca = $_POST['idmarca'] ?? 0; 

        if ($idmodelo <= 0 || $idmarca <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID no válido.']);
            return;
        }

     
        $modelo = $this->modeloModel->getById($idmodelo);
        if ($modelo && $modelo['imagenreferencial']) {
            @unlink($this->uploadPath . $modelo['imagenreferencial']);
        }

        $filas = $this->modeloModel->delete($idmodelo);

        if ($filas > 0) {
            $nuevoConteo = $this->modeloModel->countByMarca($idmarca);
            echo json_encode([
                'success' => true, 
                'message' => 'Modelo eliminado',
                'nuevoConteo' => $nuevoConteo
            ]);
        } elseif ($filas === -2) {
            echo json_encode(['success' => false, 'message' => 'Este modelo no se puede eliminar, está siendo utilizado.', 'type' => 'ERROR']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el modelo.']);
        }
    }
    
   
    private function handleFileUpload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null; // No hay archivo o hubo un error
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombreUnico = uniqid('modelo_') . '.' . $extension;
        $destino = $this->uploadPath . $nombreUnico;

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $destino)) {
            return $nombreUnico;
        }

        return null;
    }
}