<?php
// Respuesta en JSON
header('Content-Type: application/json; charset=utf-8');
function obtenerIGV() {
    $url = "https://orientacion.sunat.gob.pe/3109-05-calculo-del-impuesto";

    // Inicializar cURLñ
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) {
        return ["error" => "No se pudo obtener la página de SUNAT"];
    }

    // Cargar HTML en DOM
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    // Buscar texto que contenga %
    $nodes = $xpath->query('//p | //li | //td | //span');

    $igv = null;
    foreach ($nodes as $node) {
        $texto = trim($node->textContent);
        if (preg_match('/(\d+)\s?%/', $texto, $matches)) {
            $valor = intval($matches[1]);
            if ($valor >= 10 && $valor <= 30) { // rango válido
                $igv = $valor;
                break;
            }
        }
    }

    if ($igv) {
        return ["igv" => $igv];
    } else {
        return ["error" => "No se encontró el IGV"];
    }
}


echo json_encode(obtenerIGV());


