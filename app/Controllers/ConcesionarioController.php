<?php

/**
 * Controlador de Concesionarios
 * 
 * app/Controller/ConsesionarioController.php 
 * 
 * Gestiona todas las operaciones relacionadas con los concesionarios proveedores
 * de vehículos, incluyendo CRUD completo, integración con API de SUNAT para
 * validación de RUC, consultas especializadas de concesionarios con órdenes
 * de compra activas, generación de reportes detallados con resumen ejecutivo,
 * y gestión de tiendas asociadas a cada concesionario.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Concesionario;

/**
 * Clase ConcesionarioController
 * 
 * Controlador para la gestión integral de concesionarios.
 * Implementa operaciones CRUD, validaciones de integridad referencial,
 * integración con API externa de SUNAT para validación de datos tributarios,
 * y generación de reportes ejecutivos con información de pagos y vehículos.
 * 
 */
class ConcesionarioController extends Controller
{
    /**
     * Modelo de Concesionario
     * @var Concesionario
     */
    private Concesionario $concesionarioModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa el modelo de Concesionario necesario para las operaciones
     * del controlador.
     */
    public function __construct()
    {
        $this->concesionarioModel = new Concesionario();
    }

    /**
     * Muestra el listado de todos los concesionarios
     * 
     * Renderiza la vista principal con el listado completo de concesionarios
     * registrados en el sistema, ordenados por fecha de creación descendente.
     * Requiere autenticación.
     * 
     * @return void
     */
    public function index(): void
    {
        $concesionarios = $this->concesionarioModel->getAll();
        $this->authRequired();
        $this->view('concesionarios.index', ['concesionarios' => $concesionarios]);
    }

    /**
     * Muestra el formulario de creación de concesionario
     * 
     * Renderiza la vista con el formulario para registrar un nuevo concesionario.
     * Incluye integración con API de SUNAT para autocompletar datos a partir
     * del RUC. Requiere autenticación.
     *
     * @return void
     */
    public function create(): void
    {
        $this->authRequired();
        $this->view('concesionarios.create');
    }

