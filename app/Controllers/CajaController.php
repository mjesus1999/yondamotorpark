<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Caja;


class CajaController extends Controller
{
    private Caja $cajaModel;
   

    public function __construct()
    {
        $this->cajaModel = new Caja();
       
    }

    // Me enlistara todos los contratos
    public function index(): void
    {
        $datos = $this->cajaModel->getAllContratosDatos();
       
        $this->view('caja.index', ['contratos' => $datos]);
    }

    public function cronogramaByContrato():void {
        $this->view('caja.cronograma');
    }


}