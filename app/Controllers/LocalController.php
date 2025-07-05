<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Models\Local;

class LocalController extends Controller
{
    private Local $localModel;

    public function __construct()
    {
        $this->localModel = new Local();
    }

    public function index(): void
    {
        $locales = $this->localModel->getAll();
        $this->view('locales.index', ['locales' => $locales]);
    }

    public function create(): void
    {
        $this->view('locales.create');
    }

    public function store(): int
    {
        // 1. Validar que la petición sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return 0;
        }
        $registro = [
            'tienda'      => trim($_POST['tienda']),
            'iddistrito'  => trim(intval($_POST['iddistrito'])),
            'idmotorpark' => trim(intval($_POST['idmotorpark'])),
            'principal'   => trim($_POST['principal']),
            'responsable' => trim($_POST['responsable']),
            'correo' =>   trim($_POST['correo']) !== '' && trim($_POST['correo']) !== 'null' ? trim($_POST['correo']) : null,
            'direccion' =>   trim($_POST['direccion']) !== '' && trim($_POST['direccion']) !== 'null' ? trim($_POST['direccion']) : null,
            'telefono' =>   trim($_POST['telefono']) !== '' && trim($_POST['telefono']) !== 'null' ? trim($_POST['telefono']) : null
            // 'latitud' =>   trim($_POST['latitud']) !== '' && trim($_POST['latitud']) !== 'null' ? trim($_POST['latitud']) : null,
            // 'longitud' =>   trim($_POST['longitud']) !== '' && trim($_POST['longitud']) !== 'null' ? trim($_POST['longitud']) : null,
        ];

        // 2. Validar campos obligatorios
        $errores = [];
        
        // Mapeo de nombres de campos para mensajes amigables
        $nombresCampos = [
            'tienda' => 'Nombre del Local',
            'iddistrito' => 'Distrito',
            'idmotorpark' => 'Tienda',
            'principal' => '¿Es principal?',
            'responsable' => 'Responsable'
        ];
        
        foreach (['tienda', 'iddistrito', 'idmotorpark', 'principal', 'responsable'] as $campo) {
            if (empty($registro[$campo])) {
                $nombreAmigable = $nombresCampos[$campo] ?? $campo;
                $errores[] = "El campo '$nombreAmigable' es obligatorio.";
            }
        }

        if (count($errores) > 0) {
            $this->view('locales.create', ['error' => implode('\n', $errores)]);
            return -1;
        }

        // 3. Insertar en base de datos
        $lastInsertId = $this->localModel->create($registro);

        if ($lastInsertId > 0) {
            // Esta es la otra manera de mostrar el toast, pero
            // redirigiendo a la página de locales, solo en locales.create con :
            // $success = '¡Local creado exitosamente!';
            // $this->view('locales.create', ['success' => $succcess]);)
            
           // $_SESSION['success_message'] = '¡Local creado exitosamente!';
            //$this->redirect('/locales');

            $success = '¡Local creado exitosamente!';
            $this->view('locales.create', ['success' => $success]);
            return $lastInsertId; // Retorna el ID insertado
            
        } else {
            // Si hubo un error en la inserción (retornó -1 o 0), muestra la vista con el error.
            $this->view('locales.create', ['error' => 'Error al crear el local.']);
            return $lastInsertId; // Retorna -1 (o el valor que `create` haya retornado en caso de fallo)
        }
    }



    public function edit(int $id): void
    {
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

    public function update(int $id): void
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $responsable = trim($_POST['responsable'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            $registro = [
                'responsable' => $responsable,
                'telefono'    => $telefono,
                'idlocal'     => $id
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


    public function delete(int $id): void
    {
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
}
