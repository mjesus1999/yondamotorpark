<?php
// public/test_find.php
// Ejecutar desde la raíz del proyecto: php public\test_find.php
// Este script intentará cargar .env (si tienes vlucas/phpdotenv) y mostrará lo que lee,
// luego probará findByEmailOrPhoneOrUsernick con varios inputs.

require __DIR__ . '/../vendor/autoload.php';
// justo después de require __DIR__ . '/../vendor/autoload.php';
putenv('DB_HOST=localhost');
putenv('DB_NAME=motorpark');
putenv('DB_USER=root');
// si tu root no tiene contraseña, define DB_PASS como cadena vacía:
putenv('DB_PASS=');

// opcional: imprime para comprobar
echo "FORZANDO ENV:\n";
echo "DB_USER=" . getenv('DB_USER') . "\n";
echo "DB_PASS=" . (getenv('DB_PASS') === '' ? "'' (vacío)" : getenv('DB_PASS')) . "\n\n";

// Si tienes vlucas/phpdotenv instalado, cargar .env (funciona tanto en web como CLI)
if (class_exists(\Dotenv\Dotenv::class)) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    } catch (Throwable $e) {
        echo "WARNING: no se pudo cargar .env: " . $e->getMessage() . "\n";
    }
} else {
    echo "INFO: phpdotenv no detectado; usando variables de entorno del sistema.\n";
}

echo "Entorno de BD detectado:\n";
echo "DB_HOST=" . (getenv('DB_HOST') ?: '<no definido>') . "\n";
echo "DB_NAME=" . (getenv('DB_NAME') ?: '<no definido>') . "\n";
echo "DB_USER=" . (getenv('DB_USER') ?: '<no definido>') . "\n";
$pass = getenv('DB_PASS');
echo "DB_PASS=" . ($pass === false ? '<no definido>' : ($pass === '' ? "'' (vacío)" : '********')) . "\n\n";

use App\Models\Usuario;

try {
    $u = new Usuario();
    $ids = ['919629135', 'Deyanirayc', '+51919629135', '919-629-135'];
    foreach ($ids as $id) {
        echo "=== pruebo: [$id] ===\n";
        $res = $u->findByEmailOrPhoneOrUsernick($id);
        var_export($res);
        echo "\n\n";
    }
} catch (Throwable $e) {
    echo "EXCEPCION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
