<?php
//app/helpers/Api_ruc.php

function searchByRUC($ruc = "")
{
    // Validación básica
    if (empty($ruc) || !preg_match('/^\d{11}$/', $ruc)) {
        echo json_encode([
            'success' => false,
            'message' => 'RUC inválido. Debe tener 11 dígitos'
        ]);
        return;
    }

    // Parámetros de API
    $api_endpoint = "https://api.decolecta.com/v1/sunat/ruc?numero=" . $ruc;
    $api_token = "sk_10118.r1mQl43gR8gxOjOkV3F9czV0Ecd266ud";
    $content_type = "application/json";

    // Configuración de cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: ' . $content_type,
        'Authorization: Bearer ' . $api_token
    ]);

    // Ejecutar la petición
    $api_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Error de cURL
    if ($api_response === false || !empty($curl_error)) {
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión con el servicio SUNAT'
        ]);
        return;
    }

    // Decodificar la respuesta
    $decoded_response = json_decode($api_response, true);

    switch ($http_code) {
        case 200:
            // Éxito - Mapear los campos correctos según la estructura real de la API
            if ($decoded_response && isset($decoded_response['razon_social'])) {

                // Construir dirección completa si hay datos de ubicación
                $direccion_completa = '';
                if (!empty($decoded_response['direccion'])) {
                    $direccion_completa = $decoded_response['direccion'];
                } else {
                    // Construir dirección desde los componentes
                    $partes_direccion = [];
                    if (!empty($decoded_response['via_tipo']) && !empty($decoded_response['via_nombre'])) {
                        $partes_direccion[] = $decoded_response['via_tipo'] . ' ' . $decoded_response['via_nombre'];
                    }
                    if (!empty($decoded_response['numero'])) {
                        $partes_direccion[] = $decoded_response['numero'];
                    }
                    if (!empty($decoded_response['interior'])) {
                        $partes_direccion[] = 'Int. ' . $decoded_response['interior'];
                    }
                    if (!empty($decoded_response['manzana'])) {
                        $partes_direccion[] = 'Mz. ' . $decoded_response['manzana'];
                    }
                    if (!empty($decoded_response['lote'])) {
                        $partes_direccion[] = 'Lt. ' . $decoded_response['lote'];
                    }

                    $direccion_completa = implode(' ', $partes_direccion);
                }

                echo json_encode([
                    'success' => true,
                    'razonsocial' => $decoded_response['razon_social'] ?? '',
                    'nombrecomercial' => $decoded_response['razon_social'] ?? '', // Usar razón social como nombre comercial si no hay uno específico
                    'estado' => $decoded_response['estado'] ?? '',
                    'condicion' => $decoded_response['condicion'] ?? '',
                    'direccion' => $direccion_completa,
                    'ubigeo' => $decoded_response['ubigeo'] ?? '',
                    'departamento' => $decoded_response['departamento'] ?? '',
                    'provincia' => $decoded_response['provincia'] ?? '',
                    'distrito' => $decoded_response['distrito'] ?? '',
                    'telefono' => '', // La API no parece devolver teléfono
                    'email' => '', // La API no parece devolver email
                    'representante' => '' // La API no parece devolver representante legal
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró información para este RUC'
                ]);
            }
            break;

        case 404:
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró la empresa con ese RUC'
            ]);
            break;

        case 401:
            echo json_encode([
                'success' => false,
                'message' => 'Token de API inválido'
            ]);
            break;

        case 429:
            echo json_encode([
                'success' => false,
                'message' => 'Límite de consultas excedido. Intente más tarde'
            ]);
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servicio SUNAT (Código: ' . $http_code . ')'
            ]);
            break;
    }
}