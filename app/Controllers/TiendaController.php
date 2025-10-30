<?php

/**
 * Controlador de Tienda
 * 
 * app/Controllers/TiendaController.php
 * 
 * Gestiona todas las operaciones CRUD relacionadas con las tiendas/sucursales
 * de concesionarios, incluyendo validaciones de datos, búsquedas por concesionario
 * y endpoints API para operaciones AJAX.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Validador;
use App\Models\Tienda;

/**
 * Clase TiendaController
 * 
 * Controlador para la gestión de tiendas/sucursales de concesionarios.
 * Proporciona endpoints AJAX exclusivamente, con validaciones robustas
 * y respuestas en formato JSON para todas las operaciones.
 * 
 */
class TiendaController extends Controller
{

    /**
     * Instancia de la conexion de la base de datos
     * @var Tienda
     */
    private Tienda $tiendaModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa el modelo de Tienda necesario para las operaciones
     * del controlador.
     */
    public function __construct()
    {
        $this->tiendaModel = new Tienda();
    }

    /**
     * Registra una nueva tienda en el sistema
     * 
     * Endpoint AJAX que procesa el formulario de creación de tienda.
     * Valida todos los campos obligatorios y responde en formato JSON.
     * Solo acepta peticiones POST y requiere autenticación.
     * 
     * Validaciones realizadas:
     * - Campos obligatorios: distrito, dirección, email, teléfono, contacto
     * 
     * Respuestas JSON:
     * - success true: Tienda creada exitosamente con ID
     * - success false: Errores de validación o fallo en creación
     * 
     * Códigos HTTP:
     * - 405: Método no permitido
     * 
     * @return int ID de la tienda creada si exitoso, 0 en caso de error
     */
    public function store(): int
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);
        $registro = [

            'iddistrito' => $data['iddistrito'] ?? '',
            'idconcesionario' => $data['idconcesionario'] ?? '',
            'direccion' => $data['direccion'] ?? '',
            'email' => $data['email'] ?? '',
            'telefono' => $data['telefono'] ?? '',
            'contacto' => $data['contacto'] ?? ''
        ];
        $errores = [];

        $errores[] = Validador::campoObligatorio($registro['iddistrito'], 'Distrito');
        $errores[] = Validador::campoObligatorio($registro['direccion'], 'Dirección');
        $errores[] = Validador::campoObligatorio($registro['email'], 'Correo');
        $errores[] = Validador::campoObligatorio($registro['telefono'], 'Teléfono');
        $errores[] = Validador::campoObligatorio($registro['contacto'], 'Contacto');

        $errores = array_filter($errores);


        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode("<br>", $errores),
                'id' => 0
            ]);
            exit;
        }

        $idTienda = $this->tiendaModel->create($registro);

        if ($idTienda > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Tienda creada exitosamente!',
                'id' => $idTienda
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo crear la tienda',
                'id' => 0
            ]);
            exit;
        }


    }

    /**
     * Obtiene los datos de una tienda para edición
     * 
     * Endpoint AJAX que retorna los datos completos de una tienda específica
     * en formato JSON. Requiere autenticación.
     * 
     * @param int $id ID de la tienda a consultar 
     * @return void
     */
    public function edit(int $id): void
    {

        $this->authRequired();
        header('Content-Type: application/json');

        $tienda = $this->tiendaModel->getTiendasById($id);

        if ($tienda) {
            echo json_encode($tienda[0]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tienda  no encontrada']);
        }
    }

    /**
     * Actualiza los datos de una tienda existente
     * 
     * Endpoint AJAX que procesa la actualización de una tienda.
     * Valida campos obligatorios incluyendo formato de teléfono y email.
     * Solo acepta peticiones POST y responde en formato JSON.
     * 
     * Validaciones realizadas:
     * - Campos obligatorios: distrito, dirección, teléfono
     * - Formato de teléfono válido
     * - Formato de email válido (si se proporciona)
     * 
     * Respuestas JSON:
     * - success true: Actualización exitosa con número de filas afectadas
     * - success false: Errores de validación o fallo en actualización
     * 
     * Códigos HTTP:
     * - 405: Método no permitido
     * 
     * @param int $id ID de la tienda a actualizar
     * @return int Número de filas afectadas o -1 en caso de error
     */
    public function update($id): int
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }

        header('Content-Type: application/json');

        $data = array_map([Validador::class, 'limpiar'], $_POST);
        $registro = [
            'iddistrito' => $data['iddistrito'] ?? '',
            'direccion' => $data['direccion'] ?? '',
            'email' => $data['email'] ?? '',
            'telefono' => $data['telefono'] ?? '',
            'contacto' => $data['contacto'] ?? '',
            'idtienda' => $id
        ];

        $errores = [];

        $errores[] = Validador::campoObligatorio($registro['iddistrito'], 'Distrito');
        $errores[] = Validador::campoObligatorio($registro['direccion'], 'Dirección');
        $errorTel = Validador::campoObligatorio($registro['telefono'], 'Teléfono');

        if ($errorTel) {
            $errores[] = $errorTel;
        } else {
            $errores[] = Validador::telefonoValido($registro['telefono'], 'Teléfono');
        }

        // Email solo si no está vacío
        if (!empty($registro['email'])) {

            $errores[] = Validador::emailValido($registro['email']);
        }

        $errores = array_filter($errores);


        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode("<br>", $errores),
                'id' => 0
            ]);
            exit;
        }

        $rowAffects = $this->tiendaModel->update($registro);

        if ($rowAffects > 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Se actualizo exitosamente!',
                'rows' => $rowAffects
            ]);
            exit;
        } else if ($rowAffects === 0) {
            echo json_encode([
                'success' => true,
                'message' => '¡Concesionario creado exitosamente!',
                'rows' => $rowAffects
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo crear el concesionario',
                'rows' => -1
            ]);
            exit;
        }
    }

    /**
     * Elimina una tienda del sistema
     * 
     * Endpoint AJAX que procesa la eliminación de una tienda.
     * Solo acepta peticiones POST y responde en formato JSON.
     * Requiere autenticación.
     * 
     * Respuestas JSON:
     * - success true: Tienda eliminada correctamente
     * - success false: No se pudo eliminar o restricciones de integridad
     * 
     * Códigos HTTP:
     * - 405: Método no permitido
     * 
     * @param int $id ID de la tienda a eliminar
     * @return void
     */
    public function delete($id): void
    {
        $this->authRequired();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->tiendaModel->delete($id) > 0) {
                echo json_encode(["success" => true, "message" => "Tienda eliminada"]);
            } else {
                echo json_encode(["success" => false, "message" => "No se pudo eliminar"]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Método no permitido"]);
        }
    }

    /**
     * API: Busca tiendas por ID de concesionario
     * 
     * Endpoint AJAX que retorna todas las tiendas asociadas a un
     * concesionario específico en formato JSON. Típicamente usado
     * para poblar elementos select en formularios.
     * 
     * Respuestas HTTP:
     * - 200 OK: Array de tiendas del concesionario
     * - 404 Not Found: No se encontraron tiendas
     * 
     * @param int $id ID del concesionario
     * @return never
     */
    public function searchTiendaByConcesionario($id): void
    {
        $this->authRequired();

        header('Content-Type: application/json');

        $tiendasConcesionario = $this->tiendaModel->getTiendasByIdConcesionario($id);
        if ($tiendasConcesionario) {
            echo json_encode($tiendasConcesionario);
        } else {
            http_response_code(404);
            echo json_encode([]);
        }

        exit();
    }

}
