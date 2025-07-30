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
        $this->view("cotizacion.index");
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
        $tipo = $_GET['tipo'] ?? '';
        $doc = $_GET['doc'] ?? '';
        if (!$tipo || !$doc) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros']);
            exit;
        }

        $data = $this->cotizacionModel->getClienteByDoc($tipo, $doc);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data ?: ['notFound' => true]);
        exit;
    }

    public function store(): void
    {
        $this->authRequired();

        $input = [
            'idformato' => $_POST['modalidad'] ?? null,
            'idcliente' => $_POST['idcliente'] ?? null,
            'idvehiculo' => $_POST['vehiculoId'] ?? null,  // Debes poner este hidden en el form cuando selecciones el vehículo
            'moneda' => $_POST['moneda'] ?? 'PEN',
            'precioventa' => $_POST['valor'] ?? 0,
            'vigenciadias' => $_POST['vigenciadias'] ?? 7,
            'inicial' => $_POST['inicial'] ?? 0,
            'numcuotas' => $_POST['numcuotas'] ?? 0,
            'valorcuota' => $_POST['valorcuota'] ?? 0,
            'idasesor' => $_SESSION['user_id'] ?? 1
        ];

        // Validar mínimos...
        if (!$input['idcliente'] || !$input['idvehiculo'] || !$input['idformato']) {
            $_SESSION['error'] = "Faltan datos obligatorios.";
            return header('Location: /cotizaciones/crear');
        }

        $this->cotizacionModel->createCotizacion($input);

        $_SESSION['success'] = "Cotización registrada correctamente.";
        header('Location: /cotizaciones/listar');
    }

    public function buscarCliente1(): void
    {
        $this->authRequired();

        // parseeo básico
        $tipo = $_GET['tipo'] ?? '';
        $doc = $_GET['doc'] ?? '';

        if (!$tipo || !$doc) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros']);
            exit;
        }

        // según tipo, buscamos en personas o empresas
        if (in_array(strtoupper($tipo), ['DNI', 'CEX', 'PAS'], true)) {
            $data = $this->cotizacionModel->getPersonaByDoc($tipo, $doc);
        } else { // RUC
            $data = $this->cotizacionModel->getEmpresaByRuc($doc);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data ?: ['notFound' => true]);
        exit;
    }

    /* public function requisitos(): void
    {
        $this->authRequired();
        $requisitos = $this->cotizacionModel->getRequisitos();
        $this->view('cotizacion.requisitos', compact('requisitos'));
    } */


    /*     public function store(): void
        {
            $this->authRequired();

            $tipocot = trim($_POST['tipocotizacion'] ?? '');
            $fi = $_POST['fechainicio'] ?? '';
            $indefinido = isset($_POST['indefinido']);
            $ff = (!$indefinido && !empty($_POST['fechafin']))
                ? $_POST['fechafin']
                : null;

            // Insertar y obtener nuevo ID
            $newId = $this->formatoModel->create($tipocot, $fi, $ff);

            header('Location: /cotizacion');
            exit;
        } */


}