<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Persona;
use App\Models\Cliente;
use App\Helpers\Validador;

class PersonaController extends Controller
{
    private Persona $personaModel;
    private Cliente $clienteModel;

    public function __construct()
    {
        $this->personaModel = new Persona();
        $this->clienteModel = new Cliente();
    }

    public function indexPersonCliente(): void
    {
        $personClientes = $this->personaModel->getAllPersonasCliente();
        $this->view('clientes.index', ['personClientes' => $personClientes]);
    }

    public function createPersonClient(): void
    {
        $this->view('clientes.create');
    }

    public function storePersonaClient(): int
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/clientes/create');
            return 0;
        }

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registroPersona = [
            'apellidos'      => $data['apellidos'] ?? '',
            'nombres'        => $data['nombres'] ?? '',
            'tipodoc'        => $data['tipodocumento'] ?? '',
            'nrodoc'         => $data['nrodoc'] ?? '',
            'genero'         => $data['genero'] ?? '',
            'fechanac'       => $data['fechanac'] ?? '',
            'estadocivil'    => $data['estadocivil'] ?? '',
            'email'          => $data['email'] ?? null,
            'iddistrito'     => !empty($data['distrito']) ? (int)$data['distrito'] : null,
            'direccion'      => $data['direccion'] ?? null,
            'referencia'     => $data['referencia'] ?? null,
            'telprimario'    => $data['telprimario'] ?? '',
            'telalternativo' => $data['telalternativo'] ?? null,
            'latitud'        => $data['latitud'] ?? null,
            'longitud'       => $data['longitud'] ?? null,
        ];

        $errores = [];

        // Validaciones obligatorias
        $errores[] = Validador::campoObligatorio($registroPersona['apellidos'], 'Apellidos');
        $errores[] = Validador::campoObligatorio($registroPersona['nombres'], 'Nombres');
        $errores[] = Validador::campoObligatorio($registroPersona['tipodoc'], 'Tipo de documento');
        $errores[] = Validador::campoObligatorio($registroPersona['nrodoc'], 'Número de documento');
        $errores[] = Validador::campoObligatorio($registroPersona['genero'], 'Género');
        $errores[] = Validador::campoObligatorio($registroPersona['fechanac'], 'Fecha de nacimiento');
        $errores[] = Validador::campoObligatorio($registroPersona['estadocivil'], 'Estado civil');
        $errores[] = Validador::campoObligatorio($registroPersona['iddistrito'], 'Distrito');

        $errorTel = Validador::campoObligatorio($registroPersona['telprimario'], 'Teléfono');
        if ($errorTel) {
            $errores[] = $errorTel;
        } else {
            $errores[] = Validador::telefonoValido($registroPersona['telprimario'], 'Teléfono');
        }

        if (!empty($registroPersona['email'])) {
            $errores[] = Validador::emailValido($registroPersona['email']);
        }

        $errores = array_filter($errores);

        if (!empty($errores)) {
            $this->view('clientes.create', [
                'error' => implode("<br>", $errores),
                'data' => $registroPersona
            ]);
            return -1;
        }

        $idPersona = $this->personaModel->create($registroPersona);

        if ($idPersona > 0) {
            $registroCliente = [
                'idpersona'      => $idPersona,
                'idempresa'      => null,
                'idcolregistra'  => null,
                'idcolactualiza' => null,
                'tipocliente'    => 'P',
            ];

            $idCliente = $this->clienteModel->create($registroCliente);

            if ($idCliente > 0) {
                $_SESSION['success'] = '¡Cliente creado exitosamente!';
                $this->redirect('/clientes');
                return $idCliente;
            } else {
                $this->view('clientes.create', ['error' => 'Error al crear el cliente.']);
            }
        } else {
            $this->view('clientes.create', ['error' => 'Error al crear la persona.']);
        }

        return -1;
    }

    public function edit(int $id): void
    {
        $personaCliente = $this->personaModel->getById($id);
        if ($personaCliente) {
            $this->view('clientes.edit', ['personaCliente' => $personaCliente]);
        } else {
            http_response_code(404);
            $this->view('errors.404');
        }
    }

    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $data = array_map([Validador::class, 'limpiar'], $_POST);

        $registro = [
            'nombres'       => $data['nombres'] ?? '',
            'apellidos'     => $data['apellidos'] ?? '',
            'email'         => $data['email'] ?? '',
            'telprimario'   => $data['telprimario'] ?? '',
            'latitud'       => $data['latitud'] ?? null,
            'longitud'      => $data['longitud'] ?? null,
            'direccion'     => $data['direccion'] ?? null,
            'iddistrito'    => !empty($data['iddistrito']) ? (int)$data['iddistrito'] : null,
            'idpersona'     => $id
        ];

        $errores = [];

        $errores[] = Validador::campoObligatorio($registro['nombres'], 'Nombres');
        $errores[] = Validador::campoObligatorio($registro['apellidos'], 'Apellidos');

        $errorTel = Validador::campoObligatorio($registro['telprimario'], 'Teléfono');
        if ($errorTel) {
            $errores[] = $errorTel;
        } else {
            $errores[] = Validador::telefonoValido($registro['telprimario'], 'Teléfono');
        }

        if (!empty($registro['email'])) {
            $errores[] = Validador::emailValido($registro['email']);
        }

        $errores = array_filter($errores);

        $personaCliente = $this->personaModel->getById($id);

        if (!empty($errores)) {
            $this->view('clientes.edit', [
                'personaCliente' => $personaCliente,
                'error' => implode("\n", $errores)
            ]);
            return;
        }

        $resultado = $this->personaModel->update($registro);

        if ($resultado > 0) {
            $_SESSION['success'] = '¡Cliente actualizado correctamente!';
            $this->redirect('/clientes');
        } elseif ($resultado === 0) {
            $this->view('clientes.edit', [
                'personaCliente' => $personaCliente,
                'error' => 'No se realizaron cambios en el cliente.'
            ]);
        } else {
            $this->view('clientes.edit', [
                'personaCliente' => $personaCliente,
                'error' => 'Error al actualizar el cliente.'
            ]);
        }
    }
}
