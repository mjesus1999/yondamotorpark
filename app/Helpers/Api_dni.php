<?php
//app/helpers/Api_dni.php
function searchByDNI($dni = ""){
    
    //Parámetros sensibles API
    $api_endpoint = "https://api.decolecta.com/v1/reniec/dni?numero=" . $dni;
    $api_token = "sk_10118.r1mQl43gR8gxOjOkV3F9czV0Ecd266ud";
    $content_type = "application/json";

    //Configuración de cURL para realización petición
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Respuesta
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type:' . $content_type,
      'Authorization: Bearer ' . $api_token
    ]);

    //Ejecutar la petición
    $api_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    //Error en el servicio
    if ($api_response === false){
      echo json_encode([
        'success'   => false,
        'message'   => 'No se pudo realizar la consulta'
      ]);
    }

    //Decodificar la respuesta
    $decoded_response = json_decode($api_response, true);

    if ($http_code === 404){
      echo json_encode([
        'success'   => false,
        'message'   => 'No encontramos la persona'
      ]);
    }

    //Persona encontrada por el API
    echo json_encode([
      'success'     => true,
      'apepaterno'  => $decoded_response['first_last_name'],
      'apematerno'  => $decoded_response['second_last_name'],
      'nombres'     => $decoded_response['first_name']
    ]);

  } //searchByDNI