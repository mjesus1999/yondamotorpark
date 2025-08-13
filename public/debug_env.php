<!-- TEST DE PRUEBA -->
<?php
require __DIR__ . '/../vendor/autoload.php';

// Cargar .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad(); // o ->load()

// SINCRONIZAR con getenv() y $_SERVER
if (!ini_get('date.timezone')) {
    date_default_timezone_set('America/Lima');
}

foreach ($_ENV as $key => $value) {
    if (!is_string($value))
        continue;
    if (getenv($key) === false) {
        putenv(sprintf('%s=%s', $key, $value));
    }
    if (!isset($_SERVER[$key])) {
        $_SERVER[$key] = $value;
    }
}

// Ahora las comprobaciones
echo '<pre>';
echo 'getenv(\'STARTIME\'): ' . var_export(getenv('STARTIME'), true) . PHP_EOL;
echo 'getenv(\'ENDTIME\'): ' . var_export(getenv('ENDTIME'), true) . PHP_EOL;
echo '$_ENV STARTIME: ' . ($_ENV['STARTIME'] ?? 'NULL') . PHP_EOL;
echo '$_SERVER STARTIME: ' . ($_SERVER['STARTIME'] ?? 'NULL') . PHP_EOL;
echo '</pre>';
