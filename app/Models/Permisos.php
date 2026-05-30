<?php

/**
 * Modelo de Permisos
 * 
 * app/Models/Permisos.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los permisos
 * y accesos de los usuarios según su cargo en el sistema. Controla qué módulos
 * de la aplicación están disponibles para cada rol.
 * 
 */
namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

/**
 * Clase Permisos
 * 
 * Modelo para la gestión de permisos y control de acceso basado en roles (RBAC).
 * Proporciona métodos para verificar y obtener los permisos de acceso a módulos
 * según el cargo del usuario.
 */
class Permisos
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
     * Obtiene los módulos permitidos para un cargo específico
     * 
     * Retorna una lista de nombres de módulos a los cuales un cargo
     * tiene permiso de acceso (permisos = 1). Utilizado para construir
     * menús dinámicos y controlar el acceso a funcionalidades.
     * 
     * @param int $idCargo ID del cargo a consultar
     * @return array Array simple con los nombres de los módulos permitidos
     *               o array vacío si no tiene permisos o hay error
     */
    public function getPermisosByCargo(int $idCargo): array
    {
        $stmt = $this->db->prepare("
            SELECT modulo
            FROM accesos
            WHERE idcargo = :idCargo AND permisos = 1
        ");
        $stmt->execute([':idCargo' => $idCargo]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function tienePermiso(int $idCargo, string $modulo): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM accesos
            WHERE idcargo = :idCargo AND modulo = :modulo AND permisos = 1
        ");
        $stmt->execute([
            ':idCargo' => $idCargo,
            ':modulo' => $modulo,
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }
}