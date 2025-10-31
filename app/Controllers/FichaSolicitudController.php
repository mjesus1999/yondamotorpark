<?php

/**
 * Controlador de Ficha de Solicitud
 * 
 * app/Controllers/FichaSolicitudController.php
 * 
 * Gestiona las peticiones HTTP relacionadas con las fichas de solicitud
 * de crédito vehicular. Coordina el proceso completo de creación de fichas:
 * búsqueda/creación de personas involucradas (titular, cónyuge, avales),
 * validación de datos, carga de documentos PDF, y actualización de estados
 * de cotizaciones. Integra consultas a APIs externas (RENIEC) para
 * validación de identidad y proporciona endpoints REST para operaciones
 * asíncronas desde el frontend.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\FichaSolicitud;
use App\Helpers\Validador;
use PDOException;

/**
 * Clase FichaSolicitudController
 * 
 * Controlador para la gestión de fichas de solicitud de crédito.
 * Hereda de Controller para acceder a funcionalidades base como
 * renderizado de vistas, validación de autenticación y manejo de sesiones.
 * Implementa el flujo completo: cotización → búsqueda de personas →
 * registro de nuevas personas → creación de ficha con documento PDF →
 * actualización de estado de cotización.
 */
class FichaSolicitudController extends Controller
{
    /**
     * Instancia del modelo FichaSolicitud
     * @var FichaSolicitud
     */
    private FichaSolicitud $fichaSolicitudModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa la instancia del modelo FichaSolicitud para
     * realizar operaciones de base de datos
     */
    public function __construct()
    {
        $this->fichaSolicitudModel = new FichaSolicitud();
    }

    /**
     * Página principal de creación de ficha
     * 
     * Renderiza el formulario de creación de ficha de solicitud
     * precargado con los datos de una cotización específica.
     * Muestra información del vehículo, condiciones de crédito,
     * y datos básicos del cliente para facilitar el llenado
     * 
     * @param int $id ID de la cotización base
     * @return void 
     */
    public function index($id): void
    {
        $this->authRequired();
        $datosCotizacion = $this->fichaSolicitudModel->getDatosCotizacion($id);
        $this->view('cotizacion.ficha', ['infoFicha' => $datosCotizacion]);
    }

    /**
     * Endpoint API: Registrar una nueva persona
     * 
     * Crea un nuevo registro de persona (titular, cónyuge, aval, o aval-cónyuge)
     * después de validar todos los campos obligatorios. Los datos son
     * sanitizados antes de procesarse. Utilizado cuando la persona no
     * existe en el sistema tras búsqueda por DNI.
     * 
     * @return void Envía respuesta JSON, retorna -1 si hay errores de validación
     */
    public function storePersona(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');
        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'iddistrito' => $data['iddistrito'],
            'apellidos' => $data['apellidos'],
            'nombres' => $data['nombres'],
            'tipodoc' => $data['tipodoc'],
            'nrodoc' => $data['nrodoc'],
            'genero' => $data['genero'],
            'telprimario' => $data['telprimario'],
            'direccion' => empty($data['direccion']) ? null : $data['direccion']
        ];

        $errores = Validador::validarPersonaCrear($registro);

        if (!empty($errores)) {
            $this->view('cotizacion.ficha', ['error' => implode("<br>", $errores), 'data' => $registro]);
            return /* -1 */ ;
        }

        $idPersona = $this->fichaSolicitudModel->createPersona($registro);

