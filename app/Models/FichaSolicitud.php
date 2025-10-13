<?php

namespace App\Models;

use App\Core\Database;
use PDO;

use PDOException;
use Exception;

class FichaSolicitud
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }



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
    public function createPersona($params = []): int
    {
        $query = "INSERT INTO personas
                (iddistrito, apellidos, nombres, tipodoc, nrodoc, genero, telprimario, direccion) 
              VALUES 
                (:iddistrito, :apellidos, :nombres, :tipodoc, :nrodoc, :genero, :telprimario, :direccion);";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":iddistrito"   => $params["iddistrito"],
                ":apellidos"    => $params["apellidos"],
                ":nombres"      => $params["nombres"],
                ":tipodoc"      => $params["tipodoc"],
                ":nrodoc"       => $params["nrodoc"],
                ":genero"       => $params["genero"],
                ":telprimario"  => $params["telprimario"],
                ":direccion"    => $params["direccion"]
            ]);

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en createPersona: " . $e->getMessage());
            return -1;
        }
    }

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
                ":idcotizacion"   => $params["idcotizacion"],
                ":idcolcredito"   => $idUsuario,
                ":idconyuge"      => $params["idconyuge"],
                ":idaval"         => $params["idaval"],
                ":idavalconyuge"  => $params["idavalconyuge"],
                ":fechavisita"    => $params["fechavisita"],
                ":rutaficha"      => $params["rutaficha"],
                ":comentarios"    => $params["comentarios"],
                ":estado"         => $params["estado"],
            ]);

            return (int) $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("Error en createFicha: " . $e->getMessage());
            return -1;
        }
    }
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
