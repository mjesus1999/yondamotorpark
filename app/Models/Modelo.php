<?php

/**
 * Modelo de Modelo (del vehiculo)
 * 
 * app/Models/Modelo.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con los modelos
 * de vehiculos, incluyendo su relacion con marcas, tipos de vehiculos 
 * y gestion de imagenes referencial.
 */

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;
use PDOException;

/**
 * Clase Modelo 
 * 
 * Modelo para gestion de modelos de vehiculos.
 * Proporciona metodos CRUD completos con soporte para imagenes,
 * manejo de resctricciones de integridad y validaciones de duplicados.
 */
class Modelo
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
     * Inicializa la conexion a la base de datos.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los modelos de una marca específica.
     * 
     * Retorna los modelos asociados a una marca, ordenados por tipo de vehiculo,
     * nombre del modelo y año. Incluye informacion del tipo de vehiculo asociado.
     * 
     * @param int $idmarca ID de la marca a consultar 
     * @return array Array asociativo con los modelos de la marca o array vacio en caso de error
     */
    public function getByMarca(int $idmarca): array
    {
        $query = "
            SELECT 
                MD.idmodelo, MD.idtipovehiculo, MD.idmarca, MD.modelo, MD.anio, MD.imagenreferencial,
                TV.tipovehiculo
            FROM modelos MD
            INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
            WHERE MD.idmarca = :idmarca
            ORDER BY TV.tipovehiculo, MD.modelo, MD.anio;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene modelos filtrados por marca y tipo de vehículo
     * 
     * Retorna los modelos que coinciden con una marca y tipo de vehículo específicos,
     * ordenados por nombre de modelo y año. Útil para selecciones en cascada.
     * 
     * @param int $idmarca ID de la marca a consultar
     * @param int $idtipovehiculo ID del tipo de vehículo a consultar
     * @return array Array asociativo con los modelos filtrados o array vacío en caso de error
     */
    public function getByMarcaYTipo(int $idmarca, int $idtipovehiculo): array
    {
        $query = "
            SELECT 
                MD.idmodelo, 
                MD.idtipovehiculo, 
                MD.idmarca, 
                MD.modelo, 
                MD.anio, 
                MD.imagenreferencial,
                TV.tipovehiculo
            FROM modelos MD
            INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
            WHERE MD.idmarca = :idmarca 
              AND MD.idtipovehiculo = :idtipovehiculo
            ORDER BY MD.modelo, MD.anio DESC;
        ";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->bindParam(':idtipovehiculo', $idtipovehiculo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getByMarcaYTipo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un modelo por su ID
     * 
     * Retorno los datos completos de un modelo especifico, incluyendo
     * informacion del tipo de vehiculo asociado.
     * 
     * @param int $idmodelo ID del modelo a cosultar
     * @return array|null Array asociativo con los datos del modelo o null si no existe o hay error.
     */
    public function getById(int $idmodelo): ?array
    {
        $query = "
            SELECT 
                MD.idmodelo, MD.idtipovehiculo, MD.idmarca, MD.modelo, MD.anio, MD.imagenreferencial,
                TV.tipovehiculo
            FROM modelos MD
            INNER JOIN tipovehiculos TV ON MD.idtipovehiculo = TV.idtipovehiculo
            WHERE MD.idmodelo = :idmodelo;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmodelo', $idmodelo, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Registra un nuevo modelo de vehiculo.
     * 
     * Crea un nuevo registro de modelo con su tipo de vehiculo, marca,
     * nombre, año y imagen referencial. Valida restriccion de unicidad.
     * 
     * @param array $data Array asociativo con los datos del modelo:
     *                      - idtipovehiculo: int (ID del tipo vehiculo)
     *                      - idmarca: int (ID de la marca)
     *                      - modelo: string (Nombre del modelo)
     *                      - anio: string (Año del modelo)
     *                      - imagenreferencial: string (URL o ruta de la imagen)
     * @return int ID del modelo creado, -1 en error generico, -2 si el modelo ya existe (validacion UNIQUE)
     */
    public function create(array $data): int
    {
        $query = "
            INSERT INTO modelos (idtipovehiculo, idmarca, modelo, anio, imagenreferencial)
            VALUES (:idtipovehiculo, :idmarca, :modelo, :anio, :imagenreferencial);
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idtipovehiculo', $data['idtipovehiculo'], PDO::PARAM_INT);
            $stmt->bindParam(':idmarca', $data['idmarca'], PDO::PARAM_INT);
            $stmt->bindParam(':modelo', $data['modelo'], PDO::PARAM_STR);
            $stmt->bindParam(':anio', $data['anio'], PDO::PARAM_STR);
            $stmt->bindParam(':imagenreferencial', $data['imagenreferencial'], PDO::PARAM_STR);
            $stmt->execute();
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') { // Error de UNIQUE constraint
                return -2;
            }
            return -1;
        }
    }

    /**
     * Actualiza los datos de un modelo existente
     * 
     * Modifica la informacion de un modelo. La imagen referencial solo se actualiza
     * si se proporciona un nuevo valor (diferente de null).
     * Actualiza automaticamente el campo 'modificado' con la fecha actual.
     * 
     * @param array $data Array asociativo con los datos a actualizar:
     *                    - idmodelo: int (ID del modelo, requerido)
     *                    - idtipovehiculo: int (Nuevo tipo de vehículo)
     *                    - idmarca: int (Nueva marca)
     *                    - modelo: string (Nuevo nombre)
     *                    - anio: string (Nuevo año)
     *                    - imagenreferencial: string|null (Nueva imagen, null para no actualizar)
     * @return int Numero de filas afectadas, -1 en error generico, -2 si el modelo ya existe con esos datos 
     */
    public function update(array $data): int
    {
        // Si no se sube una nueva imagen, no actualizamos ese campo
        $sqlImagen = ($data['imagenreferencial'] !== null) ? ", imagenreferencial = :imagenreferencial" : "";

        $query = "
            UPDATE modelos SET 
                idtipovehiculo = :idtipovehiculo,
                idmarca = :idmarca,
                modelo = :modelo,
                anio = :anio,
                modificado = NOW()
                {$sqlImagen}
            WHERE idmodelo = :idmodelo;
        ";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idtipovehiculo', $data['idtipovehiculo'], PDO::PARAM_INT);
            $stmt->bindParam(':idmarca', $data['idmarca'], PDO::PARAM_INT);
            $stmt->bindParam(':modelo', $data['modelo'], PDO::PARAM_STR);
            $stmt->bindParam(':anio', $data['anio'], PDO::PARAM_STR);
            $stmt->bindParam(':idmodelo', $data['idmodelo'], PDO::PARAM_INT);

            if ($data['imagenreferencial'] !== null) {
                $stmt->bindParam(':imagenreferencial', $data['imagenreferencial'], PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                return -2;
            }
            return -1;
        }
    }

    /**
     * Elimina un modelo
     * 
     * Elimina fisicamente un modelo de la base de datos. Si el modelo tiene 
     * vehiculos asociados, la operacion fallara debido a restriccion de integridad referencial (FOREIGN KEY)
     * 
     * @param int $idmodelo ID del modelo a eliminar
     * @return int Numero de registros eliminados (1 si existoso, 0 si no existe), -1 en error generico,
     *              -2 si tiene vehiculos asociados (Violacion KF)
     */
    public function delete(int $idmodelo): int
    {
        $query = "DELETE FROM modelos WHERE idmodelo = :idmodelo";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmodelo', $idmodelo, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {

            if ($e->getCode() == '23000') {  // Modelo con vehículos
                return -2;
            }
            return -1;
        }
    }

    /**
     * Obtiene el conteo de modelos para una marca especifica 
     * 
     * Cuenta la cantidad de modelos asociados a una marca determinada.
     * Util para validaciones antes de eliminar marcas o para estadisticas 
     *
     * @param int $idmarca ID de la marca a consultar
     * @return int Cantidad de modelos asociados a la marca o 0 en caso de error
     */
    public function countByMarca(int $idmarca): int
    {
        $query = "SELECT COUNT(idmodelo) FROM modelos WHERE idmarca = :idmarca";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idmarca', $idmarca, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Agrega un nuevo año a un modelo existente
     * 
     * Crea una copia de un modelo existente con un nuevo año.
     * Útil para registrar el mismo modelo pero de un año diferente,
     * manteniendo la misma marca, tipo de vehículo, nombre e imagen referencial.
     * 
     * Proceso:
     * 1. Obtiene los datos del modelo base por su ID
     * 2. Crea un nuevo registro duplicando toda la información excepto el año
     * 3. Asigna el nuevo año especificado
     * 4. Retorna el ID del nuevo modelo creado
     * 
     * @param int $idmodeloBase ID del modelo base a copiar
     * @param int $anio Nuevo año para el modelo
     * @return int ID del nuevo modelo creado, -1 en caso de error, -2 si ya existe ese año
     * 
     * @throws PDOException Si hay error en la base de datos
     */
    public function addYearToModelo(int $idmodeloBase, int $anio): int
    {
        try {
            // Obtener el modelo base
            $modeloBase = $this->getById($idmodeloBase);

            if (!$modeloBase) {
                error_log("Modelo base no encontrado: {$idmodeloBase}");
                return -1;
            }

            // Verificar si ya existe ese modelo con ese año
            $query = "
                SELECT COUNT(*) 
                FROM modelos 
                WHERE idmarca = :idmarca 
                  AND idtipovehiculo = :idtipovehiculo 
                  AND modelo = :modelo 
                  AND anio = :anio
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idmarca' => $modeloBase['idmarca'],
                ':idtipovehiculo' => $modeloBase['idtipovehiculo'],
                ':modelo' => $modeloBase['modelo'],
                ':anio' => (string) $anio
            ]);

            if ($stmt->fetchColumn() > 0) {
                error_log("Ya existe un modelo con ese año: {$modeloBase['modelo']} - {$anio}");
                return -2; // Ya existe
            }

            // Crear nuevo modelo con el nuevo año
            $nuevoModelo = [
                'idtipovehiculo' => $modeloBase['idtipovehiculo'],
                'idmarca' => $modeloBase['idmarca'],
                'modelo' => $modeloBase['modelo'],
                'anio' => (string) $anio,
                'imagenreferencial' => $modeloBase['imagenreferencial']
            ];

            $nuevoId = $this->create($nuevoModelo);

            if ($nuevoId > 0) {
                error_log("Nuevo año agregado exitosamente: Modelo {$modeloBase['modelo']} - Año {$anio} - ID: {$nuevoId}");
            }

            return $nuevoId;

        } catch (PDOException $e) {
            error_log("Error en addYearToModelo: " . $e->getMessage());
            return -1;
        }
    }

}