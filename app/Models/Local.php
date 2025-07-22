<?php
// app/Models/Local.php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class Local
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene un local por su nombre (tienda).
     */
    public function getByTienda(string $tienda): ?array
    {
        $stmt = $this->db->prepare("
        SELECT idlocal
        FROM locales
        WHERE tienda = :tienda
        LIMIT 1
    ");
        $stmt->bindParam(':tienda', $tienda, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
