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
        $puedeVerTodas = in_array($idcargo, $cargosSupervisores);

        if ($puedeVerTodas) {
            // Supervisores/Jefes ven todas las cotizaciones con información del asesor
            $cotizaciones = $this->cotizacionModel->getAll();

            // Log para debugging (opcional)
            error_log('USUARIO SUPERVISOR - Puede ver todas las cotizaciones');
            error_log('COTIZACIONES CARGADAS: ' . count($cotizaciones));
        } else {
            // Asesores y otros cargos ven solo sus cotizaciones
            $cotizaciones = $this->cotizacionModel->getAllByAsesor($idasesor);

            // Log para debugging (opcional)
            error_log('USUARIO ASESOR - Solo ve sus cotizaciones');
            error_log('COTIZACIONES DEL ASESOR ' . $idasesor . ': ' . count($cotizaciones));
        }

        $this->view("cotizacion.index", [
            'cotizaciones' => $cotizaciones,
            'puede_ver_todas' => $puedeVerTodas,
            'usuario_actual' => [
                'id' => $idasesor,
                'cargo' => $idcargo,
                'es_supervisor' => $puedeVerTodas
            ]
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

    // En CotizacionController.php - Método apiShow actualizado
    public function apiShow(int $idcotizacion): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $cot = $this->cotizacionModel->getById($idcotizacion);
        if (!$cot) {
            http_response_code(404);
            echo json_encode(['error' => 'Cotización no encontrada']);
            exit;
        }

        $idformato = isset($cot['idformato']) ? (int) $cot['idformato'] : null;
        $requisitos = $idformato ? $this->formatoModel->getDetalleRequisitos($idformato) : [];
        $financiamientos = $this->cotizacionModel->getFinanciamientos($idcotizacion);

        echo json_encode([
            'cotizacion' => [
                'idformato' => $idformato,
                'tipocotizacion' => $cot['tipocotizacion'] ?? null,
                'fecha' => $cot['fechaRegistro'] ?? null,
                'cliente' => [
                    'nombre' => $cot['cliente_nombre'] ?? null,
                    'dni' => $cot['cliente_documento'] ?? null,
                    'celular' => $cot['cliente_telefono'] ?? null,
                ],
                'vehiculo' => [
                    'marca' => $cot['vehiculo_marca'] ?? null,
                    'modelo' => $cot['vehiculo_modelo'] ?? null,
                    'anio' => $cot['vehiculo_anio'] ?? null,
                    'color' => $cot['vehiculo_color'] ?? null,
                ],
                'precios' => [
                    'precio_usd' => number_format($cot['precioventa'] ?? 0, 2, '.', ''),
                    'inicial_soles' => number_format($cot['inicial'] ?? 0, 2, '.', ''),
                ],
                // NUEVA SECCIÓN: INFORMACIÓN DEL ASESOR
                'asesor' => [
                    'nombre' => $cot['asesor_nombre'] ?? 'CHARLY YACTAYO ORTIZ',
                    'nombre_completo' => $cot['asesor_nombre_completo'] ?? 'CHARLY YACTAYO ORTIZ',
                    'cargo' => $cot['asesor_cargo'] ?? 'Ejecutivo de Ventas',
                    'telefono' => $cot['asesor_telefono'] ?? '934 008 037',
                    'telefono_alt' => $cot['asesor_telefono_alt'] ?? null,
                    'usuario' => $cot['asesor_usuario'] ?? null
                ],
                'requisitos' => $requisitos,
                'opciones_financiamiento' => $financiamientos
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
        /* echo json_encode($data ?: ['notFound' => true]);
        exit; */

        if ($data) {
            echo json_encode($data);
        } else {
            //Agregar parámetro para identificar origen
            echo json_encode([
                'notFound' => true,
                'documento' => $doc,
                'tipo' => $tipo
            ]);
        }
        exit;
    }

    public function store(): void
    {
        $this->authRequired();

        $idasesor = $_SESSION['user']['id'] ?? null;
        if (!$idasesor) {
            $_SESSION['error'] = "No se encontro al usuario.";
            header('Location: /cotizacion/create');
            exit;
        }

        // Decodificar las opciones de financiamiento para obtener la inicial
        $opcionesJson = $_POST['opciones_financiamiento'] ?? '[]';
        $opciones = json_decode($opcionesJson, true);
        if (!is_array($opciones))
            $opciones = [];

        // Obtener la inicial de la primera opción (ya que todas deben tener la misma inicial)
        $inicialPrincipal = 0;
        if (!empty($opciones)) {
            $inicialPrincipal = $opciones[0]['inicial'] ?? 0;
        }

        $input = [
            'idformato' => $_POST['modalidad'] ?? null,
            'idcliente' => $_POST['idcliente'] ?? null,
            'idvehiculo' => $_POST['idvehiculo'] ?? null,
            'moneda' => $_POST['moneda'] ?? 'PEN',
            'precioventa' => $_POST['precioventa'] ?? 0,
            'vigenciadias' => $_POST['vigenciadias'] ?? 7,
            'inicial' => $inicialPrincipal, // USAR LA INICIAL DE LAS OPCIONES
            'numcuotas' => $_POST['numcuotas'] ?? 0,
            'valorcuota' => $_POST['valorcuota'] ?? 0,
            'idasesor' => $idasesor,
        ];

        if (!$input['idcliente'] || !$input['idvehiculo'] || !$input['idformato']) {
            $_SESSION['error'] = "Faltan datos obligatorios.";
            header('Location: /cotizacion/create');
            exit;
        }

        try {
            // insertar cotización y obtener id
            $idcot = $this->cotizacionModel->create($input);

            // insertar cada opción
            foreach ($opciones as $opt) {
                $numcuotas = (int) ($opt['numcuotas'] ?? 0);
                $valorcuota = (float) ($opt['valorcuota'] ?? 0);
                $inicial = (float) ($opt['inicial'] ?? $input['inicial']);
                $moneda = $input['moneda'];
                $precioventa = (float) ($opt['precioventa'] ?? $input['precioventa']);

                if ($numcuotas > 0 && $valorcuota >= 0) {
                    $this->cotizacionModel->createFinanciamiento($idcot, $numcuotas, $inicial, $valorcuota, $moneda, $precioventa);
                }
            }

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


    //NUEVAS FUNCIONES (BUSCA EL DNI DEL ULTIMO CLIENTE (GET) Y LLEVA A UNA COTIZACION (POST))
    /**
     * API para obtener el último cliente registrado
     */
    public function ultimoClienteRegistrado(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $ultimoCliente = $_SESSION['ultimo_cliente_registrado'] ?? null;

        if ($ultimoCliente) {
            // Verificar si el registro es reciente (menos de 30 minutos)
            $tiempoTranscurrido = time() - $ultimoCliente['timestamp'];

            if ($tiempoTranscurrido < 1800) { // 30 minutos = 1800 segundos
                echo json_encode([
                    'success' => true,
                    'cliente' => $ultimoCliente
                ]);
            } else {
                // Si es muy antiguo, eliminar de sesión
                unset($_SESSION['ultimo_cliente_registrado']);
                echo json_encode([
                    'success' => false,
                    'message' => 'No hay cliente reciente'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No hay cliente reciente'
            ]);
        }
        exit;
    }

    /**
     * API para limpiar el último cliente registrado de la sesión
     */
    public function limpiarUltimoCliente(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (isset($_SESSION['ultimo_cliente_registrado'])) {
            unset($_SESSION['ultimo_cliente_registrado']);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Sesión limpiada correctamente'
        ]);
        exit;
    }

}
