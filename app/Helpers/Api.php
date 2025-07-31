<?php

function obtenerTipoCambio():float{

  $url = "https://api.decolecta.com/v1/tipo-cambio/sunat"; // Reemplaza con la URL de la API
  $headers = [
      "Authorization: sk_5727.3Nr5vMLAu8pJFjidwC6GA4zEoT5jcIvz", // Reemplaza con tu token de acceso
      "Accept: application/json"
  ];
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Para recibir la respuesta como string
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
  $response = curl_exec($ch);
  
  if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
  }
  
  curl_close($ch);
  
  // Procesa la respuesta (ejemplo: decodificando JSON)
  $data = json_decode($response, true);
  
  if ($data) {
      // Procesa los datos
      //echo json_encode($data); //PASAR A JSON
      //var_dump($data);
      return $data['buy_price'];
  } else {
      //echo "Error al decodificar la respuesta";
      return 0;
  }
}

echo obtenerTipoCambio();

?>