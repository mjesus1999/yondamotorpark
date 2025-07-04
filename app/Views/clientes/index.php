<?php
session_start();
include __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Clientes (Normales)</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-6 d-flex align-items-center justify-content-end gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/clientes/empresas">Jurídicas (Empresas)</a></li>
                    </ol>
                </nav>
                <a class="btn btn-sm btn-outline-primary" href="/clientes/createpersonclient">Registrar</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-clientes-personas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ubicación</th>
                                    <th>Direccion</th>
                                    <th>Nombre completo</th>
                                    <th>Tipo documento</th>
                                    <th>N° documento</th>
                                    <th>Correo</th>
                                    <th>Telefóno</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <<tbody>

                                <?php if (empty($personClientes)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay clientes personas registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1 ?>
                                    <?php foreach ($personClientes as $personCliente): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($numeroFila++) ?></td>
                                            <td><?= htmlspecialchars($personCliente['ubicacion']) ?></td>
                                            <td><?= htmlspecialchars($personCliente['direccion'] ?? 'No asignado') ?></td>
                                            <td><?= htmlspecialchars($personCliente['nombrecompleto']) ?></td>
                                            <td><?= htmlspecialchars($personCliente['tipodoc']) ?></td>
                                            <td><?= htmlspecialchars($personCliente['nrodoc']) ?></td>
                                            <td><?= htmlspecialchars($personCliente['email'] ?? 'No asignado')  ?></td>
                                            <td><?= htmlspecialchars($personCliente['telprimario']) ?></td>
                                            <td>
                                                <a href="/personaCliente/edit/<?= htmlspecialchars($personCliente['idpersona']) ?>"
                                                    class="btn btn-sm btn-outline-primary"> <i class="fa-solid fa-pen"></i></a>

                                                <form action="/personaCliente/delete/<?= htmlspecialchars($personCliente['idcliente']) ?>" method="POST" class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?');">
                                                    <button type="submit" class='btn btn-sm btn-outline-danger delete' title='Eliminar'>
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php

// if (isset($_SESSION['message'])) {
//     $message = $_SESSION['message'];
//     $messageType = $_SESSION['message_type'] ?? 'INFO'; // Tipo por defecto

//     echo '<script>
//         document.addEventListener("DOMContentLoaded", function() {
//             showToast(' . json_encode($message) . ', "' . $messageType . '", 1000, null);
//         });
//     </script>';

//     // Limpia los mensajes después de mostrarlos
//     unset($_SESSION['message']);
//     unset($_SESSION['message_type']);
// }
?>

<?php include __DIR__ . '/../layout/footer.php'; ?>