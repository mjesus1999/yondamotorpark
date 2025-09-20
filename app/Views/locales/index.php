<?php include __DIR__ . '/../layout/header.php'; ?>

<?php if (isset($_SESSION['success'])) : ?>

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
                        <li class="breadcrumb-item"><a href="#">Locales</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="/locales/create"
                    class="">Registrar</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <!-- Vista de escritorio (tabla) -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-sm table-hover" id="tabla-locales">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tienda</th>
                                    <th>Ubicación</th>
                                    <th>Dirección</th>
                                    <th>Responsable</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($locales)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay locales registrados.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1 ?>
                                    <?php foreach ($locales as $local): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($numeroFila++) ?></td>
                                            <td><?= htmlspecialchars($local['tienda']) ?></td>
                                            <td><?= htmlspecialchars($local['departamento'] . "/" . $local['provincia'] . '/' . $local['distrito']) ?></td>
                                            <td><?= $local['direccion'] === null ? 'N/A' : htmlspecialchars($local['direccion']) ?></td>
                                            <td><?= htmlspecialchars($local['responsable']) ?></td>
                                            <td><?= $local['correo'] ? htmlspecialchars($local['correo']) : 'N/A' ?></td>
                                            <td><?= htmlspecialchars($local['telefono']) ?></td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-primary btn-edit-local"
                                                    data-bs-toggle="modal" data-bs-target="#modal-locales"
                                                    data-idlocal="<?= htmlspecialchars($local['idlocal']) ?>"
                                                    title="Editar">
                                                    <i class="fa-solid fa-pen" data-idlocal="<?= htmlspecialchars($local['idlocal']) ?>"></i>
                                                </a>
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

                    <!-- Vista móvil (acordeón) -->
                   <div class="d-block d-md-none">
    <?php if (empty($locales)) : ?>
        <div class="text-center text-muted p-3">No hay locales registrados.</div>
    <?php else: ?>
        <div class="accordion" id="acordeonLocales">
            <?php $numeroFila = 1; ?>
            <?php foreach ($locales as $local): ?>
                <div class="accordion-item mb-2 shadow-sm">
                    <h2 class="accordion-header" id="heading-<?= $local['idlocal'] ?>">
                        <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseLocal<?= $local['idlocal'] ?>"
                            aria-expanded="false"
                            aria-controls="collapseLocal<?= $local['idlocal'] ?>">
                            <div class="d-flex flex-column align-items-start text-start w-100">
                                <span class="fw-bold mb-1"><i class="bi bi-shop me-2 text-primary"></i><?= htmlspecialchars($local['tienda']) ?></span>
                                <span class="badge bg-secondary text-white text-truncate w-100">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    <?= htmlspecialchars($local['departamento'] . "/" . $local['provincia'] . '/' . $local['distrito']) ?>
                                </span>
                                <span class="badge bg-info text-dark text-truncate w-100 mt-1">
                                    <i class="bi bi-house me-1"></i>
                                    <?= $local['direccion'] === null ? 'N/A' : htmlspecialchars($local['direccion']) ?>
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseLocal<?= $local['idlocal'] ?>"
                        class="accordion-collapse collapse"
                        aria-labelledby="heading-<?= $local['idlocal'] ?>"
                        data-bs-parent="#acordeonLocales">
                        <div class="accordion-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>#:</strong> <?= htmlspecialchars($numeroFila++) ?></li>
                                <li class="list-group-item"><strong>Ubicación:</strong> <?= htmlspecialchars($local['departamento'] . "/" . $local['provincia'] . '/' . $local['distrito']) ?></li>
                                <li class="list-group-item"><strong>Dirección:</strong> <?= $local['direccion'] === null ? 'N/A' : htmlspecialchars($local['direccion']) ?></li>
                                <li class="list-group-item"><strong>Responsable:</strong> <?= htmlspecialchars($local['responsable']) ?></li>
                                <li class="list-group-item"><strong>Correo:</strong> <?= $local['correo'] ? htmlspecialchars($local['correo']) : 'No asignado' ?></li>
                                <li class="list-group-item"><strong>Teléfono:</strong> <?= htmlspecialchars($local['telefono']) ?></li>
                            </ul>
                            <div class="mt-3">
                                <strong>Acciones:</strong>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <a class="btn btn-sm btn-outline-primary btn-edit-local"
                                        data-bs-toggle="modal" data-bs-target="#modal-locales"
                                        data-idlocal="<?= htmlspecialchars($local['idlocal']) ?>"
                                        title="Editar">
                                        <i class="fa-solid fa-pen" data-idlocal="<?= htmlspecialchars($local['idlocal']) ?>"></i>
                                    </a>
                                    <form action="/locales/delete/<?= htmlspecialchars($local['idlocal']) ?>" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Estás seguro de que quieres eliminar este local?');">
                                        <button type="submit" class='btn btn-sm btn-outline-danger delete' title='Eliminar'>
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
    <?php endif; ?>
