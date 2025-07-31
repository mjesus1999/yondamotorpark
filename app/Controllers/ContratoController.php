<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Contrato;


class ContratoController extends Controller
{
    private Contrato $contratoModel;
   

    public function __construct()
    {
        $this->contratoModel = new Contrato();
       
    }

    // Me enlistara todos los contratos
    public function index(): void
    {
       
        $this->view('contratos.index');
    }

    public function cronogramaByContrato():void {
        $this->view('contratos.cronograma');
    }


}