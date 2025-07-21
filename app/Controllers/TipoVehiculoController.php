<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\TipoVehiculo;

class TipovehiculoController extends Controller
{
    private TipoVehiculo $vehiculoModel;

    public function __construct()
    {
        $this->vehiculoModel = new TipoVehiculo();
    }
    public function getTipoVehiculoByMarca(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $idmarca = (int) ($_GET['idmarca'] ?? 0);
        if ($idmarca <= 0) {
            echo json_encode(['success' => false, 'tipos' => []]);
            exit;
        }
        $tipos = $this->vehiculoModel->getTipoVehiculoByMarca($idmarca);
        echo json_encode(['success' => true, 'tipos' => $tipos], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
