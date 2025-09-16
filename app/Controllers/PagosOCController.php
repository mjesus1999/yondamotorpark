<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagosOC;
use Exception;
use PDOException;


class PagosOCController extends Controller
{
    private PagosOC $pagoOCModel;

    public function __construct()
    {
        $this->pagoOCModel = new PagosOC();
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido',
                'id'      => 0
            ]);
            exit;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idorden'       => (int)($data['idorden'] ?? 0),
            'identidadpago' => (int)($data['identidadpago'] ?? 0),
            'fecharealpago' => $data['fecharealpago'] ?? null,
            'numtransaccion' => empty($data['numtransaccion']) ? null : $data['numtransaccion'],
            'moneda'        => $data['moneda'] ?? 'USD',
            'tipocambio'    => empty($data['tipocambio']) ? null : (float)$data['tipocambio'],
            'valorUSD'    => empty($data['valorUSD']) ? null : (float)$data['valorUSD'],
            'amortizacion'  => (float)($data['amortizacion'] ?? 0),
            'comprobante'   => '',
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
                'id'      => 0
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
                    'id'      => 0
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
                'id'      => $idPago
            ]);
        } catch (PDOException $e) {
            // Captura errores del trigger o SQL
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error en BD: ' . $e->getMessage(),
                'id'      => 0
            ]);
        } catch (Exception $e) {
            // Otros errores
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
                'id'      => 0
            ]);
        }
    }
}
