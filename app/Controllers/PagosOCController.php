<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\PagosOC;

class PagosOCController extends Controller
{
    private PagosOC $pagoOCModel;

    public function __construct()
    {
        $this->pagoOCModel = new PagosOC();
    }

    public function store(): void
    {
        header('Content-Type: application/json');
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idorden = (int)($_POST['idorden'] ?? 0);
            $idlogistica = (int)($_POST['idlogistica'] ?? 2); 
            $amortizacion = (float)($_POST['amortizacion'] ?? 0);
    
            if ($idorden <= 0 || $amortizacion <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
                return;
            }
    
            
            $rutaComprobante = '';
            if (!empty($_FILES['comprobante']['name'])) {
                $nombreArchivo = uniqid('comprobante_') . '_' . basename($_FILES['comprobante']['name']);
                $directorioDestino = 'storage/comprobantes/';
    
                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0777, true);
                }
    
                $rutaCompleta =  $directorioDestino . $nombreArchivo;
    
                if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaCompleta)) {
                    echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar el comprobante']);
                    return;
                }
    
                $rutaComprobante = '/'. $rutaCompleta;
            }
    
            //  Registro en BD
            $resultado = $this->pagoOCModel->create([
                'idorden' => $idorden,
                'idlogistica' => $idlogistica,
                'amortizacion' => $amortizacion,
                'comprobante' => $rutaComprobante
            ]);
    
            if ($resultado > 0) {
                echo json_encode(['success' => true, 'message' => 'Pago registrado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo registrar el pago']);
            }
        } else {
            echo json_encode(['sucess' => false, 'message' => 'Método no permitido']);
        }
    }
    
  

    


}