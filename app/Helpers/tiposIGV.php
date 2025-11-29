<?php

function getIGVGravada(float $total): array
{
    $valorGravada = $total / 1.18;
    $valorIgv = $valorGravada * 0.18;

    if ($valorGravada > 0 && $valorIgv > 0) {
        return [
            "gravada" => round($valorGravada, 2),
            "igv" => round($valorIgv, 2)
        ];
    }

    return [
        "message" => "No se pudo calcular el IGV Gravada"
    ];
}











