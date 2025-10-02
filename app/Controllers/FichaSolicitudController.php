<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FichaSolicitud;


class FichaSolicitudController extends Controller
{
    private FichaSolicitud $fichaSolicitudModel;

    public function __construct()
    {
        $this->fichaSolicitudModel = new FichaSolicitud();
    }

    public function index(){

        $this->view('cotizacion.ficha');
    }




}