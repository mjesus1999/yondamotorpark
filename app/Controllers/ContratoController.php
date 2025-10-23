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

    public function index()
    {
        $this->authRequired();
        $this->view("contratos.index");
    }

    public function store()
    {
        $this->authRequired();
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
                'observaciones' => empty($_POST['observaciones']) ? null : $_POST['observaciones']

            ];

            // Crear contrato + cronograma + actualizar estado
            $idcontrato = $this->contratoModel->createContratoYCronograma($contractData, $dataCotizacion);

            if ($idcontrato > 0) {
                $this->contratoModel->updateVehiculoEstadoVendido($idcotizacion);
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

    public function disabledContrato()
    {
        $this->authRequired();
        header("Content-Type: application/json");
        $idcontrato = $_POST["idcontrato"] ?? null;

        if (!empty($idcontrato)) {
            $seElimino = $this->contratoModel->disabledContrato($idcontrato);
            if ($seElimino > 0) {
                echo json_encode(['success' => true, 'message' => 'Se eliminó el contrato']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se ha podido elimnar el contrato']);
            }
        } else {
            echo json_encode(['message' => 'No se ha pasado ningún id']);
        }
    }


    public function apiGetContratos()
    {
        $this->authRequired();
        header("Content-Type: application/json");

        $contratos = $this->contratoModel->getAll();

        if ($contratos) {
            echo json_encode(['success' => true, 'data' => $contratos]);
        } else {
            echo json_encode([]);
        }
    }

    public function apiGetPDFContrato(int $idcontrato)
    {
        // $this->authRequired();
        $data = $this->contratoModel->getDataPDFContrato($idcontrato);

        if ($data === false) {
            echo json_encode([
                'success' => false,
                'message' => 'No se ha podido establecer la conexión'
            ]);
            return;
        }

        if (empty($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'No se han encontrado datos para el contrato solicitado'
            ]);
            return;
        }

   
        $row = $data[0];

        $response = [
            'success' => true,
            'data' => [
                'idcontrato' => $row['idcontrato'],
                'codigo_contrato' => $row['codigo_contrato'] ?? ('N° ' . str_pad($row['idcontrato'], 5, '0', STR_PAD_LEFT)),
                'sede' => $row['sede'],

                'cliente' => [
                    'nombre' => $row['cliente'],
                    'documento' => $row['documentoCliente'],
                    'direccion' => $row['direccionCliente'],
                    'distrito' => $row['distritoCliente'],
                    'provincia' => $row['provinciaCliente'],
                    'departamento' => $row['departamentoCliente'],
                    'email' => $row['emailCliente'],
                    'telefono' => $row['telCliente'],
                    'tipocliente' => $row['tipocliente']
                ],

                'conyuge' => [
                    'nombre' => $row['conyuge'],
                    'documento' => $row['documentoConyuge'],
                    'direccion' => $row['direccionConyuge'],
                    'distrito' => $row['distritoConyuge'],
                    'provincia' => $row['provinciaConyuge'],
                    'departamento' => $row['departamentoConyuge'],
                    'email' => $row['emailConyuge'],
                    'telefono' => $row['telConyuge']
                ],

                'aval' => [
                    'nombre' => $row['aval'],
                    'documento' => $row['documentoAval'],
                    'direccion' => $row['direccionAval'],
                    'distrito' => $row['distritoAval'],
                    'provincia' => $row['provinciaAval'],
                    'departamento' => $row['departamentoAval'],
                    'email' => $row['emailAval'],
                    'telefono' => $row['telAval'],
                    'conyuge' => [
                        'nombre' => $row['avalConyuge'],
                        'documento' => $row['documentoAvalConyuge'],
                        'direccion' => $row['direccionAvalConyuge'],
                        'distrito' => $row['distritoAvalConyuge'],
                        'provincia' => $row['provinciaAvalConyuge'],
                        'departamento' => $row['departamentoAvalConyuge'],
                        'email' => $row['emailAvalConyuge'],
                        'telefono' => $row['telAvalConyuge']
                    ]
                ],
              

                'vehiculo' => [
                    'marca' => $row['marca'],
                    'modelo' => $row['modelo'],
                    'anio' => $row['anio'],
                    'color' => $row['color'],
                    'placa' => $row['placa'],
                    'serie_motor' => $row['seriemotor'],
                    'chasis' => $row['chasis']
                ],

                'financiamiento' => [
                    'montoFinanciado' => $row['financiado'],
                    'moneda' => $row['moneda'],
                    'diaPago' => $row['diapago'],
                    'totalPagar' => $row['totalPagar'],
                    'penalidadBase' => $row['penalidadbase'],
                    'cuotaInicial' => $row['cuotainicial'],
                    'numCuotas' => $row['numcuotas'],
                    'valorCuota' => $row['valorcuota'],
                    'tasaAnual' => $row['tasaanual'],
                    'tasaMensual'=> $row['tasamensual'],
               
                ]
            ]
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