        if ($idPersona > 0) {
            echo json_encode([
                'success' => true,
                'lastId' => $idPersona
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se ha podido registrar la persona'
            ]);
        }
    }

    /**
     * Endpoint API: Registrar ficha de solicitud completa
     * 
     * Procesa el registro de una ficha de solicitud incluyendo:
     * - Validación de datos obligatorios
     * - Carga y validación de archivo PDF
     * - Almacenamiento del archivo en el sistema de archivos
     * - Registro en base de datos con ruta del archivo
     * - Actualización automática del estado de la cotización según resultado
     * 
     * @return void Envía respuesta JSON y termina ejecución
     */
    public function storeFicha(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idcotizacion' => $data['idcotizacion'],
            'idconyuge' => empty($data['idconyuge']) ? null : $data['idconyuge'],
            'idaval' => empty($data['idaval']) ? null : $data['idaval'],
            'idavalconyuge' => empty($data['idavalconyuge']) ? null : $data['idavalconyuge'],
            'fechavisita' => $data['fechavisita'],
            'rutaficha' => '',
            'comentarios' => empty($data['comentarios']) ? null : $data['comentarios'],
            'estado' => $data['estado']
        ];

        // Validaciones obligatorias
        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['idcotizacion'], 'Id Cotización');
        $errores[] = Validador::campoObligatorio($registro['fechavisita'], 'Fecha de visita');
        $errores[] = Validador::campoObligatorio($registro['estado'], 'Estado');
        $errores = array_filter($errores);

        // Validar archivo subido
        if (empty($_FILES['rutaficha']['name'])) {
            $errores[] = 'Debe subir un archivo PDF para la ficha';
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
            // Subida del archivo
            $subdirectorio = 'fichasCotizacion';
            $nombreArchivo = uniqid('ficha_') . '_' . basename($_FILES['rutaficha']['name']);
            $directorioDestino = __DIR__ . '/../../storage/' . $subdirectorio . '/';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            $rutaCompleta = $directorioDestino . $nombreArchivo;

            // Validar extensión PDF
            $extension = strtolower(pathinfo($_FILES['rutaficha']['name'], PATHINFO_EXTENSION));
            if ($extension !== 'pdf') {
                echo json_encode(['success' => false, 'message' => 'El archivo debe ser un PDF', 'id' => 0]);
                exit;
            }

            if (!move_uploaded_file($_FILES['rutaficha']['tmp_name'], $rutaCompleta)) {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar el archivo PDF', 'id' => 0]);
                exit;
            }

            // Guardar ruta relativa en BD
            $registro['rutaficha'] = $subdirectorio . '/' . $nombreArchivo;

            // Guardar en la base de datos
            $idFicha = $this->fichaSolicitudModel->createFicha($registro);

            if ($idFicha > 0) {

                echo json_encode([
                    'success' => true,
                    'message' => 'Ficha registrada correctamente',
                    'id' => $idFicha
                ]);

                // error_log('DATOOS DE LA COTIZACION: ' .$registro['estado'].$idFicha);

                switch (strtolower(trim($registro['estado']))) {
                    case 'aprobado':
                        $this->fichaSolicitudModel->updateCotizacion($registro['idcotizacion'], 'A');
                        break;
                    case 'observado':
                        $this->fichaSolicitudModel->updateCotizacion($registro['idcotizacion'], 'O');
                        break;
                    case 'anulado':
                        $this->fichaSolicitudModel->updateCotizacion($registro['idcotizacion'], 'R');
                        break;
                }
            } else {
                // si falla, borro el archivo para no dejar basura
                if (file_exists($rutaCompleta)) {
                    @unlink($rutaCompleta);
                }
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al registrar la ficha en la base de datos',
                    'id' => 0
                ]);
            }
        } catch (\Exception $e) {
            if (isset($rutaCompleta) && file_exists($rutaCompleta)) {
                @unlink($rutaCompleta);
            }

            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar la ficha: ' . $e->getMessage(),
                'id' => 0
            ]);
        }
    }

    /**
     * Endpoint API: Buscar persona por DNI en base de datos local
     * 
     * Consulta si una persona ya existe en el sistema mediante su DNI.
     * Retorna información completa si la encuentra, permitiendo reutilizar
     * el registro en lugar de crear uno duplicado. Primera opción de búsqueda
     * antes de consultar API externa de RENIEC.
     * 
     * @param string $dni Número de DNI a buscar
     * @return void Envía respuesta JSON
     */
    public function searchPersonaByDNI(string $dni)
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        $persona = $this->fichaSolicitudModel->searchPersonaByDNI($dni);

        if ($persona) {
            echo json_encode(['success' => true, 'persona' => $persona]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró la persona en el regsitro']);
        }
    }

    /**
     * Endpoint API: Buscar persona en RENIEC mediante API externa
     * 
     * Consulta la API de RENIEC para obtener nombres y apellidos de una
     * persona mediante su DNI. Utilizado como segunda opción cuando la
     * persona no existe en la base de datos local. Permite prellenar
     * el formulario de registro con datos oficiales.

     * @param string $dni Número de DNI a consultar en RENIEC
     * @return void Envía respuesta JSON
     */
    public function searchPersonaByReniec(string $dni): void
    {
        $this->authRequired();

        try {
            require_once __DIR__ . '/../Helpers/Api_dni.php';
            // Capturar la salida de la función
            ob_start();
            searchByDNI($dni);
            $apiResponse = ob_get_clean();

            $responseData = json_decode($apiResponse, true);

            if ($responseData && $responseData['success']) {
                echo json_encode([
                    'success' => true,
                    'source' => 'api',
                    'message' => 'Persona encontrada en RENIEC',
                    'apellidos' => trim($responseData['apepaterno'] . ' ' . $responseData['apematerno']),
                    'nombres' => $responseData['nombres']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $responseData['message'] ?? 'No se encontró la persona'
                ]);
            }
        } catch (PDOException $e) {
            error_log('Error en búsqueda por API DNI: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

}
