<?php

/**
 * Controlador de Pagos de Orden de Compra
 * 
 * app/Controllers/PagosOCController.php
 * 
 * Gestiona el registro de pagos para órdenes de compra de vehículos,
 * incluyendo validaciones de moneda, tipo de cambio, manejo de archivos
 * de comprobantes y cálculo de amortizaciones.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagosOC;
use Exception;
use PDOException;

/**
 * Clase PagosOCController
 * 
 * Controlador para la gestión de pagos de órdenes de compra.
 * Proporciona endpoints para registrar pagos con soporte de múltiples monedas,
 * conversión de tipo de cambio y almacenamiento de comprobantes.
 */
class PagosOCController extends Controller
{

    /**
     * Modelo de PagoOC
     * @var PagosOC
     */
    private PagosOC $pagoOCModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa el modelo de pagoOC necesario para las operaciones del controlador
     */
    public function __construct()
    {
        $this->pagoOCModel = new PagosOC();
    }

    /**
     * Registra un nuevo pago de orden de compra
     * 
     * Endpoint AJAX que procesa el registro de un pago para una orden de compra.
     * Maneja pagos en USD y PEN con conversión de tipo de cambio cuando aplica.
     * Requiere archivo de comprobante obligatorio y valida campos según la moneda.
     * Solo acepta peticiones POST y requiere autenticación.
     * 
     * Validaciones realizadas:
     * - Campos obligatorios: orden, monto, fecha de pago, entidad de pago, número de transacción, comprobante
     * - Tipo de cambio: obligatorio y mayor a 0 solo para pagos en SOLES (PEN)
     * - Archivo de comprobante: obligatorio, se almacena con nombre único
     * 
     * 1. Valida datos del formulario
     * 2. Procesa y guarda el archivo de comprobante
     * 3. Registra el pago en la base de datos
     * 4. Maneja errores de triggers de BD
     * 
     * @return void
     */
    public function store(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido',
                'id' => 0
            ]);
            exit;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idorden' => (int) ($data['idorden'] ?? 0),
            'identidadpago' => (int) ($data['identidadpago'] ?? 0),
            'fecharealpago' => $data['fecharealpago'] ?? null,
            'numtransaccion' => empty($data['numtransaccion']) ? null : $data['numtransaccion'],
            'moneda' => $data['moneda'] ?? 'USD',
            'tipocambio' => empty($data['tipocambio']) ? null : (float) $data['tipocambio'],
            'valorUSD' => empty($data['valorUSD']) ? null : (float) $data['valorUSD'],
            'amortizacion' => (float) ($data['amortizacion'] ?? 0),
            'comprobante' => '',
            'observaciones' => empty($data['observaciones']) ? null : $data['observaciones'],
        ];

        // Validaciones
        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['idorden'], 'Orden');
        $errores[] = Validador::campoObligatorio($registro['amortizacion'], 'Monto a pagar');
        $errores[] = Validador::campoObligatorio($registro['fecharealpago'], 'Fecha de pago');
        $errores[] = Validador::campoObligatorio($registro['identidadpago'], 'Entidad de pago');
        $errores[] = Validador::campoObligatorio($registro['numtransaccion'], 'Número de transacción');

        if (empty($_FILES['comprobante']['name'])) {
            $errores[] = 'El comprobante es obligatorio.';
        }

        if ($registro['moneda'] === 'PEN') {
            if (empty($registro['tipocambio']) || !is_numeric($registro['tipocambio']) || $registro['tipocambio'] <= 0) {
                $errores[] = 'El tipo de cambio es obligatorio y debe ser mayor a 0 para pagos en SOLES.';
            }
        }

        $errores = array_filter($errores);
        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        // Manejo de comprobante
        if (!empty($_FILES['comprobante']['name'])) {
            $nombreArchivo = uniqid('comprobante_') . '_' . basename($_FILES['comprobante']['name']);
            $directorioDestino = __DIR__ . '/../../storage/comprobantes/';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            $rutaCompleta = $directorioDestino . $nombreArchivo;

            if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaCompleta)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo guardar el comprobante',
                    'id' => 0
                ]);
                exit;
            }

            $registro['comprobante'] = "comprobantes/{$nombreArchivo}";
        }

        // Guardar en la BD con try/catch
        try {
            $idPago = $this->pagoOCModel->create($registro);

            echo json_encode([
                'success' => $idPago > 0,
                'message' => $idPago > 0 ? '¡Pago registrado correctamente!' : 'No se pudo registrar el pago',
                'id' => $idPago
            ]);
        } catch (PDOException $e) {
            // Captura errores del trigger o SQL
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error en BD: ' . $e->getMessage(),
                'id' => 0
            ]);
        } catch (Exception $e) {
            // Otros errores
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
                'id' => 0
            ]);
        }
    }

}
