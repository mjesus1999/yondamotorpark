<?php
// app/views/pdf/cot/reporte.php

use Spipu\Html2Pdf\Html2Pdf;
require_once __DIR__ . '/../../../../vendor/autoload.php';

try {
    ob_end_clean();
    ob_start();
    include 'cotizacion.php';
    $html = ob_get_clean();

    // Configurar HTML2PDF con márgenes
    $pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', array(10, 10, 10, 10));

    $pdf->writeHTML($html);
    $pdf->output('reporte-cotizacion.pdf');
} catch (Exception $e) {
    echo 'Error al generar el PDF: ' . $e->getMessage();
}