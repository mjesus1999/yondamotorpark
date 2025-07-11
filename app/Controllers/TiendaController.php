<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Tienda;
use Dotenv\Parser\Value;

class TiendaController extends Controller
{

    private  Tienda $tiendaModel;

    public function __construct()
    {
        $this->tiendaModel = new Tienda();
    }


    public function store(): int
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

         header('Content-Type: application/json');

         $data = array_map([Validador::class, 'limpiar'], $_POST);
        $registro = [

            'iddistrito' => $data['iddistrito'] ?? '',
            'idconcesionario' => $data['idconcesionario'] ?? '',
            'direccion' => $data['direccion'] ?? '',
            'email' => $data['email'] ?? '',
            'telefono' => $data['telefono'] ?? '',
            'contacto' => $data['contacto'] ?? ''
        ];
        $errores = [];

        $errores[] = Validador::campoObligatorio($registro['iddistrito'], 'Distrito');
        $errores[] = Validador::campoObligatorio($registro['direccion'], 'Dirección');
        $errores[] = Validador::campoObligatorio($registro['email'], 'Correo');
        $errores[] = Validador::campoObligatorio($registro['telefono'], 'Teléfono');
        $errores[] = Validador::campoObligatorio($registro['contacto'], 'Contacto');

        $errores = array_filter($errores);

        
        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode("<br>", $errores),
                'id' => 0
            ]);
            exit;
        }

        $idTienda = $this->tiendaModel->create($registro);

        if ($idTienda > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Tienda creada exitosamente!',
                'id' => $idTienda
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo crear la tienda',
                'id' => 0
            ]);
            exit;
        }
        



    }


    // METODO PARA APIS

    // BUSCAR LAS TIENDAS DE UN CONCESIONARIO POR ID.
    public function searchTiendaByConcesionario($id): void
    {

        header('Content-Type: application/json');

        $tiendasConcesionario = $this->tiendaModel->getTiendasByIdConcesionario($id);
        if ($tiendasConcesionario) {
            echo json_encode($tiendasConcesionario);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }

        exit();
    }
}
