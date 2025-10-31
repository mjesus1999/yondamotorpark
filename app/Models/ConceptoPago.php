<?php

/**
 * Modelo de Concepto de Pago
 * 
 * app/Models/Concepto
 * 
 * Gestiona las operaciones de base de datos relacionadas con los conceptos
 * de pago del sistema. Los conceptos de pago son categorías o tipos de
 * transacciones financieras que se registran en el sistema, como pagos
 * de inicial, cuotas, gastos administrativos, transferencias.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase ConceptoPago
 * 
 * Modelo que representa y gestiona los conceptos de pago en el sistema.
 * Los conceptos de pago son categorías que clasifican las transacciones
 * financieras, permitiendo un control detallado de los diferentes tipos
 * de pagos realizados en el sistema.
 */
class ConceptoPago
{

    /**
     * Instancia de conexión a la base de datos
     * @var 
     */
    private $db;

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
     * Obtiene el listado completo de conceptos de pago
     * 
     * Recupera todos los conceptos de pago disponibles en el sistema.
     * Estos conceptos son utilizados para clasificar y categorizar las
     * diferentes transacciones financieras registradas.
     * 
     * @return array Array de conceptos de pago con estructura:
     *   - idconcepto (int): Identificador único del concepto
     *   - concepto (string): Descripción/nombre del concepto de pago
     *   Retorna array vacío si no hay conceptos o si ocurre un error
     */
    public function getConceptos(): array
    {
        $query = "SELECT idconcepto,concepto FROM conceptospago;";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

}
