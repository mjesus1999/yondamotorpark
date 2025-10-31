<?php

/**
 * Modelo de marca
 * app/Models/Marca.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las marcas de vehiculos o productos,
 * incluyendo su relacion con modelos asociados.
 * 
 */
namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

/**
 * Clase Marca
 * 
 * Modelo para la gestion CRUD completos para marcas, incluyendo conteo
 * de modelos asociados y manejo de restricciones de intregidad.
 * 
 */
class Marca
{
    /**
     * Intancia de conexion a la base de datos
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor del modelo
     * 
     * Inicializa la conexion a la base de datos
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todas las marcas ordenadas alfabeticamente,
     * incluyendo el conteo de modelos asociados a cada uno.
     * 
     * @return array Array asociativo con idmarca, marca y cantidad de modelos,
     *                  o array vacio en caso de error
     */
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

    /**
     * Obtiene una marca especifica incluyendo el conteo de modelos asociados.
     * 
     * @param int $idmarca ID de la marca a consultar
     * @return array|null Array asociativo con los datos de la marca o null si no existe o hay error
     */
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
     * Registra una marca nueva
     * 
     * Crea un nuevo registro de marca verificando que no exista una marca con el mismo nombre
     * (contraint UNIQUE)
     * @param string $marca Nombre de la marca a registrar  
     * @return int ID de la marca creada, -1 en error generico, -2 si la marca ya existe (violacion UNIQUE)
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
     * Actualiza los datos de una marca existente 
     * 
     * Modifica el nombre de un marca y actualiza automaticamente el campo 'modificado' con la fecha actual.
     * Verifica que no exista otra con el mismo nombre.
     * 
     * @param int $idmarca ID de la marca a actualizar
     * @param string $marca Nuevo nombre de la marca
     * @return int Numero de registros afectados (generalmente 1), -1 en error generico, -2 si la marca ya existe
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

    /**
     * Elimina una marca
     * 
     * Elimina fisicamente una marca de la base de datos. si la marca tiene modelos asociados,
     * la operacion fallara debido a restricciones de intregidad referencial (FOREIGN KEY).
     * 
     * @param int $idmarca ID de la marca a eliminar
     * @return int Numero de registros eliminados (1 si, exitoso - 0 si no existe),
     *              -1 en error generico, -2 si tiene modelos asociados (Violacion FK)
     */
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