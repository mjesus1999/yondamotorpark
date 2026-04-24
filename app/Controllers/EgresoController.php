<?php

/**
 * Controlador de Egreso
 * 
 * app/Controllers/EgresoController.php
 * 
 * Gestiona las peticiones HTTP relacionadas con los egresos del sistema
 * (salidas de dinero y gastos operativos). Proporciona interfaces web para
 * visualizar, registrar y validar egresos con sus comprobantes asociados.
 * Implementa el flujo completo: registro de egreso → carga de comprobante
 * (si aplica) → validación contable. Incluye manejo transaccional con
 * rollback automático en caso de errores, gestión de archivos PDF, y
 * generación de reportes financieros por periodo. Todos los métodos
 * requieren autenticación previa.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Egreso;

/**
 * Clase EgresoController
 * 
 * Controlador para la gestión de egresos y comprobantes.
 * Hereda de Controller para acceder a funcionalidades base como
 * renderizado de vistas, validación de autenticación y manejo de sesiones.
 * Implementa lógica transaccional robusta: si falla el registro del
 * comprobante, revierte automáticamente el egreso creado y elimina
 * archivos temporales para mantener consistencia de datos.
 */
class EgresoController extends Controller
{
    /**
     * Instancia del modelo Egreso
     * @var Egreso
     */
    private Egreso $egresoModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa la instancia del modelo Egreso para
     * realizar operaciones de base de datos y gestión financiera
     */
    public function __construct()
    {
        $this->egresoModel = new Egreso();
    }

    /**
     * Página principal de egresos filtrados por estado
     * 
     * Renderiza la vista principal del módulo mostrando el listado de
     * egresos según el estado solicitado. Maneja dos vistas diferentes:
     * 
     * @param string $estado Estado de los egresos a filtrar (default: 'N').
     *                       Valor especial 'validados' para comprobantes validados
     * @return void Renderiza la vista principal de egresos
     */
    public function index(string $estado = 'N'): void
    {
        $this->authRequired();

        if ($estado === 'validados') {
            $egresos = $this->egresoModel->getEgresoWithComprobantesValidados();


            $this->view('egresos.index', ['egresos' => $egresos, 'estado' => 'validados']);
        } else {
            $egresos = $this->egresoModel->getAllEgresosByEstado($estado);

            $this->view('egresos.index', ['egresos' => $egresos, 'estado' => $estado]);
        }
    }

    /**
     * Página de reporte de egresos por fechas
     * 
     * Renderiza la interfaz para generar reportes de egresos filtrados
     * por rango de fechas. Incluye controles de fecha y botones para
     * generar reportes ejecutivos con múltiples vistas.
     * 
     * @return void
     */
    public function indexReporteByFecha(): void
    {
        $this->authRequired();
        $this->view('egresos.reporteByFecha');
    }

    /**
     * Página para adjuntar comprobantes a egresos existentes
     * 
     * Renderiza la interfaz para cargar comprobantes (facturas, boletas)
     * a egresos que fueron registrados sin comprobante inicialmente pero
     * que ahora requieren documentación de respaldo.
     * 
     * @return void
     */
    public function indexAjuntarComprobante(): void
    {
        $this->authRequired();
        $this->view('egresos.adjuntarComprobante');
    }

    /**
     * Página de creación de nuevo egreso
     * 
     * Renderiza el formulario para registrar un nuevo egreso.
     * Incluye selectores de concepto, solicitante, proveedor (si aplica),
     * y campos para cargar comprobante si es requerido.

     * @return void
     */
    public function create(): void
    {
        $this->authRequired();
        $this->view('egresos.create');
    }

