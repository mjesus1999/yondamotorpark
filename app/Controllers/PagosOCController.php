<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagosOC;

class OrdenCompraController extends Controller
{
    private PagosOC $pagoOCModel;

    public function __construct()
    {
        $this->pagoOCModel = new PagosOC();
    }

    public function create($idOC):void {
        $this->view('oc.pagos',$idOC);
    }

    


}