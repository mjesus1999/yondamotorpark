<?php

/**
 * Controlador de Comprobantes
 * 
 * app/Controllers/ComprobantesController.php
 * 
 * Gestiona la visualización y entrega de archivos de comprobantes fiscales
 * (facturas y boletas) almacenados en el servidor. Proporciona acceso seguro
 * a los documentos PDF escaneados asociados a las compras registradas,
 * validando la existencia de archivos, determinando el tipo MIME correcto,
 * y enviando los archivos al navegador para visualización inline.
 * 
 */
namespace App\Controllers;

use App\Core\Controller;

/**
 * Clase ComprobantesController
 * 
 * Controlador especializado en la gestión y entrega de archivos de documentación
 * fiscal. Implementa mecanismos de seguridad para prevenir acceso no autorizado
 * a archivos mediante validación de rutas (path traversal protection), verificación
 * de existencia de archivos, y detección automática de tipo MIME. Los archivos
 * se entregan con headers HTTP apropiados para visualización directa en el navegador.
 */
class ComprobantesController extends Controller
{
    public function verArchivo(string $tipo, string $nombreArchivo): void
    {
        $this->authRequired();

        $rutaArchivo = __DIR__ . "/../../storage/" . basename($tipo) . "/" . basename($nombreArchivo);

        if (!file_exists($rutaArchivo)) {
            http_response_code(404);
            die('Archivo no encontrado.');
        }

        $tipoContenido = mime_content_type($rutaArchivo) ?: 'application/octet-stream';

        header("Content-Type: {$tipoContenido}");
        header("Content-Length: " . filesize($rutaArchivo));
        header("Content-Disposition: inline; filename=\"" . basename($nombreArchivo) . "\"");

        readfile($rutaArchivo);
        exit;
    }
}
