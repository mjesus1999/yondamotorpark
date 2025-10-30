<?php

/**
 * Modelo de Formato de Cotización
 * 
 * app/Models/FormatoCotizacion.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los formatos
 * de cotización y sus requisitos asociados. Permite crear plantillas de
 * cotización con periodos de vigencia y requisitos configurables que se
 * aplicarán a las cotizaciones generadas en el sistema.
 */
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Clase FormatoCotizacion
 * 
 * Modelo para la gestión de formatos de cotización.
 * Proporciona métodos para CRUD de formatos, gestión de requisitos
 * asociados (detalle), y consulta de catálogos de requisitos disponibles.
 * Cada formato tiene un tipo, periodo de vigencia y lista de requisitos
 * que deben cumplirse en las cotizaciones.
 */
class FormatoCotizacion
{
    
    /**
     * Instancia de conexión a la base de datos
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
     * Obtiene todos los formatos de cotización
     * 
     * Recupera la lista completa de formatos registrados en el sistema,
     * ordenados por ID de forma descendente (más recientes primero).
     * Incluye información básica: identificador, tipo, y periodo de vigencia.
     * 
     * @return array Array asociativo con los formatos encontrados.
     *               Cada elemento contiene:
     *               - idformato: int (Identificador único del formato)
     *               - tipocotizacion: string (Tipo de cotización)
     *               - fechainicio: string (Fecha de inicio de vigencia)
     *               - fechafin: string|null (Fecha de fin de vigencia)
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT idformato, tipocotizacion, fechainicio, fechafin FROM formatocotizacion ORDER BY idformato DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el catálogo completo de requisitos
     * 
     * Retorna todos los requisitos disponibles en el sistema que pueden
     * ser asociados a un formato de cotización. Esta lista se utiliza
     * para poblar selectores y checkboxes en formularios de configuración.
     * 
     * @return array Array asociativo con los requisitos disponibles.
     *               Cada elemento contiene:
     *               - idrequisito: int (Identificador único del requisito)
     *               - requisito: string (Descripción del requisito)
     *               Ordenado por ID de requisito
     */
    public function getRequisitos(): array
    {
        $stmt = $this->db->prepare("SELECT idrequisito, requisito FROM requisitos ORDER BY idrequisito");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo formato de cotización
     * 
     * Registra un nuevo formato con su tipo y periodo de vigencia.
     * No incluye los requisitos asociados; estos deben agregarse
     * posteriormente mediante el método addDetalle().
     * 
     * @param string $tipocotizacion Tipo de cotización
     * @param string $fechainicio Fecha de inicio de vigencia del formato
     * @param string|null $fechafin Fecha de fin de vigencia / Null indica vigencia indefinida
     * @return int ID del formato creado
     */
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

    /**
     * Obtiene los requisitos asociados a un formato
     * 
     * Retorna la lista de requisitos que han sido configurados para
     * un formato de cotización específico mediante JOIN con la tabla
     * de detalle (detallerequisitos).
     * 
     * @param int $idformato ID del formato de cotización
     * @return array Array asociativo con los requisitos del formato.
     *               Cada elemento contiene:
     *               - idrequisito: int (Identificador del requisito)
     *               - requisito: string (Descripción del requisito)
     *               Ordenado por ID de requisito
     */
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

    /**
     * Elimina todos los requisitos de un formato
     * 
     * Borra todos los registros de detalle (requisitos asociados) de un
     * formato específico. Útil para actualizar la configuración de requisitos
     * sin eliminar el formato completo.

     * @param int $idformato ID del formato de cotización
     * @return void
     */
    public function deleteDetalle(int $idformato): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM detallerequisitos WHERE idformato = :idformato"
        );
        $stmt->execute([':idformato' => $idformato]);
    }

    /**
     * Asocia un requisito a un formato
     * 
     * Agrega un requisito específico a un formato de cotización,
     * creando un registro en la tabla de detalle (detallerequisitos).

     * @param int $idformato ID del formato de cotización
     * @param int $idrequisito ID del requisito a asociar
     * @return void
     */
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

    /**
     * Elimina un formato de cotización completo
     * 
     * Borra un formato y todos sus requisitos asociados (detalle).
     * Ejecuta dos operaciones DELETE en cascada:
     * 1. Elimina registros de detallerequisitos
     * 2. Elimina el registro del formato

     * @param int $idformato ID del formato a eliminar
     * @return void
     */
    public function delete(int $idformato): void
    {
        $stmt = $this->db->prepare("DELETE FROM detallerequisitos WHERE idformato = :id");
        $stmt->execute([':id' => $idformato]);

        $stmt = $this->db->prepare("DELETE FROM formatocotizacion WHERE idformato = :id");
        $stmt->execute([':id' => $idformato]);
    }

}
