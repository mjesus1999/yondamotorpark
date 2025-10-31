<?php

/**
 * Modelo de Entidad de Pago
 * 
 * app/Models/EntidadPago.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las entidades
 * de pago del sistema (bancos, financieras, billeteras digitales, etc.).
 * Proporciona acceso al catálogo de instituciones financieras disponibles
 * para realizar pagos, depósitos y transacciones monetarias en el sistema.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Modelo de Entidad de Pago
 * 
 * app/Models/EntidadPago.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las entidades
 * de pago del sistema (bancos, financieras, billeteras digitales, etc.).
 * Proporciona acceso al catálogo de instituciones financieras disponibles
 * para realizar pagos, depósitos y transacciones monetarias en el sistema.
 */
class EntidadPago
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
     * Obtiene todas las entidades de pago registradas
     * 
     * Recupera el catálogo completo de entidades de pago (bancos,
     * financieras, billeteras digitales) disponibles en el sistema,
     * ordenadas alfabéticamente por nombre de entidad para facilitar
     * su selección en interfaces de usuario.
     * 
     * @return array Array asociativo con las entidades de pago.
     *               Cada elemento contiene:
     *               - identidadpago: int (Identificador único de la entidad)
     *               - entidad: string (Nombre de la entidad: "BCP", "BBVA", "Yape", etc.)
     *               Ordenado alfabéticamente por nombre de entidad.
     *               Retorna array vacío en caso de error
     */
    public function getAllEntidadesPago(): array
    {
        $query = 'SELECT identidadpago, entidad FROM entidadespago ORDER BY entidad ASC;';
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            error_log('Error en getEntidadesPago PagosOC: ' . $error->getMessage());
            return [];
        }
    }

}

// $orden = new OrdenCompra();

// var_dump($orden->getAll());
