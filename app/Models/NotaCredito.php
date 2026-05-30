<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class NotaCredito
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * @return array<int, array<string, mixed>> idpago => nota emitida
     */
    public function getMapaPorContrato(int $idContrato): array
    {
        if (!$this->tablaExiste()) {
            return [];
        }

        $sql = "SELECT nc.* FROM notas_credito nc
                INNER JOIN pagos p ON p.idpago = nc.idpago
                INNER JOIN cronogramas c ON c.idcronograma = p.idcronograma
                WHERE c.idcontrato = :idcontrato AND nc.estado = 'EMITIDA'";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idcontrato' => $idContrato]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $mapa = [];
            foreach ($rows as $row) {
                $mapa[(int) $row['idpago']] = $row;
            }
            return $mapa;
        } catch (PDOException $e) {
            error_log('NotaCredito::getMapaPorContrato: ' . $e->getMessage());
            return [];
        }
    }

    public function existeNotaEmitidaParaPago(int $idPago): bool
    {
        if (!$this->tablaExiste()) {
            return false;
        }

        $sql = "SELECT 1 FROM notas_credito WHERE idpago = :idpago AND estado = 'EMITIDA' LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idpago' => $idPago]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('NotaCredito::existeNotaEmitidaParaPago: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Datos del pago + cliente para emitir NC (no altera el pago).
     *
     * @return array<string, mixed>|null
     */
    public function getContextoPago(int $idPago): ?array
    {
        $sql = "SELECT
                    p.idpago,
                    p.amortizacion,
                    p.enlace_pdf_nubefact,
                    p.numero_boleta_sunat,
                    p.comprobante_serie,
                    p.comprobante_tipo_nubefact,
                    p.tipo AS tipo_pago,
                    p.mediopago,
                    c.numcuota,
                    c.idcontrato,
                    per.nrodoc,
                    per.direccion,
                    per.email,
                    TRIM(CONCAT(COALESCE(per.apellidos, ''), ' ', COALESCE(per.nombres, ''))) AS razon_social
                FROM pagos p
                INNER JOIN cronogramas c ON c.idcronograma = p.idcronograma
                INNER JOIN contratos co ON co.idcontrato = c.idcontrato
                INNER JOIN clientes cl ON cl.idcliente = co.idcliente
                INNER JOIN personas per ON per.idpersona = cl.idpersona
                WHERE p.idpago = :idpago
                LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':idpago' => $idPago]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('NotaCredito::getContextoPago: ' . $e->getMessage());
            return null;
        }
    }

    public function registrar(array $data): int
    {
        $sql = "INSERT INTO notas_credito (
                    idpago, idcontrato, idcolaborador, tipo_nota_credito,
                    documento_modifica_tipo, documento_modifica_serie, documento_modifica_numero,
                    nc_serie, nc_numero, total, motivo,
                    enlace_pdf_nubefact, enlace_xml_nubefact, enlace_del_cdr,
                    estado, mensaje_error
                ) VALUES (
                    :idpago, :idcontrato, :idcolaborador, :tipo_nota,
                    :doc_tipo, :doc_serie, :doc_numero,
                    :nc_serie, :nc_numero, :total, :motivo,
                    :pdf, :xml, :cdr,
                    :estado, :mensaje
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':idpago' => $data['idpago'],
            ':idcontrato' => $data['idcontrato'] ?? null,
            ':idcolaborador' => $data['idcolaborador'] ?? null,
            ':tipo_nota' => $data['tipo_nota_credito'] ?? '01',
            ':doc_tipo' => $data['documento_modifica_tipo'],
            ':doc_serie' => $data['documento_modifica_serie'],
            ':doc_numero' => $data['documento_modifica_numero'],
            ':nc_serie' => $data['nc_serie'],
            ':nc_numero' => $data['nc_numero'],
            ':total' => $data['total'],
            ':motivo' => $data['motivo'] ?? null,
            ':pdf' => $data['enlace_pdf'] ?? null,
            ':xml' => $data['enlace_xml'] ?? null,
            ':cdr' => $data['enlace_cdr'] ?? null,
            ':estado' => $data['estado'] ?? 'EMITIDA',
            ':mensaje' => $data['mensaje_error'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function tablaExiste(): bool
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'notas_credito'");
            $cache = (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $cache = false;
        }

        return $cache;
    }

    /**
     * Resuelve serie y tipo del comprobante original (guardado o inferido).
     *
     * @return array{tipo:int, serie:string, numero:int}|null
     */
    public static function resolverDocumentoAfectado(array $ctx): ?array
    {
        $numero = (int) ($ctx['numero_boleta_sunat'] ?? 0);
        if ($numero <= 0 || empty($ctx['enlace_pdf_nubefact'])) {
            return null;
        }

        $tipo = (int) ($ctx['comprobante_tipo_nubefact'] ?? 0);
        $serie = trim((string) ($ctx['comprobante_serie'] ?? ''));

        if ($tipo <= 0 || $serie === '') {
            $nrodoc = preg_replace('/\D+/', '', (string) ($ctx['nrodoc'] ?? ''));
            $esRuc = strlen($nrodoc) === 11;
            $tipo = $esRuc ? 1 : 2;
            $serie = $esRuc ? 'FFF1' : 'BBB1';
        }

        if ($tipo !== 1 && $tipo !== 2) {
            return null;
        }

        return [
            'tipo' => $tipo,
            'serie' => $serie,
            'numero' => $numero,
        ];
    }
}
