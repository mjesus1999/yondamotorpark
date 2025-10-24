<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TipoVehiculo;

class TipoVehiculoController extends Controller
{
    private TipoVehiculo $tipoModel;

    public function __construct()
    {
        $this->tipoModel = new TipoVehiculo();
    }

    public function getall(): void
    {
        header('Content-Type: application/json');
        $data = $this->tipoModel->getAll();
        
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontraron tipos de vehículo']);
        }
    }
}