<?php
//app/controllers/CobranzaController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cobranza;
use Exception;
use PDOException;

class CobranzaController extends Controller
{
    private Cobranza $cobranzaModel;

    public function __construct()
    {
        $this->cobranzaModel = new Cobranza();
    }

    public function index(): void
    {
        $this->authRequired();
        $this->view('cobranza.index');
    }


    /* OBTENER ESTADISTICAS */
    public function getEstadisticas(): void
    {
        $this->authRequired();

        header('Content-Type: application/json');

        try {
            $data = $this->cobranzaModel->getEstadisticasContratos();
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ]);
        }
    }

    /* OBTENER TARJETAS */
    public function getTarjetas(): void
    {
        $this->authRequired();

        header('Content-Type: application/json');

        try {
            $tarjetas = $this->cobranzaModel->getTarjetasCobranza();
            echo json_encode([
                'success' => true,
                'data' => $tarjetas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener tarjetas'
            ]);
        }
    }

    /* OBTENER INFORMACIÓN DEL CLIENTE */
    public function getInfoCliente($idContrato): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getInfoCliente($idContrato);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener información del cliente'
            ]);
        }
    }

    /* OBTENER DETALLE DEL CONTRATO */
    public function getDetalleContrato($idContrato): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getDetalleContrato($idContrato);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener detalle del contrato'
            ]);
        }
    }

    /* OBTENER CRONOGRAMA DE PAGOS */
    public function getCronogramaPagos($idContrato): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getCronogramaPagos($idContrato);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener cronograma de pagos'
            ]);
        }
    }

    /* OBTENER RESUMEN FINANCIERO */
    /* public function getResumenFinanciero($idContrato): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getResumenFinanciero($idContrato);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener resumen financiero'
            ]);
        }
    } */

    /* OBTENER HISTORIAL DE PAGOS */
    public function getHistorialPagos($idContrato, $limite = 5): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $this->cobranzaModel->getHistorialPagos($idContrato, $limite);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener historial de pagos'
            ]);
        }
    }

    public function getClientesNotificar(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $clientes = $this->cobranzaModel->getClientesNotificar();
            echo json_encode([
                'success' => true,
                'data' => $clientes
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage()
            ]);
        }
    }

    public function indexNotificar()
    {
        $this->authRequired();
        $this->view('cobranza.indexNotificar');
    }

    public function indexVencidos()
    {
        $this->authRequired();

        $vencidos = $this->cobranzaModel->getCuotasVencidas();
        $this->view('cobranza.indexVencidos', ['vencidos' => $vencidos]);
    }

    //AVANCE DE LOS REPORTES
    /* public function reporteCobranzaAtrasado()
    {
        $this->authRequired();

        // Obtener el idcontrato del parámetro GET
        $idContrato = $_GET['contrato'] ?? null;

        if (!$idContrato) {
            // Si no hay contrato, redirigir o mostrar error
            header('Location: /Cobranza/vencidos');
            exit;
        }

        // Obtener los datos del reporte
        $datosReporte = $this->cobranzaModel->getReporteNotificar($idContrato);

        if (empty($datosReporte)) {
            // Manejar caso donde no se encuentran datos
            echo "No se encontraron datos para el contrato especificado.";
            exit;
        }

        // Pasar los datos a la vista
        $this->view('cobranza/reports.reporte_atraso_01_mes', [
            'datos' => $datosReporte
        ]);
    } */

    public function reporteCobranzaAtrasado()
    {
        $this->authRequired();

        $this->view('cobranza/reports.reporte_atraso_01_mes');
    }

    public function reporteCobranzaAtrasado2()
    {
        $this->authRequired();
        $this->view('cobranza/reports.reporte_atraso_01_mes2');
    }

    public function reporteRecojoVehicular()
    {
        $this->authRequired();
        $this->view('cobranza/reports.reporte-constancia-recojo');
    }

    public function enviarSmsNotificacion()
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $idContrato = $data['idcontrato'] ?? null;
            $telefono = $data['telefono'] ?? null;
            $nombreCliente = $data['cliente'] ?? null;
            $montoCuota = $data['monto_cuota'] ?? null;
            $fechaVencimiento = $data['fecha_vencimiento'] ?? null;

            if (!$telefono || !$nombreCliente || !$montoCuota || !$fechaVencimiento) {
                throw new Exception('Datos incompletos para enviar SMS');
            }

            $resultado = $this->cobranzaModel->enviarSmsNotificacion(
                $idContrato,
                $telefono,
                $nombreCliente,
                $montoCuota,
                $fechaVencimiento
            );

            echo json_encode($resultado);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al enviar SMS: ' . $e->getMessage()
            ]);
        }

    }

    public function actualizarTelefono(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $idContrato = $data['idcontrato'] ?? null;
            $telefonoActual = $data['telefono_actual'] ?? null;
            $telefonoNuevo = $data['telefono_nuevo'] ?? null;

            if (!$idContrato || !$telefonoActual || !$telefonoNuevo) {
                throw new Exception('Datos incompletos');
            }

            $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefonoNuevo);
            if (strlen($telefonoLimpio) < 9 || strlen($telefonoLimpio) > 15) {
                throw new Exception('El teléfono debe tener entre 9 y 15 dígitos');
            }

            $resultado = $this->cobranzaModel->actualizarTelefonoCliente(
                $idContrato,
                $telefonoActual,
                $telefonoLimpio
            );

            echo json_encode($resultado);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    //AVANCE DE LOS REPORTES DE ATRASO
    /* public function getDatosReporteNotificacion(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $idContrato = $_GET['contrato'] ?? null;

            if (!$idContrato) {
                throw new Exception('ID de contrato no proporcionado');
            }

            $datos = $this->cobranzaModel->getReporteNotificacion($idContrato);

            if (!$datos) {
                throw new Exception('No se encontraron datos para el contrato');
            }

            echo json_encode([
                'success' => true,
                'data' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos del reporte: ' . $e->getMessage()
            ]);
        }
    } */

    public function getDatosReporteNotificacion(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        try {
            $idContrato = $_GET['contrato'] ?? null;

            if (!$idContrato) {
                throw new Exception('ID de contrato no proporcionado');
            }

            $datos = $this->cobranzaModel->getReporteNotificacion($idContrato);

            if (!$datos) {
                throw new Exception('No se encontraron datos para el contrato');
            }

            echo json_encode([
                'success' => true,
                'data' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos del reporte: ' . $e->getMessage()
            ]);
        }
    }

}