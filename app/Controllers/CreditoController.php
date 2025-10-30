<?php

/**
 * Controlador de Crédito
 * 
 * app/Controllers/CreditoController.php
 * 
 * Gestiona las peticiones HTTP relacionadas con la gestión y seguimiento
 * de créditos morosos del sistema. Proporciona interfaces web para
 * visualizar dashboard de morosidad, registrar seguimientos de cobranza
 * (llamadas, visitas, acuerdos), consultar historiales completos de
 * gestión, y obtener estadísticas ejecutivas. Implementa detección
 * automática de peticiones AJAX para responder apropiadamente con JSON
 * o redirecciones según el tipo de cliente. Maneja carga de archivos
 * de evidencia (fotos, grabaciones, documentos) con validaciones de
 * seguridad.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Credito;

/**
 * Clase CreditoController
 * 
 * Controlador para la gestión de créditos y control de morosidad.
 * Hereda de Controller para acceder a funcionalidades base como
 * renderizado de vistas, validación de autenticación y manejo de sesiones.
 * Implementa endpoints REST para consultas asíncronas y métodos tradicionales
 * para navegación web, adaptando respuestas según tipo de petición (AJAX o
 * navegador). Maneja almacenamiento seguro de evidencias de seguimiento.
 */
class CreditoController extends Controller
{
    /**
     * Instancia del modelo Credito
     * @var Credito
     */
    private Credito $creditoModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa la instancia del modelo Credito para
     * realizar operaciones de gestión de cobranza y morosidad
     */
    public function __construct()
    {
        $this->creditoModel = new Credito();
    }

    /**
     * Página principal del módulo de créditos
     * 
     * Renderiza el dashboard principal de gestión de morosidad.
     * Incluye gráficos estadísticos, clasificación de morosos por
     * nivel de atraso (5 días, 2 semanas, 1+ mes), y acceso a
     * funcionalidades de registro de seguimientos.
     * 
     * @return void
     */
    public function index(): void
    {
        $this->authRequired();
        $this->view('creditos.index', []);
    }

