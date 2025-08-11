<?php

namespace App\Models;

use App\Core\Database;
use FFI\CData;
use PDO;
use PDOException;

class Caja
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllContratosDatos(): ?array
    {
        $query = "
                SELECT
                    con.idcontrato,
                    CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
                    p.tipodoc AS documento,
                    p.nrodoc AS ndocumento,

                    
                    l.tienda,

                    CONCAT(
                        IFNULL(mar.marca, 'Sin marca'), '/',
                        IFNULL(model.modelo, 'Sin modelo'),
                        ' / ',
                        IFNULL(c.combustible, 'Sin combustible'),
                        ' / ',
                        IFNULL(v.color, 'Sin color')
                    ) AS vehiculo,

                    
                    cot.numcuotas AS meses,
                    cot.valorcuota AS cuota
                FROM
                    cotizaciones AS cot
                JOIN
                    clientes AS cli ON cot.idcliente = cli.idcliente
                JOIN
                    personas AS p ON cli.idpersona = p.idpersona
                JOIN
                    vehiculos AS v ON cot.idvehiculo = v.idvehiculo
                JOIN
                    modelos AS model ON v.idmodelo = model.idmodelo
                JOIN
                    marcas AS mar ON model.idmarca = mar.idmarca
                JOIN
                    combustibles AS c ON v.idcombustible = c.idcombustible
                LEFT JOIN
                    contratos AS con ON cot.idcotizacion = con.idcotizacion
                LEFT JOIN
                    locales AS l ON con.idlocal = l.idlocal;

                ";
        try {

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }


    public function getCronogramaByIdContrato() {
        $sql = "";

        try {



        }catch(PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }
}