    /**
     * Registra un nuevo concesionario en el sistema
     * 
     * Endpoint AJAX que procesa el formulario de creación de concesionario.
     * Realiza validaciones exhaustivas de campos obligatorios y registra
     * el nuevo concesionario en la base de datos.
     * 
     * Validaciones realizadas:
     * - RUC: Obligatorio, debe tener 11 dígitos
     * - Nombre Comercial: Obligatorio
     * - Razón Social: Obligatoria
     *
     * @return int ID del concesionario creado (positivo) o 0 en caso de error
     * @throws \Exception Si el método HTTP no es POST
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
            'ruc' => $data['ruc'] ?? '',
            'nombrecomercial' => $data['nombrecomercial'] ?? '',
            'razonsocial' => $data['razonsocial'] ?? ''
        ];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['ruc'], 'RUC');
        $errores[] = Validador::campoObligatorio($registro['nombrecomercial'], 'Nombre Comercial');
        $errores[] = Validador::campoObligatorio($registro['razonsocial'], 'Razón Social');
        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        $idConcesionario = $this->concesionarioModel->create($registro);

        if ($idConcesionario > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Concesionario creado exitosamente!',
                'id' => $idConcesionario
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo crear el concesionario',
                'id' => 0
            ]);
            exit;
        }
    }

    /**
     * Summary of updateActualiza el nombre comercial de un concesionario
     * 
     * Endpoint AJAX que procesa la actualización del nombre comercial de un
     * concesionario existente. No permite modificar RUC ni razón social por
     * ser datos tributarios que no deben cambiar.
     * 
     * Validaciones:
     * - Nombre Comercial: Obligatorio
     * - ID de Concesionario: Debe existir en el sistema
     *
     * @param int $id ID del concesionario a actualizar
     * @return int Número de filas afectadas o 0 si no se realizó actualización
     */
    public function update($id): int
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = ['nombrecomercial' => $data['nombrecomercial'] ?? '', 'idconcesionario' => $id];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['nombrecomercial'], 'Nombre Comercial');

        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        $rowAffects = $this->concesionarioModel->update($registro);

        if ($rowAffects > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Concesionario actualizado exitosamente!',
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo actualizar el concesionario',
            ]);
            exit;
        }
    }

    /**
     * Summary of gestionarMuestra la vista de gestión de tiendas de un concesionario
     * 
     * Renderiza la interfaz para administrar las tiendas asociadas a un
     * concesionario específico, buscado por su RUC. Si el concesionario
     * no existe, muestra una página de error 404.
     *
     * @param string $ruc RUC del concesionario
     * @return void
     */
    public function gestionar($ruc): void
    {
        $this->authRequired();
        $concesionario = $this->concesionarioModel->getConcesionarioByRUC($ruc);

        if (!$concesionario || count($concesionario) === 0) {
            $this->view('errors.404', ['mensaje' => 'Concesionario no encontrado']);
            return;
        }

        // Le pasamos solo el RUC, el JS se encargará de cargar el resto.
        $this->view('concesionarios.create', ['ruc' => $concesionario[0]['ruc']]);
    }

    /**
     * Elimina un concesionario con validación de integridad referencial
     * 
     * Endpoint AJAX que elimina un concesionario después de verificar que
     * no tenga órdenes de compra registradas. Si tiene órdenes de compra,
     * la eliminación es rechazada para mantener la integridad referencial.
     *
     * @param int $id ID del concesionario a eliminar
     * @return void Respuesta JSON con resultado de la operación
     */
    public function delete($id)
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            // Paso 1: Verificar si el concesionario tiene OC
            $oc = $this->concesionarioModel->getOC($id);

            if ($oc > 0) {
                // Tiene OC, no se puede eliminar
                echo json_encode([
                    'success' => false,
                    'message' => 'No se puede eliminar. Tiene órdenes de compra registradas.'
                ]);
                return;
            }

            // Paso 2: Proceder a eliminar si no tiene OC
            $resultado = $this->concesionarioModel->delete($id);

            if ($resultado > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Concesionario eliminado correctamente.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo eliminar el concesionario.'
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }
    }


    // ========================================================================
    // MÉTODOS PARA APIS
    // ========================================================================


    /**
     * API: Consulta datos de RUC en la API de SUNAT
     * 
     * Endpoint AJAX que consulta la API externa de apis.net.pe para obtener
     * información completa de un RUC desde los registros de SUNAT.
     * Utilizado para autocompletar datos al registrar nuevos concesionarios.
     *
     * @param string $ruc Número de RUC a consultar
     * @return void Respuesta JSON con datos del RUC o error
     */
    public function searchRucSunat($ruc): void
    {
        $this->authRequired();
        if (strlen($ruc) != 11) {
            http_response_code(400);
            echo json_encode(['error' => 'RUC inválido']);
            return;
        }

        $token = 'apis-token-10575.ycXCIbBCEoM8ufZplATB5oIDwVTo7mzp';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/ruc/full?numero=' . $ruc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Referer: http://apis.net.pe/api-ruc',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        header('Content-Type: application/json; charset=utf-8');
        echo $response;
    }

    /**
     *API: Busca un concesionario por RUC en la base de datos local
     * 
     * Endpoint AJAX que busca un concesionario registrado en el sistema
     * utilizando su número de RUC. Útil para verificar si un concesionario
     * ya está registrado antes de crear uno nuevo, o para recuperar sus datos.
     *
     * @param string $ruc Número de RUC del concesionario a buscar
     * @return never Respuesta JSON con datos del concesionario o array vacío si no existe
     */
    public function searchRucDB($ruc): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $concesionario = $this->concesionarioModel->getConcesionarioByRUC($ruc);

        if ($concesionario) {
            echo json_encode($concesionario);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }

        exit();
    }

    /**
     * API: Obtiene el listado completo de concesionarios
     * 
     * Endpoint AJAX que retorna todos los concesionarios registrados en el
     * sistema. Utilizado para poblar selectores, tablas dinámicas y otros
     * componentes de interfaz que requieren listar concesionarios.
     *
     * @return never  Respuesta JSON con array de concesionarios o array vacío
     */
    public function getConcesionariosDB(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $concesionarios = $this->concesionarioModel->getAll();

        if ($concesionarios) {
            echo json_encode($concesionarios);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }

    /**
     * *
     * API: Obtiene concesionarios con órdenes de compra en proceso
     * 
     * Endpoint AJAX que retorna únicamente los concesionarios que tienen
     * al menos una orden de compra en estado "proceso". Útil para filtrar
     * proveedores con operaciones comerciales activas o pendientes de completar.
     *
     * @return never Respuesta JSON con array de concesionarios o array vacío
     */
    public function getConcesionariosWhitOCProceso(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');
        $concesionarios = $this->concesionarioModel->getConcesionariosWhitOCProceso();
        error_log('Concesionarios con OC en proceso: ' . print_r($concesionarios, true));

        if ($concesionarios) {
            echo json_encode($concesionarios);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }

    /**
     * API: Genera reporte detallado completo de un concesionario
     * 
     * Endpoint AJAX que genera un reporte ejecutivo completo con tres secciones
     * de información sobre un concesionario específico. Utiliza procedimiento
     * almacenado para obtener datos agregados y detallados.
     *
     * @param int $id ID del concesionario para el reporte
     * @return void Respuesta JSON con tres arrays de datos o error 404
     */
    public function getReporteByConcesionario(int $id): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        $datos = $this->concesionarioModel->getReporteConcesionarioDetallado($id);

        if ($datos !== null) {
            $respuesta = [
                'resumenEjecutivo' => $datos['resumenEjecutivo'],
                'detallePagos' => $datos['detallePagos'],
                'detalleVehiculos' => $datos['detalleVehiculos']
            ];

            // Codifica el array completo en JSON
            echo json_encode($respuesta);

            // error_log(print_r($respuesta, true));
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
    }
}
