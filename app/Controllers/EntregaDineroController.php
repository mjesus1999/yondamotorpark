<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\EntregaDinero;
use Exception;

class EntregaDineroController extends Controller
{
    private EntregaDinero $entregaDineroModel;

    public function __construct()
    {
        $this->entregaDineroModel = new EntregaDinero();
    }


    public function index(): void
    {
        try {
            $this->authRequired();

            $entregas = $this->entregaDineroModel->listarResumen();


            $this->view('arqueo-caja.entregas', ['entregas' => $entregas]);
        } catch (Exception $e) {

            // error_log($e->getMessage());
            $this->view('errors.404', ['message' => 'Error al cargar el reporte de entregas.']);
        }
    }

    public function detalle(int $identrega): void
    {

        $this->authRequired();
        header('Content-Type: application/json');

        try {

            $arqueosDetalle = $this->entregaDineroModel->obtenerDetalleArqueos($identrega);
            $destinosDetalle = $this->entregaDineroModel->obtenerDetalleDestinos($identrega);

            $arqueos = array_map(function ($arqueo) {
               
                $arqueo['ingresos_efectivo'] = (float) $arqueo['ingresos_efectivo'];
                $arqueo['ingresos_digital'] = (float) $arqueo['ingresos_digital'];
                $arqueo['egresos_dia'] = (float) $arqueo['egresos_dia'];
                $arqueo['total'] = (float) $arqueo['total'];               
                return $arqueo;
            }, $arqueosDetalle);

            
            $destinos = array_map(function ($destino) {
                $destino['monto'] = (float) $destino['monto'];
                return $destino;
            }, $destinosDetalle);

            echo json_encode([
                'success' => true,
                'data' => [
                    'arqueos' => $arqueos, 
                    'destinos' => $destinos 
                ]
            ]);
        } catch (Exception $e) {

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener los detalles de la entrega.'
            ]);
            error_log($e->getMessage());
        }
    }
}
