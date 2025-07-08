<?php

$host = 'localhost';
$dbname = 'motorpark';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}


function rucExiste(PDO $pdo, string $ruc): bool {
    $sql = "SELECT COUNT(*) as total FROM empresas WHERE ruc = :ruc";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ruc' => $ruc]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] > 0;
}


$ruc = '20602742131';
echo rucExiste($pdo, $ruc)
    ? " El RUC $ruc ya existe\n"
    : " El RUC $ruc no existe\n";
