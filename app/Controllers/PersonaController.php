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

    public function storePersonaClient(): int
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return 0;
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
        $nombresCampos = [
            'apellidos' => 'Apellidos',
            'nombres' => 'Nombres',
            'tipodoc' => 'Tipo de documento',
            'nrodoc' => 'Número de documento',
            'genero' => 'Género',
            'fechanac' => 'Fecha de nacimiento',
            'estadocivil' => 'Estado civil',
            'iddistrito' => 'Distrito',
            'telprimario' => 'Teléfono'
        ];

        foreach (['apellidos', 'nombres', 'tipodoc', 'nrodoc', 'genero', 'fechanac', 'estadocivil', 'iddistrito', 'telprimario'] as $campo) {
            if (empty($registroPersona[$campo])) {
                $nombreAmigable = $nombresCampos[$campo] ?? $campo;
                $errores[] = "El campo '$nombreAmigable' es obligatorio.";
            }
        }

        if (count($errores) > 0) {

            $this->view('clientes.create', ['error' => implode('\n', $errores)]);
            return -1 ;
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
                $success = '¡Cliente creado exitosamente!';
                $this->view('clientes.create', ['success' => $success]);
                return $idCliente;
            } else {
                $this->view('clientes.create', ['error' => 'Error al crear el registro del cliente.']);
                return -1;
            }
        } else {
            
            $this->view('clientes.create', ['error' => 'Error al crear la persona.']);
        }
        return $idCliente;
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
                'latitud' => ($_POST['latitud'] ?? '') !== '' ? trim($_POST['latitud']) : null,
                'longitud' => ($_POST['longitud'] ?? '') !== '' ? trim($_POST['longitud']) : null,
'                 direccion' => ($_POST['direccion'] ?? '') !== '' ? trim($_POST['direccion']) : null,

                'iddistrito' => trim($_POST['iddistrito']) ?? '',
                'idpersona' => $id
            ];

            // Validación amigable
            $errores = [];
            $nombresCampos = [
                'nombres' => 'Nombres',
                'apellidos' => 'Apellidos',
                'telprimario' => 'Teléfono'
            ];
            foreach (['nombres', 'apellidos', 'telprimario'] as $campo) {
                if (empty($registro[$campo])) {
                    $nombreAmigable = $nombresCampos[$campo] ?? $campo;
                    $errores[] = "El campo '$nombreAmigable' es obligatorio.";
                }
            }

            if (count($errores) > 0) {
                $personaCliente = $this->personaModel->getById($id);
                $this->view('clientes.edit', [
                    'personaCliente' => $personaCliente,
                    'error' => implode('\n', $errores)
                ]);
                return;
            }

            // Si la actualización fue exitosa
            $resultado = $this->personaModel->update($registro);
           // var_dump($resultado); exit;

           // No muestra la notificación que no hubo cambios, porque el Now() del model fuerza un cambio
            if ($resultado > 0) {
                // Actualización exitosa
                $personaCliente = $this->personaModel->getById($id);
                $success = '¡Cliente actualizado correctamente!';
                $this->view('clientes.edit', [
                    'personaCliente' => $personaCliente,
                    'success' => $success
                ]);
            } elseif ($resultado === 0) {
                // No hubo cambios
                $personaCliente = $this->personaModel->getById($id);
                $this->view('clientes.edit', [
                    'personaCliente' => $personaCliente,
                    'error' => 'No se realizaron cambios en el cliente.'
                ]);
            } else {
                // Error real
                $personaCliente = $this->personaModel->getById($id);
                $this->view('clientes.edit', [
                    'personaCliente' => $personaCliente,
                    'error' => 'Error al actualizar el cliente.'
                ]);
            }
        }
    }
}
