<?php

/**
 * Funcion de Persistir Datos Formulario
 * 
 * app/Helpers/funtions.php
 * 
 */
namespace App\Helpers;


function persistirDatosFormulario(array $data): void
{
    if (empty($data)) return;

    echo '<script>';
    echo 'document.addEventListener("DOMContentLoaded", () => {';
    echo 'const old = ' . json_encode($data) . ';';
    echo 'function applyPersistencia() {';
    echo '  for (const [key, value] of Object.entries(old)) {';
    echo '    const input = document.querySelector(`[name="${key}"]`);';
    echo '    if (input) {';
    echo '      if (input.tagName === "SELECT") {';
    echo '        const option = Array.from(input.options).find(o => o.value == value);';
    echo '        if (option) option.selected = true;';
    echo '      } else {';
    echo '        input.value = value;';
    echo '      }';
    echo '    }';
    echo '  }';
    echo '}';
    echo 'setTimeout(applyPersistencia, 300);'; // Espera breve para que AJAX cargue los <select>
    echo '});';
    echo '</script>';
}





// $datos = [
//     "Nombre" => "Josué",
//     "Apellidos" => "Pilpe"
// ];
// persistirDatosFormulario($datos);