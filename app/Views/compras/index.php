<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Compras</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="/compras/create"
                    class="">Registrar</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-compras">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Concesionario</th>
                                    <th>Fecha entrega</th>
                                    <th>Fecha recepción</th>
                                    <th>Tipo doc</th>
                                    <th>Serie</th>
                                    <th>Número</th>
                                    
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (empty($locales)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay compras registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1 ?>
                                    <?php foreach ($locales as $local): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($numeroFila++) ?></td>
                                            <td><?= htmlspecialchars($local['tienda']) ?></td>
                                            <td><?= htmlspecialchars($local['departamento'] . "/" . $local['provincia'] . '/' . $local['distrito']) ?></td>
                                            <td><?= htmlspecialchars($local['direccion']) ?? 'No asignado' ?></td>
                                            <td><?= htmlspecialchars($local['responsable']) ?></td>
                                            <td><?= $local['correo'] ? htmlspecialchars($local['correo']) : 'No asignado' ?></td>
                                            <td><?= htmlspecialchars($local['telefono']) ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-primary btn-edit-local"
                                                    data-bs-toggle="modal" data-bs-target="#modal-locales"
                                                    data-idlocal="<?= htmlspecialchars($local['idlocal']) ?>"
                                                    title="Editar"> <i class="fa-solid fa-pen" data-idlocal="<?= htmlspecialchars($local['idlocal']) ?>"></i> </a>


                                                <form action="/locales/delete/<?= htmlspecialchars($local['idlocal']) ?>" method="POST" class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar este local?');">
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

    <?php include __DIR__ . '/../layout/footer.php'; ?>