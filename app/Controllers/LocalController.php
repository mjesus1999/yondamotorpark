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
            'iddistrito'  => trim($_POST['iddistrito']),
            'idmotorpark' => trim($_POST['idmotorpark']),
            'principal'   => trim($_POST['principal']),
            'responsable' => trim($_POST['responsable']),
            'correo'      => trim($_POST['correo'] ?? ''),    // opcional
            'direccion'   => trim($_POST['direccion'] ?? ''), // opcional
            'telefono'    => trim($_POST['telefono'] ?? ''),  // opcional
            'latitud'     => trim($_POST['latitud'] ?? ''),   // opcional
            'longitud'    => trim($_POST['longitud'] ?? '')   // opcional
        ];

        var_dump($registro);
        exit();

        // 2. Validar campos obligatorios
        $errores = [];
        foreach (['tienda', 'iddistrito', 'idmotorpark', 'principal', 'responsable'] as $campo) {
            if (empty($registro[$campo])) {
                $errores[] = "El campo '$campo' es obligatorio.";
            }
        }

        if (count($errores) > 0) {

            $this->view('locales.create', ['error' => implode('<br>', $errores)]);
            return -1;
        }

        // 3. Insertar en base de datos
        $lastInsertId = $this->localModel->create($registro);

        if ($lastInsertId > 0) {

            $this->redirect('/locales');
            return $lastInsertId; // Retorna el ID insertado
        } else {
            // Si hubo un error en la inserción (retornó -1 o 0), muestra la vista con el error.
            $this->view('locales.create', ['error' => 'Error al crear el local.']);
            return $lastInsertId; // Retorna -1 (o el valor que `create` haya retornado en caso de fallo)
        }
    }
}
