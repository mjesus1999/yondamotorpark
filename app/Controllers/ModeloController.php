<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Models\Modelo;

class ModeloController extends Controller
{
    private Modelo $modeloModel;

    public function __construct()
    {
        $this->modeloModel = new Modelo();
    }





    // API PARA OBTENER LOS MODELOS ÓR TIPO Y MARCA

    public function getModeloByTipoMarca(int $idmarca, int $idtipovehiculo): void
    {
        header('Content-Type: application/json');

        $modelosByTipoMarca = $this->modeloModel->getModelosByTipoMarca($idmarca, $idtipovehiculo);

        if ($modelosByTipoMarca) {
            echo json_encode($modelosByTipoMarca);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}
