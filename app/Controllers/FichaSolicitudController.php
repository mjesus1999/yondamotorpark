<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FichaSolicitud;
use App\Helpers\Validador;
use PDOException;


class FichaSolicitudController extends Controller
{
    private FichaSolicitud $fichaSolicitudModel;

    public function __construct()
    {
        $this->fichaSolicitudModel = new FichaSolicitud();
    }

    public function index($id)
    {
        $datosCotizacion = $this->fichaSolicitudModel->getDatosCotizacion($id);
        $this->view('cotizacion.ficha', ['infoFicha' => $datosCotizacion]);
    }


    public function storePersona()
    {
        header('Content-Type: application/json; charset=utf-8');
        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'iddistrito' => $data['iddistrito'],
            'apellidos' => $data['apellidos'],
            'nombres' => $data['nombres'],
            'tipodoc' => $data['tipodoc'],
            'nrodoc' => $data['nrodoc'],
            'genero' => $data['genero'],
            'telprimario' => $data['telprimario'],
            'direccion' => empty($data['direccion']) ? null : $data['direccion']
        ];

        $errores = Validador::validarPersonaCrear($registro);

        if (!empty($errores)) {
            $this->view('cotizacion.ficha', ['error' => implode("<br>", $errores), 'data' => $registro]);
            return -1;
        }

        $idPersona = $this->fichaSolicitudModel->createPersona($registro);

        if ($idPersona > 0) {
            echo json_encode([
                'success' => true,
                'lastId' => $idPersona
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se ha podido registrar la persona'
            ]);
        }
    }



    public function storeFicha()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'idcotizacion'   => $data['idcotizacion'],
            'idconyuge'      => empty($data['idconyuge']) ? null : $data['idconyuge'],
            'idaval'         => empty($data['idaval']) ? null : $data['idaval'],
            'idavalconyuge'  => empty($data['idavalconyuge']) ? null : $data['idavalconyuge'],
            'fechavisita'    => $data['fechavisita'],
            'rutaficha'      => '',
            'comentarios'    => empty($data['comentarios']) ? null : $data['comentarios'],
            'estado'         => $data['estado']
        ];

        // Validaciones obligatorias
        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['idcotizacion'], 'Id Cotización');
        $errores[] = Validador::campoObligatorio($registro['fechavisita'], 'Fecha de visita');
        $errores[] = Validador::campoObligatorio($registro['estado'], 'Estado');
        $errores = array_filter($errores);

        // Validar archivo subido
        if (empty($_FILES['rutaficha']['name'])) {
            $errores[] = 'Debe subir un archivo PDF para la ficha';
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
            // Subida del archivo
            $subdirectorio = 'fichasCotizacion';
            $nombreArchivo = uniqid('ficha_') . '_' . basename($_FILES['rutaficha']['name']);
            $directorioDestino = __DIR__ . '/../../storage/' . $subdirectorio . '/';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            $rutaCompleta = $directorioDestino . $nombreArchivo;

            // Validar extensión PDF
            $extension = strtolower(pathinfo($_FILES['rutaficha']['name'], PATHINFO_EXTENSION));
            if ($extension !== 'pdf') {
                echo json_encode(['success' => false, 'message' => 'El archivo debe ser un PDF', 'id' => 0]);
                exit;
            }

            if (!move_uploaded_file($_FILES['rutaficha']['tmp_name'], $rutaCompleta)) {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar el archivo PDF', 'id' => 0]);
                exit;
            }

            // Guardar ruta relativa en BD
            $registro['rutaficha'] = $subdirectorio . '/' . $nombreArchivo;

            // Guardar en la base de datos
            $idFicha = $this->fichaSolicitudModel->createFicha($registro);

            if ($idFicha > 0) {

                echo json_encode([
                    'success' => true,
                    'message' => 'Ficha registrada correctamente',
                    'id' => $idFicha
                ]);


                switch ($registro['estado']) {
                    case 'Aprobado':
                        $this->fichaSolicitudModel->updateCotizacion($registro['idcotizacion'], 'A'); // Aprobada
                        break;
                    case 'Observado':
                        $this->fichaSolicitudModel->updateCotizacion($registro['idcotizacion'], 'O'); // Observado
                        break;
                    case 'Anulado':
                        $this->fichaSolicitudModel->updateCotizacion($registro['idcotizacion'], 'R'); // OJO: aquí R significa Rechazada
                        break;
                }
            } else {
                // si falla, borro el archivo para no dejar basura
                if (file_exists($rutaCompleta)) {
                    @unlink($rutaCompleta);
                }
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al registrar la ficha en la base de datos',
                    'id' => 0
                ]);
            }
        } catch (\Exception $e) {
            if (isset($rutaCompleta) && file_exists($rutaCompleta)) {
                @unlink($rutaCompleta);
            }

            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar la ficha: ' . $e->getMessage(),
                'id' => 0
            ]);
        }
    }

















    public function searchPersonaByDNI(string $dni)
    {
        header('Content-Type: application/json; charset=utf-8');

        $persona = $this->fichaSolicitudModel->searchPersonaByDNI($dni);

        if ($persona) {
            echo json_encode(['success' => true, 'persona' => $persona]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró la persona en el regsitro']);
        }
    }

    public function searchPersonaByReniec(string $dni): void
    {
        try {
            require_once __DIR__ . '/../Helpers/Api_dni.php';
            // Capturar la salida de la función
            ob_start();
            searchByDNI($dni);
            $apiResponse = ob_get_clean();

            $responseData = json_decode($apiResponse, true);

            if ($responseData && $responseData['success']) {
                echo json_encode([
                    'success' => true,
                    'source' => 'api',
                    'message' => 'Persona encontrada en RENIEC',
                    'apellidos' => trim($responseData['apepaterno'] . ' ' . $responseData['apematerno']),
                    'nombres' => $responseData['nombres']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $responseData['message'] ?? 'No se encontró la persona'
                ]);
            }
        } catch (PDOException $e) {
            error_log('Error en búsqueda por API DNI: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }
}
