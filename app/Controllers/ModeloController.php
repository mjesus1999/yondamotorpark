<?php

/**
 * Controlador de Modelo (vehiculo)
 * 
 * app/Controllers/ModeloController.php
 * 
 * Gestiona todas las operaciones CRUD relacionadas con los modelos de vehiculos,
 * incluyendo carga y gestion de imagen referencial, validaciones de datos y actualizacion
 * por marcas.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Modelo;

/**
 * Clase ModeloController
 * 
 * Controlador para la gestion de modelos de vehiculos.
 * Proporciona EndPoints AJAX para operaciones CRUD, manejo de imagenes y validacion de 
 * restricciones de integridad.
 * 
 */
class ModeloController extends Controller
{
    /**
     * Modelo de Modelo (del vehiculo)
     * @var Modelo
     */
    private Modelo $modeloModel;

    /**
     * Ruta del directorio de carga de imagenes
     * 
     * @var string
     */
    private string $uploadPath = __DIR__ . '/../../public/assets/images/vehiculos/';


    /**
     * Contructor del controlador
     * 
     * Inicializa el modelo de Modelo necesario para las operaciones del controlador
     */
    public function __construct()
    {
        $this->modeloModel = new Modelo();
    }

    /**
     * Obtiene todos los modelos de una marca especifica 
     * 
     * EndPoint AJAX que retorna todos los modelos asociados a una marca en formato JSON.
     * Valida que el ID de marca sea valido.
     * 
     * @return void
     */
    public function getall(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $idmarca = $_GET['idmarca'] ?? 0;

        if ($idmarca <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de marca no válido']);
            return;
        }

        $data = $this->modeloModel->getByMarca($idmarca);
        echo json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * Obtiene modelos filtrados por marca y tipo de vehículo
     * 
     * Endpoint AJAX que retorna los modelos que coinciden con una marca
     * y tipo de vehículo específicos. Utilizado en selecciones en cascada
     * para filtrar modelos disponibles según marca y tipo seleccionados.
     * 
     * @param int $idmarca ID de la marca
     * @param int $idtipovehiculo ID del tipo de vehículo
     * @return void Respuesta JSON con array de modelos filtrados
     * 
     * @uses Modelo::getByMarcaYTipo() Para obtener modelos filtrados
     * 
     * @api
     * @httpmethod GET
     * @response 200 JSON con array de modelos
     * @response 200 JSON vacío si no hay modelos
     * 
     * @example GET /api/getModeloByTipoMarca/2/3
     */
    /**
     * Lista modelos por marca y tipo (query string: idmarca, idtipovehiculo).
     */
    public function getByMarcaYTipo(): void
    {
        $this->authRequired();
        $idmarca = (int) ($_GET['idmarca'] ?? $_GET['marca'] ?? 0);
        $idtipovehiculo = (int) ($_GET['idtipovehiculo'] ?? $_GET['tipo'] ?? 0);
        if ($idmarca <= 0 || $idtipovehiculo <= 0) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }
        $this->getModeloByTipoMarca($idmarca, $idtipovehiculo);
    }

    public function getModeloByTipoMarca(int $idmarca, int $idtipovehiculo): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        
        $modelos = $this->modeloModel->getByMarcaYTipo($idmarca, $idtipovehiculo);
        
        if ($modelos) {
            echo json_encode($modelos);
        } else {
            echo json_encode([]);
        }
    }

    /**
     * Obtiene un modelo especifico por ID
     * 
     * Endpoint AJAX que retorna los datos de un modelo en formato JSON.
     * Valida el ID sea valido antes de realizar la busqueda.
     * @return void
     */
    public function show(): void
    {
        $this->authRequired();
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

    /**
     * Registro un nuevo modelo de vehiculo
     * 
     * EndPoint AJAX que procesa el formulario de registro de modelo.
     * Valida campos obligatorios, procesa la imagen referencial mediante upload de archivos y maneja duplicados.
     * Retorna el nuevo conteo de modelos de la marca.
     * 
     * Validaciones: 
     *  - ID de marca y tipo de vehiculo deben ser mayores a 0
     *  - Nombre del modelo y año no pueden estar vacios
     *  - Manejo de imagen opcional
     * @return void
     */
    public function store()
    {
        $this->authRequired();
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

    /**
     * Actualiza un modelo existente 
     * 
     * Endpoint AJAX que procesa la actualizacion de un modelo.
     * Si se proporciona una nueva imagen, elimina la anterior y guarda la nueva.
     * Valida datos completos y maneja duplicados. retorna el nuevo conteo de modelos para la marca.
     * 
     * Comportamiento de la imagen:
     *  - Si no se proporciona nueva imagen, mantiene la actual.
     *  - Si se proporciona nueva imagen, eliminar la anterior
     * 
     * @return void
     */
    public function update()
    {
        $this->authRequired();
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

    /**
     * Elimina un modelo
     * 
     * EndPoint AJAX que procesa la eliminacion de un modelo.
     * Elimina fisicamente el archivo de imagen asociada y el registro de la base de datos.
     * Maneja restricciones de integridad si el modelo esta siendo utilizado por varios vehiculos.
     * Retorna el nuevo conteo de modelos
     * @return void
     */
    public function destroy()
    {
        $this->authRequired();
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

    /**
     * Maneja la carga de archivos de imagen
     * 
     * Procesa la subida de una imagen, generando un nombre unico y moviendole el directorio de destino.
     * Crea el directorio si no exite.
     * 
     * @param array $file Array del archivo $_FILE con informacion de Upload
     * @return string|null Nombre unico del archivo guardado o null si fallo
     */
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