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

    public function index(): void
    {
        $this->authRequired();
        $this->view('egresos.index');
    }


    public function create(): void
    {
        $this->authRequired();
        $this->view('egresos.create');
    }






}
