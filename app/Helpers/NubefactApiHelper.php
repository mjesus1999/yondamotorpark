<?php

namespace App\Helpers;

use Exception;
class NubefactApiHelper {

    private $ruta;
    private $token;

    // Constructor que recibe las credenciales
    public function __construct(string $ruta, string $token) {
        $this->ruta = $ruta;
        $this->token = $token;
    }

    /**
     * Envía un array de comprobante a la API de NUBEFACT.
     * @param array $data_array Array PHP con la estructura del comprobante (Factura o Boleta).
     * @return array Retorna la respuesta de NUBEFACT decodificada.
     * @throws Exception Si ocurre un error grave (conexión, token, error HTTP).
     */
    public function enviarComprobante(array $data_array): array {
        $data_json = json_encode($data_array);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->ruta);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Authorization: Token token="' . $this->token . '"',
                'Content-Type: application/json',
            ]
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        CurlHelper::applySslOptions($ch);

        $respuesta = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("Error de conexión cURL: " . $error_msg);
        }
        
        curl_close($ch);

        $leer_respuesta = json_decode($respuesta, true);
        if (!is_array($leer_respuesta)) {
            throw new Exception(
                'Nubefact devolvió una respuesta que no es JSON (¿error HTTP/HTML?). Revise ruta y token.'
            );
        }

        //  Manejo de errores HTTP
        if ($http_code !== 200) {
            $error_desc = isset($leer_respuesta['errors']) ? $leer_respuesta['errors'] : "Error HTTP $http_code. Revise el Token o la Ruta.";
            throw new Exception("Error de API ($http_code): " . $error_desc);
        }

        //  Manejo de errores de NUBEFACT (código 20, 21, 23, etc.)
        if (isset($leer_respuesta['errors'])) {
             throw new Exception("Error NUBEFACT (Código {$leer_respuesta['codigo']}): " . $leer_respuesta['errors']);
        }

        return $leer_respuesta;
    }

    /**
     * Consulta un comprobante ya emitido (revisar estado SUNAT / CDR).
     * Nubefact acepta esta operación usando la misma RUTA/TOKEN (POST JSON).
     */
    public function consultarComprobante(int $tipoComprobante, string $serie, int $numero): array
    {
        return $this->enviarComprobante([
            'operacion' => 'consultar_comprobante',
            'tipo_de_comprobante' => $tipoComprobante,
            'serie' => $serie,
            'numero' => $numero,
        ]);
    }
}