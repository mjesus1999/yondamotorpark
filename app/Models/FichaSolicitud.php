<?php

/**
 * Modelo de Ficha de Solicitud
 * 
 * app/Models/FichaSolicitud.php
 * 
 * Gestiona las operaciones de base de datos relacionadas con las fichas
 * de solicitud de crédito vehicular. Coordina la creación de personas
 * (titulares, cónyuges, avales), búsqueda de registros existentes,
 * y generación de fichas con toda la información requerida para
 * evaluación crediticia. Integra datos de cotizaciones y personas
 * en el proceso de solicitud.
 */
namespace App\Models;

use App\Core\Database;
use PDO;

use PDOException;
use Exception;

/**
 * Clase FichaSolicitud
 * 
 * Modelo para la gestión de fichas de solicitud de crédito.
 * Proporciona métodos para CRUD de fichas, gestión de personas
 * involucradas (titular, cónyuge, aval, aval-cónyuge), búsqueda
 * de personas existentes por DNI, y actualización de estados de
 * cotizaciones asociadas. Cada ficha representa una solicitud
 * formal de crédito vehicular con todos sus participantes.
 */
class FichaSolicitud
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
     * Obtiene los datos de una cotización específica
     * 
     * Recupera información detallada de una cotización desde la vista
     * vwGetAllCotizacion, incluyendo datos del vehículo, condiciones
     * financieras, y datos básicos del cliente. Esta información es
     * utilizada como base para crear la ficha de solicitud.
     * 
     * @param string $id ID de la cotización
     * @return array Array asociativo con los datos de la cotización.
     *               Incluye:
     *               - idcotizacion: int|string (Identificador único)
     *               - tipocotizacion: string (Tipo de cotización)
     *               - vehiculo: string (Vehículo concatenado con color)
     *               - precioventa: decimal (Precio de venta del vehículo)
     *               - moneda: string (Moneda: USD/PEN)
     *               - inicial: decimal (Monto inicial/cuota inicial)
     *               - nombrecliente: string (Nombre completo del cliente)
     *               - documento: string (Número de documento)
     *               - telefono: string (Teléfono de contacto)
     *               - direccion: string (Dirección del cliente)
     *               - numcuotas: int (Número de cuotas)
     *               - valorcuota: decimal (Valor de cada cuota)
     *               - estadocotizacion: string (Estado actual)
     *               Retorna array vacío si no se encuentra
     */
    public function getDatosCotizacion(string $id): array
    {

        $query = "SELECT 
              idcotizacion,
              tipocotizacion,
              CONCAT(vehiculo, ' / ', color) AS vehiculo,
              precioventa,
              moneda,
              inicial,
              nombrecliente,
              documento,
              telefono,
              direccion,
              numcuotas,
              valorcuota,
              estadocotizacion
            FROM vwGetAllCotizacion
            WHERE idcotizacion = :idcotizacion LIMIT 1;";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idcotizacion", $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una persona registrada por su DNI
     * 
     * Consulta si una persona ya existe en la base de datos mediante su
     * número de DNI. Retorna información completa incluyendo datos
     * personales transformados (género y estado civil legibles).
     * Útil para evitar duplicados y reutilizar registros existentes
     * en nuevas fichas de solicitud.
     * 
     * @param string $dni Número de DNI a buscar
     * @return array|false Array asociativo con datos de la persona si existe:
     *                     - idpersona: int (ID único de la persona)
     *                     - nombres: string (Nombres)
     *                     - apellidos: string (Apellidos)
     *                     - dni: string (Número de documento)
     *                     - telprimario: string (Teléfono principal)
     *                     - genero: string (Masculino/Femenino)
     *                     - estadocivil: string (Estado civil legible)
     *                     Retorna false si no existe o hay error
     */
    public function searchPersonaByDNI(string $dni): array|false
    {
        $query = "SELECT
                idpersona,
                nombres,
                apellidos,
                nrodoc AS dni,
                telprimario,
                CASE genero
                    WHEN 'M' THEN 'Masculino'
                    ELSE 'Femenino' 
                    END AS genero,
               
                CASE estadocivil
                    WHEN 'SOL' THEN 'Soltero(a)'
                    WHEN 'CAS' THEN 'Casado(a)'
                    WHEN 'VDO' THEN 'Viudo(a)'
                    WHEN 'DVC' THEN 'Divorciado(a)'
                    WHEN 'CNV' THEN 'Conviviente'
                    ELSE 'No especificado'
                END AS estadocivil
              FROM personas 
              WHERE tipodoc = 'DNI' AND nrodoc = :dni";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":dni", $dni, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: false;
        } catch (PDOException $e) {
            error_log("Error en searchPersonaByDNI: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra una nueva persona en el sistema
     * 
     * Crea un nuevo registro de persona con sus datos personales y
     * de ubicación. Utilizado para registrar titulares, cónyuges,
     * avales y aval-cónyuges que no existen previamente en la base
     * de datos antes de crear la ficha de solicitud.
     * 
     * @param mixed $params Array asociativo con los datos de la persona:
     *                      - iddistrito: int (ID del distrito de residencia)
     *                      - apellidos: string (Apellidos completos)
     *                      - nombres: string (Nombres completos)
     *                      - tipodoc: string (Tipo documento: DNI, CE, etc.)
     *                      - nrodoc: string (Número de documento)
     *                      - genero: string (M: Masculino, F: Femenino)
     *                      - telprimario: string (Teléfono principal)
     *                      - direccion: string (Dirección completa)
     * @return int ID de la persona creada, o -1 en caso de error
     */
    public function createPersona($params = []): int
    {
        $query = "INSERT INTO personas
                (iddistrito, apellidos, nombres, tipodoc, nrodoc, genero, telprimario, direccion) 
              VALUES 
                (:iddistrito, :apellidos, :nombres, :tipodoc, :nrodoc, :genero, :telprimario, :direccion);";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":iddistrito" => $params["iddistrito"],
                ":apellidos" => $params["apellidos"],
                ":nombres" => $params["nombres"],
                ":tipodoc" => $params["tipodoc"],
                ":nrodoc" => $params["nrodoc"],
                ":genero" => $params["genero"],
                ":telprimario" => $params["telprimario"],
                ":direccion" => $params["direccion"]
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en createPersona: " . $e->getMessage());
            return -1;
        }
    }

    /**
     * Crea una nueva ficha de solicitud de crédito
     * 
     * Registra una ficha completa que vincula una cotización con todas
     * las personas involucradas (cónyuge, aval, aval-cónyuge) y datos
     * adicionales como fecha de visita y documentación adjunta.
     * El ID del colaborador de créditos se obtiene automáticamente
     * de la sesión del usuario autenticado.
     * 
     * @param array $params Array asociativo con los datos de la ficha:
     *                      - idcotizacion: int (ID de la cotización asociada)
     *                      - idconyuge: int|null (ID del cónyuge, null si no aplica)
     *                      - idaval: int|null (ID del aval, null si no requiere)
     *                      - idavalconyuge: int|null (ID cónyuge del aval)
     *                      - fechavisita: string (Fecha visita domiciliaria YYYY-MM-DD)
     *                      - rutaficha: string (Ruta del PDF generado)
     *                      - comentarios: string (Observaciones adicionales)
     *                      - estado: string (Estado inicial de la ficha)
     * @throws \Exception Si no hay usuario autenticado en sesión
     * @return int ID de la ficha creada, o -1 en caso de error
     */
    public function createFicha($params = []): int
    {
        $query = "INSERT INTO fichasolicitud(
                    idcotizacion,
                    idcolcredito,
                    idconyuge,
                    idaval,
                    idavalconyuge,
                    fechavisita,
                    rutaficha,
                    comentarios,
                    estado
              )
              VALUES (
                    :idcotizacion,
                    :idcolcredito,
                    :idconyuge,
                    :idaval,
                    :idavalconyuge,
                    :fechavisita,
                    :rutaficha,
                    :comentarios,
                    :estado
              )";


        try {
            if (!isset($_SESSION['user']['id'])) {
                throw new Exception("Usuario no autenticado en sesión");
            }

            $idUsuario = $_SESSION['user']['id'];

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":idcotizacion" => $params["idcotizacion"],
                ":idcolcredito" => $idUsuario,
                ":idconyuge" => $params["idconyuge"],
                ":idaval" => $params["idaval"],
                ":idavalconyuge" => $params["idavalconyuge"],
                ":fechavisita" => $params["fechavisita"],
                ":rutaficha" => $params["rutaficha"],
                ":comentarios" => $params["comentarios"],
                ":estado" => $params["estado"],
            ]);

            return (int) $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("Error en createFicha: " . $e->getMessage());
            return -1;
        }
    }

    /**
     * Actualiza el estado de una cotización
     * 
     * Modifica el estado de una cotización específica. Utilizado
     * principalmente para cambiar el estado a "En proceso" o "Aprobado"
     * cuando se crea o aprueba una ficha de solicitud asociada.
     * 
     * Estados comunes:
     * - Pendiente: Cotización sin procesar
     * - En proceso: Tiene ficha de solicitud asociada
     * - Aprobado: Crédito aprobado
     * - Rechazado: Crédito rechazado
     * - Anulado: Cotización cancelada
     * 
     * @param int $id ID de la cotización a actualizar
     * @param string $estado Nuevo estado de la cotización
     * @return int Número de filas afectadas (1 si exitoso, 0 si no existe), o -1 en caso de error
     */
    public function updateCotizacion(int $id, string $estado): int
    {
        $query = "UPDATE cotizaciones SET estadocotizacion = :estado WHERE idcotizacion = :id";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->bindValue(":estado", $estado, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error en updateCotizacion: " . $e->getMessage());
            return -1;
        }
    }
}
