<?php include __DIR__ . '/../layout/header.php'; ?>


<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Egresos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <a href="/egreso/create" class="btn btn-outline-primary btn-sm">Registrar</a>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <?php $estadoActual = $estado ?? '' ?>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="btn-group m-1 mb-2" id="botones-filtro">
                    <a href="/egreso/listar/N"
                        class="btn btn-sm <?= $estadoActual === 'N' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        Sin comprobantes
                    </a>

                    <a href="/egreso/listar/S"
                        class="btn btn-sm <?= $estadoActual === 'S' ? 'btn-warning' : 'btn-outline-warning' ?>">
                        Pendientes con comprobantes
                    </a>
                </div>

            </div>

        </div>

        <div class="card-body" id="lista-oc">

            <div class="table-responsive d-none d-md-block">
                <table class="table table-sm table-hover" id="tabla-egresos">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Solicitante</th>
                            <th>Monto</th>
                            <th>Comentario</th>
                            <?php if ($estado == 'S'): ?>
                                <th>Adjuntar comprobante</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($egresos)): ?>
                            <tr>
                                <td colspan="12" class="text-center">No hay egresos registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php $numeroFila = 1; ?>
                            <?php foreach ($egresos as $egreso): ?>
                                <tr>
                                    <td><?= $numeroFila++ ?></td>
                                    <td><?= htmlspecialchars($egreso['fecha']) ?></td>
                                    <td><?= htmlspecialchars($egreso['concepto']) ?></td>
                                    <td><?= htmlspecialchars($egreso['solicitante']) ?></td>
                                    <td><?= htmlspecialchars(number_format($egreso['monto'], 2)) ?></td>
                                    <td><?= htmlspecialchars($egreso['comentario'] ?? '') ?></td>
                                    <?php if ($estado == 'S'): ?>
                                        <td>
                                            <?php if ($estado == 'S'): ?>
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-comprobante" data-id="<?= $egreso['idegreso'] ?>">

                                                    <i class="bi bi-upload"></i>

                                                    Adjuntar</button>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No requiere</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>



                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>



        </div>
    </div>


    <div class="modal fade" id="modal-comprobante" tabindex="-1" aria-labelledby="title-modal" aria-hidden="true">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h1 class="modal-title fs-5" id="title-modal"><i class="bi bi-file-earmark-plus me-2"> Registro de comprobante</i></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form id="comprobanteForm" enctype="multipart/form-data">
                        <div class="row">

                        <input type="hidden" id="idegreso" name="idegreso" value="">
                            <div class="col-md-6 mb-3">
                                <label for="tipoDoc" class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                                <select id="tipoDoc" name="tipoDoc" class="form-select" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="B">Boleta</option>
                                    <option value="F">Factura</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rucProveedor" class="form-label fw-semibold">RUC del Proveedor <span class="text-danger">*</span></label>
                                <input type="text" id="rucProveedor" name="rucProveedor" class="form-control" maxlength="11" placeholder="Ej: 12345678901" required>
                                <div class="form-text">Debe contener 11 dígitos</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="serie" class="form-label fw-semibold">Serie <span class="text-danger">*</span></label>
                                <input type="text" id="serie" name="serie" class="form-control" placeholder="Ej: B001" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numDocumento" class="form-label fw-semibold">Número de Documento <span class="text-danger">*</span></label>
                                <input type="text" id="numDocumento" name="numDocumento" class="form-control" placeholder="Ej: 00001234" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="monto" class="form-label fw-semibold">Monto <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="text" id="monto" name="monto" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="archivoComprobante" class="form-label fw-semibold">Subir Archivo <span class="text-danger">*</span></label>
                                <input type="file" id="archivoComprobante" name="archivoComprobante" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>

                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 gap-2">
                            <button type="reset" class="btn btn-outline-secondary btn-sm ">Cancelar</button>
                            <button type="submit" class="btn btn-outline-primary" id="saveComprobanteBtn">
                                Guardar Comprobante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>





</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {

        // Validar RUC (solo números, máximo 11 dígitos)
        document.getElementById('rucProveedor').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });

        // Validar monto (formato decimal)
        document.getElementById('monto').addEventListener('input', function(e) {
            // Permitir solo números y un punto decimal
            this.value = this.value.replace(/[^0-9.]/g, '');

            // Permitir solo un punto decimal
            if ((this.value.match(/\./g) || []).length > 1) {
                this.value = this.value.substring(0, this.value.lastIndexOf('.'));
            }

            // Limitar a 2 decimales
            if (this.value.includes('.')) {
                const parts = this.value.split('.');
                if (parts[1].length > 2) {
                    parts[1] = parts[1].substring(0, 2);
                    this.value = parts.join('.');
                }
            }
        });



        const comprobanteModal = document.getElementById('modal-comprobante');
        comprobanteModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget; // Para saber que boton especificamente dio clic el usuario con 'realatedTarget'
            const egresoId = button.getAttribute('data-id');
            const hiddenInput = document.getElementById('idegreso');
            hiddenInput.value = egresoId;
        });

    });
</script>


<?php include __DIR__ . '/../layout/footer.php'; ?>