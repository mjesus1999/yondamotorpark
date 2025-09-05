<?php
$host = '127.0.0.1';
$db = 'motorpark';
$user = 'root';
$pass = '';
$port = 3306;
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "PDO: Conexión OK\n";

    // 1) Contar filas en la vista (lo que verá la UI)
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM vwGetAllCotizacion");
    $row = $stmt->fetch();
    echo "Filas en vwGetAllCotizacion: " . ($row['c'] ?? 0) . "\n";

    // 2) Mostrar las primeras 10 filas (id, fechaRegistro, vigenciadias si existe)
    $stmt = $pdo->query("SELECT idcotizacion, fechaRegistro, COALESCE(vigenciadias, '') AS vigenciadias FROM vwGetAllCotizacion ORDER BY fechaRegistro DESC LIMIT 10");
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        echo implode(" | ", $r) . "\n";
    }

} catch (PDOException $e) {
    echo "ERROR PDO: " . $e->getMessage() . "\n";
    exit(1);
}
