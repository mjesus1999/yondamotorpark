<?php include __DIR__ . '/../../layout/header.php'; ?>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
                        <li class="breadcrumb-item"><a href="#">Empresas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-6 d-flex align-items-center justify-content-end gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/clientes">Clientes (Normales)</a></li>
                    </ol>
                </nav>
                <a class="btn btn-sm btn-outline-primary" href="/clientes/empresas/createempresaclient">Registrar</a>
            </div>
        </div>


    </div>

   <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                <!-- Vista de escritorio (tabla) -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-sm table-hover" id="tabla-cliente-empresa">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ubicación</th>
                                <th>Dirección</th>
                                <th>Responsable</th>
                                <th>RUC</th>
                                <th>Empresa</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($empresasClientes)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">No hay clientes empresas registradas.</td>
                                </tr>
                            <?php else: ?>
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($empresasClientes as $empresaCliente): ?>
                                    <tr>
                                        <td class="align-middle"><?= htmlspecialchars($numeroFila++) ?></td>
                                        <td class="align-middle"><?= htmlspecialchars($empresaCliente['ubicacion']) ?></td>
                                        <td class="align-middle">
                                            <?= $empresaCliente['direccion'] ? htmlspecialchars($empresaCliente['direccion']) : 'No asignado' ?>
                                        </td>
                                        <td class="align-middle"><?= htmlspecialchars($empresaCliente['responsable']) ?></td>
                                        <td class="align-middle"><?= htmlspecialchars($empresaCliente['ruc']) ?></td>
                                        <td class="align-middle"><?= htmlspecialchars($empresaCliente['nombrecomercial']) ?></td>
                                        <td class="align-middle">
                                            <?= $empresaCliente['email'] ? htmlspecialchars($empresaCliente['email']) : 'No asignado' ?>
                                        </td>
                                        <td class="align-middle"><?= htmlspecialchars($empresaCliente['telprimario']) ?></td>
                                        <td class="align-middle">
                                            <div class="d-flex gap-1">
                                                <a href="/clientes/empresaCliente/edit/<?= htmlspecialchars($empresaCliente['idempresa']) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form action="/empresaCliente/delete/<?= htmlspecialchars($empresaCliente['idcliente']) ?>" method="POST" class="d-inline"
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

                <!-- Vista móvil (acordeón) -->
                <div class="d-block d-md-none">
                    <?php if (!empty($empresasClientes)) : ?>
                        <?php $numeroFila = 1; ?>
                        <?php foreach ($empresasClientes as $empresaCliente): ?>
                            <div class="card mb-2 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center"
                                     data-bs-toggle="collapse"
                                     data-bs-target="#collapseEmpresa<?= $empresaCliente['idcliente'] ?>"
                                     aria-expanded="false"
                                     aria-controls="collapseEmpresa<?= $empresaCliente['idcliente'] ?>"
                                     style="cursor: pointer;">
                                    <span><i class="bi bi-building me-2 text-primary fw-bold"></i><?= htmlspecialchars($empresaCliente['nombrecomercial']) ?></span>
                                    <i class="bi bi-chevron-down"></i>
                                </div>
                                <div id="collapseEmpresa<?= $empresaCliente['idcliente'] ?>" class="collapse">
                                    <div class="card-body">
                                        <p><strong>#:</strong> <?= htmlspecialchars($numeroFila++) ?></p>
                                        <p><strong>Ubicación:</strong> <?= htmlspecialchars($empresaCliente['ubicacion']) ?></p>
                                        <p><strong>Dirección:</strong> <?= $empresaCliente['direccion'] ? htmlspecialchars($empresaCliente['direccion']) : 'No asignado' ?></p>
                                        <p><strong>Responsable:</strong> <?= htmlspecialchars($empresaCliente['responsable']) ?></p>
                                        <p><strong>RUC:</strong> <?= htmlspecialchars($empresaCliente['ruc']) ?></p>
                                        <p><strong>Correo:</strong> <?= $empresaCliente['email'] ? htmlspecialchars($empresaCliente['email']) : 'No asignado' ?></p>
                                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($empresaCliente['telprimario']) ?></p>
                                        <p><strong>Acciones:</strong></p>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="/clientes/empresaCliente/edit/<?= htmlspecialchars($empresaCliente['idempresa']) ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="/empresaCliente/delete/<?= htmlspecialchars($empresaCliente['idcliente']) ?>" method="POST"
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
                        <div class="text-center text-muted p-3">No hay clientes empresas registradas.</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>


</div>





<?php include __DIR__ . '/../../layout/footer.php'; ?>