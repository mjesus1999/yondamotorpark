<?php
//app/controllers/CotizacionController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cotizacion;
use App\Models\Vehiculo;
use App\Models\FormatoCotizacion;

class CotizacionController extends Controller
{
    private Cotizacion $cotizacionModel;
    private Vehiculo $vehiculoModel;
    private FormatoCotizacion $formatoModel;

    public function __construct()
    {
        $this->cotizacionModel = new Cotizacion();
        $this->vehiculoModel = new Vehiculo();
        $this->formatoModel = new FormatoCotizacion();
    }

    public function index(): void
    {
        $this->authRequired();

        $cotizaciones = $this->cotizacionModel->getAll();

        $this->view("cotizacion.index", [
            'cotizaciones' => $cotizaciones
        ]);
    }


    public function create(): void
    {
        $this->authRequired();
        $formatos = $this->formatoModel->getAll();
        $vehiculos = $this->vehiculoModel->getAll(['Libre', 'Proceso']);
        $this->view('cotizacion.create', [
            'vehiculos' => $vehiculos,
            'formatos' => $formatos
        ]);
    }

    public function requisitos(int $idformato): void
    {
        $this->authRequired();

        $detalle = $this->formatoModel->getDetalleRequisitos($idformato);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($detalle);
        exit;
    }

    public function buscarCliente(): void
    {
        $this->authRequired();

        $tipo = strtolower($_GET['tipo'] ?? '');
        $doc = trim($_GET['doc'] ?? '');

        if (!$tipo || !$doc) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros']);
            exit;
        }

        // Llamamos al único método que trae idcliente + datos
        $data = $this->cotizacionModel->getClienteByDoc($tipo, $doc);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data ?: ['notFound' => true]);
        exit;
    }

    public function store(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        $idasesor = $_SESSION['user']['id'] ?? null;
        if (!$idasesor) {
            $_SESSION['error'] = "No se encontro al usuario.";
            header('Location: /cotizacion/create');
            exit;
        }

        $input = [
            'idformato' => $_POST['modalidad'] ?? null,
            'idcliente' => $_POST['idcliente'] ?? null,
            'idvehiculo' => $_POST['idvehiculo'] ?? null,
            'moneda' => $_POST['moneda'] ?? 'PEN',
            'precioventa' => $_POST['precioventa'] ?? 0,
            'vigenciadias' => $_POST['vigenciadias'] ?? 7,
            'inicial' => $_POST['inicial'] ?? 0,
            'numcuotas' => $_POST['numcuotas'] ?? 0,
            'valorcuota' => $_POST['valorcuota'] ?? 0,
            'idasesor' => $idasesor,
        ];

        // Validar datos obligatorios
        if (!$input['idcliente'] || !$input['idvehiculo'] || !$input['idformato']) {
            $_SESSION['error'] = "Faltan datos obligatorios.";
            header('Location: /cotizacion/create');
            exit;
        }

        // Inserta la cotización
        try {
            $this->cotizacionModel->create($input);
            $_SESSION['success_message'] = "Cotización registrada correctamente.";
            header('Location: /cotizacion');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Error al registrar cotización: " . $e->getMessage();
            header('Location: /cotizacion/create');
            exit;
        }
    }

}