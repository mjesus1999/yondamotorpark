<?php

/**
 * Controlador de Compras
 * 
 * app/Controllers/CompraController.php
 * 
 * Gestiona todas las operaciones relacionadas con el registro y consulta de compras
 * de vehículos realizadas a concesionarios. Incluye el registro de compras con su
 * documentación fiscal (facturas y boletas), carga de documentos PDF escaneados,
 * consultas especializadas de órdenes de compra por concesionario, y listado completo
 * de compras registradas en el sistema con información consolidada de concesionarios.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Compra;

/**
 * Clase CompraController
 * 
 * Controlador para la gestión integral de compras de vehículos.
 * Implementa operaciones de registro de compras con validación exhaustiva,
 * gestión de archivos PDF de documentación fiscal, consultas especializadas
 * de órdenes de compra disponibles para registro, y endpoints API para
 * integración con interfaces dinámicas de selección de concesionarios y
 * órdenes de compra.
 * 
 */
class CompraController extends Controller
{
    /**
     * Modelo de Compra
     * @var Compra
     */
    private Compra $compraModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa el modelo de Compra necesario para las operaciones
     * del controlador.
     */
    public function __construct()
    {
        $this->compraModel = new Compra();
    }

    /**
     * Muestra el listado de todas las compras registradas
     * 
     * Renderiza la vista principal con el listado completo de compras
     * registradas en el sistema, incluyendo información consolidada del
     * concesionario asociado a cada compra. Los datos se ordenan por
     * fecha de creación descendente. Requiere autenticación.
     * 
     * @return void
     */
    public function index(): void
    {
        $compras = $this->compraModel->getAll();
        $this->authRequired();
        $this->view('components.compras.index', ['compras' => $compras]);
    }

    /**
     * Muestra el formulario de registro de compra
     * 
     * Renderiza la vista con el formulario para registrar una nueva compra.
     * El formulario incluye selección dinámica de concesionarios con órdenes
     * activas, selección de orden de compra específica, datos de facturación
     * y carga de documento PDF escaneado. Requiere autenticación.
     * 
     * @return void
     */
    public function create(): void
    {
        $this->authRequired();
        $this->view('components.compras.create');
    }

