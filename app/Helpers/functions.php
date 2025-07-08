<?php

namespace App\Helpers;


function persistirDatosFormulario(array $data): void
{
    if (empty($data)) return;

    echo '<script>';
    echo 'document.addEventListener("DOMContentLoaded", () => {';
    echo 'const old = ' . json_encode($data) . ';';
    echo 'for (const [key, value] of Object.entries(old)) {';
    echo '  const input = document.querySelector(`[name=\\"${key}\\"]`);';
    echo '  if (input) input.value = value;';
    echo '}';
    echo '});';
    echo '</script>';
}




// $datos = [
//     "Nombre" => "Josué",
//     "Apellidos" => "Pilpe"
// ];
// persistirDatosFormulario($datos);