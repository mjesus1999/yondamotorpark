<?php

/**
 * Modelo de Colaborador
 * 
 * app/Models/Colaborador.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los colaboradores
 * del sistema. Un colaborador es un usuario del sistema asociado a un contrato
 * laboral que tiene credenciales de acceso (usuario y contraseña) y puede estar
 * asignado a un local específico. Incluye funcionalidades para registro de
 * colaboradores con restricciones horarias configurables y asignación opcional
 * a locales para control de acceso geográfico.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Colaborador
 * 
 * Modelo que representa y gestiona los colaboradores en el sistema.
 * Un colaborador es la representación digital de un empleado con acceso
 * al sistema, vinculado a su contrato laboral y con credenciales únicas
 * de autenticación. Permite configurar restricciones de acceso por horario
 * y asignación a locales específicos para control de operaciones por ubicación.
 */
class Colaborador
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
     * Registra un nuevo colaborador con configuración completa
     * 
     * Crea un nuevo registro de colaborador en el sistema con todas las opciones
     * de configuración disponibles: asignación a contrato laboral, local específico,
     * credenciales de acceso y restricciones horarias. Este método es la versión
     * completa que permite configurar todos los aspectos del colaborador.
     * 
     * La restricción horaria determina si el colaborador puede acceder al sistema
     * solo durante su horario laboral asignado ('S') o en cualquier momento ('N').
     * Por defecto, se establece restricción horaria ('S') por seguridad.
     * 
     * @param int $idContrato ID del contrato laboral asociado al colaborador.
     * @param string $usernick Nombre de usuario único para autenticación.
     * @param string $passwordHash Contraseña hasheada
     * @param string $restr Restricción horaria: 'S' (Sí, con restricción) o 
     *                      'N' (No, sin restricción). Por defecto 'S'
     * @param mixed $idlocal ID del local al que se asigna el colaborador.
     *                       Null si no tiene local específico asignado
     * @return int ID del colaborador creado (positivo) si es exitoso,
     *             -1 en caso de error
     */
    public function create(int $idContrato, string $usernick, string $passwordHash, string $restr = 'S', ?int $idlocal = null): int
    {
        //Forzar el valor de entrar en restriccion Horaria
        $restr = (strtoupper($restr) === 'N') ? 'N' : 'S';

        $sql = "INSERT INTO colaboradores
              (idcontratolaboral, idlocal, usernick, userpassword, restriccionhoraria, creado)
            VALUES
              (:idcontrato, :idlocal, :usernick, :userpassword, :restr, NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':idcontrato' => $idContrato,
                ':idlocal' => $idlocal,
                ':usernick' => $usernick,
                ':userpassword' => $passwordHash,
                ':restr' => $restr,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Colaborador::create error - ' . $e->getMessage());
            return -1;
        }
    }

}
