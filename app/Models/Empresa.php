<?php

/**
 * Modelo de Empresa
 * 
 * app/Models/Empresa.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las empresas cliente
 * del sistema, incluyendo informacion fiscal (RUC), de contacto, ubicacion geografica
 * y representantes legales.
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Empresa
 * 
 * Modelo para la gestion de empresas.
 * Proporciona metodos CRUD para empresas cliente, validacion de RUC, busquedas especificas
 * y relaciones con ubicacion geografica.
 */
class Empresa
{
    /**
     * Instancia de conexion a la base de datos
     * @var PDO
     */
    private PDO $db;

    /**
     * Contructor del modelo
     * 
     * Inicializa la conexion a la base de datos 
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todas las empresas que son clientes 
     * 
     * Retorna un listado de empresas registradas como clientes de tipo 'E' (empresa) con estado activo
     * incluyendo su informacion fiscal, de contacto y ubicacion geografica completa (DIS, PROV, DEPT)
     * 
     * @return array|null Array asociativo con los datos de las empresas cliente o array vacio en caso de error
     */
    public function getAllEmpresasCliente(): ?array
    {
        try {

            $query = "SELECT
                            c.idcliente,
                            e.idempresa,
                            CONCAT(dep.departamento, ' / ', p.provincia, ' / ', d.distrito) AS ubicacion,
                            e.direccion,
                            e.representante AS responsable,
                            e.ruc,
                            e.nombrecomercial,
                            e.telprimario,
                            e.email,
                            e.estado
                        FROM clientes c
                        INNER JOIN empresas e ON c.idempresa = e.idempresa
                        INNER JOIN distritos d ON e.iddistrito = d.iddistrito
                        INNER JOIN provincias p ON d.idprovincia = p.idprovincia
                        INNER JOIN departamentos dep ON p.iddepartamento = dep.iddepartamento
                        WHERE c.tipocliente = 'E'  AND c.estado = 'ACT'
                        ORDER BY c.idcliente DESC;";

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
     * Obtiene una empresa por su ID
     * 
     * Retorna los datos principales de una empresa especifica, incluyendo informacion fiscal y de contacto
     * 
     * @param mixed $idempresa ID de la empresa a consultar 
     */
    public function getById($idempresa = 0): ?array
    {
        $query = "
                SELECT
                    e.idempresa,
                    e.razonsocial,
                    e.nombrecomercial,
                    e.ruc,
                    e.representante,
                    e.email,
                    e.telprimario
                FROM empresas e
              
                WHERE e.idempresa = ?;
                
                ";
        try {

            $cmd = $this->db->prepare($query);
            $cmd->execute(array($idempresa));
            $results = $cmd->fetch(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    /**
     * Crea un nuevo registro de empresa
     * 
     * Inserta una nueva empresa en la base de datos con su informacion fiscal, comercial, de contacto y 
     * ubicacion geografica completa.
     * 
     * @param array $params Array asociativo con los datos de la empresa:
     *                      - iddistrito: int (ID del distrito)
     *                      - razonsocial: string (Razón social de la empresa)
     *                      - nombrecomercial: string (Nombre comercial)
     *                      - ruc: string (Registro Único de Contribuyentes)
     *                      - representante: string (Representante legal)
     *                      - email: string|null (Email de contacto)
     *                      - direccion: string (Dirección física)
     *                      - referencia: string|null (Referencia de ubicacion)
     *                      - latitud: float|null (Coordenada de latitud)
     *                      - longitud: float|null (Coordenada de longitud)
     *                      - telprimario: string (Teléfono principal)
     *                      - telsecundario: string|null (Teléfono secundario)
     * @return int ID de la empresa creada o -1 en caso de error
     */
    public function create($params = []): int
    {
        $query = "INSERT INTO empresas (
                    iddistrito,
                    razonsocial,
                    nombrecomercial,
                    ruc,
                    representante,
                    email,
                    direccion,
                    referencia,
                    latitud,
                    longitud,
                    telprimario,
                    telsecundario
                ) VALUES(:iddistrito,:razonsocial,:nombrecomercial,:ruc,:representante,:email,:direccion,:referencia,:latitud,:longitud,:telprimario,:telsecundario) ";

        try {

            $cmd = $this->db->prepare($query);
            $cmd->execute(array(
                ':iddistrito' => $params['iddistrito'],
                ':razonsocial' => $params['razonsocial'],
                ':nombrecomercial' => $params['nombrecomercial'],
                ':ruc' => $params['ruc'],
                ':representante' => $params['representante'],
                ':email' => $params['email'], // null
                ':direccion' => $params['direccion'],
                ':referencia' => $params['referencia'], // null
                ':latitud' => $params['latitud'], // null
                ':longitud' => $params['longitud'], // null
                ':telprimario' => $params['telprimario'],
                ':telsecundario' => $params['telsecundario'] //null

            ));

            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Actualiza los datos de una empresa existente
     * 
     * Modifica la informacion principal de una empresa, incluyendo datos fiscales, comerciales y de contacto
     * 
     * @param array $params Array asociativo con los datos a actualizar:
     *                      - idempresa: int (ID de la empresa, requerido)
     *                      - razonsocial: string (Nueva razon social)
     *                      - nombrecomercial: string (Nuevo nombre comercial)
     *                      - ruc: string (Nuevo RUC)
     *                      - representante: string (Nuevo representante legal)
     *                      - email: string (Nuevo email)
     *                      - telprimario: string (Nuevo teléfono principal)
     * @return int Numero de filas afectadas o -1 en caso de error
     */
    public function update($params = []): int
    {
        try {
            $query = "UPDATE empresas SET  
                razonsocial= ?, 
                nombrecomercial = ?, 
                ruc = ?,
                representante = ?,
                email = ?,
                telprimario = ?
              WHERE idempresa  = ?";

            $cmd = $this->db->prepare($query);
            $cmd->execute([
                $params['razonsocial'],
                $params['nombrecomercial'],
                $params['ruc'],
                $params['representante'],
                $params['email'],
                $params['telprimario'],
                $params['idempresa']
            ]);

            return (int) $cmd->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }

    /**
     * Verifica si un RUC ya existe.
     * 
     * Comprueba la existencia de un RUC en la base de datos, con opcion de excluir un ID especifico
     * (es util para las validacion en actualizacion).
     * 
     * @param string $ruc RUC a verificar 
     * @param mixed $excluirId ID de empresa a exluir de la empresa de la busqueda
     * @return bool True si el RUC ya existe, false en caso contrario
     */
    public function rucExiste(string $ruc, ?int $excluirId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM empresas  WHERE ruc=:ruc";
        $params = [":ruc" => $ruc];

        if ($excluirId !== null) {
            $sql .= " AND idempresa !=:id";
            $params[":id"] = $excluirId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $resultado = $stmt->fetch();

        return $resultado['total'] > 0;
    }

    /**
     * Buscar empresa por RUC en la base de datos local
     * 
     * Realiza una busqueda exacta del registro unico de contribuyentes (RUC)
     * y retorna informacion basica de la empresa si existe.
     *  
     * @param string $ruc RUC de la empresa a buscar
     */
    public function searchByRUC(string $ruc): ?array
    {
        $query = "SELECT 
                    idempresa, 
                    razonsocial, 
                    nombrecomercial, 
                    representante,
                    email,
                    telprimario
                FROM empresas 
                WHERE ruc = :ruc 
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':ruc', $ruc, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            //error_log('Error en búsqueda por RUC: ' . $e->getMessage());
            return null;
        }
    }
}
