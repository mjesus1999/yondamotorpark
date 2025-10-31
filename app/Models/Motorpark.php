<?php

/**
 * Modelo de Motorpark
 * 
 * app/Models/Motorpark.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los motorpark, proporcionando acceso a la informació
 * 
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Motorpark
 * 
 * Modelo para gestionar las operaciones relacionadas con la tabla motorpark
 * en la base de datos
 */
class Motorpark
{

    /**
     * Instancia de conexion a la base de datos
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor del modelo
     * 
     * Inicializa la conexión a la base de datos
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los motorparks registrados
     * 
     * Recupera la lista completa de motorparks desde la base de datos,
     * incluyendo el identificador único y el nombre comercial de cada
     * parque automotriz. Esta información es utilizada principalmente
     * para poblar selectores y formularios de registro.
     * 
     * @return array Array asociativo con los motorparks encontrados.
     *               Cada elemento contiene:
     *               - idmotorpark: int|string (Identificador único del motorpark)
     *               - nombrecomercial: string (Nombre comercial del motorpark)
     *               Retorna array vacío en caso de error
     */
    public function getMotorPark()
    {
        try {
            $query = "SELECT idmotorpark, nombrecomercial FROM motorpark";
            $cmd = $this->db->prepare($query);
            $cmd->execute();
            $results = $cmd->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

}