<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Concesionario;

class ConcesionarioController extends Controller
{
    private Concesionario $concesionarioModel;

    public function __construct()
    {
        $this->concesionarioModel = new Concesionario();
    }

    public function index(): void
    {
        $concesionarios = $this->concesionarioModel->getAll();
        $this->authRequired();
        $this->view('concesionarios.index', ['concesionarios' => $concesionarios]);
    }

    public function create(): void
    {
        $this->view('concesionarios.create');
    }

    public function store(): int
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'ruc' => $data['ruc'] ?? '',
            'nombrecomercial' => $data['nombrecomercial'] ?? '',
            'razonsocial' => $data['razonsocial'] ?? ''
        ];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['ruc'], 'RUC');
        $errores[] = Validador::campoObligatorio($registro['nombrecomercial'], 'Nombre Comercial');
        $errores[] = Validador::campoObligatorio($registro['razonsocial'], 'Razón Social');
        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        $idConcesionario = $this->concesionarioModel->create($registro);

        if ($idConcesionario > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Concesionario creado exitosamente!',
                'id' => $idConcesionario
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo crear el concesionario',
                'id' => 0
            ]);
            exit;
        }
    }

    public function update($id): int
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = ['nombrecomercial' => $data['nombrecomercial'] ?? '', 'idconcesionario' => $id];

        $errores = [];
        $errores[] = Validador::campoObligatorio($registro['nombrecomercial'], 'Nombre Comercial');

        $errores = array_filter($errores);

        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores),
                'id' => 0
            ]);
            exit;
        }

        $rowAffects = $this->concesionarioModel->update($registro);

        if ($rowAffects > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Concesionario actualizado exitosamente!',
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo actualizar el concesionario',
            ]);
            exit;
        }
    }

    // Metodo que me permite ver las tiendas del concesionario.
    public function gestionar($ruc): void
    {
        $concesionario = $this->concesionarioModel->getConcesionarioByRUC($ruc);

        if (!$concesionario || count($concesionario) === 0) {
            $this->view('errors.404', ['mensaje' => 'Concesionario no encontrado']);
            return;
        }

        // Le pasamos solo el RUC, el JS se encargará de cargar el resto.
        $this->view('concesionarios.create', ['ruc' => $concesionario[0]['ruc']]);
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            // Paso 1: Verificar si el concesionario tiene OC
            $oc = $this->concesionarioModel->getOC($id);

            if ($oc > 0) {
                // Tiene OC, no se puede eliminar
                echo json_encode([
                    'success' => false,
                    'message' => 'No se puede eliminar. Tiene órdenes de compra registradas.'
                ]);
                return;
            }

            // Paso 2: Proceder a eliminar si no tiene OC
            $resultado = $this->concesionarioModel->delete($id);

            if ($resultado > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Concesionario eliminado correctamente.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo eliminar el concesionario.'
                ]);
            }
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }
    }

    // METODOS PARA LAS APIS:

    // Retorna los datos de un Concesionario buscado mediante la api de Sunat
    public function searchRucSunat($ruc): void
    {
        if (strlen($ruc) != 11) {
            http_response_code(400);
            echo json_encode(['error' => 'RUC inválido']);
            return;
        }

        $token = 'apis-token-10575.ycXCIbBCEoM8ufZplATB5oIDwVTo7mzp';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/ruc/full?numero=' . $ruc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Referer: http://apis.net.pe/api-ruc',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        header('Content-Type: application/json; charset=utf-8');
        echo $response;
    }

    // Buscará el Ruc del concesionario en la DB.

    public function searchRucDB($ruc): void
    {
        header('Content-Type: application/json');
        $concesionario = $this->concesionarioModel->getConcesionarioByRUC($ruc);

        if ($concesionario) {
            echo json_encode($concesionario);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }

        exit();
    }


    public function getConcesionariosDB(): void
    {
        header('Content-Type: application/json');
        $concesionarios = $this->concesionarioModel->getAll();

        if ($concesionarios) {
            echo json_encode($concesionarios);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }
        exit();
    }
}
