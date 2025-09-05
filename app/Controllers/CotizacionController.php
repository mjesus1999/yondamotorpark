<?php
//app/controllers/CotizacionController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cotizacion;
use App\Models\Vehiculo;
use App\Models\FormatoCotizacion;

class CotizacionController extends Controller
{
    private Cotizacion $cotizacionModel;
    private Vehiculo $vehiculoModel;
    private FormatoCotizacion $formatoModel;

    public function __construct()
    {
        $this->cotizacionModel = new Cotizacion();
        $this->vehiculoModel = new Vehiculo();
        $this->formatoModel = new FormatoCotizacion();
    }

    public function index(): void
    {
        $this->authRequired();

        // Obtener información del usuario logueado
        $idasesor = $_SESSION['user']['id'] ?? null;
        $idcargo = $_SESSION['user']['idcargo'] ?? null;

        if (!$idasesor || !$idcargo) {
            $_SESSION['error_message'] = "No se pudo identificar al usuario.";
            header('Location: /login');
            exit;
        }

        // Definir cargos que pueden ver todas las cotizaciones (supervisores/jefes)
        $cargosSupervisores = [
            1,  // Jefe de sistemas
            8,  // Jefe de Logística
            10, // Jefe de Recursos Humanos
            13, // Jefe de Contabilidad
            14, // Jefe de Marketing
            16, // Jefe de Ventas
            17  // Jefe de Caja
        ];

        // Verificar si el usuario puede ver todas las cotizaciones o solo las suyas
        if (in_array($idcargo, $cargosSupervisores)) {
            // Supervisores/Jefes ven todas las cotizaciones
            $cotizaciones = $this->cotizacionModel->getAll();
        } else {
            // Asesores y otros cargos ven solo sus cotizaciones
            $cotizaciones = $this->cotizacionModel->getAllByAsesor($idasesor);
        }
        error_log('COTIZACIONES: ' . json_encode($cotizaciones));

        $this->view("cotizacion.index", [
            'cotizaciones' => $cotizaciones,
            'puede_ver_todas' => in_array($idcargo, $cargosSupervisores)
        ]);
    }

    public function html2pdfReport($id): void
    {
        $this->authRequired();

        // Obtener información del usuario logueado
        $idasesor = $_SESSION['user']['id'] ?? null;
        $idcargo = $_SESSION['user']['idcargo'] ?? null;
        $cotizacion = $this->cotizacionModel->getById($id);

        if (!$cotizacion) {
            http_response_code(404);
            $_SESSION['error_message'] = "Cotización no encontrada.";
            header('Location: /cotizacion');
            exit;
        }

        // Definir cargos supervisores
        $cargosSupervisores = [1, 8, 10, 13, 14, 16, 17];

        // Verificar permisos: supervisores pueden ver cualquier cotización, asesores solo las suyas
        if (!in_array($idcargo, $cargosSupervisores) && $cotizacion['idasesor'] != $idasesor) {
            http_response_code(403);
            $_SESSION['error_message'] = "No tienes permisos para acceder a esta cotización.";
            header('Location: /cotizacion');
            exit;
        }

        $this->view('pdf/cotizacion/cotizacion-html2pdf', ['id' => $id]);
    }

    public function apiShow(int $idcotizacion): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $cot = $this->cotizacionModel->getById($idcotizacion);
        if (!$cot) {
            http_response_code(404);
            echo json_encode(['error' => 'Cotización no encontrada']);
            exit;
        }
        // obtener requisitos del formato (si existe idformato)
        $idformato = isset($cot['idformato']) ? (int) $cot['idformato'] : null;
        $requisitos = [];
        $tipocotizacion = null;
        if ($idformato) {
            $requisitos = $this->formatoModel->getDetalleRequisitos($idformato);
            // opcional: obtener el nombre del formato (tipocotizacion)
            $formatos = $this->formatoModel->getAll(); // getAll devuelve idformato y tipocotizacion
            foreach ($formatos as $f) {
                if ((int) $f['idformato'] === $idformato) {
                    $tipocotizacion = $f['tipocotizacion'];
                    break;
                }
            }
        }

