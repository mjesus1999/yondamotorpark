<?php
//app/controllers/CobranzaController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cobranza;

class CobranzaController extends Controller
{
    private Cobranza $cobranzaModel;

    public function __construct()
    {
        $this->cobranzaModel = new Cobranza();
    }

    public function index(): void
    {
        $this->authRequired();
        $this->view('cobranza.index');
    }

    public function indexNotificar()
    {
        $this->authRequired();
        $this->view('cobranza.indexNotificar');
    }

    public function indexVencidos()
    {
        $this->authRequired();
        $this->view('cobranza.indexVencidos');
    }

    public function reporteCobranzaAtrasado()
    {
        $this->authRequired();
        $this->view('cobranza.reporteCobranzaAtrasado');
    }
    

    public function reportesAtrasado()
    {
        $this->authRequired();
        $this->view('cobranza.reporteCobranzaPDF');
    }

}