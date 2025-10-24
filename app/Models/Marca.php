<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

class Marca
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        $query = "
        SELECT
            MR.idmarca, MR.marca, COUNT(MD.idmodelo) 'modelos'
            FROM marcas MR
                LEFT JOIN modelos MD ON MD.idmarca = MR.idmarca
            GROUP BY MR.idmarca, MR.marca
            ORDER BY MR.marca;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

 
    public function getById(int $idmarca): ?array
    {
        $query = "
            SELECT
                MR.idmarca, MR.marca, COUNT(MD.idmodelo) 'modelos'
            FROM marcas MR
            LEFT JOIN modelos MD ON MD.idmarca = MR.idmarca
            WHERE MR.idmarca = :idmarca
            GROUP BY MR.idmarca, MR.marca;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Registra una nueva marca
     * @return int PK obtenida, -1 en error genérico, -2 si la marca ya existe (UNIQUE constraint)
     */
    public function create(string $marca = ''): int
    {
        $query = "INSERT INTO marcas (marca) VALUES (:marca)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
            $stmt->execute();
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { 
                return -2;
            }
            return -1;
        }
    }

    /**
     * Actualiza una marca
     * @return int Registros afectados, -1 en error, -2 si la marca ya existe
     */
    public function update(int $idmarca, string $marca): int
    {
        $query = "UPDATE marcas SET marca = :marca, modificado = NOW() WHERE idmarca = :idmarca";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Error de UNIQUE constraint
                return -2;
            }
            return -1;
        }
    }

   
    public function delete(int $idmarca): int
    {
        $query = "DELETE FROM marcas WHERE idmarca = :idmarca";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Error de FOREIGN KEY constraint
                return -2;
            }
            return -1;
        }
    }
}