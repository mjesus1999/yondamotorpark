<?php
// app/Controllers/ForCotController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\FormatoCotizacion;

class ForCotController extends Controller
{
    private FormatoCotizacion $formatoModel;

    public function __construct()
    {
        $this->formatoModel = new FormatoCotizacion();
    }

    public function index(): void
    {
        $this->authRequired();

        $formatos = $this->formatoModel->getAll();
        $requisitos = $this->formatoModel->getRequisitos();
        $this->view("formatoCotizacion.index", compact("formatos", "requisitos"));
    }

    public function create(): void
    {
        $this->authRequired();
        $this->view('formatoCotizacion.create');
    }

    public function store(): void
    {
        $this->authRequired();

        $tipocot = trim($_POST['tipocotizacion'] ?? '');
        $fi = $_POST['fechainicio'] ?? '';
        $indefinido = isset($_POST['indefinido']);
        $ff = (!$indefinido && !empty($_POST['fechafin']))
            ? $_POST['fechafin']
            : null;

        // Insertar y obtener nuevo ID
        $newId = $this->formatoModel->create($tipocot, $fi, $ff);

        header('Location: /formatoCotizacion');
        exit;
    }

    public function getRequisitos(int $idformato): void
    {
        $this->authRequired();
        $formatos = $this->formatoModel->getAll();
        $formato = array_filter($formatos, fn($f) => $f['idformato'] == $idformato);
        $formato = $formato ? array_shift($formato) : null;

        //los requisitos disponibles
        $todosRequisitos = $this->formatoModel->getRequisitos();

        //Obtengolos requisitos asignados a este formato
        $asignados = $this->formatoModel->getDetalleRequisitos($idformato);

        //ambas listas a la vista
        $this->view(
            'formatoCotizacion.requisitos',
            compact('formato', 'todosRequisitos', 'asignados')
        );
    }

    public function saveRequisitos(): void
    {
        $this->authRequired();
        $idformato = (int) ($_POST['idformato'] ?? 0);
        $requisitos = $_POST['requisitos'] ?? [];

        if ($idformato <= 0) {
            header('Location: /formatoCotizacion');
            exit;
        }

        // Elimina las relaciones previas
        $this->formatoModel->deleteDetalle($idformato);

        // Inserta cada requisito nuevo
        foreach ($requisitos as $idreq) {
            $this->formatoModel->addDetalle($idformato, (int) $idreq);
        }

        header('Location: /formatoCotizacion');
        exit;
    }

    public function details(int $idformato): void
    {
        $this->authRequired();
        $detalles = $this->formatoModel->getDetalleRequisitos($idformato);
        header('Content-Type: application/json');
        echo json_encode($detalles);
    }

    public function delete(int $id): void
    {
        $this->authRequired();
        $this->formatoModel->delete($id);
        http_response_code(200);
        echo json_encode(['success' => true]);
    }
}