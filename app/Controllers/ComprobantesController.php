<?php


namespace App\Controllers;

use App\Core\Controller;

class ComprobantesController extends Controller
{
    public function verArchivo(string $tipo, string $nombreArchivo): void
    {
        // Construir la ruta absoluta al archivo.

        $rutaArchivo = __DIR__ . "/../../storage/" . basename($tipo) . "/" . basename($nombreArchivo);

        //  Verificar que el archivo existe en la ruta especificada.
        if (!file_exists($rutaArchivo)) {
            // Si el archivo no existe, respondemos con un error 404.
            http_response_code(404);
            die('Archivo no encontrado.');
        }

        $tipoContenido = mime_content_type($rutaArchivo);

        header("Content-Type: {$tipoContenido}");
        header("Content-Length: " . filesize($rutaArchivo));
        header("Content-Disposition: inline; filename=\"" . basename($nombreArchivo) . "\"");
        $this->authRequired();

        error_log("Ruta construida: " . $rutaArchivo);

        // Leer el archivo y enviarlo al navegador.
        readfile($rutaArchivo);
    }
}
