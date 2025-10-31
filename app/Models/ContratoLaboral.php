<?php

/**
 * Modelo de Contrato Laboral
 * 
 * app/Models/ContratoLaboral.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los contratos
 * laborales de los empleados, incluyendo la creación de nuevos contratos
 * con diferentes tipos (plazo fijo, indefinido, temporal) y su eliminación.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase ContratoLaboral
 * 
 * Modelo que representa y gestiona los contratos laborales en el sistema.
 * Permite crear relaciones entre personas, cargos y períodos de contratación,
 * soportando diferentes modalidades contractuales y gestión de fechas de inicio/fin.
 * 
 */
class ContratoLaboral
{
    /**
     * Instancia de conexión a la base de datos
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor del modelo
     * 
     * Inicializa la conexión a la base de datos mediante el patrón Singleton,
     * garantizando una única instancia de conexión compartida.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Crea un nuevo contrato laboral en la base de datos
     * 
     * Registra un nuevo contrato laboral asociando un empleado (persona) con un
     * cargo específico, estableciendo fechas de vigencia y el tipo de contrato.
     * Soporta contratos con fecha de finalización definida o indefinida (NULL).
     * 
     * Tipos de contrato comunes:
     * - "Plazo Fijo": Contrato con fecha de término definida
     * - "Indefinido": Sin fecha de finalización
     * - "Temporal": Por período específico o proyecto
     * - "Prueba": Período de prueba laboral
     * 
     * @param int $idPersona ID del empleado a contratar
     * @param int $idCargo ID del cargo o posición asignada
     * @param string $fechaInicio Fecha de inicio del contrato
     * @param mixed $fechaFin Fecha de finalización del contrato o NULL para indefinido
     * @param string $tipoContrato Modalidad del contrato
     * @return int
     */
    public function create(int $idPersona, int $idCargo, string $fechaInicio, ?string $fechaFin, string $tipoContrato): int
    {
        $query = "INSERT INTO contratoslaborales
                  (idpersona, idcargo, fechainicio, fechafin, tipocontrato)
                VALUES
                  (:idpersona, :idcargo, :fechainicio, :fechafin, :tipocontrato)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idpersona' => $idPersona,
                ':idcargo' => $idCargo,
                ':fechainicio' => $fechaInicio,
                ':fechafin' => $fechaFin ?: null,
                ':tipocontrato' => $tipoContrato,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return -1;
        }
    }

    /**
     * Elimina un contrato laboral de la base de datos
     * 
     * Realiza una eliminación permanente del registro de contrato laboral
     * identificado por su ID. Esta operación es irreversible y puede afectar
     * las relaciones con otras tablas si existen restricciones de integridad
     * referencial.
     *
     * @param int $idcontrato ID del contrato laboral a eliminar
     * @return int Número de filas afectadas (1 si exitoso, 0 si no existe) o -1 en error
     */
    public function delete(int $idcontrato): int
    {
        $query = "DELETE FROM contratoslaborales WHERE idcontratolaboral = :id";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $idcontrato]);
            return $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error);
            return -1;
        }
    }
}
