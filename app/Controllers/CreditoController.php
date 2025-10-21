<?php
// app/Controllers/CreditoController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Credito;

class CreditoController extends Controller
{
    private Credito $creditoModel;

    public function __construct()
    {
        $this->creditoModel = new Credito();
    }

    /**
     * Vista principal
     */
    public function index(): void
    {
        $this->authRequired();
        $this->view('creditos.index', []);
    }

    /**
     * API: Obtener estadísticas
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
     * API: Obtener morosos clasificados
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
     * Registrar seguimiento
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
     * Ver historial de seguimientos
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
     * Guardar evidencia
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