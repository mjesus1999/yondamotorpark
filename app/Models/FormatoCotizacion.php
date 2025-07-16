<?php
//app/Controllers/FormatoCotizacion.php

namespace App\Models;

use App\Core\Database;
use PDO;

class FormatoCotizacion
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT idformato, tipocotizacion, fechainicio, fechafin FROM formatocotizacion ORDER BY idformato DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $tipocotizacion, string $fechainicio, ?string $fechafin): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO formatocotizacion (tipocotizacion, fechainicio, fechafin) VALUES (:tipocotizacion, :fechainicio, :fechafin)"
        );
        $stmt->execute([
            ':tipocotizacion' => $tipocotizacion,
            ':fechainicio' => $fechainicio,
            ':fechafin' => $fechafin,
        ]);
        return (int) $this->db->lastInsertId();
    }

}
