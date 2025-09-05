<?php
// app/helpers/Api.php

function obtenerTipoCambio(): float 
{
    $cacheFile = __DIR__ . '/tipo_cambio_cache.json'; 
    $TTL = 10 * 60; 

    // Validar si existe cache y si está vigente
    if (file_exists($cacheFile)) {
        $dataCache = json_decode(file_get_contents($cacheFile), true);
        if (isset($dataCache['timestamp']) && (time() - $dataCache['timestamp']) < $TTL) {
            return floatval($dataCache['valor']);
        }
    }

    // Si no hay cache o expiró, hacer petición a la API externa
    $url = "https://api.decolecta.com/v1/tipo-cambio/sunat"; 
    $headers = [
        "Authorization: sk_10045.3rP3nvkLJFtXSap8SFl5IIu51Wt0Y09R", 
        "Accept: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Error CURL tipo cambio: ' . curl_error($ch));
        curl_close($ch);
        return 0;
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if ($data && isset($data['buy_price'])) {
        $tipoCambio = round(floatval($data['buy_price']) * 1.05, 4); // 5% adicional redondeado

        //  Guardar en cache
        file_put_contents($cacheFile, json_encode([
            'valor' => $tipoCambio,
            'timestamp' => time()
        ]));

        return $tipoCambio;
    }

    return 0;
} 
/* echo obtenerTipoCambio(); */
?>
