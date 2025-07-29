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

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT idformato, tipocotizacion, fechainicio, fechafin FROM formatocotizacion ORDER BY idformato DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRequisitos(): array
    {
        $stmt = $this->db->prepare("SELECT idrequisito, requisito FROM requisitos ORDER BY idrequisito");
        $stmt->execute();
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
            ORDER BY r.idrequisito
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':idformato' => $idformato]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteDetalle(int $idformato): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM detallerequisitos WHERE idformato = :idformato"
        );
        $stmt->execute([':idformato' => $idformato]);
    }

    public function addDetalle(int $idformato, int $idrequisito): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO detallerequisitos (idformato, idrequisito) VALUES (:idformato, :idrequisito)"
        );
        $stmt->execute([
            ':idformato' => $idformato,
            ':idrequisito' => $idrequisito
        ]);
    }

    public function delete(int $idformato): void
    {
        $stmt = $this->db->prepare("DELETE FROM detallerequisitos WHERE idformato = :id");
        $stmt->execute([':id' => $idformato]);

        $stmt = $this->db->prepare("DELETE FROM formatocotizacion WHERE idformato = :id");
        $stmt->execute([':id' => $idformato]);
    }

}
