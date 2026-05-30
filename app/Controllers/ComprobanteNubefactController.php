<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Config\Credencialesnubefact;
use App\Helpers\NubefactApiHelper;
use Exception;

class ComprobanteNubefactController extends Controller
{
    private NubefactApiHelper $nubefactModel;

    public function __construct()
    {
        date_default_timezone_set('America/Lima');
        $this->nubefactModel = new NubefactApiHelper(
            Credencialesnubefact::getRuta(),
            Credencialesnubefact::getToken()
        );
    }

    /**
     * API REST: emite comprobante vía Nubefact (JSON en body o POST).
     */
    public function apiEmitirComprobante(): void
    {
        $this->authRequired();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $raw = file_get_contents('php://input');
        $datos = json_decode($raw, true);
        if (!is_array($datos)) {
            $datos = $_POST;
        }

        echo json_encode($this->procesarPagoYEmitirComprobante($datos));
    }

    /**
     * Método Genérico para emitir cualquier comprobante
     */
    public function procesarPagoYEmitirComprobante(array $datos)
    {
        $fechaHoy = date('d-m-Y');


        $campos_requeridos = [
            'tipo_comprobante', // 1=Factura, 2=Boleta
            'serie',
            'numero_comprobante',
            'datos_cliente',
            'items',
            'totales'
        ];

        foreach ($campos_requeridos as $campo) {
            if (!isset($datos[$campo]) || (empty($datos[$campo]) && $datos[$campo] !== 0.0 && $datos[$campo] !== 0 && $datos[$campo] !== [])) {
                return ['success' => false, 'message' => "Falta el parámetro de entrada o está vacío: $campo."];
            }
        }

        try {
            $tipo_comprobante = (int) $datos['tipo_comprobante'];
            $serie = (string) $datos['serie'];
            $numero_comprobante = (int) $datos['numero_comprobante'];


            $totales = $datos['totales'];
            $total_gravada = $totales['total_gravada'] ?? 0.00;
            $total_inafecta = $totales['total_inafecta'] ?? 0.00;
            $total_exonerada = $totales['total_exonerada'] ?? 0.00;
            $total_igv = $totales['total_igv'] ?? 0.00;
            $total_final = $totales['total_venta'];

            // Preparar datos del cliente
            $cliente = [
                "cliente_tipo_de_documento" => $datos['datos_cliente']['tipo_documento'] ?? 1,
                "cliente_numero_de_documento" => $datos['datos_cliente']['numero_documento'],
                "cliente_denominacion" => $datos['datos_cliente']['denominacion'],
                "cliente_direccion" => $datos['datos_cliente']['direccion'],
                "cliente_email" => $datos['datos_cliente']['email'],
            ];

            // Estructura base de NUBEFACT
            $json_data = array_merge([
                "operacion" => "generar_comprobante",
                "tipo_de_comprobante" => $tipo_comprobante,
                "serie" => $serie,
                "numero" => $numero_comprobante,
                "sunat_transaction" => 1,
                "fecha_de_emision" => $fechaHoy,
                "fecha_de_vencimiento" => "",
                "moneda" => 1, // 1 = Soles
                "porcentaje_de_igv" => ($total_igv > 0) ? 18.00 : 0.00,
                "total_gravada" => round($total_gravada, 2),
                "total_inafecta" => 0,
                "total_exonerada" => round($total_exonerada, 2),
                "total_igv" => round($total_igv, 2),
                "total" => round($total_final, 2),
                "formato_de_pdf" => "TICKET",
                "medio_de_pago" => $datos['mediopago'],
                "detraccion" => false,
                "observaciones" => $datos['observaciones'] ?? '',
                "enviar_automaticamente_a_la_sunat" => true,
                "enviar_automaticamente_al_cliente" => $datos['enviar_al_cliente'] ?? false,

            ], $cliente);

            $json_data['items'] = $datos['items'];
            $respuesta_api = $this->nubefactModel->enviarComprobante($json_data);

            $aceptada = !empty($respuesta_api['aceptada_por_sunat']);
            $estado_sunat = $aceptada ? 'ACEPTADA' : 'PENDIENTE';

            $enlacePdf = $respuesta_api['enlace_del_pdf'] ?? $respuesta_api['url_pdf'] ?? null;
            if ($enlacePdf === '') {
                $enlacePdf = null;
            }

            return [
                'success' => true,
                'status' => $estado_sunat,
                'message' => $respuesta_api['sunat_description'] ?? 'Comprobante enviado con éxito.',
                'enlace_pdf' => $enlacePdf,
                'enlace_xml' => $respuesta_api['enlace_del_xml'] ?? null,
                'enlace_cdr' => $respuesta_api['enlace_del_cdr'] ?? null,
                'respuesta_completa_nubefact' => $respuesta_api
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => "Fallo al emitir comprobante: " . $e->getMessage()
            ];
        }
    }

