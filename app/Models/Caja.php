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
        $query = "CALL sp_getAll_contratos_caja()";
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

    public function getCronogramaByIdContrato(int $id): array
    {
        $query = "CALL  sp_get_cronogramas_by_idcontrato(:idcontrato)";

        try {

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(':idcontrato' => $id));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return [];
        }
    }

    //VER TODOS LOS MOROSOS
    public function getMorososResumen(): array
    {
        $sql = "
        SELECT
            cont.idcontrato,
            CONCAT(p.apellidos, ' ', p.nombres) AS cliente,
            p.nrodoc AS ndocumento,
            COALESCE(SUM((coti.valorcuota + cro.penalidad) - COALESCE(ps.total_amortizado, 0)), 0) AS deuda_total,
            COALESCE(MAX(
                CASE
                    WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado' THEN DATEDIFF(CURDATE(), cro.fechapago)
                    ELSE 0
                END
            ), 0) AS dias_max_vencido,
            COALESCE(SUM(
                CASE
                    WHEN cro.fechapago < CURDATE() AND cro.estado != 'Pagado' THEN 1
                    ELSE 0
                END
            ), 0) AS cuotas_vencidas,
            cont.idlocal
        FROM contratos cont
        JOIN cotizaciones coti ON cont.idcotizacion = coti.idcotizacion
        JOIN cronogramas cro ON cro.idcontrato = cont.idcontrato
        JOIN clientes cli ON coti.idcliente = cli.idcliente
        JOIN personas p ON cli.idpersona = p.idpersona
        LEFT JOIN (
            SELECT idcronograma, COALESCE(SUM(amortizacion),0) AS total_amortizado
            FROM pagos
            GROUP BY idcronograma
        ) ps ON ps.idcronograma = cro.idcronograma
        WHERE cont.estado = 'ACT'
        GROUP BY cont.idcontrato, cliente, ndocumento, cont.idlocal
        HAVING deuda_total > 0 OR cuotas_vencidas > 0
        ORDER BY dias_max_vencido DESC, deuda_total DESC
    ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en getMorososResumen: ' . $e->getMessage());
            return [];
        }
    }

}
