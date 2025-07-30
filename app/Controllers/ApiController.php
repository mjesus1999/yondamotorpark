<?php
namespace App\Controllers;

class ApiController
{
    public function tipoCambio()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Obtener parámetros
        $from = $_GET['from'] ?? '';
        $to   = $_GET['to']   ?? '';

        if (!$from || !$to) {
            http_response_code(400);
            echo json_encode(['error' => 'Parámetros from y to obligatorios']);
            return;
        }

        // Llamada a API pública (por ejemplo exchangerate.host)
        $url = sprintf(
            'https://api.exchangerate.host/latest?base=%s&symbols=%s',
            urlencode($from),
            urlencode($to)
        );

        $json = @file_get_contents($url);
        if ($json === false) {
            http_response_code(502);
            echo json_encode(['error' => 'No se pudo conectar a la API de tipo de cambio']);
            return;
        }

        $data = json_decode($json, true);
        if (!isset($data['rates'][$to])) {
            http_response_code(500);
            echo json_encode(['error' => 'Tasa de cambio no disponible']);
            return;
        }

        // Devolver sólo la tasa
        echo json_encode(['tasa' => $data['rates'][$to]]);
    }
}
