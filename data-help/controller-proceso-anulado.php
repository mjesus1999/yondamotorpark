// METODO PARA CAMBIAR EL ESTADO  EN LA TABAL OC  = 'PROCESO,ANULADO'
    public function setEstado($estado, $idOC): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'estado' => $estado,
            'observaciones' => $data['observaciones'] ?? '',
            'idordencompra' => $idOC
        ];

        $rowAffects = $this->ordenCompraModel->updateEstado($registro);

        //  Interpretar los valores del modelo
        if ($rowAffects === -2) {
            echo json_encode([
                'success' => false,
                'message' => 'No puede pasar a PROCESO: hay autos sin verificar.'
            ]);
            exit;
        }

        if ($rowAffects === -1) {
            echo json_encode([
                'success' => false,
                'message' => 'Ocurrió un error en la base de datos.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => $rowAffects > 0,
            'message' => $rowAffects > 0
                ? "¡Se actualizó la OC a {$estado}!"
                : "¡No se realizó ningún cambio en la OC!"
        ]);
        exit();
    }
