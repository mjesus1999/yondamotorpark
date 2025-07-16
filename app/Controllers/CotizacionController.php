<?php
//app/controllers/CotizacionController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cotizacion;
use App\Models\FormatoCotizacion;

class CotizacionController extends Controller
{
    private Cotizacion $cotizacionModel;
    private FormatoCotizacion $formatoModel;

    public function __construct()
    {
        $this->cotizacionModel = new Cotizacion();
        $this->formatoModel = new FormatoCotizacion();
    }

    public function index(): void
    {
        $this->authRequired();
        $formatos = $this->formatoModel->getAll();
        $requisitos = $this->cotizacionModel->getRequisitos();

        $this->view("cotizacion.index", compact('formatos', 'requisitos'));
    }

    public function create(): void
    {
        $this->authRequired();
        $this->view('cotizacion.create');
    }
    

/*     public function requisitos(): void
    {
        $this->authRequired();
        $requisitos = $this->cotizacionModel->getRequisitos();
        $this->view('cotizacion.requisitos', compact('requisitos'));
    } */


    public function store(): void
    {
        $this->authRequired();

        $tipocot = trim($_POST['tipocotizacion'] ?? '');
        $fi = $_POST['fechainicio'] ?? '';
        $indefinido = isset($_POST['indefinido']);

        // Si no es indefinido y hay fecha, la tomamos; si no, null
        $ff = (!$indefinido && !empty($_POST['fechafin']))
            ? $_POST['fechafin']
            : null;

        // Insertar y obtener nuevo ID
        $newId = $this->formatoModel->create($tipocot, $fi, $ff);

        // Redirigir de vuelta al listado
        header('Location: /cotizacion');
        exit;
    }

    

}