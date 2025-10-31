<?php
// app/Controllers/ForCotController.php

/**
 * Controlador de Formato de Cotización
 * 
 * app/Controllers/ForCotController.php
 * 
 * Gestiona las peticiones HTTP relacionadas con los formatos de cotización
 * del sistema. Proporciona interfaces web para CRUD de formatos, gestión
 * de requisitos asociados, y endpoints API para operaciones asíncronas.
 * Todos los métodos requieren autenticación previa del usuario.
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\FormatoCotizacion;

/**
 * Clase ForCotController
 * 
 * Controlador para la gestión de formatos de cotización.
 * Hereda de Controller para acceder a funcionalidades base como
 * renderizado de vistas, validación de autenticación y manejo de sesiones.
 * Implementa el patrón MVC para separar la lógica de presentación
 * de la lógica de negocio.
 */
class ForCotController extends Controller
{

    /**
     * Instancia del modelo FormatoCotizacion
     * @var FormatoCotizacion
     */
    private FormatoCotizacion $formatoModel;

    /**
     * Constructor del controlador
     * 
     * Inicializa la instancia del modelo FormatoCotizacion para
     * realizar operaciones de base de datos
     */
    public function __construct()
    {
        $this->formatoModel = new FormatoCotizacion();
    }

    /**
     * Página principal de formatos de cotización
     * 
     * Renderiza la vista principal del módulo mostrando el listado
     * completo de formatos registrados y el catálogo de requisitos
     * disponibles. Requiere autenticación.
     * 
     * @return void
     */
    public function index(): void
    {
        $this->authRequired();

        $formatos = $this->formatoModel->getAll();
        $requisitos = $this->formatoModel->getRequisitos();
        $this->view("formatoCotizacion.index", compact("formatos", "requisitos"));
    }

    /**
     * Página de creación de formato
     * 
     * Renderiza el formulario para crear un nuevo formato de cotización.
     * Requiere autenticación.
     * 
     * @return void
     */
    public function create(): void
    {
        $this->authRequired();
        $this->view('formatoCotizacion.create');
    }

    /**
     * Procesa el registro de un nuevo formato
     * 
     * Recibe los datos del formulario POST y crea un nuevo formato de cotización
     * en la base de datos. Maneja el caso especial de vigencia indefinida
     * (fechafin = null). Establece mensajes de sesión según el resultado
     * y redirige al índice.
     * 
     * @return void
     */
    public function store(): void
    {
        $this->authRequired();

        $tipocot = trim($_POST['tipocotizacion'] ?? '');
        $fi = $_POST['fechainicio'] ?? '';
        $indefinido = isset($_POST['indefinido']);
        $ff = (!$indefinido && !empty($_POST['fechafin']))
            ? $_POST['fechafin']
            : null;

        //inserta y obtiene nuevo ID
        $newId = $this->formatoModel->create($tipocot, $fi, $ff);

        if ($newId > 0) {
            $_SESSION['success_message'] = 'Formato de cotización "' . htmlspecialchars($tipocot, ENT_QUOTES, 'UTF-8') . '" registrado correctamente';
        } else {
            $_SESSION['error_message'] = 'No se pudo registrar el formato. Intente nuevamente.';
        }

        header('Location: /formatoCotizacion');
        exit;
    }

    /**
     * Página de gestión de requisitos de un formato
     * 
     * Renderiza la vista para asignar/desasignar requisitos a un formato
     * específico. Muestra el catálogo completo de requisitos y marca
     * cuáles ya están asignados al formato.
     * 
     * @param int $idformato ID del formato a gestionar
     * @return void
     */
    public function getRequisitos(int $idformato): void
    {
        $this->authRequired();
        $formatos = $this->formatoModel->getAll();
        $formato = array_filter($formatos, fn($f) => $f['idformato'] == $idformato);
        $formato = $formato ? array_shift($formato) : null;

        //los requisitos disponibles
        $todosRequisitos = $this->formatoModel->getRequisitos();
        //obtengo los requisitos asignados a este formato
        $asignados = $this->formatoModel->getDetalleRequisitos($idformato);

        //muestra en lista 
        $this->view(
            'formatoCotizacion.requisitos',
            compact('formato', 'todosRequisitos', 'asignados')
        );
    }

    /**
     * Procesa la actualización de requisitos de un formato
     * 
     * Recibe un array de requisitos seleccionados y actualiza las relaciones
     * del formato. Primero elimina todas las asignaciones previas y luego
     * inserta las nuevas selecciones. Esto garantiza sincronización completa
     * entre el formulario y la base de datos.
     * 
     * @return void
     */
    public function storeRequisitos(): void
    {
        $this->authRequired();
        $idformato = (int) ($_POST['idformato'] ?? 0);
        $requisitos = $_POST['requisitos'] ?? [];

        if ($idformato <= 0) {
            header('Location: /formatoCotizacion');
            exit;
        }

        //se elimina las relaciones previas
        $this->formatoModel->deleteDetalle($idformato);
        //se inserta cada requisito nuevo
        foreach ($requisitos as $idreq) {
            $this->formatoModel->addDetalle($idformato, (int) $idreq);
        }
        header('Location: /formatoCotizacion');
        exit;
    }

    /**
     * Endpoint API: Obtener detalles de requisitos de un formato
     * 
     * Retorna en formato JSON la lista de requisitos asociados a un
     * formato específico. Útil para peticiones AJAX y operaciones
     * asíncronas desde el frontend.
     * 
     * @param int $idformato ID del formato a consultar
     * @return void Envía respuesta JSON
     */
    public function details(int $idformato): void
    {
        $this->authRequired();
        $detalles = $this->formatoModel->getDetalleRequisitos($idformato);
        header('Content-Type: application/json');
        echo json_encode($detalles);
    }

    /**
     * Endpoint API: Eliminar un formato de cotización
     * 
     * Elimina un formato y todos sus requisitos asociados de forma permanente.
     * Operación irreversible que debe usarse con precaución.
     * 
     * @param int $id ID del formato a eliminar
     * @return void Envía respuesta JSON
     */
    public function delete(int $id): void
    {
        $this->authRequired();
        $this->formatoModel->delete($id);
        http_response_code(200);
        echo json_encode(['success' => true]);
    }
}