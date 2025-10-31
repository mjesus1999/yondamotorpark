<?php

/**
 * Controlador de Detalle de Orden de Compra
 * 
 * app/Controllers/DetalleOCController.php
 * 
 * Gestiona las peticiones HTTP relacionadas con los ítems (vehículos)
 * de las órdenes de compra. Proporciona endpoints API para agregar
 * vehículos a órdenes de compra existentes, validando datos y
 * estableciendo precios individuales de compra. Forma parte del
 * proceso de registro de órdenes multi-ítem donde cada vehículo
 * puede tener condiciones de precio específicas según negociación
 * con el concesionario.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\DetalleOC;

/**
 * Clase DetalleOCController
 * 
 * Controlador para la gestión de detalles de órdenes de compra.
 * Hereda de Controller para acceder a funcionalidades base como
 * validación de autenticación y manejo de sesiones. Implementa
 * endpoints REST para operaciones de creación de ítems en órdenes
 * de compra, sanitizando datos y validando coherencia antes de
 * persistir en base de datos.
 */
class DetalleOCController extends Controller
{

    /**
     * Instancia del modelo DetalleOC
     * @var DetalleOC
     */
    private DetalleOC $detalleOCModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa la instancia del modelo DetalleOC para
     * realizar operaciones de registro de ítems en órdenes de compra
     */
    public function __construct()
    {
        $this->detalleOCModel = new DetalleOC();
    }

    /**
     * Endpoint API: Registrar ítem en orden de compra
     * 
     * Agrega un vehículo específico a una orden de compra existente
     * con su precio unitario negociado. Este endpoint se invoca
     * múltiples veces cuando se registra una orden con varios vehículos,
     * creando un ítem por cada llamada.
     * 
     * @return int ID del detalle creado (retorna pero también envía JSON).
     *             Nota: El retorno int parece ser para uso interno,
     *             ya que el método termina con exit después de enviar JSON
     */
    public function store(): int
    {

        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idordencompra' => $data['idordencompra'],
            'idvehiculo' => $data['idvehiculo'],
            'preciocompra' => $data['preciocompra']
        ];

        $idDetalleOC = $this->detalleOCModel->create($registro);

        if ($idDetalleOC > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Se ha creado la orden de compra con sus detalles!',
                'id' => $idDetalleOC
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo crear la orden de compra',
                'id' => $idDetalleOC
            ]);
            exit;
        }

    }

}