<?php

include __DIR__ . '/../layout/header.php';
?>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('<?= addslashes($_SESSION['success']) ?>', 'SUCCESS', 1000);
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>


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

                    <!-- VISTA DE ESCRITORIO (tabla) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-sm table-hover" id="tabla-clientes-personas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ubicación</th>
                                    <th>Dirección</th>
                                    <th>Nombre completo</th>
                                    <th>Tipo documento</th>
                                    <th>N° documento</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($personClientes)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay clientes personas registradas.</td>
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
                                            <td><?= htmlspecialchars($personCliente['email'] ?? 'No asignado') ?></td>
                                            <td><?= htmlspecialchars($personCliente['telprimario']) ?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="/personaCliente/edit/<?= htmlspecialchars($personCliente['idpersona']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </a>
                                                    <form action="/personaCliente/delete/<?= htmlspecialchars($personCliente['idcliente']) ?>" method="POST" class="d-inline"
                                                        onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?');">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger delete" title="Eliminar">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- VISTA MÓVIL (ACORDEÓN) -->
                    <div class="d-block d-md-none">
                        <?php if (!empty($personClientes)): ?>
                            <?php $numeroFila = 1; ?>
                            <?php foreach ($personClientes as $personCliente): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseCliente<?= $personCliente['idcliente'] ?>"
                                        aria-expanded="false"
                                        aria-controls="collapseCliente<?= $personCliente['idcliente'] ?>"
                                        style="cursor: pointer;">
                                        <span><i class="bi bi-person-fill me-2 text-primary fw-bold"></i><?= htmlspecialchars($personCliente['nombrecompleto']) ?></span>
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                    <div id="collapseCliente<?= $personCliente['idcliente'] ?>" class="collapse">
                                        <div class="card-body">
                                            <p><strong>#:</strong> <?= $numeroFila++ ?></p>
                                            <p><strong>Ubicación:</strong> <?= htmlspecialchars($personCliente['ubicacion']) ?></p>
                                            <p><strong>Dirección:</strong> <?= htmlspecialchars($personCliente['direccion'] ?? 'No asignado') ?></p>
                                            <p><strong>Tipo documento:</strong> <?= htmlspecialchars($personCliente['tipodoc']) ?></p>
                                            <p><strong>N° documento:</strong> <?= htmlspecialchars($personCliente['nrodoc']) ?></p>
                                            <p><strong>Correo:</strong> <?= htmlspecialchars($personCliente['email'] ?? 'No asignado') ?></p>
                                            <p><strong>Teléfono:</strong> <?= htmlspecialchars($personCliente['telprimario']) ?></p>
                                            <p><strong>Acciones:</strong></p>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="/personaCliente/edit/<?= htmlspecialchars($personCliente['idpersona']) ?>"
                                                    class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>

                                                <form action="/personaCliente/delete/<?= htmlspecialchars($personCliente['idcliente']) ?>" method="POST"
                                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cliente?');" class="m-0 p-0">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger delete" title="Eliminar">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted p-3">No hay clientes personas registradas.</div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>


</div>


<?php include __DIR__ . '/../layout/footer.php'; ?>