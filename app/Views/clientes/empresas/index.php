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
                                    <th><span class="text-body badge">#</span></th>
                                    <th> <span class="text-body badge">Ubicación</span></th>
                                    <th> <span class="text-body badge">Dirección</span></th>
                                    <th> <span class="text-body badge">Responsable</span></th>
                                    <th><span class="text-body badge">RUC</span></th>
                                    <th> <span class="text-body badge">Empresa</span></th>
                                    <th> <span class="text-body badge">Correo</span></th>
                                    <th><span class="text-body badge">Teléfono</span></th>
                                    <th><span class="text-body badge">Acciones</span></th>
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
                                            <td><span class="text-body badge"><?= htmlspecialchars($numeroFila++) ?></span></td>
                                            <td><span class="text-body badge"><?= htmlspecialchars($empresaCliente['ubicacion']) ?></span></td>
                                            <td>
                                                <span class="text-body badge"><?= $empresaCliente['direccion'] ? htmlspecialchars($empresaCliente['direccion']) : 'N/A' ?></span>

                                            </td>
                                            <td> <span class="text-body badge"><?= htmlspecialchars($empresaCliente['responsable']) ?></span></td>
                                            <td> <span class="text-body badge"><?= htmlspecialchars($empresaCliente['ruc']) ?></span></td>
                                            <td> <span class="text-body badge"><?= htmlspecialchars($empresaCliente['nombrecomercial']) ?></span></td>
                                            <td>
                                                <span class="text-body badge"><?= $empresaCliente['email'] ? htmlspecialchars($empresaCliente['email']) : 'N/A' ?></span>
                                            </td>
                                            <td> <span class="text-body badge"><?= htmlspecialchars($empresaCliente['telprimario']) ?></span></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <div>
                                                        <a href="/clientes/empresaCliente/edit/<?= htmlspecialchars($empresaCliente['idempresa']) ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </a>

                                                    </div>
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
                            <div class="accordion" id="acordeonEmpresas">
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($empresasClientes as $empresaCliente): ?>
                                    <div class="accordion-item mb-2 shadow-sm">
                                        <h2 class="accordion-header" id="heading-<?= $empresaCliente['idcliente'] ?>">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseEmpresa<?= $empresaCliente['idcliente'] ?>"
                                                aria-expanded="false"
                                                aria-controls="collapseEmpresa<?= $empresaCliente['idcliente'] ?>">
                                                <span><i class="bi bi-building me-2 text-primary fw-bold"></i><?= htmlspecialchars($empresaCliente['nombrecomercial']) ?></span>
                                            </button>
                                        </h2>
                                        <div id="collapseEmpresa<?= $empresaCliente['idcliente'] ?>"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="heading-<?= $empresaCliente['idcliente'] ?>"
                                            data-bs-parent="#acordeonEmpresas">
                                            <div class="accordion-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>#:</strong> <?= htmlspecialchars($numeroFila++) ?></li>
                                                    <li class="list-group-item"><strong>Ubicación:</strong> <?= htmlspecialchars($empresaCliente['ubicacion']) ?></li>
                                                    <li class="list-group-item"><strong>Dirección:</strong> <?= $empresaCliente['direccion'] ? htmlspecialchars($empresaCliente['direccion']) : 'N/A' ?></li>
                                                    <li class="list-group-item"><strong>Responsable:</strong> <?= htmlspecialchars($empresaCliente['responsable']) ?></li>
                                                    <li class="list-group-item"><strong>RUC:</strong> <?= htmlspecialchars($empresaCliente['ruc']) ?></li>
                                                    <li class="list-group-item"><strong>Correo:</strong> <?= $empresaCliente['email'] ? htmlspecialchars($empresaCliente['email']) : 'N/A' ?></li>
                                                    <li class="list-group-item"><strong>Teléfono:</strong> <?= htmlspecialchars($empresaCliente['telprimario']) ?></li>
                                                </ul>
                                                <div class="mt-3">
                                                    <strong>Acciones:</strong>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
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
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-body p-3">No hay clientes empresas registradas.</div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>


</div>





<?php include __DIR__ . '/../../layout/footer.php'; ?>