    /**
     * Endpoint API: Obtener estadísticas de morosidad
     * 
     * Retorna indicadores clave de gestión de cobranza: cantidad de
     * clientes morosos, deuda total acumulada, seguimientos realizados
     * en el día actual, y promedio de días de atraso. Utilizado para
     * alimentar widgets del dashboard en tiempo real.
     * 
     * @return void Envía respuesta JSON
     */
    public function getEstadisticas(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $estadisticas = $this->creditoModel->getEstadisticasMorosos();

            echo json_encode([
                'success' => true,
                'total_morosos' => (int) $estadisticas['total_morosos'],
                'deuda_total' => (float) $estadisticas['deuda_total'],
                'seguimientos_hoy' => (int) $estadisticas['seguimientos_hoy'],
                'dias_promedio' => (float) $estadisticas['dias_promedio']
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ]);
        }
    }

    /**
     * Endpoint API: Obtener morosos clasificados por nivel de atraso
     * 
     * Retorna la lista completa de clientes morosos organizados en tres
     * categorías según días de atraso. Actualiza automáticamente el
     * estado de cuotas vencidas antes de consultar para garantizar
     * información en tiempo real.
     * 
     * Categorías retornadas:
     * - 5-dias: Clientes con 1-5 días de atraso (gestión temprana)
     * - 2-semanas: Clientes con 6-14 días (seguimiento activo)
     * - 1-mes: Clientes con 15+ días (gestión intensiva/legal)
     * 
     * @return void Envía respuesta JSON
     */
    public function getMorosos(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $morosos = $this->creditoModel->getMorososClasificados();

            echo json_encode([
                'success' => true,
                '5-dias' => $morosos['5-dias'] ?? [],
                '2-semanas' => $morosos['2-semanas'] ?? [],
                '1-mes' => $morosos['1-mes'] ?? []
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener morosos',
                '5-dias' => [],
                '2-semanas' => [],
                '1-mes' => []
            ]);
        }
    }

    /**
     * Registrar seguimiento de gestión de cobranza
     * 
     * Procesa el registro de un seguimiento de cobranza con carga de
     * archivo de evidencia. Implementa detección automática de peticiones
     * AJAX para responder apropiadamente:
     * - Peticiones AJAX: Responde con JSON
     * - Peticiones tradicionales: Redirige con mensaje flash
     * 
     * @return void Envía JSON o redirige según tipo de petición
     */
    public function registrarSeguimiento(): void
    {
        // Asegurar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Helper para detectar AJAX
        $isAjax = false;
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            $isAjax = true;
        } elseif (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            $isAjax = true;
        }

        // Sólo poner header json si es AJAX
        if ($isAjax) {
            header('Content-Type: application/json');
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                if ($isAjax) {
                    http_response_code(405);
                    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                    return;
                } else {
                    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Método no permitido'];
                    header('Location: /creditos');
                    exit;
                }
            }

            $data = $_POST;
            $errores = [];

            // Validaciones básicas
            $idContrato = (int) ($data['idcontrato'] ?? 0);
            $tipoSeguimiento = $data['tipoSeguimiento'] ?? '';
            $observaciones = trim($data['observaciones'] ?? '');

            if ($idContrato <= 0) {
                $errores[] = 'ID de contrato no válido';
            }

            if (empty($tipoSeguimiento)) {
                $errores[] = 'Tipo de seguimiento es requerido';
            }

            if (empty($observaciones)) {
                $errores[] = 'Las observaciones son requeridas';
            }

            // Procesar evidencia
            $rutaEvidencia = null;
            if (isset($_FILES['evidenciaFile']) && $_FILES['evidenciaFile']['error'] === UPLOAD_ERR_OK) {
                $rutaEvidencia = $this->guardarEvidencia($_FILES['evidenciaFile']);
                if (!$rutaEvidencia) {
                    $errores[] = 'Error al subir la evidencia';
                }
            } else {
                $errores[] = 'La evidencia es requerida';
            }

            if (!empty($errores)) {
                $message = implode('<br>', $errores);
                if ($isAjax) {
                    echo json_encode(['success' => false, 'message' => $message]);
                    return;
                } else {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => $message];
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/creditos'));
                    exit;
                }
            }

            // Registrar seguimiento
            $seguimientoData = [
                'idcontrato' => $idContrato,
                'tipo' => $tipoSeguimiento,
                'observaciones' => $observaciones,
                'evidencia' => $rutaEvidencia,
                'fecha_seguimiento' => date('Y-m-d H:i:s'),
                'usuario_registro' => $_SESSION['user']['id'] ?? null
            ];

            $idSeguimiento = $this->creditoModel->registrarSeguimiento($seguimientoData);

            if ($idSeguimiento > 0) {
                if ($isAjax) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Seguimiento registrado correctamente',
                        'id' => $idSeguimiento
                    ]);
                    return;
                } else {
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Seguimiento registrado correctamente'];
                    header('Location: /creditos');
                    exit;
                }
            } else {
                if ($isAjax) {
                    echo json_encode(['success' => false, 'message' => 'Error al registrar el seguimiento']);
                    return;
                } else {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Error al registrar el seguimiento'];
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/creditos'));
                    exit;
                }
            }

        } catch (\Throwable $th) {
            error_log($th->getMessage());
            if ($isAjax) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error inesperado del servidor']);
                return;
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Error inesperado del servidor'];
                header('Location: /creditos');
                exit;
            }
        }
    }

    /**
     * Página de historial de seguimientos de un contrato
     * 
     * Renderiza la vista con el historial completo de todas las gestiones
     * de cobranza realizadas sobre un contrato específico. Incluye datos
     * del cliente y línea temporal de seguimientos ordenados cronológicamente.
     * 
     * Permite auditar la gestión realizada, evaluar efectividad de
     * estrategias, y documentar acciones ante posibles procesos legales.
     * 
     * @param int $idContrato ID del contrato a consultar
     * @return void
     */
    public function verHistorial(int $idContrato): void
    {
        $this->authRequired();
        $idContrato = (int) $idContrato;

        $historial = $this->creditoModel->getHistorialSeguimientos($idContrato);
        $cliente = $this->creditoModel->getClienteByContrato($idContrato);

        $this->view('creditos.historial', [
            'historial' => $historial,
            'cliente' => $cliente
        ]);
    }

    /**
     * Guarda archivo de evidencia de seguimiento
     * 
     * Método privado que procesa y almacena archivos de evidencia
     * (fotos, grabaciones, documentos) con validaciones de seguridad:
     * extensiones permitidas, tamaño máximo, y nombre único para
     * evitar sobrescrituras.
     * 
     * @param array $archivo Array de información del archivo $_FILES
     * @return string|null Ruta relativa del archivo guardado (ej: 'seguimientos/seguimiento_abc123.jpg'),
     *                     o null si falla la validación o subida
     */
    private function guardarEvidencia(array $archivo): ?string
    {
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'pdf'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            return null;
        }

        if ($archivo['size'] > 5 * 1024 * 1024) { // 5MB máximo
            return null;
        }

        $nombreArchivo = 'seguimiento_' . uniqid() . '.' . $extension;
        $directorioDestino = __DIR__ . '/../../storage/seguimientos/';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return 'seguimientos/' . $nombreArchivo;
        }

        return null;
    }

}