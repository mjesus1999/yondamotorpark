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

    public function procesarPagoYEmitirComprobante(array $datos) 
    {
        
        $fechaHoy = date('d-m-Y'); 
        
     
        $campos_requeridos = [
            'tipo_comprobante',
            'serie',
            'numero_comprobante',
            'datos_cliente',
            'total_capital',
            'total_interes'
        ];

        foreach ($campos_requeridos as $campo) {
            if (!isset($datos[$campo]) || (empty($datos[$campo]) && $datos[$campo] !== 0.0 && $datos[$campo] !== 0)) {
                return ['success' => false, 'message' => "Falta el parámetro de entrada o está vacío: $campo."];
            }
        }

        try {
      
            $tipo_comprobante = (int) $datos['tipo_comprobante'];
            $serie = (string) $datos['serie'];
            $numero_comprobante = (int) $datos['numero_comprobante'];

            $total_capital = (float) $datos['total_capital'];
            $total_interes = (float) $datos['total_interes'];
            $total_operacion = $total_capital + $total_interes;

    
            $cliente = [
                "cliente_tipo_de_documento" => $datos['datos_cliente']['tipo_documento'] ?? 1,
                "cliente_numero_de_documento" => $datos['datos_cliente']['numero_documento'],
                "cliente_denominacion" => $datos['datos_cliente']['denominacion'],
                "cliente_direccion" => $datos['datos_cliente']['direccion'],
                "cliente_email" => $datos['datos_cliente']['email'],
            ];

            $json_data = array_merge([
                "operacion" => "generar_comprobante",
                "tipo_de_comprobante" => $tipo_comprobante,
                "serie" => $serie,
                "numero" => $numero_comprobante,
                "sunat_transaction" => 1,
                "fecha_de_emision" => $fechaHoy, 
                "moneda" => 1,
                "porcentaje_de_igv" => 0.00,
                "total_gravada" => 0.00,
                "total_inafecta" => round($total_operacion, 2),
                "total_igv" => 0.00,
                "total" => round($total_operacion, 2),
                "detraccion" => false,
                "enviar_automaticamente_a_la_sunat" => true,
                "enviar_automaticamente_al_cliente" => false,
            ], $cliente);

            $json_data['items'] = [
                [
                    "unidad_de_medida" => "ZZ", "descripcion" => "Abono a Capital",
                    "cantidad" => 1, "valor_unitario" => $total_capital,
                    "precio_unitario" => $total_capital, "subtotal" => $total_capital,
                    "tipo_de_igv" => 9, "igv" => 0.00, "total" => $total_capital
                ],
                [
                    "unidad_de_medida" => "ZZ", "descripcion" => "Interés",
                    "cantidad" => 1, "valor_unitario" => $total_interes,
                    "precio_unitario" => $total_interes, "subtotal" => $total_interes,
                    "tipo_de_igv" => 9, "igv" => 0.00, "total" => $total_interes
                ]
            ];
            $respuesta_api = $this->nubefactModel->enviarComprobante($json_data);

     
            $estado_sunat = $respuesta_api['aceptada_por_sunat'] ? 'ACEPTADA' : 'PENDIENTE';

            return [
                'success' => true,
                'status' => $estado_sunat,
                'message' => $respuesta_api['sunat_description'] ?? 'Comprobante enviado con éxito.',
                'enlace_pdf' => $respuesta_api['enlace_del_pdf'] ?? null,
                'enlace_xml' => $respuesta_api['enlace_del_xml'] ?? null,
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


    // public function apiEmitirComprobante(): void
    // {
    //     header('Content-Type: application/json');
    //     $input = json_decode(file_get_contents('php://input'), true);

    //     if (!$input) {
    //         echo json_encode([
    //             'success' => false,
    //             'message' => 'JSON inválido o vacío'
    //         ]);
    //         return;
    //     }

    //     $resultado = $this->procesarPagoYEmitirComprobante($input);
    //     echo json_encode($resultado);
    // }
}