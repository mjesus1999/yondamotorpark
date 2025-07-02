<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Locales</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="locales/create"
                    class="">Registrar</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-locales">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tienda</th>
                                    <th>Ubicación</th>
                                    <th>Direccion</th>
                                    <th>Responsable</th>
                                    <th>Correo</th>
                                    <th>Telefono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (empty($locales)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay productos registrados.</td>
                                    </tr>
                                <?php else: ?>

                                    <?php $numeroFila = 1 ?>

                                    <?php foreach ($locales as $local): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($numeroFila++)?></td>
                                            <td><?= htmlspecialchars($local['tienda']) ?></td>
                                            <td><?= htmlspecialchars($local['departamento'] . "/" . $local['provincia'] . '/' . $local['distrito']) ?></td>
                                            <td><?= htmlspecialchars($local['direccion']) ?></td>
                                            <td><?= htmlspecialchars($local['responsable']) ?></td>
                                            <td><?= htmlspecialchars($local['correo']) ?></td>
                                            <td><?= htmlspecialchars($local['telefono']) ?></td>
                                            <td>
                                                <a href="/locales/edit/<?= htmlspecialchars($local['idlocal']) ?>"
                                                    class="btn btn-sm btn-outline-primary" title="Editar">Editar</a>
                                                <form action="/locales/delete/<?= htmlspecialchars($local['idlocal']) ?>" method="POST" class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar este local');">
                                                    <a href='#' title='Eliminar'
                                                        class='btn btn-sm btn-outline-danger delete'>
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
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
<div class="modal fade" id="modal-locales" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modal-locales" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" autocomplete="off" id="formulario-locales">
            <input type="hidden" id="idlocal">
            <div class="modal-content">
                <div class="modal-header bg-yonda">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Actualizar local</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="responsable" placeholder="Responsable" required>
                        <label for="nombre-comercial">Responsable</label>
                    </div>
                    <div class="form-floating mt-2">
                        <input type="text" class="form-control" id="telefono" placeholder="Telefóno" required>
                        <label for="nombre-comercial">Telefóno</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>