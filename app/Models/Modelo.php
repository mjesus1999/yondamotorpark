<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

class Modelo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los modelos de una marca específica.
     */
    public function getByMarca(int $idmarca): array
    {
        $query = "
            SELECT 
                MD.idmodelo, MD.idtipovehiculo, MD.idmarca, MD.modelo, MD.anio, MD.imagenreferencial,
                TV.tipovehiculo
            FROM modelos MD
            INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
            WHERE MD.idmarca = :idmarca
            ORDER BY TV.tipovehiculo, MD.modelo, MD.anio;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    
    public function getById(int $idmodelo): ?array
    {
        $query = "
            SELECT 
                MD.idmodelo, MD.idtipovehiculo, MD.idmarca, MD.modelo, MD.anio, MD.imagenreferencial,
                TV.tipovehiculo
            FROM modelos MD
            INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
            WHERE MD.idmodelo = :idmodelo;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmodelo', $idmodelo, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Registra un nuevo modelo.
     * @return int PK, -1 error, -2 duplicado.
     */
    public function create(array $data): int
    {
        $query = "
            INSERT INTO modelos (idtipovehiculo, idmarca, modelo, anio, imagenreferencial)
            VALUES (:idtipovehiculo, :idmarca, :modelo, :anio, :imagenreferencial);
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idtipovehiculo', $data['idtipovehiculo'], PDO::PARAM_INT);
            $stmt->bindParam(':idmarca', $data['idmarca'], PDO::PARAM_INT);
            $stmt->bindParam(':modelo', $data['modelo'], PDO::PARAM_STR);
            $stmt->bindParam(':anio', $data['anio'], PDO::PARAM_STR);
            $stmt->bindParam(':imagenreferencial', $data['imagenreferencial'], PDO::PARAM_STR);
            $stmt->execute();
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Error de UNIQUE constraint
                return -2;
            }
            return -1;
        }
    }

    /**
     * Actualiza un modelo.
     * @return int Filas, -1 error, -2 duplicado.
     */
    public function update(array $data): int
    {
        // Si no se sube una nueva imagen, no actualizamos ese campo
        $sqlImagen = ($data['imagenreferencial'] !== null) ? ", imagenreferencial = :imagenreferencial" : "";
        
        $query = "
            UPDATE modelos SET 
                idtipovehiculo = :idtipovehiculo,
                idmarca = :idmarca,
                modelo = :modelo,
                anio = :anio,
                modificado = NOW()
                {$sqlImagen}
            WHERE idmodelo = :idmodelo;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idtipovehiculo', $data['idtipovehiculo'], PDO::PARAM_INT);
            $stmt->bindParam(':idmarca', $data['idmarca'], PDO::PARAM_INT);
            $stmt->bindParam(':modelo', $data['modelo'], PDO::PARAM_STR);
            $stmt->bindParam(':anio', $data['anio'], PDO::PARAM_STR);
            $stmt->bindParam(':idmodelo', $data['idmodelo'], PDO::PARAM_INT);
            
            if ($data['imagenreferencial'] !== null) {
                $stmt->bindParam(':imagenreferencial', $data['imagenreferencial'], PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return -2;
            }
            return -1;
        }
    }

   
    public function delete(int $idmodelo): int
    {
        $query = "DELETE FROM modelos WHERE idmodelo = :idmodelo";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmodelo', $idmodelo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
           
            if ($e->getCode() == '23000') {  // Modelo con vehículos
                return -2; 
            }
            return -1;
        }
    }
    
    /**
     * Obtiene el conteo de modelos para una marca.
     */
     public function countByMarca(int $idmarca): int
     {
         $query = "SELECT COUNT(idmodelo) FROM modelos WHERE idmarca = :idmarca";
         try {
             $stmt = $this->db->prepare($query);
             $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
             $stmt->execute();
             return (int) $stmt->fetchColumn();
         } catch(Exception $e) {
             return 0;
         }
     }
}