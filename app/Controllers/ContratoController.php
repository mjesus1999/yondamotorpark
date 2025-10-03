<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contrato;
use Exception;

class ContratoController extends Controller
{
    private Contrato $contratoModel;

    public function __construct()
    {
        $this->contratoModel = new Contrato();
    }

    public function store()
    {
        header("Content-Type: application/json");

        try {
            
            if (empty($_POST['idcotizacion'])) {
                echo json_encode(["success" => false, "message" => "Falta ID de cotización"]);
                return;
            }

            $idcotizacion = (int) $_POST['idcotizacion'];

            // Obtener datos de la cotización
            $dataCotizacion = $this->contratoModel->getCotizacionDetails($idcotizacion);

            if (!$dataCotizacion) {
                echo json_encode(["success" => false, "message" => "Cotización no encontrada"]);
                return;
            }

           
            $contractData = [
                'idlocal'       => $_POST['idlocal'] ?? null,
                'idcotizacion'  => $idcotizacion,
                'fechainicio'   => $_POST['fechainicio'] ?? date("Y-m-d"),
                'diapago'       => $_POST['diapago'] ?? date("d"),
                'fecharevision' => empty($_POST['fecharevision']) ? null : $_POST['fecharevision'],
                'observaciones' => empty($_POST['observaciones'] ? null:$_POST['observaciones'])
            ];

            // Crear contrato + cronograma + actualizar estado
            $idcontrato = $this->contratoModel->createContratoYCronograma($contractData, $dataCotizacion);

            if ($idcontrato > 0) {
                echo json_encode([
                    "success" => true,
                    "idcontrato" => $idcontrato,
                    "message" => "Contrato creado correctamente"
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo crear el contrato. Intente nuevamente."
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error en el controlador: " . $e->getMessage()
            ]);
        }
    }
}
