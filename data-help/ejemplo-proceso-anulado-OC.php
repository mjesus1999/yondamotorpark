// METODO QUE EPRMITE ACTUALIZAR EL CAMPO ESTADO EN 'PROCESO' O 'ANULADO' EN LA TABLA OC
    public function updateEstado($params = []): int
    {
        try {
            if ($params['estado'] === 'proceso') {
                // . Validar que todos los autos sean correctos antes de actualizar
                $stmtValid = $this->db->prepare("
                SELECT COUNT(*) AS pendientes
                FROM detordencompra
                WHERE idordencompra = :idordencompra
                  AND (escorrecto IS NULL OR escorrecto != 'S');
            ");
                $stmtValid->execute([':idordencompra' => $params['idordencompra']]);
                $pendientes = (int)$stmtValid->fetch(PDO::FETCH_ASSOC)['pendientes'];

                if ($pendientes > 0) {
                    return -2;
                }

                //  2. Si todo está correcto, actualizar a PROCESO
                $stmt = $this->db->prepare("
                UPDATE ordenescompra 
                SET estado = :estado, observaciones = :observaciones 
                WHERE idordencompra = :idordencompra;
            ");
                $stmt->execute([
                    ':estado' => $params['estado'],
                    ':observaciones' => $params['observaciones'],
                    ':idordencompra' => $params['idordencompra']
                ]);

                return (int)$stmt->rowCount();
            }

            if ($params['estado'] === 'anulado') {
                // 3. Si es ANULADO, usar el SP
                $stmt = $this->db->prepare("CALL sp__anular_OC(:estado, :observaciones, :idordencompra)");
                $stmt->execute([
                    ':estado' => $params['estado'],
                    ':observaciones' => $params['observaciones'],
                    ':idordencompra' => $params['idordencompra']
                ]);

                return $stmt->rowCount();
            }

            return 0; // Si no es proceso ni anulado - no afeftc ninguna fila

        } catch (PDOException $error) {
            error_log($error->getMessage());
            return -1;
        }
    }
