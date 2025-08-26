<?php
// app/Controllers/ModeloController.php

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

    /**
     * GET /modelos/lista?marca={idmarca}&tipo={idtipovehiculo}
     * Devuelve JSON con la lista de modelos (idmodelo, modelo, anio).
     */
    public function getByMarcaYTipo(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $idmarca = isset($_GET['marca']) ? (int) $_GET['marca'] : 0;
        $idtipo  = isset($_GET['tipo'])  ? (int) $_GET['tipo']  : 0;

        if ($idmarca <= 0 || $idtipo <= 0) {
            echo json_encode(['success' => false, 'modelos' => []]);
            exit;
        }  

        $data = $this->modeloModel->getModelosByTipoMarca($idmarca, $idtipo);
        echo json_encode(['success' => true, 'modelos' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
