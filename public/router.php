<?php
// router.php para servidor embebido de PHP
if (php_sapi_name() == 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];

    // Si el archivo existe en public (CSS, JS, imágenes, etc.), lo sirve directamente
    if (is_file($file)) {
        return false;
    }
}

// Todo lo demás pasa por index.php
require_once __DIR__ . '/index.php';