    /**
     * Registra una nueva compra en el sistema con documentación fiscal
     * 
     * Endpoint AJAX que procesa el formulario de creación de compra.
     * Realiza validaciones exhaustivas de campos obligatorios, gestiona
     * la carga del archivo PDF de la factura o boleta, organiza los archivos
     * en subdirectorios según tipo de documento, y registra la compra en
     * la base de datos asociándola a su orden de compra correspondiente.
     *
     * Validaciones realizadas:
     * - Orden de Compra: Obligatoria, debe existir y estar en estado válido
     * - Fecha de Compra: Obligatoria, formato válido
     * - Tipo de Documento: Obligatorio, debe ser 'F' (Factura) o 'B' (Boleta)
     * - Serie: Obligatoria, formato de serie de comprobante
     * - Número de Documento: Obligatorio, número del comprobante
     * - Archivo PDF: Obligatorio, debe ser formato PDF válido
     *
     * @return void Respuesta JSON con resultado de la operación
     */
    public function store(): void
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
            'idorden' => $data['idorden'] ?? 0,
            'fechacompra' => $data['fechacompra'] ?? '',
            'tipodoc' => $data['tipodoc'] ?? '',
            'serie' => $data['serie'] ?? '',
            'numdocumento' => $data['numdocumento'] ?? '',
            'rutadoc' => ''
        ];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['idorden'], 'Orden de Compra');
        $errores[] = Validador::campoObligatorio($registro['fechacompra'], 'Fecha de Compra');
        $errores[] = Validador::campoObligatorio($registro['tipodoc'], 'Tipo de Documento');
        $errores[] = Validador::campoObligatorio($registro['serie'], 'Serie');
        $errores[] = Validador::campoObligatorio($registro['numdocumento'], 'Número de Documento');
        $errores = array_filter($errores);

        // Validar que se haya subido un archivo
        if (empty($_FILES['rutadoc']['name'])) {
            $errores[] = 'Debe subir un archivo PDF';
        }

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        try {

            $subdirectorio = '';
            switch ($registro['tipodoc']) {
                case 'F':
                    $subdirectorio = 'facturas';
                    break;
                case 'B':
                    $subdirectorio = 'boletas';
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'Tipo de documento no válido', 'id' => 0]);
                    exit;
            }

            $nombreArchivo = uniqid($subdirectorio . '_') . '_' . basename($_FILES['rutadoc']['name']);

            $directorioDestino = __DIR__ . '/../../storage/' . $subdirectorio . '/';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            $rutaCompleta = $directorioDestino . $nombreArchivo;

            // Validar que sea un PDF
            $extension = strtolower(pathinfo($_FILES['rutadoc']['name'], PATHINFO_EXTENSION));
            if ($extension !== 'pdf') {
                echo json_encode(['success' => false, 'message' => 'El archivo debe ser un PDF', 'id' => 0]);
                exit;
            }

            if (!move_uploaded_file($_FILES['rutadoc']['tmp_name'], $rutaCompleta)) {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar el archivo PDF', 'id' => 0]);
                exit;
            }

            $registro['rutadoc'] = $subdirectorio . '/' . $nombreArchivo;

            $idCompra = $this->compraModel->create($registro);

            if ($idCompra > 0) {

                echo json_encode([
                    'success' => true,
                    'message' => 'Compra registrada correctamente',
                    'id' => $idCompra
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al registrar la compra: ',
                    'id' => 0
                ]);
            }
        } catch (\Exception $e) {
            if (isset($rutaCompleta) && file_exists($rutaCompleta)) {
                @unlink($rutaCompleta); // Rmover archivo
            }

            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage(),
                'id' => 0
            ]);
        }
    }

    // ========================================================================
    // MÉTODOS PARA APIS
    // ========================================================================

    /**
     * API: Obtiene detalle de órdenes de compra de un concesionario específico
     * 
     * Endpoint AJAX que retorna información detallada y agrupada de todas las
     * órdenes de compra de un concesionario que estén en estado "proceso" o
     * "pagado". Los datos se reorganizan jerárquicamente agrupando los detalles
     * de vehículos bajo cada orden de compra correspondiente.
     *
     * @param int $id ID del concesionario
     * @return never Respuesta JSON con órdenes agrupadas o array vacío
     */
    public function searchDetOCByConcesionario($id)
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $datos = $this->compraModel->getDetOCByConcesionario($id);

        if ($datos) {
            // Reorganizar por orden de compra
            $resultado = [];

            foreach ($datos as $fila) {
                $idOrden = $fila['idordencompra'];

                if (!isset($resultado[$idOrden])) {
                    $resultado[$idOrden] = [
                        'idordencompra' => $idOrden,
                        'emision' => $fila['emision'],
                        'detalle' => []
                    ];
                }

                // Quitar campos duplicados que ya están al nivel superior
                unset($fila['idordencompra'], $fila['emision']);

                $resultado[$idOrden]['detalle'][] = $fila;
            }

            // Convertir de asociativo a numérico
            echo json_encode(array_values($resultado));
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }

    /**
     * API: Obtiene concesionarios con órdenes de compra activas
     * 
     * Endpoint AJAX que retorna únicamente los concesionarios que tienen al menos
     * una orden de compra en estado "proceso" o "pagado". Estos son los
     * concesionarios elegibles para registrar nuevas compras, ya que tienen
     * operaciones comerciales pendientes de materialización.
     *
     * @return never Respuesta JSON con array de concesionarios o array vacío
     */
    public function searchConcesionarioOCActiva(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $concesionarios = $this->compraModel->getConcesionariosConOCEnProcesoOPagado();

        if ($concesionarios) {
            echo json_encode($concesionarios);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }

}