</div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-locales" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modal-locales-label" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" autocomplete="off" id="formulario-locales">
                <input type="hidden" name="idlocal" id="modal-idlocal" value="<?= htmlspecialchars($local['idlocal']) ?>">
                <div class="modal-content">
                    <div class="modal-header bg-yonda">
                        <h1 class="modal-title fs-5" id="modal-locales-label">Actualizar Local</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating">
                            <input type="text" class="form-control" name="responsable" id="modal-responsable" placeholder="Responsable" required>
                            <label for="modal-responsable">Responsable</label>
                        </div>
                        <div class="form-floating mt-2">
                            <input type="text" class="form-control" name="telefono" id="modal-telefono" placeholder="Teléfono" required>
                            <label for="modal-telefono">Teléfono</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-actualizar-local">Actualizar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto" id="toast-title">Notificación</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toast-body">
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const modalLocales = document.getElementById('modal-locales');
        const formularioLocales = document.getElementById('formulario-locales');
        const modalIdlocal = document.getElementById('modal-idlocal');
        const modalResponsable = document.getElementById('modal-responsable');
        const modalTelefono = document.getElementById('modal-telefono');



        document.querySelectorAll('.btn-edit-local').forEach(button => {
            button.addEventListener('click', async (event) => {
                const idlocal = event.target.dataset.idlocal;

                if (!idlocal) {
                    showToast("Error: No se pudo obtener el ID del local para editar.", "ERROR");
                    return;
                }

                try {
                    const response = await fetch(`/api/locales/${idlocal}`, {
                        method: 'GET'
                    });

                    const contentType = response.headers.get("content-type");
                    if (!contentType || contentType.indexOf("application/json") === -1) {
                        const errorText = await response.text();
                        showToast("Error inesperado del servidor al cargar datos. Revisa la consola.", "ERROR");
                        return;
                    }

                    const result = await response.json();


                    if (result.success && result.local) {
                        modalIdlocal.value = result.local.idlocal;
                        modalResponsable.value = result.local.responsable;
                        modalTelefono.value = result.local.telefono;
                    } else {

                    }
                } catch (error) {

                    showToast("Error de conexión al cargar datos del local.", "ERROR");
                }
            });
        });

        formularioLocales.addEventListener('submit', async (event) => {
            event.preventDefault();

            const idlocal = modalIdlocal.value;
            const parsedIdlocal = parseInt(idlocal, 10);

            if (isNaN(parsedIdlocal) || parsedIdlocal <= 0) {
                showToast("Error: El ID del local para actualizar es inválido.", "ERROR");
                return;
            }

            const formData = new FormData(formularioLocales);

            if (confirm("¿Desea actualizar este local?")) {
                try {
                    const response = await fetch(`/locales/update/${parsedIdlocal}`, {
                        method: 'POST',
                        body: formData
                    });

                    const contentType = response.headers.get("content-type");
                    if (!contentType || contentType.indexOf("application/json") === -1) {
                        const errorText = await response.text();
                        showToast("Error inesperado del servidor al actualizar. Revisa la consola.", "ERROR");
                        return;
                    }

                    const result = await response.json();


                    if (result.success) {
                        showToast(result.message, "SUCCESS");
                        const modalInstance = bootstrap.Modal.getInstance(modalLocales);
                        modalInstance.hide();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);

                    } else {
                        showToast(result.message, "ERROR");
                    }
                } catch (error) {
                    console.error("Error al actualizar el local:", error);
                    showToast("Ocurrió un error inesperado al actualizar el local.", "ERROR");
                }
            }
        });

    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>