<?php

/**
 * Modelo de Cliente
 * 
 * Gestiona las operaciones de base de datos relacionales con los clientes del sistema, tanto personas naturales como empresas,
 * incluyendo su creacion, consulta y gestion de estados 
 */
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Clase Cliente 
 * 
 * Modelo para la gestion de cliente del sistema.
 * Proporciona metodos para crear, consultar y administrar clientes, diferenciando entre personas naturales y empresas
 */
class Cliente
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
     * Obtiene el tipo de cliente por su ID
     * 
     * Determina si un cliente es tipo 'P' (Persona) o 'E' (Empresa).
     * 
     * @param int $id ID del cliente
     * @return string|null Tipo de cliente ('P' o 'E') o null si no existe
     */
    public function getTipoClienteById(int $id): ?string
    {
        try {
            $query = "SELECT tipocliente FROM clientes WHERE idcliente = :idcliente";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':idcliente' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['tipocliente'] : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Busca un cliente persona por DNI
     * 
     * Realiza una busqueda de clientes de tipo persona (tipocliente = 'P')
     * utilizando el numero de documento de identidad.
     * 
     * @param string $dni Numero de documento de identidad
     * @return array Array asociativo con los datos del cliente (idcliente, cliente, nrodoc, telprimario) o array vacio si no se encuentra y hay error.
     */
    public function searchClienteDB(string $dni): array
    {
        $query = "SELECT c.idcliente,
                        CONCAT(p.apellidos, ' ' ,p.nombres) AS cliente,
                        p.nrodoc,
                        p.telprimario 
                FROM clientes c
                INNER JOIN personas p ON p.idpersona = c.idpersona
                WHERE p.nrodoc = :dni AND c.tipocliente = 'P'
                LIMIT 1;";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : [];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Crea un cliente en el sistema
     * 
     * Registra un nuevo cliente asociado a una persona o empresa.
     * El colaborador que registra se obtiene automaticamente de la sesion.
     * 
     * @param array $params Array asociativo con los datos del cliente:
     *                      - idpersonas: int (ID de la persona, requerido si el tipocliente = 'P')
     *                      - idempresa: int|null (ID de la empresa, requerido si tipocliente = 'E')
     *                      - idcolactualiza: int|null (ID del colaborador que actualiza)
     *                      - tipocliente: string ('P' para persona y 'E' para empresa)
     * @return int ID del cliente creado o -1 en caso de error 
     */
    public function create($params = []): int
    {
        $query = "INSERT INTO clientes(idpersona, idempresa, idcolregistra, idcolactualiza, tipocliente)
              VALUES(:idpersona, :idempresa, :idcolregistra, :idcolactualiza, :tipocliente)";
        try {
            $idUsuario = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':idpersona' => $params['idpersona'],
                ':idempresa' => $params['idempresa'],
                ':idcolregistra' => $idUsuario,
                ':idcolactualiza' => $params['idcolactualiza'],
                ':tipocliente' => $params['tipocliente'],
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
    

    /**
     * Deshabilita un cliente
     * 
     * Marca el cliente como inactivo cambiando su estado a 'INACT'.
     * No elimina el registro fisicamente de la base de datos.
     * 
     * @param int $idcliente ID del cliente a deshabilitar
     * @return int Numero de filas afectadas o -1 en caso de error
     */
    public function disabled($idcliente = -1): int
    {
        try {
            $query = "UPDATE clientes SET estado = 'INACT' WHERE idcliente = :id ";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':id' => $idcliente));

            return (int) $stmt->rowCount();
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
}
