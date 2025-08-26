<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Models\Marca;
use App\Models\TipoVehiculo;

class TipoVehiculoController extends Controller
{
    private TipoVehiculo $tipoVehiculoModel;

    public function __construct()
    {
        $this->tipoVehiculoModel = new TipoVehiculo();
    }


    // public function getTipoVehiculoByMarca(): void
    // {
    //     header('Content-Type: application/json; charset=utf-8');
    //     $idmarca = (int) ($_GET['idmarca'] ?? 0);
    //     if ($idmarca <= 0) {
    //         echo json_encode(['success' => false, 'tipos' => []]);
    //         exit;
    //     }
    //     $tipos = $this->tipoVehiculoModel->getTipoVehiculoByMarca($idmarca);
    //     echo json_encode(['success' => true, 'tipos' => $tipos], JSON_UNESCAPED_UNICODE);
    //     exit;
    // }
    



    // API PARA TRAER LOS TIPOS DE VECHICULOS SEGÚN LA MARCA:

    public function getTipoVehiculoByMarca($id): void
    {

        header('Content-Type: application/json');

        $tipoVehiculos = $this->tipoVehiculoModel->getTipoVehiculoByMarca($id);

        if ($tipoVehiculos) {
            echo json_encode($tipoVehiculos);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}
