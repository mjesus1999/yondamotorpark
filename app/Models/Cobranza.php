<?php
// app/Models/Cobranza.php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Cobranza
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getEstadisticasContratos()
    {
        $query = "CALL sp_get_estadisticas_cobranza()";
        try{
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'total_deudores' => 0,
                'por_vender_3dias' => 0,
                'vencidos' => 0,
                'total_por_cobrar' => 0
            ];

            $resumen = [];
            if($stmt->nextRowset()) {
                $resumen = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            }

            $stmt->closeCursor();

            return[
                'estadisticas' => $estadisticas,
                'resumen' => $resumen
            ];

            /* $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [
                'total_deudores' => 0,
                'por_vencer_3dias' => 0,
                'vencidos' => 0,
                'total_por_cobrar' => 0
            ]; */

        } catch (PDOException $error) {
            error_log("Error en estadisticas cobranza: " . $error->getMessage());
            return[
                'estadisticas' => [
                    'total_deudores' => 0,
                    'por_vencer_3dias' => 0,
                    'vencidos' => 0,
                    'total_por_cobrar' => 0
                ],
                'resumen' => []
            ];
            /* return [
                'total_deudores' => 0,
                'por_vencer_3dias' => 0,
                'vencidos' => 0,
                'total_por_cobrar' => 0
            ]; */
        }
    }

    public function getClientesNotificar()
    {
        $query = "CALL sp_get_cuotas_proximas_vencer()";
        try{
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (PDOException $error) {
            error_log($error->getMessage());
            return[];
        }
    }

    public function getCuotasVencidas() 
    {
        $query = "CALL sp_get_cuotas_vencidas()";

        try{
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $error) {
            error_log("Error en getVencidas: " . $error->getMessage());
            return [];
        }

    }

}