    /**
     * Consulta estado SUNAT/retorna CDR en Nubefact.
     * @return array{success:bool,status?:string,message?:string,enlace_cdr?:string|null,enlace_pdf?:string|null,respuesta_completa_nubefact?:array}
     */
    public function consultarEstadoComprobante(int $tipoComprobante, string $serie, int $numero): array
    {
        try {
            $respuesta_api = $this->nubefactModel->consultarComprobante($tipoComprobante, $serie, $numero);
            $aceptada = !empty($respuesta_api['aceptada_por_sunat']);
            $estado_sunat = $aceptada ? 'ACEPTADA' : 'PENDIENTE';

            $enlacePdf = $respuesta_api['enlace_del_pdf'] ?? $respuesta_api['url_pdf'] ?? null;
            if ($enlacePdf === '') $enlacePdf = null;

            $enlaceCdr = $respuesta_api['enlace_del_cdr'] ?? null;
            if ($enlaceCdr === '') $enlaceCdr = null;

            return [
                'success' => true,
                'status' => $estado_sunat,
                'message' => $respuesta_api['sunat_description'] ?? 'Consulta OK.',
                'enlace_pdf' => $enlacePdf,
                'enlace_cdr' => $enlaceCdr,
                'respuesta_completa_nubefact' => $respuesta_api,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => "Fallo al consultar comprobante: " . $e->getMessage()
            ];
        }
    }

    /**
     * Emite nota de crédito (tipo 3) sin modificar el flujo de boletas/facturas.
     *
     * @param array<string, mixed> $datos
     */
    public function emitirNotaCredito(array $datos): array
    {
        $fechaHoy = date('d-m-Y');

        $requeridos = [
            'serie', 'numero_comprobante', 'datos_cliente', 'items', 'totales',
            'documento_modifica_tipo', 'documento_modifica_serie', 'documento_modifica_numero',
            'tipo_nota_credito',
        ];

        foreach ($requeridos as $campo) {
            if (!isset($datos[$campo]) || ($datos[$campo] === '' && $datos[$campo] !== 0 && $datos[$campo] !== 0.0)) {
                return ['success' => false, 'message' => "Falta el parámetro: $campo."];
            }
        }

        try {
            $totales = $datos['totales'];
            $totalGravada = (float) ($totales['total_gravada'] ?? 0);
            $totalExonerada = (float) ($totales['total_exonerada'] ?? 0);
            $totalIgv = (float) ($totales['total_igv'] ?? 0);
            $totalFinal = (float) $totales['total_venta'];

            $cliente = [
                'cliente_tipo_de_documento' => $datos['datos_cliente']['tipo_documento'] ?? 1,
                'cliente_numero_de_documento' => $datos['datos_cliente']['numero_documento'],
                'cliente_denominacion' => $datos['datos_cliente']['denominacion'],
                'cliente_direccion' => $datos['datos_cliente']['direccion'] ?? '-',
                'cliente_email' => $datos['datos_cliente']['email'] ?? '',
            ];

            $json_data = array_merge([
                'operacion' => 'generar_comprobante',
                'tipo_de_comprobante' => 3,
                'serie' => (string) $datos['serie'],
                'numero' => (int) $datos['numero_comprobante'],
                'sunat_transaction' => 1,
                'fecha_de_emision' => $fechaHoy,
                'fecha_de_vencimiento' => '',
                'moneda' => 1,
                'porcentaje_de_igv' => ($totalIgv > 0) ? 18.00 : 0.00,
                'total_gravada' => round($totalGravada, 2),
                'total_inafecta' => 0,
                'total_exonerada' => round($totalExonerada, 2),
                'total_igv' => round($totalIgv, 2),
                'total' => round($totalFinal, 2),
                'formato_de_pdf' => 'TICKET',
                'medio_de_pago' => $datos['mediopago'] ?? '',
                'detraccion' => false,
                'observaciones' => $datos['observaciones'] ?? '',
                'documento_que_se_modifica_tipo' => (int) $datos['documento_modifica_tipo'],
                'documento_que_se_modifica_serie' => (string) $datos['documento_modifica_serie'],
                'documento_que_se_modifica_numero' => (int) $datos['documento_modifica_numero'],
                'tipo_de_nota_de_credito' => (string) $datos['tipo_nota_credito'],
                'tipo_de_nota_de_debito' => '',
                'enviar_automaticamente_a_la_sunat' => true,
                'enviar_automaticamente_al_cliente' => $datos['enviar_al_cliente'] ?? false,
            ], $cliente);

            $json_data['items'] = $datos['items'];
            $respuesta_api = $this->nubefactModel->enviarComprobante($json_data);

            $aceptada = !empty($respuesta_api['aceptada_por_sunat']);
            $estadoSunat = $aceptada ? 'ACEPTADA' : 'PENDIENTE';

            $enlacePdf = $respuesta_api['enlace_del_pdf'] ?? $respuesta_api['url_pdf'] ?? null;
            if ($enlacePdf === '') {
                $enlacePdf = null;
            }

            return [
                'success' => true,
                'status' => $estadoSunat,
                'message' => $respuesta_api['sunat_description'] ?? 'Nota de crédito enviada.',
                'enlace_pdf' => $enlacePdf,
                'enlace_xml' => $respuesta_api['enlace_del_xml'] ?? null,
                'enlace_cdr' => $respuesta_api['enlace_del_cdr'] ?? null,
                'respuesta_completa_nubefact' => $respuesta_api,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'status' => 'ERROR',
                'message' => 'Fallo al emitir nota de crédito: ' . $e->getMessage(),
            ];
        }
    }
}
