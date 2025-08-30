<?php
// public/archivos.php
// Seguridad mínima: evita path traversal y sirve archivos desde /storage
$rel = $_GET['f'] ?? '';
$rel = trim($rel);
$rel = str_replace(['..', '\\'], '', $rel); // evitar ../ y backslashes peligrosos
$rel = ltrim($rel, "/\\");

// Ruta real al storage (ajusta si tu proyecto tiene otra estructura)
$storageBase = realpath(__DIR__ . '/../storage'); // public/.. => project/storage

if ($storageBase === false) {
    http_response_code(500);
    exit('Storage no configurado');
}

$full = $storageBase . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel);

// Seguridad: solo permitir rutas dentro de storage
$real = realpath($full);
if ($real === false || strpos($real, $storageBase) !== 0) {
    http_response_code(404);
    exit('Archivo no encontrado');
}

if (!is_file($real) || !is_readable($real)) {
    http_response_code(404);
    exit('Archivo no encontrado');
}

$ext = strtolower(pathinfo($real, PATHINFO_EXTENSION));
$ctype = 'application/octet-stream';
switch ($ext) {
    case 'jpg':
    case 'jpeg': $ctype = 'image/jpeg'; break;
    case 'png': $ctype = 'image/png'; break;
    case 'gif': $ctype = 'image/gif'; break;
    case 'pdf': $ctype = 'application/pdf'; break;
    case 'txt': $ctype = 'text/plain'; break;
}

header('Content-Type: ' . $ctype);
header('Content-Length: ' . filesize($real));
header('Cache-Control: public, max-age=3600');
readfile($real);
exit;
