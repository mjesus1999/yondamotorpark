<?php
// app/Controllers/PersonaController.php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Persona;

class PersonaController extends Controller
{
    private Persona $personaModal;

    public function __construct()
    {
        $this->personaModal = new Persona();
    }

    /**
     * POST /usuarios/store
     */
    public function store(): void
    {
        // Solo aceptamos POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios/create');
            return;
        }

        // 1) Recoger y sanear datos
        $apellidos = trim($_POST['apellidos'] ?? '');
        $nombres = trim($_POST['nombres'] ?? '');
        $tipodoc = trim($_POST['tipodoc'] ?? '');
        $nrodoc = trim($_POST['nrodoc'] ?? '');
        $genero = trim($_POST['genero'] ?? '');
        $fechanac = trim($_POST['fechanac'] ?? '');
        $estadocivil = trim($_POST['estadocivil'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $iddistrito = trim($_POST['iddistrito'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $referencia = trim($_POST['referencia'] ?? '');
        $telprimario = trim($_POST['telprimario'] ?? '');
        $telalternativo = trim($_POST['telalternativo'] ?? '');

        // 2) Preparar array para el modelo
        $data = [
            'apellidos' => $apellidos,
            'nombres' => $nombres,
            'tipodoc' => $tipodoc,
            'nrodoc' => $nrodoc,
            'genero' => $genero,
            'fechanac' => $fechanac ?: null,
            'estadocivil' => $estadocivil ?: null,
            'email' => $email ?: null,
            'iddistrito' => $iddistrito !== '' ? (int) $iddistrito : null,
            'direccion' => $direccion ?: null,
            'referencia' => $referencia ?: null,
            'telprimario' => $telprimario,
            'telalternativo' => $telalternativo ?: null,
        ];

        // 3) Validaciones mínimas
        $required = ['apellidos', 'nombres', 'tipodoc', 'nrodoc', 'genero', 'telprimario'];
        $errors = [];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "El campo «{$field}» es obligatorio.";
            }
        }

        if (!empty($errors)) {
            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
            ) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'errors' => $errors,
                ]);
                exit;
            }
            // si no es AJAX, renderizamos la vista normal
            $this->view('usuarios.create', [
                'error' => implode('<br>', $errors),
                'old' => $data
            ]);
            return;
        }

        // 4) Insertar y obtener nuevo ID
        $newId = $this->personaModal->create($data);

        // Respuesta AJAX
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
        ) {
            header('Content-Type: application/json; charset=utf-8');
            if ($newId > 0) {
                // devolvemos también los datos clave para rellenar el form principal
                echo json_encode([
                    'success' => true,
                    'idpersona' => $newId,
                    'nrodoc' => $data['nrodoc'],
                    'apellidos' => $data['apellidos'],
                    'nombres' => $data['nombres'],
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'errors' => ['Error al crear la persona.']
                ]);
            }
            exit;
        }

        // Respuesta normal en flujo síncrono
        if ($newId > 0) {
            $_SESSION['success_message'] = 'Persona registrada con ID ' . $newId;
            $this->redirect('/usuarios');
        } else {
            $this->view('usuarios.create', [
                'error' => 'Error al crear la persona. Intente de nuevo.',
                'old' => $data
            ]);
        }
    }

}