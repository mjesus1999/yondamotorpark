<?php

/**
 * Controlador de Entrega de Dinero
 * 
 * app/Controllers/EntregaDineroController.php
 * 
 * Gestiona las peticiones HTTP relacionadas con las entregas de dinero
 * del sistema. Proporciona interfaces web para visualizar el resumen
 * ejecutivo de entregas y endpoints API para consultar detalles completos
 * (arqueos cubiertos y destinos) de cada entrega. Facilita auditoría
 * y control del flujo de efectivo desde arqueos de caja hacia gerentes
 * y cuentas bancarias.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\EntregaDinero;
use Exception;

/**
 * Clase EntregaDineroController
 * 
 * Controlador para la gestión de entregas de dinero.
 * Hereda de Controller para acceder a funcionalidades base como
 * renderizado de vistas, validación de autenticación y manejo de sesiones.
 * Implementa el patrón MVC para separar la lógica de presentación del
 * control financiero. Todos los métodos requieren autenticación previa.
 */
class EntregaDineroController extends Controller
{

    /**
     * Instancia del modelo EntregaDinero
     * @var EntregaDinero
     */
    private EntregaDinero $entregaDineroModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa la instancia del modelo EntregaDinero para
     * realizar operaciones de consulta financiera
     */
    public function __construct()
    {
        $this->entregaDineroModel = new EntregaDinero();
    }

    /**
     * Página principal de entregas de dinero
     * 
     * Renderiza la vista principal del módulo mostrando el listado
     * ejecutivo de todas las entregas realizadas. Incluye información
     * resumida: fecha, monto, colaborador responsable, arqueos cubiertos
     * y tipos de destino. Requiere autenticación.
     * 
     * En caso de error durante la carga de datos, redirige a una página
     * de error 404
     * 
     * @return void 
     */
    public function index(): void
    {
        try {
            $this->authRequired();

            $entregas = $this->entregaDineroModel->listarResumen();


            $this->view('arqueo-caja.entregas', ['entregas' => $entregas]);
        } catch (Exception $e) {

            // error_log($e->getMessage());
            $this->view('errors.404', ['message' => 'Error al cargar el reporte de entregas.']);
        }
    }

    /**
     * Endpoint API: Obtener detalle completo de una entrega
     * 
     * Retorna en formato JSON el detalle completo de una entrega específica,
     * incluyendo dos conjuntos de datos:
     * 
     * 1. Arqueos asociados: Lista de arqueos de caja cubiertos en esta
     *    entrega con sus montos de ingresos, egresos y totales.
     * 
     * 2. Destinos del dinero: Lista de destinos (gerentes o depósitos
     *    bancarios) con los montos asignados a cada uno.
     * 
     * @param int $identrega ID de la entrega a consultar
     * @return void Envía respuesta JSON
     */
    public function detalle(int $identrega): void
    {

        $this->authRequired();
        header('Content-Type: application/json');

        try {

            $arqueosDetalle = $this->entregaDineroModel->obtenerDetalleArqueos($identrega);
            $destinosDetalle = $this->entregaDineroModel->obtenerDetalleDestinos($identrega);

            $arqueos = array_map(function ($arqueo) {

                $arqueo['ingresos_efectivo'] = (float) $arqueo['ingresos_efectivo'];
                $arqueo['ingresos_digital'] = (float) $arqueo['ingresos_digital'];
                $arqueo['egresos_dia'] = (float) $arqueo['egresos_dia'];
                $arqueo['total'] = (float) $arqueo['total'];
                return $arqueo;
            }, $arqueosDetalle);


            $destinos = array_map(function ($destino) {
                $destino['monto'] = (float) $destino['monto'];
                return $destino;
            }, $destinosDetalle);

            echo json_encode([
                'success' => true,
                'data' => [
                    'arqueos' => $arqueos,
                    'destinos' => $destinos
                ]
            ]);
        } catch (Exception $e) {

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener los detalles de la entrega.'
            ]);
            error_log($e->getMessage());
        }
    }
    
}
