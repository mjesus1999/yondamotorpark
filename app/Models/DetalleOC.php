<?php

/**
 * Modelo de Detalle de Orden de Compra
 * 
 * app/Models/DetalleOC.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con el detalle
 * de las órdenes de compra de vehículos. Cada registro representa un
 * vehículo específico incluido en una orden de compra, con su precio
 * de compra unitario. Permite construir órdenes de compra multi-ítem
 * donde cada vehículo puede tener un precio diferente según negociación
 * con el concesionario.
 */
namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

/**
 * Clase DetalleOC
 * 
 * Modelo para la gestión del detalle de órdenes de compra.
 * Proporciona métodos para registrar ítems (vehículos) en una orden
 * de compra existente. Cada detalle vincula un vehículo específico
 * con su precio de compra negociado, permitiendo que una misma orden
 * incluya múltiples unidades con precios individualizados según
 * modelo, versión, color o condiciones especiales de negociación.
 */
class DetalleOC
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
     * Registra un ítem (vehículo) en una orden de compra
     * 
     * Crea un nuevo registro de detalle asociando un vehículo específico
     * a una orden de compra existente con su precio unitario de compra.
     * Este método se ejecuta múltiples veces cuando se agregan varios
     * vehículos a una misma orden de compra.
     * 
     * @param array $params Array asociativo con los datos del ítem:
     *                      - idordencompra: int (ID de la orden de compra padre)
     *                      - idvehiculo: int (ID del vehículo a comprar)
     *                      - preciocompra: decimal (Precio unitario negociado)
     * @return int ID del detalle creado (última inserción),
     *             o -1 en caso de error
     */
    public function create($params = []): int
    {
        $query = "INSERT INTO detordencompra(idordencompra,idvehiculo,preciocompra) VALUES(:idordencompra,:idvehiculo,:preciocompra)";
        try {

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(

                ':idordencompra' => $params['idordencompra'],
                ':idvehiculo' => $params['idvehiculo'],
                ':preciocompra' => $params['preciocompra']
            ));
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
    
}
