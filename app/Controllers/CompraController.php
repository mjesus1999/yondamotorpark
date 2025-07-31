<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Compra;


class CompraController extends Controller
{
    private Compra $compraModel;

    public function __construct()
    {
        $this->compraModel = new Compra();
    }

    public function index(): void
    {
        $compras = $this->compraModel->getAll();
        $this->view('compras.index', ['compras' => $compras]);
        
    }


    public function create(): void
    {
        $this->view('compras.create');
    }

   public function store(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit;
    }

    header('Content-Type: application/json');

    $data = array_map([Validador::class, 'limpiar'], $_POST);

    $registro = [
        'idorden' => $data['idorden'] ?? 0,
        'idlogistica' => 2,
        'fechacompra' => $data['fechacompra'] ?? '',
        'tipodoc' => $data['tipodoc'] ?? '',
        'serie' => $data['serie'] ?? '',
        'numdocumento' => $data['numdocumento'] ?? '',
        'rutadoc' => '' // Inicializamos vacío, se asignará después de subir el archivo
    ];

    $errores = [];
    $errores[] = Validador::campoObligatorio($registro['idorden'], 'Orden de Compra');
    $errores[] = Validador::campoObligatorio($registro['idlogistica'], 'Logística');
    $errores[] = Validador::campoObligatorio($registro['fechacompra'], 'Fecha de Compra');
    $errores[] = Validador::campoObligatorio($registro['tipodoc'], 'Tipo de Documento');
    $errores[] = Validador::campoObligatorio($registro['serie'], 'Serie');
    $errores[] = Validador::campoObligatorio($registro['numdocumento'], 'Número de Documento');
    $errores = array_filter($errores);

    // Validar que se haya subido un archivo
    if (empty($_FILES['rutadoc']['name'])) {
        $errores[] = 'Debe subir un archivo PDF';
    }

    if (!empty($errores)) {
        echo json_encode([
            'success' => false,
            'message' => implode('<br>', $errores),
            'id' => 0
        ]);
        exit;
    }

    try {
        // Procesar el archivo subido
        $nombreArchivo = uniqid('factura_') . '_' . basename($_FILES['rutadoc']['name']);
        $directorioDestino = 'storage/facturas/';

        // Crear directorio si no existe
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        $rutaCompleta = $directorioDestino . $nombreArchivo;

        // Validar que sea un PDF
        $extension = strtolower(pathinfo($_FILES['rutadoc']['name'], PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            echo json_encode(['success' => false, 'message' => 'El archivo debe ser un PDF', 'id' => 0]);
            exit;
        }

        // Mover el archivo subido al directorio destino
        if (!move_uploaded_file($_FILES['rutadoc']['tmp_name'], $rutaCompleta)) {
            echo json_encode(['success' => false, 'message' => 'No se pudo guardar el archivo PDF', 'id' => 0]);
            exit;
        }

        // Asignar la ruta relativa al registro
        $registro['rutadoc'] = '/' . $rutaCompleta;

        // Guardar en la base de datos
        $idCompra = $this->compraModel->create($registro);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Compra registrada correctamente', 
            'id' => $idCompra,
            'ruta' => $registro['rutadoc'] 
            
        ]);
    } catch (\Exception $e) {
        // Eliminar el archivo si hubo error en la base de datos
        if (isset($rutaCompleta)) {
            @unlink($rutaCompleta);
        }
        
        echo json_encode([
            'success' => false, 
            'message' => 'Error al registrar la compra: ' . $e->getMessage(), 
            'id' => 0
        ]);
    }
}




    // APIS
    public function searchDetOCByConcesionario($id)
    {
        header('Content-Type: application/json');
        $datos = $this->compraModel->getDetOCByConcesionario($id);

        if ($datos) {
            // Reorganizar por orden de compra
            $resultado = [];

            foreach ($datos as $fila) {
                $idOrden = $fila['idordencompra'];

                if (!isset($resultado[$idOrden])) {
                    $resultado[$idOrden] = [
                        'idordencompra' => $idOrden,
                        'emision' => $fila['emision'],
                        'detalle' => []
                    ];
                }

                // Quitar campos duplicados que ya están al nivel superior
                unset($fila['idordencompra'], $fila['emision']);

                $resultado[$idOrden]['detalle'][] = $fila;
            }

            // Convertir de asociativo a numérico
            echo json_encode(array_values($resultado));
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}
