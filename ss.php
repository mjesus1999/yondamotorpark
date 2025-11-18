<?php

require __DIR__ . '/vendor/autoload.php';  // ★ OBLIGATORIO ★

use App\Controllers\ComprobanteNubefactController;

$OBJ = new ComprobanteNubefactController();

$datos_para_boleta = [
    'tipo_comprobante' => 2,
    'serie' => 'BBB1',
    'numero_comprobante' => 4,
    'total_capital' => 600.00,
    'total_interes' => 108.00,
    'datos_cliente' => [
        'tipo_documento' => 1,
        'numero_documento' => '71882015',
        'denominacion' => 'JOSUE ISAI PILPE YATACO',
        'direccion' => 'CALLE LIBERTAD 116 MIRAFLORES - LIMA - PERU',
        'email' => 'josueyataco96@gmail.com'
    ]
];

echo "Enviando Boleta...<br>";

$resultado = $OBJ->procesarPagoYEmitirComprobante($datos_para_boleta);

var_dump($resultado);
