<?php


$clave = "flor123";

$hash = password_hash($clave, PASSWORD_BCRYPT);
var_dump($hash);
echo "Hash: $hash\n";

if (password_verify("flor123", $hash)) {
    echo "Coincide!";
} else {
    echo "Non coincide!";
}
