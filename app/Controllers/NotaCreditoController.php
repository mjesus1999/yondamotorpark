<?php

namespace App\Controllers;

use App\Config\Credencialesnubefact;
use App\Core\Controller;
use App\Models\Caja;
use App\Models\NotaCredito;
use Exception;

class NotaCreditoController extends Controller
{
    private NotaCredito $notaCreditoModel;
    private Caja $cajaModel;

    public function __construct()
    {
        $this->notaCreditoModel = new NotaCredito();
        $this->cajaModel = new Caja();
    }

    /**
     * GET /api/nota-credito/pago/{id} — datos para el modal (solo lectura).
     */
    public function apiInfoPago(int $idPago): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        if (!$this->notaCreditoModel->tablaExiste()) {
            http_response_code(503);
            echo json_encode([
                'success' => false,
                'message' => 'Ejecute el script SQL HOSTINGER_notas_credito_2026.sql en la base de datos.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ctx = $this->notaCreditoModel->getContextoPago($idPago);
        if (!$ctx) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Pago no encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $doc = NotaCredito::resolverDocumentoAfectado($ctx);
        if (!$doc) {
            echo json_encode([
                'success' => false,
                'message' => 'Este pago no tiene comprobante electrónico (boleta/factura) para anular.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($this->notaCreditoModel->existeNotaEmitidaParaPago($idPago)) {
            echo json_encode([
                'success' => false,
                'message' => 'Ya existe una nota de crédito emitida para este pago.',
                'ya_tiene_nc' => true,
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $tipoDoc = (int) $doc['tipo'];
        $serieNc = $tipoDoc === 1
            ? Credencialesnubefact::getSerieNcFactura()
            : Credencialesnubefact::getSerieNcBoleta();

        echo json_encode([
            'success' => true,
            'data' => [
                'idpago' => $idPago,
                'idcontrato' => (int) ($ctx['idcontrato'] ?? 0),
                'numcuota' => (int) ($ctx['numcuota'] ?? 0),
                'amortizacion' => (float) $ctx['amortizacion'],
                'cliente' => $ctx['razon_social'],
                'nrodoc' => $ctx['nrodoc'],
                'documento_afectado' => [
                    'tipo' => $doc['tipo'],
                    'tipo_label' => $doc['tipo'] === 1 ? 'Factura' : 'Boleta',
                    'serie' => $doc['serie'],
                    'numero' => $doc['numero'],
                ],
                'serie_nc' => $serieNc,
            ],
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /api/nota-credito/emitir
     * body: idpago, tipo_nota_credito (opcional, default 01), motivo (opcional)
     */
    public function apiEmitir(): void
    {
        $this->authRequired();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if (!$this->notaCreditoModel->tablaExiste()) {
            http_response_code(503);
            echo json_encode([
                'success' => false,
                'message' => 'Ejecute el script SQL HOSTINGER_notas_credito_2026.sql en la base de datos.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $idPago = (int) ($_POST['idpago'] ?? 0);
        $tipoNota = trim((string) ($_POST['tipo_nota_credito'] ?? '01'));
        $motivo = trim((string) ($_POST['motivo'] ?? ''));

        if ($idPago <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'idpago inválido.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $tiposPermitidos = ['01', '06', '07'];
        if (!in_array($tipoNota, $tiposPermitidos, true)) {
            $tipoNota = '01';
        }

        if ($this->notaCreditoModel->existeNotaEmitidaParaPago($idPago)) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Ya existe una nota de crédito para este pago.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ctx = $this->notaCreditoModel->getContextoPago($idPago);
        if (!$ctx) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Pago no encontrado.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $doc = NotaCredito::resolverDocumentoAfectado($ctx);
        if (!$doc) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Sin comprobante electrónico asociado.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $montoTotal = round((float) $ctx['amortizacion'], 2);
        if ($montoTotal <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Monto del pago inválido para nota de crédito.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $tipoDocOriginal = (int) $doc['tipo'];
        $serieNc = $tipoDocOriginal === 1
            ? Credencialesnubefact::getSerieNcFactura()
            : Credencialesnubefact::getSerieNcBoleta();

        $factorIgv = 1.18;
        $valorUnitario = round($montoTotal / $factorIgv, 2);
        $igv = round($montoTotal - $valorUnitario, 2);
        $totalItem = round($valorUnitario + $igv, 2);

        $descCuota = 'Cuota ' . (int) ($ctx['numcuota'] ?? 0);
        if (trim((string) ($ctx['tipo_pago'] ?? '')) === 'Penalidad') {
            $descCuota = 'Penalidad / Mora';
        }

        $items = [[
            'unidad_de_medida' => 'ZZ',
            'descripcion' => $descCuota . ' — Nota de crédito',
            'cantidad' => 1,
            'valor_unitario' => $valorUnitario,
            'precio_unitario' => $totalItem,
            'subtotal' => $valorUnitario,
            'tipo_de_igv' => 1,
            'igv' => $igv,
            'total' => $totalItem,
        ]];

        $nrodoc = preg_replace('/\D+/', '', (string) ($ctx['nrodoc'] ?? ''));
        $tipoDocCliente = strlen($nrodoc) === 11 ? 6 : 1;

        $datosCliente = [
            'tipo_documento' => $tipoDocCliente,
            'numero_documento' => $nrodoc,
            'denominacion' => trim((string) ($ctx['razon_social'] ?? 'CLIENTE')) ?: 'CLIENTE',
            'direccion' => trim((string) ($ctx['direccion'] ?? '')) ?: '-',
            'email' => trim((string) ($ctx['email'] ?? '')),
        ];

        $obs = $motivo !== '' ? $motivo : 'Anulación de comprobante por pago #' . $idPago;

        $user = $_SESSION['user'] ?? [];
        $idColaborador = isset($user['idcolaborador']) ? (int) $user['idcolaborador'] : null;

        try {
            $nuevoNumero = $this->cajaModel->obtenerSiguienteCorrelativoBloqueado($serieNc);
            if (!$nuevoNumero) {
                throw new Exception('No se pudo obtener correlativo para la nota de crédito.');
            }

            $nubefact = new ComprobanteNubefactController();
            $resp = $nubefact->emitirNotaCredito([
                'serie' => $serieNc,
                'numero_comprobante' => $nuevoNumero,
                'mediopago' => (string) ($ctx['mediopago'] ?? ''),
                'items' => $items,
                'totales' => [
                    'total_gravada' => $valorUnitario,
                    'total_exonerada' => 0,
                    'total_igv' => $igv,
                    'total_venta' => $totalItem,
                ],
                'datos_cliente' => $datosCliente,
                'documento_modifica_tipo' => $tipoDocOriginal,
                'documento_modifica_serie' => $doc['serie'],
                'documento_modifica_numero' => $doc['numero'],
                'tipo_nota_credito' => $tipoNota,
                'observaciones' => $obs,
                'enviar_al_cliente' => false,
            ]);

            if (empty($resp['success'])) {
                $this->cajaModel->cancelarCorrelativoBloqueado();
                $this->notaCreditoModel->registrar([
                    'idpago' => $idPago,
                    'idcontrato' => (int) ($ctx['idcontrato'] ?? 0),
                    'idcolaborador' => $idColaborador,
                    'tipo_nota_credito' => $tipoNota,
                    'documento_modifica_tipo' => $tipoDocOriginal,
                    'documento_modifica_serie' => $doc['serie'],
                    'documento_modifica_numero' => $doc['numero'],
                    'nc_serie' => $serieNc,
                    'nc_numero' => (int) $nuevoNumero,
                    'total' => $montoTotal,
                    'motivo' => $motivo,
                    'estado' => 'ERROR',
                    'mensaje_error' => $resp['message'] ?? 'Error Nubefact',
                ]);

                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => $resp['message'] ?? 'No se pudo emitir la nota de crédito.',
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $this->cajaModel->confirmarCorrelativoBloqueado($serieNc, (int) $nuevoNumero);

            $idNota = $this->notaCreditoModel->registrar([
                'idpago' => $idPago,
                'idcontrato' => (int) ($ctx['idcontrato'] ?? 0),
                'idcolaborador' => $idColaborador,
                'tipo_nota_credito' => $tipoNota,
                'documento_modifica_tipo' => $tipoDocOriginal,
                'documento_modifica_serie' => $doc['serie'],
                'documento_modifica_numero' => $doc['numero'],
                'nc_serie' => $serieNc,
                'nc_numero' => (int) $nuevoNumero,
                'total' => $montoTotal,
                'motivo' => $motivo,
                'enlace_pdf' => $resp['enlace_pdf'] ?? null,
                'enlace_xml' => $resp['enlace_xml'] ?? null,
                'enlace_cdr' => $resp['enlace_cdr'] ?? null,
                'estado' => 'EMITIDA',
            ]);

            $idContrato = (int) ($ctx['idcontrato'] ?? 0);
            if ($idContrato > 0) {
                $this->limpiarCacheHistorialContrato($idContrato);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Nota de crédito emitida correctamente.',
                'idnota_credito' => $idNota,
                'nc_serie' => $serieNc,
                'nc_numero' => (int) $nuevoNumero,
                'enlace_pdf' => $resp['enlace_pdf'] ?? null,
                'enlace_xml' => $resp['enlace_xml'] ?? null,
                'enlace_cdr' => $resp['enlace_cdr'] ?? null,
                'status_sunat' => $resp['status'] ?? 'PENDIENTE',
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $this->cajaModel->cancelarCorrelativoBloqueado();
            error_log('NotaCreditoController::apiEmitir: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    private function limpiarCacheHistorialContrato(int $idContrato): void
    {
        $cacheFile = __DIR__ . "/../../storage/cache/historial-pagos-contratos/historial-pagos-contrato{$idContrato}.json";
        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }
    }
}
