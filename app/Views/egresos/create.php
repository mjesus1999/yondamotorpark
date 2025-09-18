<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="alert alert-primary mt-3 border-0 shadow-sm" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i> Egresos
                            </a>
                        </li>
                        <li class="breadcrumb-item active fw-semibold" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end mt-2 mt-md-0">
                <a class="btn btn-outline-primary btn-sm shadow-sm" href="/egreso">
                    <i class="fas fa-list me-1"></i>Mostrar lista
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card card-header fs-3 bg-primary text-white">
                    <i class="bi bi-file-earmark-plus me-2"> Registrar Egreso</i> 
                </div>
                <div class="card-body p-4">
                    <form id="egresoForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="concepto" class="form-label fw-semibold">Concepto de Egreso <span class="text-danger">*</span></label>
                                <select id="concepto" name="concepto" class="form-select" required>
                                    <option value="">Seleccione un concepto</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="colaborador" class="form-label fw-semibold">Colaborador que Solicita <span class="text-danger">*</span></label>
                                <input list="colaboradores-list" id="colaborador" name="colaborador" class="form-control" placeholder="Escriba o seleccione un colaborador" required>
                                <datalist id="colaboradores-list"></datalist>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="monto" class="form-label fw-semibold">Monto (S/) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                    <input type="text" id="monto" name="monto" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-4 pt-2">
                                    <input class="form-check-input" type="checkbox" id="requiereComprobante" name="requiereComprobante">
                                    <label class="form-check-label fw-semibold" for="requiereComprobante">
                                        ¿Requiere Comprobante?
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-semibold">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" rows="3" class="form-control" placeholder="Ingrese observaciones adicionales (opcional)..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
                            <button type="submit" class="btn btn-outline-primary">
                                Registrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const egresoForm = document.getElementById('egresoForm');
        const requiereComprobanteCheckbox = document.getElementById('requiereComprobante');
        const selectConcepto = document.getElementById('concepto');
        const comprobanteModal = new bootstrap.Modal(document.getElementById('modal-comprobante'));

        // Lógica para abrir/cerrar el modal manualmente
        requiereComprobanteCheckbox.addEventListener('change', () => {
            if (requiereComprobanteCheckbox.checked) {
                comprobanteModal.show();
            } else {
                comprobanteModal.hide();
            }
        });

        // Limpiar el formulario del modal cuando se cierra
        comprobanteModal._element.addEventListener('hide.bs.modal', () => {
            const comprobanteForm = document.getElementById('comprobanteForm');
            comprobanteForm.reset();
        });


        
       
        async function loadConceptosEgreso() {
            try {
                const response = await fetch('/api/egreso/conceptos');
                const result = await response.json();

                if (result.status) {
                    result.data.forEach(concepto => {
                        const option = document.createElement('option');
                        option.value = concepto.idconceptoegreso;
                        option.textContent = concepto.descripcion;
                        selectConcepto.appendChild(option);
                    });
                } else {
                    // Manejar error
                }
            } catch (error) {
                console.error('Error en la solicitud:', error);
            }
        }

        async function loadColaboradores() {
            try {
                const response = await fetch('/api/egreso/colaboradores');
                const result = await response.json();

                if (result.status) {
                    const datalist = document.getElementById('colaboradores-list');
                    datalist.innerHTML = '';
                    result.data.forEach(colaborador => {
                        const option = document.createElement('option');
                        option.value = colaborador.colaborador;
                        option.dataset.id = colaborador.idcolaborador;
                        datalist.appendChild(option);
                    });
                } else {
                    // Manejar error
                }
            } catch (error) {
                console.error('Error en la solicitud:', error);
            }
        }

        loadConceptosEgreso();
        loadColaboradores();

        document.getElementById('monto').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });

        egresoForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const conceptoId = selectConcepto.value;
            const colaboradorInput = document.getElementById('colaborador');
            const colaboradorOption = Array.from(document.getElementById('colaboradores-list').options)
                .find(option => option.value === colaboradorInput.value);
            const colaboradorId = colaboradorOption ? colaboradorOption.dataset.id : null;
            const monto = document.getElementById('monto').value.trim();
            const observaciones = document.getElementById('observaciones').value.trim();
            const requiereComprobante = requiereComprobanteCheckbox.checked ? 'S' : 'N';

            if (!conceptoId) {
                showToast('Por favor, seleccione un concepto de egreso.', 'WARNING', 1300);
                return;
            }

            if (!colaboradorId) {
                showToast('Por favor, seleccione un colaborador válido de la lista.', 'WARNING', 1300);
                return;
            }

            if (!monto || isNaN(monto) || parseFloat(monto) <= 0) {
                showToast('Por favor, ingrese un monto válido mayor a cero.', 'WARNING', 1300);
                return;
            }

            const formData = new FormData();
            formData.append('idconceptoegreso', conceptoId);
            formData.append('idsolicitante', colaboradorId);
            formData.append('monto', parseFloat(monto).toFixed(2));
            formData.append('comentario', observaciones);
            formData.append('requierecomprobante', requiereComprobante);

            if (await ask('¿Registrar egreso?', 'Confirmar')) {
                try {
                    const response = await fetch('/egreso/store', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        showToast(result.message, 'SUCCESS', 1200);
                        egresoForm.reset();
                    } else {
                        showToast(result.message || 'Error al registrar el egreso. Intente nuevamente.', 'ERROR', 1300);
                    }
                } catch (error) {
                    showToast('Error al registrar el egreso. Intente nuevamente.', 'ERROR', 1300);
                }
            }
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>