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

    public function getDetalleRequisitos(int $idformato): array
    {
        $sql = "
            SELECT r.idrequisito, r.requisito
            FROM requisitos AS r
            JOIN detallerequisitos AS dr ON dr.idrequisito = r.idrequisito
            WHERE dr.idformato = :idformato
            ORDER BY r.requisito
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idformato' => $idformato]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
