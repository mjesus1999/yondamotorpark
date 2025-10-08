<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\ConceptoPago;


class ConceptoPagoController extends Controller
{
    private ConceptoPago $conceptoPagoModel;


    public function __construct()
    {
        $this->conceptoPagoModel = new ConceptoPago();
    }



    public function getConceptosPago(): void
    {
        header('Content-Type: application/json');

        $conceptos = $this->conceptoPagoModel->getConceptos();


        if ($conceptos) {
            echo json_encode($conceptos);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se ha podido traer los conceptos de pagos']);
        }
    }
}
