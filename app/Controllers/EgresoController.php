<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Egreso;



class EgresoController extends Controller
{
    private Egreso $egresoModel;


    public function __construct()
    {
        $this->egresoModel = new Egreso();
    }

    /**
     * Renderiza la vista principal de egresos.
     * @return void Renderiza la vista principal de egresos.
     */
    public function index(string $estado = 'N'): void
    {
        $this->authRequired();

        if ($estado === 'validados') {
            $egresos = $this->egresoModel->getEgresoWithComprobantesValidados();


            $this->view('egresos.index', ['egresos' => $egresos, 'estado' => 'validados']);
        } else {
            $egresos = $this->egresoModel->getAllEgresosByEstado($estado);

            $this->view('egresos.index', ['egresos' => $egresos, 'estado' => $estado]);
        }
    }



    public function indexAjuntarComprobante(): void
    {
        $this->view('egresos.adjuntarComprobante');
    }


    public function create(): void
    {
        $this->authRequired();
        $this->view('egresos.create');
    }



    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }

        header('Content-Type: application/json');
        $data = array_map([Validador::class, 'limpiar'], $_POST);


        $registroEgreso = [
            'idconceptoegreso' => $data['idconceptoegreso'] ?? null,
            'idsolicitante' => empty($data['idsolicitante']) ? null : (int)$data['idsolicitante'],
            'monto' => empty($data['monto']) ? null : (float)$data['monto'],
            'comentario' => empty($data['comentario']) ? null : $data['comentario'],
            'requierecomprobante' => empty($data['requierecomprobante']) ? 'N' : $data['requierecomprobante']
        ];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registroEgreso['idconceptoegreso'], 'Concepto de Egreso');
        $errores[] = Validador::campoObligatorio($registroEgreso['idsolicitante'], 'Solicitante');
        $errores[] = Validador::campoObligatorio($registroEgreso['monto'], 'Monto');

        if (!empty(array_filter($errores))) {
            echo json_encode(['success' => false, 'message' => implode('<br>', array_filter($errores)), 'id' => 0]);
            exit;
        }

        $newId = $this->egresoModel->add($registroEgreso);

        if ($newId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el egreso. Intente nuevamente.', 'id' => 0]);
            exit;
        }

        // Si el egreso no requiere comprobante, la operación termina aquí.
        if ($registroEgreso['requierecomprobante'] === 'N') {
            echo json_encode(['success' => true, 'message' => 'Egreso registrado con éxito.', 'id' => $newId]);
            exit;
        }


        $registroComprobante = [
            'idegreso' => $newId,
            'idproovedor' => empty($data['idproovedor']) ? null : (int)$data['idproovedor'],
            'tipodoc' => $data['tipodoc'] ?? null,
            'serie' => $data['serie'] ?? null,
            'numdocumento' => $data['numdocumento'] ?? null,
            'monto' => empty($data['monto_comprobante']) ? null : (float)$data['monto_comprobante']
        ];

        $erroresComprobante = [];
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['idproovedor'], 'Proveedor');
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['tipodoc'], 'Tipo de Documento');
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['serie'], 'Serie');
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['numdocumento'], 'Número de Documento');
        $erroresComprobante[] = Validador::campoObligatorio($registroComprobante['monto'], 'Monto');

        if (!empty(array_filter($erroresComprobante))) {
            // Rollback: Eliminar el egreso principal si el comprobante no es válido.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => implode('<br>', array_filter($erroresComprobante)), 'id' => 0]);
            exit;
        }

        if (!isset($_FILES['rutacomprobante']) || $_FILES['rutacomprobante']['error'] !== UPLOAD_ERR_OK) {
            // Rollback: Eliminar el egreso principal si el archivo no se sube.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'Error al subir el archivo de comprobante.', 'id' => 0]);
            exit;
        }

        $archivoTemp = $_FILES['rutacomprobante']['tmp_name'];
        $nombreOriginal = $_FILES['rutacomprobante']['name'];
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if ($extension !== 'pdf') {
            // Rollback: Eliminar el egreso principal si el archivo no es PDF.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'El archivo debe ser un PDF.', 'id' => 0]);
            exit;
        }

        $subdirectorio = '';
        switch ($registroComprobante['tipodoc']) {
            case 'F':
                $subdirectorio = 'facturas';
                break;
            case 'B':
                $subdirectorio = 'boletas';
                break;
            default:
                // Rollback: Eliminar el egreso principal si el tipo de documento no es válido.
                $this->egresoModel->deleteEgreso($newId);
                echo json_encode(['success' => false, 'message' => 'Tipo de documento no válido.', 'id' => 0]);
                exit;
        }

        $nombreArchivo = uniqid($subdirectorio . '_') . '.' . $extension;
        $directorioDestino = __DIR__ . '/../../storage/' . $subdirectorio . '/';
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (!is_dir($directorioDestino) && !mkdir($directorioDestino, 0777, true)) {
            // Rollback: Eliminar el egreso principal si no se puede crear el directorio.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'Error al crear el directorio de destino.', 'id' => 0]);
            exit;
        }

        if (!move_uploaded_file($archivoTemp, $rutaCompleta)) {
            // Rollback: Eliminar el egreso principal si falla la subida.
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'No se pudo guardar el archivo de comprobante.', 'id' => 0]);
            exit;
        }

        $registroComprobante['rutacomprobante'] = $subdirectorio . '/' . $nombreArchivo;

        $successComprobante = $this->egresoModel->addComprobante($registroComprobante);

        if ($successComprobante) {
            echo json_encode(['success' => true, 'message' => 'Egreso y comprobante registrados con éxito.', 'id' => $newId]);
        } else {
            // Rollback: Eliminar el archivo subido y el egreso principal si falla la inserción en la base de datos.
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
            $this->egresoModel->deleteEgreso($newId);
            echo json_encode(['success' => false, 'message' => 'Error al guardar el comprobante. El egreso fue revertido.', 'id' => 0]);
        }
    }




    public function validarComprobante($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }

        header('Content-Type: application/json');

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de comprobante inválido.']);
            exit;
        }

        $success = $this->egresoModel->validarComprobante($id);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Comprobante validado con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al validar el comprobante. Intente nuevamente.']);
        }
    }






    public function getConceptosEgreso(): void
    {
        header('Content-Type: application/json');
        $this->authRequired();
        $data = $this->egresoModel->getConceptosEgreso();

        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron conceptos de egreso.']);
        }
    }

    public function getColaboradores(): void
    {
        header('Content-Type: application/json');
        $this->authRequired();
        $data = $this->egresoModel->getColaboradores();

        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron colaboradores.']);
        }
    }

    public function getProovedores(): void
    {
        header('Content-Type: application/json');
        $this->authRequired();
        $data = $this->egresoModel->getProovedores();

        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron proovedores.']);
        }
    }


    public function getDetalleEgreso(int $id): void
    {
        header('Content-Type: application/json');
        $this->authRequired();

        $data =  $this->egresoModel->getDetalleEgresoById($id);


        if (!empty($data)) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron detalles para el egreso especificado.']);
        }
    }
}