        echo json_encode([
            'cotizacion' => [
                'idformato' => $idformato,
                'tipocotizacion' => $tipocotizacion,
                'fecha' => $cot['fechaRegistro'],
                'cliente' => [
                    'nombre' => $cot['cliente_nombre'],
                    'dni' => $cot['cliente_documento'],
                    'celular' => $cot['cliente_telefono'],
                ],
                'vehiculo' => [
                    'marca' => $cot['vehiculo_marca'],
                    'modelo' => $cot['vehiculo_modelo'],
                    'anio' => $cot['vehiculo_anio'],
                    'color' => $cot['vehiculo_color'],
                ],
                'precios' => [
                    'precio_usd' => number_format($cot['precioventa'], 2, '.', ''),
                    'inicial_soles' => number_format($cot['inicial'], 2, '.', ''),
                    'meses_24' => $cot['numcuotas'] == 24 ? $cot['valorcuota'] : null,
                    'meses_36' => $cot['numcuotas'] == 36 ? $cot['valorcuota'] : null,
                    'meses_48' => $cot['numcuotas'] == 48 ? $cot['valorcuota'] : null,
                    'meses_60' => $cot['numcuotas'] == 60 ? $cot['valorcuota'] : null,
                ],
                // incluimos requisitos aquí para que el front no haga otra petición
                'requisitos' => $requisitos
            ]
        ]);
        exit;
    }

    public function create(): void
    {
        $this->authRequired();
        $formatos = $this->formatoModel->getAll();
        $vehiculos = $this->vehiculoModel->getAll(['Libre', 'Proceso']);
        $this->view('cotizacion.create', [
            'vehiculos' => $vehiculos,
            'formatos' => $formatos
        ]);
    }

    public function requisitos(int $idformato): void
    {
        $this->authRequired();

        $detalle = $this->formatoModel->getDetalleRequisitos($idformato);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($detalle);
        exit;
    }

    public function buscarCliente(): void
    {
        $this->authRequired();

        $tipo = strtolower($_GET['tipo'] ?? '');
        $doc = trim($_GET['doc'] ?? '');

        if (!$tipo || !$doc) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros']);
            exit;
        }

        // Llamamos al único método que trae idcliente + datos
        $data = $this->cotizacionModel->getClienteByDoc($tipo, $doc);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data ?: ['notFound' => true]);
        exit;
    }


    public function store(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        $idasesor = $_SESSION['user']['id'] ?? null;
        if (!$idasesor) {
            $_SESSION['error'] = "No se encontro al usuario.";
            header('Location: /cotizacion/create');
            exit;
        }

        $input = [
            'idformato' => $_POST['modalidad'] ?? null,
            'idcliente' => $_POST['idcliente'] ?? null,
            'idvehiculo' => $_POST['idvehiculo'] ?? null,
            'moneda' => $_POST['moneda'] ?? 'PEN',
            'precioventa' => $_POST['precioventa'] ?? 0,
            'vigenciadias' => $_POST['vigenciadias'] ?? 7,
            'inicial' => $_POST['inicial'] ?? 0,
            'numcuotas' => $_POST['numcuotas'] ?? 0,
            'valorcuota' => $_POST['valorcuota'] ?? 0,
            'idasesor' => $idasesor,
        ];

        // Validar datos obligatorios
        if (!$input['idcliente'] || !$input['idvehiculo'] || !$input['idformato']) {
            $_SESSION['error'] = "Faltan datos obligatorios.";
            header('Location: /cotizacion/create');
            exit;
        }

        // Inserta la cotización
        try {
            $this->cotizacionModel->create($input);
            $_SESSION['success_message'] = "Cotización registrada correctamente.";
            header('Location: /cotizacion');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Error al registrar cotización: " . $e->getMessage();
            header('Location: /cotizacion/create');
            exit;
        }
    }

    public function tipoCambio(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/../Helpers/Api.php';
        $tc = obtenerTipoCambio();
        echo json_encode(['tipo_cambio' => $tc]);
        exit;
    }

    public function calcularPagoMensual(float $importeTotal, float $inicial, int $meses): void
    {
        header('Content-Type: application/json');
        $pagoMensual = $this->cotizacionModel->calcularPagoMensual($importeTotal, $inicial, $meses);
        echo json_encode(["pago_mensual" => $pagoMensual]);
        exit();
    }

    // Generar cronograma:
    public function generarCronograma(float $importeTotal, float $inicial, int $meses): void
    {
        header('Content-Type: application/json');
        $cronograma = $this->cotizacionModel->generarCronograma($importeTotal, $inicial, $meses);
        echo json_encode($cronograma);
        exit();
    }
}
