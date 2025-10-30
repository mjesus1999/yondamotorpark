<?php

/**
 * Modelo de Tipo de Vehículo
 * 
 * app/Models/TipoVehiculo.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los tipos de vehículos,
 * incluyendo consultas generales y filtradas por marca.
 * 
 */
namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

/**
 * Clase TipoVehiculo
 * 
 * Modelo para la gestión de tipos de vehículos.
 * Proporciona métodos para obtener tipos de vehículos tanto de forma general
 * como filtrados por marca específica, basándose en modelos existentes.
 * 
 */
class TipoVehiculo
{

    /**
     * Instancia de la conexion a la base de datos
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
     * Obtiene todos los tipos de vehículos
     * 
     * Retorna el listado completo de tipos de vehículos registrados en el sistema,
     * ordenados alfabéticamente por nombre de tipo.
     * 
     * @return array Array asociativo con idtipovehiculo y tipovehiculo,
     *               o array vacío en caso de error
     */
    public function getAll(): array
    {
        $stmt = $this->db->prepare("
            SELECT idtipovehiculo, tipovehiculo
            FROM tipovehiculos
            ORDER BY tipovehiculo
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los tipos de vehículos asociados a una marca específica
     * 
     * Retorna los tipos de vehículos que tienen al menos un modelo registrado
     * para una marca determinada. Útil para filtros en cascada donde primero
     * se selecciona la marca y luego el tipo de vehículo.
     * 
     * Realiza una consulta DISTINCT para evitar duplicados en caso de que
     * una marca tenga múltiples modelos del mismo tipo.
     * 
     * @param int $idmarca ID de la marca a consultar
     * @return array Array asociativo con idtipovehiculo y tipovehiculo
     *               de los tipos que tienen modelos para la marca,
     *               ordenados alfabéticamente, o array vacío en caso de error
     */
    public function getTipoVehiculoByMarca(int $idmarca): array
    {
        $query = "
                SELECT
                    DISTINCT(TV.tipovehiculo), MD.idtipovehiculo 
                    FROM modelos MD
                    INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
                    WHERE MD.idmarca = ?
                    ORDER BY TV. tipovehiculo;
                ";
        try {
            $query = $this->db->prepare($query);
            $query->execute([$idmarca]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e);
            return [];
        }
    }


}
