<?php

/**
 * Modelo de local
 * app/Models/Local.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los locales
 * o sucursales de la empresa, incluyendo su informacion de ubicacion, responsables
 * y datos de contactp.
 * 
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Local 
 * 
 * Modelo para la gestion de locales/sucursales.
 * Proporciona metodos CRUD para locales, incluyendo informacion
 * geografica completa (distrito, provincia, departamento).
 */
class Local
{

    /**
     * Instancia de conexion a la base de datos
     * 
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
     * Obtiene todos los locales activos con informacion geografica 
     * 
     * Retorna un listado completo de locales con estado activo,
     * incluyendo datos de la ubicacion (distrito, provincia y departamento).
     * 
     * @return array Array asociativo con los datos de todos los locales activos o array vacio en caso de error
     */
    public function getAll(): array
    {
        try {
            $query = "
            SELECT 
                l.*, 
                d.distrito, 
                p.provincia, 
                dp.departamento
            FROM locales l
            INNER JOIN distritos d ON l.iddistrito = d.iddistrito
            INNER JOIN provincias p ON d.idprovincia = p.idprovincia
            INNER JOIN departamentos dp ON p.iddepartamento = dp.iddepartamento
            WHERE l.estado = 'ACT';
        ";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un local por su ID
     * 
     * Retorna informacion basica de un local especifico (responsable y telefono).
     * 
     * @param int $idlocal ID del local a consultar
     * @return array|null Array asociativo con los datos del local o array vacio en caso de error
     */
    public function getById($idlocal = 0): ?array
    {
        $query = "SELECT idlocal, responsable, telefono FROM locales WHERE idlocal = ?";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($idlocal));
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los locales activos desde la base de datos.
     *
     * Este método invoca el procedimiento almacenado `sp_getAll_locales` para traer la lista de locales
     * activos, incluyendo únicamente los campos: ID, nombre y dirección.
     *
     * @return array<int, array<string,string>> Lista de locales activos en formato:
     * [
     *   ['id' => '1', 'local' => 'Motorpark/Ica/Chicnha/Chincha Alta',],
     * ]
     * En caso de error, retorna un array vacío.
     */
    public function getAllLocales(): array
    {
        $query = "CALL sp_getAll_locales()";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Crea un nuevo local.
     * 
     * Registra un nuevo local/sucursal con toda su informacion de ubicacion, responsable y datos de contacto.
     * 
     * @param array $params Array asociativo con los datos del local:
     *                      - tienda: string (Nombre del local)
     *                      - iddistrito: int (ID del distrito)
     *                      - idmotorpark: int (ID del motorpark asociado)
     *                      - principal: string|int (Indicador de local principal)
     *                      - responsable: string (Nombre del responsable)
     *                      - correo: string|null (Email del local)
     *                      - direccion: string|null (Dirección física)
     *                      - telefono: string|null (Teléfono de contacto)
     * @return int ID del local creado o -1 en caso de error
     */
    public function create($params = []): int
    {
        $query = "INSERT INTO locales(tienda, iddistrito, idmotorpark, principal, responsable, correo, direccion, telefono) 
                    VALUES (:tienda,:iddistrito,:idmotorpark,:principal,:responsable,:correo,:direccion,:telefono)";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(
                array(
                    ':tienda' => $params['tienda'],
                    ':iddistrito' => $params['iddistrito'],
                    ':idmotorpark' => $params['idmotorpark'],
                    ':principal' => $params['principal'],
                    ':responsable' => $params['responsable'],
                    ':correo' => $params['correo'], // Puede ser null
                    ':direccion' => $params['direccion'], // Puede ser null
                    ':telefono' => $params['telefono'] // Puede ser null

                )
            );
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Actualiza los datos de un local existente.
     * 
     * Modifica la informacion del responsable y telefono de contacto del local.
     * Actualiza automaticamente el campo 'Modificado' con la fecha actual.
     * 
     * @param array $params Array asociativo con los datos a actualizar:
     *                      - idlocal: int (ID del local, requerido)
     *                      - responsable: string (Nuevo responsable)
     *                      - telefono: string (Nuevo teléfono)
     * @return int Numero de filas afectadas o -1 en caso de error
     */
    public function update($params): int
    {
        try {
            $query = "UPDATE locales SET  
                responsable = :responsable, 
                telefono = :telefono, 
                modificado = NOW()
              WHERE idlocal = :idlocal";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':responsable' => $params['responsable'],
                ':telefono' => $params['telefono'],
                ':idlocal' => $params['idlocal']
            ]);

            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Deshabilita un local.
     * 
     * Marca el local como inactivo cambiando a su estado a 'INACT'.
     * No elimina el registro fisicamente de la base de datos.
     * 
     * @param int $idlocal ID local a deshabilitar
     * @return int Numero de filas afectadas o -1 en caso de error
     */
    public function disable($idlocal = -1): int
    {
        try {
            $stmt = $this->db->prepare("UPDATE locales SET estado = 'INACT' WHERE idlocal=?");
            $stmt->execute(array($idlocal));
            $stmt->fetchAll(PDO::FETCH_ASSOC);
            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Buscar un local por su nombre de tienda
     * 
     * Realiza una busqueda exacta del nombre de tienda y retorna
     * unicamente el ID del local si existe.
     * @param string $tienda Nombre de la tienda a buscar
     * @return array|null Array asociativo con el idlocal o null si no existe
     */
    public function getByTienda(string $tienda): ?array
    {
        $stmt = $this->db->prepare("
            SELECT idlocal
            FROM locales
            WHERE tienda = :tienda
            LIMIT 1
        ");
        $stmt->bindParam(':tienda', $tienda, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

// $local = new Local();

// var_dump($local->getAll());
