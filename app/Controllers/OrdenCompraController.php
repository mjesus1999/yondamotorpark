<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\OrdenCompra;

class OrdenCompraController extends Controller
{
    private OrdenCompra $ordenCompraModel;

    public function __construct()
    {
        $this->ordenCompraModel = new OrdenCompra();
    }

    // Me enlistara todas las ordenes de compras: 
    public function index():void {
        $ordenCompras = $this->ordenCompraModel->getAll();
        $this->view('oc.index', ['ordenCompras' => $ordenCompras]);
    }
    // Me llevará a la voista de crear 

    public function create():void {
        $this->view('oc.create');
    }

}


