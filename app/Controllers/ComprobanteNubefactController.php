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
            Credencialesnubefact::NUBEFACT_RUTA, 
            Credencialesnubefact::NUBEFACT_TOKEN
        );
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
                "moneda" => 1, // 1 = Soles
                "porcentaje_de_igv" => ($total_igv > 0) ? 18.00 : 0.00,
                "total_gravada" => round($total_gravada, 2),
                "total_inafecta" => round($total_inafecta, 2),
                "total_exonerada" => round($total_exonerada, 2),
                "total_igv" => round($total_igv, 2),
                "total" => round($total_final, 2),
                
                "detraccion" => false,
                "enviar_automaticamente_a_la_sunat" => true,
                "enviar_automaticamente_al_cliente" => false, 
            ], $cliente);

            $json_data['items'] = $datos['items'];
            $respuesta_api = $this->nubefactModel->enviarComprobante($json_data);
            
            $estado_sunat = $respuesta_api['aceptada_por_sunat'] ? 'ACEPTADA' : 'PENDIENTE';

            return [
                'success' => true,
                'status' => $estado_sunat,
                'message' => $respuesta_api['sunat_description'] ?? 'Comprobante enviado con éxito.',
                'enlace_pdf' => $respuesta_api['enlace_del_pdf'] ?? null,
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
}