<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Egreso;



class EgresoController extends Controller
{
    private Egreso $egresoModel;


    public function __construct()
    {
        $this->egresoModel = new Egreso();
    }

    /**
     * Renderiza la vista principal de egresos.
     * @return void Renderiza la vista principal de egresos.
     */
    public function index(string $estado = 'N'): void
    {
        $this->authRequired();
        $egresos = $this->egresoModel->getAllEgresosByEstado($estado);

        $this->view('egresos.index', ['egresos' => $egresos, 'estado' => $estado]);
    }

    public function indexAjuntarComprobante(): void
    {
        $this->view('egresos.adjuntarComprobante');
    }


    public function create(): void
    {
        $this->authRequired();
        $this->view('egresos.create');
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');
        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idconceptoegreso' => $data['idconceptoegreso'] ?? null,
            'idsolicitante' => empty($data['idsolicitante']) ?  null : (int)$data['idsolicitante'],
            'monto' => empty($data['monto']) ? null : (float)$data['monto'],
            'comentario' => empty($data['comentario']) ? null : $data['comentario'],
            'requierecomprobante' => empty($data['requierecomprobante']) ? null : $data['requierecomprobante']
        ];

        $errores = [];


        $errores[] = Validador::campoObligatorio($registro['idconceptoegreso'], 'Concepto de Egreso');
        $errores[] = Validador::campoObligatorio($registro['idsolicitante'], 'Solicitante');
        $errores[] = Validador::campoObligatorio($registro['monto'], 'Monto');

        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        $newId = $this->egresoModel->add($registro);


        if ($newId > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Egreso registrado con éxito.',
                'id' => $newId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar el egreso. Intente nuevamente.',
                'id' => 0
            ]);
        }
    }


    public function storeComprobante(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');
        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idegreso' => $data['idegreso'] ?? null,
            'idproovedor' => empty($data['idproovedor']) ?  null : (int)$data['idproovedor'],
            'tipodocumento' => $data['tipodocumento'] ?? null,
            'serie' => $data['serie'] ?? null,
            'numdocumento' => $data['numdocumento'] ?? null,
            'monto' => empty($data['monto']) ? null : (float)$data['monto'],
            'rutacomprobante' => $data['rutacomprobante'] ?? null
            
        ];

        $errores = [];


        $errores[] = Validador::campoObligatorio($registro['idegreso'], 'Egreso');
        $errores[] = Validador::campoObligatorio($registro['idproovedor'], 'Proovedor');
        $errores[] = Validador::campoObligatorio($registro['tipodocumento'], 'Tipo de Documento');
        $errores[] = Validador::campoObligatorio($registro['serie'], 'Serie');
        $errores[] = Validador::campoObligatorio($registro['numdocumento'], 'Número de Documento');
        $errores[] = Validador::campoObligatorio($registro['monto'], 'Monto');
        $errores[] = Validador::campoObligatorio($registro['rutacomprobante'], 'Comprobante');

        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        $newId = $this->egresoModel->add($registro);


        if ($newId > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Egreso registrado con éxito.',
                'id' => $newId
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar el egreso. Intente nuevamente.',
                'id' => 0
            ]);
        }
    }















    public function getConceptosEgreso(): void
    {
        header('Content-Type: application/json');
        $this->authRequired();
        $data = $this->egresoModel->getConceptosEgreso();

        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron conceptos de egreso.']);
        }
    }

    public function getColaboradores(): void
    {
        header('Content-Type: application/json');
        $this->authRequired();
        $data = $this->egresoModel->getColaboradores();

        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron colaboradores.']);
        }
    }
}
