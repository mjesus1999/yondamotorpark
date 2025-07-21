<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Formato de Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registro</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <!-- <a href="/formatoCotizacion/create" class="">[ Registrar ]</a> -->
            </div>
        </div>
    </div>

    <div class="mb-2">
        <form action="/formatoCotizacion/store" method="POST" id="formCotizacion" autocomplete="OFF">
            <!-- Formato de cotizacion -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- CAMPOS -->
                    <div class="row g-2">

                        <!-- COTIZACION -->
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="tipocotizacion" name="tipocotizacion"
                                    required>
                                <label for="tipocotizacion">Formato Cotizacion</label>
                            </div>
                        </div>
                        <!-- FECHA INICIO -->
                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="fechainicio" name="fechainicio" required>
                                <label for="fechainicio">Fecha Inicio</label>
                            </div>
                        </div>
                        <!-- FECHA FIN -->
                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="fechafin" name="fechafin" disabled>
                                <label for="fechafin">Fecha Fin</label>
                            </div>
                        </div>
                        <div class="col-md-1 mb-2 d-flex align-items-center">
                            <div class="form-check mt-2">
                                <input type="checkbox" id="indefinido" name="indefinido" checked>
                                <label for="indefinido">Indefinido</label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary btn-sm btnGuardarFormatoCotizacion">Agregar</button>
                </div>
            </div>
        </form>
    </div>

    <div class="mb-2">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">Lista Formato de cotizacion</div>
                    <!-- <div class="col text-end"><a href="#" id="lnk-agregar-marca">[ Agregar ]</a></div> -->
                </div>
            </div>
            <div class="card-body">
                <div class="card-body">
                    <table class="table table-sm" id="tabla-formatocotizacion">
                        <thead>
                            <tr>
                                <th>Nombre de la cotizacion</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formatos as $f): ?>
                                <tr data-id="<?= $f['idformato'] ?>">
                                    <td><?= htmlspecialchars($f['tipocotizacion']) ?></td>
                                    <td><?= $f['fechainicio'] ?></td>
                                    <td><?= $f['fechafin'] ?? 'Indefinido' ?></td>
                                    <td class="text-center">
                                        <!-- Ver -->
                                        <button type="button" class="btn btn-sm btn-outline-primary mx-1 btn-ver-requisitos"
                                            data-id="<?= $f['idformato'] ?>" title="Ver detalles">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        <!-- Requisitos -->
                                        <a href="/formatoCotizacion/requisitos/<?= $f['idformato'] ?>"
                                            class="btn btn-sm btn-outline-secondary mx-1" title="Agregar requisitos">
                                            <i class="fa fa-plus-circle"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger mx-1 btn-eliminar-formato"
                                            data-id="<?= $f['idformato'] ?>" title="Eliminar formato">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <span style="font-style: italic;">Seleccione un elemento</span>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="modalRequisitos" tabindex="-1" aria-labelledby="modalRequisitosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-yonda">
                <h5 class="modal-title" id="modalRequisitosLabel">Requisitos del Formato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <ul id="listaRequisitos" class="list-group">
                    <!-- se insertan datos dinámicamente -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chk = document.getElementById('indefinido');
        const fechaFin = document.getElementById('fechafin');

        function toggleFecha() {
            fechaFin.disabled = chk.checked;
            if (chk.checked) {
                fechaFin.value = '';
            }
        }

        toggleFecha();
        chk.addEventListener('change', toggleFecha);

        //ELIMINAR
        document.querySelectorAll('.btn-eliminar-formato').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirigir o enviar petición AJAX para eliminar
                        fetch(`/formatoCotizacion/delete/${id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id })
                        })
                            .then(res => res.ok ? location.reload() : Promise.reject(res))
                            .catch(() => Swal.fire('Error', 'No se pudo eliminar.', 'error'));
                    }
                });
            });
        });

        document.querySelectorAll('.btn-ver-requisitos').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                try {
                    const res = await fetch(`/formatoCotizacion/detalle/${id}`);
                    if (!res.ok) throw new Error('Error al cargar detalles');
                    const requisitos = await res.json();

                    // Limpia la lista actual
                    const ul = document.getElementById('listaRequisitos');
                    ul.innerHTML = '';

                    if (requisitos.length === 0) {
                        ul.innerHTML = '<li class="list-group-item">No hay requisitos.</li>';
                    } else {
                        requisitos.forEach(r => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item';
                            li.textContent = r.requisito;
                            ul.appendChild(li);
                        });
                    }

                    //muestra el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalRequisitos'));
                    modal.show();

                } catch (err) {
                    Swal.fire('Error', err.message, 'error');
                }
            });
        });
    });

</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>