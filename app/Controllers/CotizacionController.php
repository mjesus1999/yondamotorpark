<?php
//app/controllers/CotizacionController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cotizacion;
use App\Models\Vehiculo;

class CotizacionController extends Controller
{
    private Cotizacion $cotizacionModel;
    private Vehiculo $vehiculoModel;

    public function __construct()
    {
        $this->cotizacionModel = new Cotizacion();
        $this->vehiculoModel = new Vehiculo();
    }

    public function index(): void
    {
        $this->authRequired();

        $this->view("cotizacion.index");
    }

    public function create(): void
    {
        $this->authRequired();
        $vehiculos = $this->vehiculoModel->getAll();
        $this->view('cotizacion.create', ['vehiculos' => $vehiculos]);
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