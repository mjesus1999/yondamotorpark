<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cotizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizacion/create" class="">[ Registrar ]</a>
            </div>
        </div>
    </div>

    <div class="mb-2">
        <form action="/cotizacion/store" method="POST" id="formCotizacion">
            <!-- Formato de cotizacion -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- CAMPOS -->
                    <div class="row g-2">

                        <!-- COTIZACION -->
                        <div class="col-md-5">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="tipocotizacion" name="tipocotizacion">
                                <label for="tipocotizacion">Formato Cotizacion</label>
                            </div>
                        </div>
                        <!-- FECHA INICIO -->
                        <div class="col-md-3 mb-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="fechainicio" name="fechainicio">
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
                    <div class="col text-end"><a href="#" id="lnk-agregar-marca">[ Agregar ]</a></div>
                </div>
            </div>
            <div class="card-body">
                <div class="card-body">
                    <table class="table table-sm" id="tabla-cotizacion">
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
                                    <td><?= $f['fechafin'] ?? '—' ?></td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-info me-1" title="Ver detalles">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        <a href="/cotizacion/requisitos/<?= $f['idformato'] ?>" class="btn btn-sm btn-secondary"
                                            title="Agregar requisitos">
                                            <i class="bi bi-plus-circle"></i> Requisitos
                                        </a>
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

    });

</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>