<?php

/**
 * Controlador de Conceptos de Pago
 * 
 * app/Controllers/ConceptoPagoController.php
 * 
 * Gestiona las operaciones relacionadas con los conceptos de pago del sistema.
 * Proporciona endpoints API para obtener los catálogos de conceptos que se
 * utilizan para clasificar las transacciones financieras en el sistema.
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ConceptoPago;
use App\Helpers\Validador;


/**
 * Clase ConceptoPagoController
 * 
 * Controlador para la gestión de conceptos de pago.
 * Proporciona acceso a los catálogos de conceptos utilizados en las
 * transacciones financieras del sistema mediante endpoints API REST.
 */
class ConceptoPagoController extends Controller
{

    /**
     * Modelo de ConceptoPago
     * @var ConceptoPago
     */
    private ConceptoPago $conceptoPagoModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa el modelo de ConceptoPago necesario para las operaciones
     * de consulta de conceptos de pago.
     */
    public function __construct()
    {
        $this->conceptoPagoModel = new ConceptoPago();
    }

    /**
     * API: Obtiene el catálogo completo de conceptos de pago
     * 
     * Endpoint AJAX que retorna todos los conceptos de pago disponibles en el
     * sistema en formato JSON. Requiere autenticación para acceder.
     * 
     * @return void
     */
    public function getConceptosPago(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        $conceptos = $this->conceptoPagoModel->getConceptos();


        if ($conceptos) {
            echo json_encode($conceptos);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se ha podido traer los conceptos de pagos']);
        }
    }

    
    public function store()
    {

        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'concepto' => empty($data['concepto']) ? null : $data['concepto'],
            'montosugerido' => empty($data['montosugerido']) ? null : $data['montosugerido'],
            'descripcion' => $data['descripcion'] ? null : $data['descripcion'],

        ];

        try {

            $idConcepto = $this->conceptoPagoModel->add($registro);

            if ($idConcepto > 0) {

                echo json_encode([
                    'success' => true,
                    'message' => 'Concepto registrado correctamente',
                    'id' => $idConcepto
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al registrar el concepto',
                    'id' => $idConcepto
                ]);
            }
        } catch (\Exception $e) {

            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar el concepto ' . $e->getMessage(),
                'id' => 0
            ]);
        }
    }
}
