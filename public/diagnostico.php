<?php
/**
 * Diagnóstico rápido en el servidor (borrar después de usar).
 * Abrir: https://mp.yondaperu.com/diagnostico.php
 */
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

$root = dirname(__DIR__);
echo "=== Diagnóstico Motorpark ===\n";
echo 'PHP: ' . PHP_VERSION . "\n\n";

$archivos = [
    'vendor/autoload.php',
    'app/Config/CajaRoutes.php',
    'app/Controllers/RedirectController.php',
    'app/Controllers/AuthController.php',
    'app/Controllers/CajaController.php',
    'app/Routes/Home.router.php',
    'app/Routes/Caja.router.php',
    '.env',
];

echo "--- Archivos ---\n";
foreach ($archivos as $f) {
    $path = $root . '/' . str_replace('/', DIRECTORY_SEPARATOR, $f);
    echo (is_readable($path) ? 'OK  ' : 'FALTA ') . $f . "\n";
}

echo "\n--- Carga PHP ---\n";
try {
    require $root . '/app/Core/Autoloader.php';
    Autoloader::register();
    require $root . '/vendor/autoload.php';
    echo "Autoload: OK\n";

    if (is_readable($root . '/.env')) {
        Dotenv\Dotenv::createImmutable($root)->load();
        echo 'DB_HOST=' . (getenv('DB_HOST') ?: '(vacío)') . "\n";
        echo 'DB_NAME=' . (getenv('DB_NAME') ?: '(vacío)') . "\n";
    } else {
        echo "AVISO: no hay .env en el servidor\n";
    }

    if (class_exists(\App\Controllers\RedirectController::class)) {
        echo "RedirectController: OK\n";
    } else {
        echo "RedirectController: FALTA (sube app/Controllers/RedirectController.php)\n";
    }

    if (class_exists(\App\Config\CajaRoutes::class)) {
        echo "CajaRoutes: OK\n";
    } else {
        echo "CajaRoutes: FALTA (sube app/Config/CajaRoutes.php)\n";
    }

    try {
        new \App\Models\Usuario();
        echo "Conexión MySQL: OK\n";
    } catch (Throwable $e) {
        echo "Conexión MySQL: ERROR - " . $e->getMessage() . "\n";
        echo "(Si FALTA .env o DB_* incorrectos, /login puede dar 500)\n";
    }

    echo "\nSi todo OK pero /login falla, sube AuthController.php e index.php más recientes.\n";
} catch (Throwable $e) {
    echo "ERROR FATAL: " . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
