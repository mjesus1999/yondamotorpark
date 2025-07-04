<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Persona;
use App\Models\Cliente;

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

    public function storePersonaClient(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->redirect('/clientes/createpersonclient');
            return;
        }

        $apellidos_val = trim($_POST['apellidos'] ?? '');
        $nombres_val = trim($_POST['nombres'] ?? '');
        $tipodoc_val = trim($_POST['tipodocumento'] ?? '');
        $nrodoc_val = trim($_POST['nrodoc'] ?? '');
        $genero_val = trim($_POST['genero'] ?? '');
        $fechanac_val = trim($_POST['fechanac'] ?? '');
        $estadocivil_val = trim($_POST['estadocivil'] ?? '');
        $email_val = trim($_POST['email'] ?? '');
        $iddistrito_val = trim($_POST['distrito'] ?? '');
        $direccion_val = trim($_POST['direccion'] ?? '');
        $referencia_val = trim($_POST['referencia'] ?? '');
        $telprimario_val = trim($_POST['telprimario'] ?? '');
        $telalternativo_val = trim($_POST['telalternativo'] ?? '');
        $latitud_val = trim($_POST['latitud'] ?? '');
        $longitud_val = trim($_POST['longitud'] ?? '');

        $registroPersona = [
            'apellidos'      => $apellidos_val,
            'nombres'        => $nombres_val,
            'tipodoc'        => $tipodoc_val,
            'nrodoc'         => $nrodoc_val,
            'genero'         => $genero_val,
            'fechanac'       => $fechanac_val,
            'estadocivil'    => $estadocivil_val,
            'email'          => ($email_val !== '') ? $email_val : null,
            'iddistrito'     => ($iddistrito_val !== '') ? (int)$iddistrito_val : null,
            'direccion'      => ($direccion_val !== '') ? $direccion_val : null,
            'referencia'     => ($referencia_val !== '') ? $referencia_val : null,
            'telprimario'    => $telprimario_val,
            'telalternativo' => ($telalternativo_val !== '') ? $telalternativo_val : null,
            'latitud'        => ($latitud_val !== '') ? $latitud_val : null,
            'longitud'       => ($longitud_val !== '') ? $longitud_val : null,
        ];

        $errores = [];
        foreach (['apellidos', 'nombres', 'tipodoc', 'nrodoc', 'genero', 'fechanac', 'estadocivil', 'iddistrito', 'telprimario'] as $campo) {
            if (empty($registroPersona[$campo])) {
                $errores[] = "El campo '" . $campo . "' es obligatorio.";
            }
        }

        if (count($errores) > 0) {

            $this->view('clientes.create', ['error' => implode('<br>', $errores)]);
            return;
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
                $_SESSION['success_message'] = '¡Persona y Cliente registrados exitosamente!';
                $this->redirect('/clientes');
            } else {
                $_SESSION['error_message'] = 'Error al crear el registro del cliente. La persona fue creada, pero no se pudo vincular como cliente.';

                $this->view('clientes.create', ['error' => 'Error al crear el registro del cliente.']);
            }
        } else {
            $_SESSION['error_message'] = 'Error al crear la persona. Por favor, intente de nuevo.';
            $this->view('clientes.create', ['error' => 'Error al crear la persona.']);
        }
        return;
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $registro = [
                'nombres' =>   trim($_POST['nombres']) ?? '',
                'apellidos' =>   trim($_POST['apellidos']) ?? '',
                'email' =>   trim($_POST['email']) ?? '',
                'direccion' =>   trim($_POST['direccion']) !== '' && trim($_POST['direccion']) !== 'null' ? trim($_POST['direccion']) : null,
                'telprimario' => trim($_POST['telprimario']) ?? '',
                'latitud' =>   trim($_POST['latitud']) !== '' && trim($_POST['latitud']) !== 'null' ? trim($_POST['latitud']) : null,
                'longitud' =>   trim($_POST['longitud']) !== '' && trim($_POST['longitud']) !== 'null' ? trim($_POST['longitud']) : null,
                'iddistrito' => trim($_POST['iddistrito']) ?? '',
                'idpersona' => $id

            ];

            if ($registro['nombres'] && $registro['apellidos'] && $registro['telprimario'] !== false) {
                if ($this->personaModel->update($registro)) {
                    $this->redirect('/clientes');
                } else {
                    $personaCliente = $this->personaModel->getById($id);
                    $this->view('clientes.edit', ['personaCliente' => $personaCliente, 'error' => 'Error al actualizar el cliente.']);
                }
            } else {
                $personaCliente = $this->personaModel->getById($id);
                $this->view('personas.edit', ['personaCliente' => $personaCliente, 'error' => 'Todos los campos son obligatorios.']);
            }
        }
    }
}
