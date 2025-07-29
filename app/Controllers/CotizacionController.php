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

        $formatoModel = new FormatoCotizacion();
        $detalle      = $formatoModel->getDetalleRequisitos($idformato);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($detalle);
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