    /**
     * Endpoint API: Registrar egreso con comprobante opcional
     * 
     * Procesa el registro de un nuevo egreso implementando lógica
     * transaccional robusta con rollback automático. El flujo varía
     * según si el egreso requiere o no comprobante

     * @return void Envía respuesta JSON y termina ejecución
     */
    public function store(): void
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }

        header('Content-Type: application/json');
        $data = array_map([Validador::class, 'limpiar'], $_POST);


        $registroEgreso = [
            'idconceptoegreso' => $data['idconceptoegreso'] ?? null,
            'idsolicitante' => empty($data['idsolicitante']) ? null : (int) $data['idsolicitante'],
            'monto' => empty($data['monto']) ? null : (float) $data['monto'],
            'comentario' => empty($data['comentario']) ? null : $data['comentario'],
            'requierecomprobante' => empty($data['requierecomprobante']) ? 'N' : $data['requierecomprobante']
        ];

        $solicitanteLibre = empty($data['solicitante_nombre']) ? '' : trim((string) $data['solicitante_nombre']);
        if ($solicitanteLibre !== '') {
            // Mantener FK válida: si no se selecciona colaborador, usar el usuario logueado como solicitante técnico
            if (empty($registroEgreso['idsolicitante'])) {
                $registroEgreso['idsolicitante'] = (int) ($_SESSION['user']['id'] ?? 0);
            }
        }

        $errores = [];
        $errores[] = Validador::campoObligatorio($registroEgreso['idconceptoegreso'], 'Concepto de Egreso');
        $errores[] = Validador::campoObligatorio($registroEgreso['idsolicitante'], 'Solicitante');
        $errores[] = Validador::campoObligatorio($registroEgreso['monto'], 'Monto');

        if (!empty(array_filter($errores))) {
            echo json_encode(['success' => false, 'message' => implode('<br>', array_filter($errores)), 'id' => 0]);
            exit;
        }

        $newId = $this->egresoModel->add($registroEgreso);

        if ($newId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el egreso. Intente nuevamente.', 'id' => 0]);
            exit;
        }

        if ($solicitanteLibre !== '') {
            // Guardar persona libre en columna dedicada (para reportes/listados)
            $this->egresoModel->setSolicitanteNombre($newId, $solicitanteLibre);
        }

        // Si el egreso no requiere comprobante, la operación termina aquí.
        if ($registroEgreso['requierecomprobante'] === 'N') {
            echo json_encode(['success' => true, 'message' => 'Egreso registrado con éxito.', 'id' => $newId]);
            exit;
        }


        $registroComprobante = [
            'idegreso' => $newId,
            'idproovedor' => empty($data['idproovedor']) ? null : (int) $data['idproovedor'],
            'tipodoc' => $data['tipodoc'] ?? null,
            'serie' => $data['serie'] ?? null,
            'numdocumento' => $data['numdocumento'] ?? null,
            'monto' => empty($data['monto_comprobante']) ? null : (float) $data['monto_comprobante']
        ];

        $erroresComprobante = [];
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['idproovedor'], 'Proveedor');
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['tipodoc'], 'Tipo de Documento');
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['serie'], 'Serie');
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['numdocumento'], 'Número de Documento');
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['monto'], 'Monto');

        if (!empty(array_filter($erroresComprobante))) {
            // Rollback: Eliminar el egreso principal si el comprobante no es válido.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => implode('<br>', array_filter($erroresComprobante)), 'id' => 0]);
            exit;
        }

        if (!isset($_FILES['rutacomprobante']) || $_FILES['rutacomprobante']['error'] !== UPLOAD_ERR_OK) {
            // Rollback: Eliminar el egreso principal si el archivo no se sube.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'Error al subir el archivo de comprobante.', 'id' => 0]);
            exit;
        }

        $archivoTemp = $_FILES['rutacomprobante']['tmp_name'];
        $nombreOriginal = $_FILES['rutacomprobante']['name'];
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if ($extension !== 'pdf') {
            // Rollback: Eliminar el egreso principal si el archivo no es PDF.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'El archivo debe ser un PDF.', 'id' => 0]);
            exit;
        }

        $subdirectorio = '';
        switch ($registroComprobante['tipodoc']) {
            case 'F':
                $subdirectorio = 'facturas';
                break;
            case 'B':
                $subdirectorio = 'boletas';
                break;
            default:
                // Rollback: Eliminar el egreso principal si el tipo de documento no es válido.
                $this->egresoModel->deleteEgreso($newId);
                echo json_encode(['success' => false, 'message' => 'Tipo de documento no válido.', 'id' => 0]);
                exit;
        }

        $nombreArchivo = uniqid($subdirectorio . '_') . '.' . $extension;
        $directorioDestino = __DIR__ . '/../../storage/' . $subdirectorio . '/';
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (!is_dir($directorioDestino) && !mkdir($directorioDestino, 0777, true)) {
            // Rollback: Eliminar el egreso principal si no se puede crear el directorio.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'Error al crear el directorio de destino.', 'id' => 0]);
            exit;
        }

        if (!move_uploaded_file($archivoTemp, $rutaCompleta)) {
            // Rollback: Eliminar el egreso principal si falla la subida.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'No se pudo guardar el archivo de comprobante.', 'id' => 0]);
            exit;
        }

        $registroComprobante['rutacomprobante'] = $subdirectorio . '/' . $nombreArchivo;

        $successComprobante = $this->egresoModel->addComprobante($registroComprobante);

        if ($successComprobante) {
            echo json_encode(['success' => true, 'message' => 'Egreso y comprobante registrados con éxito.', 'id' => $newId]);
        } else {
            // Rollback: Eliminar el archivo subido y el egreso principal si falla la inserción en la base de datos.
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'Error al guardar el comprobante. El egreso fue revertido.', 'id' => 0]);
        }
    }

    public function storeConceptoEgreso(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
            exit;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);
        $descripcion = trim((string) ($data['descripcion'] ?? ''));

        if ($descripcion === '') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'La descripción es obligatoria.']);
            exit;
        }

        $id = $this->egresoModel->addConceptoEgreso($descripcion);
        if ($id > 0) {
            echo json_encode(['status' => 'success', 'idconceptoegreso' => $id, 'descripcion' => $descripcion]);
            exit;
        }

        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'No se pudo registrar el concepto.']);
        exit;
    }

    /**
     * Endpoint API: Validar comprobante contablemente
     * 
     * Marca un comprobante como validado por el área de contabilidad,
     * actualizando su estado a 'cargadocontabilidad = S' y registrando
     * la fecha/hora de validación. Este proceso confirma que el
     * comprobante cumple requisitos fiscales y contables.
     *
     * @param int $id ID del comprobante a validar
     * @return void Envía respuesta JSON y termina ejecución
     */
    public function validarComprobante($id): void
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }

        header('Content-Type: application/json');

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de comprobante inválido.']);
            exit;
        }

        $success = $this->egresoModel->validarComprobante($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Comprobante validado con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al validar el comprobante. Intente nuevamente.']);
        }
    }

    /**
     * Endpoint API: Obtener catálogo de conceptos de egreso
     * 
     * Retorna la lista completa de conceptos disponibles para clasificar
     * egresos (combustible, viáticos, servicios). Utilizado para
     * poblar selectores en formularios de registro.
     *
     * @return void Envía respuesta JSON
     */
    public function getConceptosEgreso(): void
    {

        header('Content-Type: application/json');
        $this->authRequired();
        $data = $this->egresoModel->getConceptosEgreso();

        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron conceptos de egreso.']);
        }
    }

    /**
     * Endpoint API: Obtener listado de colaboradores activos
     * 
     * Retorna todos los colaboradores con contratos vigentes (excluyendo
     * practicantes) para seleccionar solicitantes de egresos.

     * @return void
     */
    public function getColaboradores(): void
    {
        header('Content-Type: application/json');
        $this->authRequired();
        $data = $this->egresoModel->getColaboradores();

        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron colaboradores.']);
        }
    }

    /**
     * Endpoint API: Obtener catálogo de proveedores
     * 
     * Retorna la lista completa de proveedores registrados para
     * seleccionar emisores de comprobantes en egresos.
     *
     * @return void Envía respuesta JSON
     */
    public function getProovedores(): void
    {
        header('Content-Type: application/json');
        $this->authRequired();
        $data = $this->egresoModel->getProovedores();

        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron proovedores.']);
        }
    }

    /**
     * Endpoint API: Obtener detalle de un egreso con comprobante
     * 
     * Retorna información completa de un egreso que tiene comprobante
     * asociado, incluyendo datos del proveedor y ruta del archivo PDF
     *
     * @param int $id ID del egreso a consultar
     * @return void Envía respuesta JSON
     */
    public function getDetalleEgreso(int $id): void
    {
        header('Content-Type: application/json');
        $this->authRequired();

        $data = $this->egresoModel->getDetalleEgresoById($id);


        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron detalles para el egreso especificado.']);
        }
    }

    /**
     * Endpoint API: Generar reporte de egresos por periodo
     * 
     * Retorna un reporte completo con múltiples vistas de egresos en un
     * rango de fechas específico. 
     *
     * El reporte incluye cuatro conjuntos:
     * 1. egresoSinComprobante: Egresos sin documentación
     * 2. egresoConComprobante: Egresos con comprobantes
     * 3. egresoByConcepto: Totales agrupados por concepto
     * 4. egresoGeneral: Resumen ejecutivo con totales generales

     * @return void Envía respuesta JSON y termina ejecución
     */
    public function getReporteEgresos(): void
    {
        header('Content-Type: application/json');

        $fechaInicio = $_GET['fecha_inicio'] ?? null;
        $fechaFin = $_GET['fecha_fin'] ?? null;

        if (!$fechaInicio || !$fechaFin) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Fechas de inicio y fin son requeridas.'
            ]);
            exit();
        }

        $datos = $this->egresoModel->getReporteByFecha($fechaInicio, $fechaFin);

        $egresoSinComprobantes = $datos[0] ?? [];
        $egresoConComprobantes = $datos[1] ?? [];
        $egresoByConceptos = $datos[2] ?? [];
        $egresoGeneral = $datos[3] ?? [];

        // Validar si hay datos en alguno de los dos primeros conjuntos
        if (empty($egresoSinComprobantes) && empty($egresoConComprobantes)) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'No se encontraron egresos en el rango de fechas seleccionado.',
                'egresoSinComprobante' => [],
                'egresoConComprobante' => [],
                'egresoByConcepto' => [],
                'egresoGeneral' => []
            ]);
            exit();
        }

        echo json_encode([
            'success' => true,
            'egresoSinComprobante' => $egresoSinComprobantes,
            'egresoConComprobante' => $egresoConComprobantes,
            'egresoByConcepto' => $egresoByConceptos,
            'egresoGeneral' => $egresoGeneral
        ]);

        exit();
    }

}
