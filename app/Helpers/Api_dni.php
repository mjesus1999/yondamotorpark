<?php
//app/helpers/Api_dni.php

function searchByDNI($dni = "")
{
  // Validación básica
  if (empty($dni) || !preg_match('/^\d{8}$/', $dni)) {
    echo json_encode([
      'success' => false,
      'message' => 'DNI invalido'
    ]);
    return;
  }

  // Parámetros sensibles API
  $api_endpoint = "https://api.decolecta.com/v1/reniec/dni?numero=" . $dni;
  $api_token = "sk_10118.r1mQl43gR8gxOjOkV3F9czV0Ecd266ud";
  $content_type = "application/json";

  // Configuración de cURL para realización petición
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api_endpoint);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 segundos
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: ' . $content_type,
    'Authorization: Bearer ' . $api_token
  ]);

  // Ejecutar la petición
  $api_response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  //$curl_error = curl_error($ch);
  curl_close($ch);

  // Error en cURL
  if ($api_response === false || !empty($curl_error)) {
    echo json_encode([
      'success' => false,
      'message' => 'Error de conexión con el servicio'
    ]);
    return;
  }

  // Decodificar la respuesta
  $decoded_response = json_decode($api_response, true);

  // Manejar diferentes códigos de respuesta
  switch ($http_code) {
    case 200:
      // Exito
      if ($decoded_response && isset($decoded_response['first_name'])) {
        echo json_encode([
          'success' => true,
          'apepaterno' => $decoded_response['first_last_name'] ?? '',
          'apematerno' => $decoded_response['second_last_name'] ?? '',
          'nombres' => $decoded_response['first_name'] ?? ''
        ]);
      } else {
        echo json_encode([
          'success' => false,
          'message' => 'Respuesta inválida del servicio'
        ]);
      }
      break;

    case 404:
      // No encontrado
      echo json_encode([
        'success' => false,
        'message' => 'No se encontró la persona con ese DNI'
      ]);
      break;

    case 401:
      // No autorizado
      echo json_encode([
        'success' => false,
        'message' => 'Token de API inválido'
      ]);
      break;

    case 429:
      // Demasiadas solicitudes
      echo json_encode([
        'success' => false,
        'message' => 'Límite de consultas excedido'
      ]);
      break;

    default:
      echo json_encode([
        'success' => false,
        'message' => 'Error en el servicio externo (Código: ' . $http_code . ')'
      ]);
      break;
  }
}

