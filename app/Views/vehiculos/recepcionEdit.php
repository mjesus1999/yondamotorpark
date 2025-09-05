<?php include __DIR__ . '/../layout/header.php'; ?>
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #183de1ff 0%, #2482dbff 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --card-shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.15);
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Cards */
    .modern-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--card-shadow-hover);
    }

    .info-card-oc .card-header,
    .vehicles-card .card-header {
        background: var(--primary-gradient);
        color: white;
        font-weight: 600;
    }

    .modern-table thead th {
        background: var(--success-gradient);
        color: white;
        font-weight: 600;
    }

    .modern-table tbody tr.selected {

        background-color: #667eea !important;
        color: white;
        transform: scale(1);
        box-shadow: 0 6px 15px rgba(23, 17, 209, 0.8);
    }


    .modern-table tbody tr.selected,
    .modern-table tbody tr.selected td {
        background-color: #667eea !important;
        color: white !important;
    }

    /* Modal */
    .modal-content {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
        animation: fadeIn 0.4s ease;
    }

    .modal-header {
        background: var(--primary-gradient);
        color: white;
    }

    .modern-input {
        border: 2px solid rgba(102, 126, 234, 0.1);
        border-radius: 10px;
        transition: var(--transition);

    }

    .modern-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .modern-table tbody tr {
        cursor: pointer;
    }

    #tabla-vehiculo th,
    #tabla-vehiculo td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        vertical-align: middle;

    }

    #tabla-vehiculo td {
        font-size: 14px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .custom-switch-container {
        border: 1px solid #dee2e6;
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    .custom-switch-container:hover {
        border-color: #0d6efd;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
    }

    .form-switch .form-check-input {
        width: 3em;
        height: 1.6em;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .status-no {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c2c7;
    }

    .status-si {
        background-color: #d1edff;
        color: #0c4a6e;
        border: 1px solid #b3d9ff;
    }


    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
<div class="container-fluid py-4">
    <div class="alert alert-info mt-2">
        <div class="row align-items-center">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="#">Recepción Vehículos</a></li>
                        <li class="breadcrumb-item active">Actualizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/recepcionVehiculos"><i class="fas fa-list-ul me-2"></i>Listar Recepciones</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card modern-card info-card-oc">
                <div class="card-header"><i class="fas fa-file-invoice me-2"></i>Información de la OC</div>
                <div class="card-body">
                    <div class="card inner-card mb-3">
                        <div class="card-header"><i class="fas fa-building me-2"></i>Concesionario</div>
                        <div class="card-body text-center">
                            <h5 class="text-primary fw-bold"><?= htmlspecialchars($info_compra['razonsocial_concesionario'] ?? 'N/A'); ?></h5>
                        </div>
                    </div>
                    <div class="card inner-card">
                        <div class="card-header"><i class="fas fa-chart-line me-2"></i>Datos de la OC</div>
                        <div class="table-responsive p-2">
                            <table class="table modern-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Amortizado</th>
                                        <th>Saldo</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= htmlspecialchars($info_compra['fechacompra'] ?? 'N/A'); ?></td>
                                        <td><span class="badge bg-success"><?= number_format($info_compra['total_amortizado'] ?? 0, 2); ?></span></td>
                                        <td><span class="badge bg-secondary"><?= number_format($info_compra['saldopendiente'] ?? 0, 2); ?></span></td>
                                        <td><span class="badge bg-primary"><?= number_format($info_compra['totalcompra'] ?? 0, 2); ?></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card modern-card vehicles-card">
                <div class="card-header"><i class="fas fa-car-side me-2"></i>Datos de los Vehículos</div>
                <div class="card-body">
                    <p class="text-muted fw-bold"><i class="fas fa-info-circle me-2"></i>Seleccione un vehículo para actualizar.</p>
                    <div class="table-responsive">
                        <table class="table modern-table table-hover" id="tabla-vehiculo">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Modelo</th>
                                    <th>Versión</th>
                                    <th>Combustible</th>
                                    <th>Año</th>
                                    <th>Chasis</th>
                                    <th>Placa</th>
                                    <th>Placa r.</th>
                                    <th>Serie motor</th>
                                    <th>Color</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($vehiculos)): ?>
                                    <?php $numeroFila = 1; ?>
                                    <?php foreach ($vehiculos as $vehiculo): ?>
                                        <tr class="vehicle-row"
                                            data-idlocal="<?= htmlspecialchars($vehiculo['idlocal'] === null ? "" : $vehiculo['idlocal']) ?>"
                                            data-num-auto="<?= htmlspecialchars($numeroFila) ?>"
                                            data-id="<?= htmlspecialchars($vehiculo['idvehiculo']); ?>"
                                            data-chasis="<?= htmlspecialchars($vehiculo['chasis'] ?? 'N/A'); ?>"
                                            data-placa="<?= htmlspecialchars($vehiculo['placa'] ?? 'N/A'); ?>"
                                            data-rotativa="<?= htmlspecialchars($vehiculo['placarotativa'] ?? 'N/A'); ?>"
                                            data-serie="<?= htmlspecialchars($vehiculo['seriemotor'] ?? 'N/A'); ?>"
                                            data-color="<?= htmlspecialchars($vehiculo['color'] ?? 'N/A'); ?>">
                                            <td><?= $numeroFila++ ?></td>
                                            <td><?= htmlspecialchars($vehiculo['modelo'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($vehiculo['version'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($vehiculo['combustible'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($vehiculo['anio'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($vehiculo['chasis'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($vehiculo['placa'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($vehiculo['placarotativa'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($vehiculo['seriemotor'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($vehiculo['color'] ?? 'N/A') ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="12" class="text-center">No hay vehículos para recepcionar.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pencil-alt me-2"></i>Actualizar Datos del Vehículo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex flex-column mt-2" id="datos-vehiculo" style="border-left: 4px solid #0d6efd; border-radius: 6px;">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-car me-2 fs-4 text-primary"></i>
                        <strong>Actualizar vehículo seleccionado:</strong>
                    </div>
                    <div id="detalle-vehiculo" class="fw-bold text-body p-2">

                    </div>
                </div>

                <form id="vehicleForm">
                    <input type="hidden" name="idvehiculo">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-lg-3">
                            <label for="chasis" class="form-label">Chasis <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control modern-input" id="chasis" name="chasis" maxlength="30" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label for="placa" class="form-label">Placa</label>
                            <input type="text" class="form-control modern-input" id="placa" name="placa" maxlength="10">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label for="placarotativa" class="form-label">Placa R.</label>
                            <input type="text" class="form-control modern-input" id="placarotativa" name="placarotativa" maxlength="10">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label for="seriemotor" class="form-label">Serie Motor <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control modern-input" id="seriemotor" name="seriemotor" maxlength="20" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="color" class="form-label">Color <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control modern-input" id="color" name="color" maxlength="30" required>
                        </div>
                        <div class="col-md-6">
                            <label for="local" class="form-label">Tienda <span class="text-danger fw-bold">*</span></label>
                            <select name="idlocal" class="form-select modern-input" id="local" required>
                                <option value="">Seleccione una tienda</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded shadow-sm custom-switch-container">
                                <div class="form-label mb-3 fw-bold text-body">Indicar si está en piso</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" type="checkbox" id="disponibilidad" name="disponibilidad">
                                        </div>
                                        <label class="form-check-label fw-bold text-body mb-0" for="disponibilidad">
                                            ¿Está en piso?
                                      </label>
                                    </div>
                                    <span id="estadoPiso" class="status-badge status-no">No</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="saveButton"><i class="fas fa-save me-2"></i>Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100" id="toast-container">
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const tablaVehiculos = document.querySelector('#tabla-vehiculo');
        const modalElement = document.getElementById('updateVehicleModal');
        const modal = new bootstrap.Modal(modalElement);
        const form = document.getElementById('vehicleForm');
        const selectLocal = document.getElementById('local');
        const saveButton = document.getElementById('saveButton');
        const estadoPisoSpan = document.getElementById('estadoPiso');
        const disponibilidadCheckbox = document.getElementById('disponibilidad');

        const chasisInput = document.getElementById('chasis');
        const placaInput = document.getElementById('placa');
        const seriemotorInput = document.getElementById('seriemotor');

        // Función para cargar locales activos
        async function getAllLocalesActivos(selectedId = '') {
            try {
                const response = await fetch('/api/localesActivos', {
                    method: 'GET'
                });
                const data = await response.json();
                selectLocal.innerHTML = `<option value="">Seleccione una tienda</option>`;
                if (data.length > 0) {
                    data.forEach(item => {
                        const selected = item.idlocal == selectedId ? 'selected' : '';
                        selectLocal.innerHTML += `<option value="${item.idlocal}" ${selected}>${item.local}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error al cargar locales:', error);
                showToast('Error al cargar la lista de tiendas.', 'ERROR', 3000);
            }
        }

        // Validación en tiempo real para el chasis
        chasisInput.addEventListener('input', () => {
            if (chasisInput.value.trim().length >= 10) {
                limpiarError(chasisInput);
            }
        });

        // Validación en tiempo real para la placa
        placaInput.addEventListener('input', () => {
            const regex = /^[A-Z0-9]{3,4}-?[A-Z0-9]{3,6}$/i;
            if (regex.test(placaInput.value.trim().toUpperCase())) {
                limpiarError(placaInput);
            }
        });

        seriemotorInput.addEventListener('input',() => {
            if(seriemotorInput.value !== '' || seriemotorInput.value.length > 10){
                limpiarError(seriemotorInput);
            }

        });

        // Selección de fila y carga de datos en el formulario
        tablaVehiculos.addEventListener('click', (e) => {
            const row = e.target.closest('tr.vehicle-row');
            if (!row) return;

            document.querySelectorAll('.vehicle-row.selected').forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');

            form.idvehiculo.value = row.dataset.id;
            form.chasis.value = row.dataset.chasis === 'N/A' ? '' : row.dataset.chasis;
            form.placa.value = row.dataset.placa === 'N/A' ? '' : row.dataset.placa;
            form.placarotativa.value = row.dataset.rotativa === 'N/A' ? '' : row.dataset.rotativa;
            form.seriemotor.value = row.dataset.serie === 'N/A' ? '' : row.dataset.serie;
            form.color.value = row.dataset.color === 'N/A' ? '' : row.dataset.color;

            const idLocalActual = row.dataset.idlocal || '';
            getAllLocalesActivos(idLocalActual);

            const detalles = `
                <i class="fas fa-hashtag text-primary p-1"></i> N°: ${row.dataset.numAuto} -
                <i class="fas fa-barcode text-success p-1"></i> Chasis: ${row.dataset.chasis} -
                <i class="fas fa-car-side text-info p-1"></i> Placa: ${row.dataset.placa} -
                <i class="fas fa-exchange-alt text-warning p-1"></i> Placa R.: ${row.dataset.rotativa} -
                <i class="fas fa-cogs text-danger p-1"></i> Serie: ${row.dataset.serie} -
                <i class="fas fa-palette text-secondary p-1"></i> Color: ${row.dataset.color}
            `;
            document.getElementById('detalle-vehiculo').innerHTML = detalles;

            modal.show();
        });


        // Limpiar selección y formulario al cerrar modal
        modalElement.addEventListener('hidden.bs.modal', () => {
            document.querySelectorAll('.vehicle-row.selected').forEach(r => r.classList.remove('selected'));
            form.reset();
            limpiarErrores();
            disponibilidadCheckbox.checked = false;
            estadoPisoSpan.textContent = 'No';
        });

        // Cambiar texto de disponibilidad
        disponibilidadCheckbox.addEventListener('change', function() {
            if (this.checked) {
                estadoPisoSpan.textContent = 'Sí';
                estadoPisoSpan.classList.remove('status-no');
                estadoPisoSpan.classList.add('status-si');
            } else {
                estadoPisoSpan.textContent = 'No';
                estadoPisoSpan.classList.remove('status-si');
                estadoPisoSpan.classList.add('status-no');
            }
        });


        // Funciones para marcar y limpiar errores
        function marcarError(input, mensaje) {
            input.classList.add('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = mensaje;
            }
        }

        function limpiarError(input) {
            input.classList.remove('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = '';
            }
        }

        function limpiarErrores() {
            form.querySelectorAll('.is-invalid').forEach(input => limpiarError(input));
        }

        // Validación del formulario al enviar
        function validarFormulario() {
            let esValido = true;
            limpiarErrores();

            if (chasisInput.value.trim().length < 10) {
                marcarError(chasisInput, 'El chasis es obligatorio y debe tener al menos 10 caracteres.');
                esValido = false;
            }


            const placaRegex = /^[A-Z0-9]{3,4}-?[A-Z0-9]{3,6}$/i;
            if (placaInput.value.trim() !== '' && !placaRegex.test(placaInput.value.trim().toUpperCase())) {
                marcarError(placaInput, 'Formato de placa inválido (ej: ABC-123, ABC1234, ABC-12345).');
                esValido = false;
            }

            if (selectLocal.value === '') {
                marcarError(selectLocal, 'Debe seleccionar una tienda.');
                esValido = false;
            }
            if(seriemotorInput.value === '' || seriemotorInput.value.length < 10) {
                marcarError(seriemotorInput,'Debe ingresar el serie motor y debe tener una logitud mayor a o igual a 10');
                esValido = false;
            }

            return esValido;
        }

        // Envío del formulario

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!validarFormulario()) {
                showToast('Por favor, corrige los errores en el formulario.', 'ERROR', 3000);
                return;
            }

            if (await ask('¿Estás seguro de actualizar los datos?', 'Confirmar')) {
                saveButton.disabled = true;
                saveButton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...`;

                try {
                    const formData = new FormData(form);
                    const idVehiculoActualizado = form.idvehiculo.value;
                    const nuevoEstado = disponibilidadCheckbox.checked ? 'libre' : 'proceso';
                    formData.set('disponibilidad', nuevoEstado);

                    const response = await fetch('/update/vehiculoRecepcionado', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        // Ocultar el modal inmediatamente después del éxito
                        modal.hide();

                        const filaSeleccionada = document.querySelector('.vehicle-row.selected');
                        if (filaSeleccionada) {
                            // Mantener la actualización de los datos en la fila
                            filaSeleccionada.dataset.idlocal = form.idlocal.value;
                            filaSeleccionada.dataset.chasis = form.chasis.value;
                            filaSeleccionada.dataset.placa = form.placa.value;
                            filaSeleccionada.dataset.rotativa = form.placarotativa.value;
                            filaSeleccionada.dataset.serie = form.seriemotor.value;
                            filaSeleccionada.dataset.color = form.color.value;

                            const celdas = filaSeleccionada.querySelectorAll('td');
                            celdas[5].textContent = form.chasis.value || 'N/A';
                            celdas[6].textContent = form.placa.value || 'N/A';
                            celdas[7].textContent = form.placarotativa.value || 'N/A';
                            celdas[8].textContent = form.seriemotor.value || 'N/A';
                            celdas[9].textContent = form.color.value || 'N/A';

                            showToast(data.message, 'SUCCESS', 1200);

                            // Eliminar la fila si el estado es 'libre'
                            if (nuevoEstado === 'libre') {
                                if (filaSeleccionada) {
                                    filaSeleccionada.remove();
                                    if (!document.querySelector('.vehicle-row')) {
                                        document.querySelector('#tabla-vehiculo tbody').innerHTML = `<tr><td colspan="12" class="text-center">No hay vehículos.</td></tr>`;
                                    }
                                }

                                // Aquí está la nueva lógica para el toast
                                showToast(data.message, 'SUCCESS', 2000);

                                const toastContainer = document.querySelector('#toast-container');
                                if (toastContainer) {
                                    const urlRedireccion = `/vehiculos?estado=libre&vehiculo_nuevo=${idVehiculoActualizado}`;
                                    const toastHTML = `
                                                    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                                        <div class="toast-header">
                                                            <strong class="me-auto">Vehículo Libre</strong>
                                                            <small>Ahora</small>
                                                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                                        </div>
                                                        <div class="toast-body d-flex justify-content-between align-items-center">
                                                            El vehículo ahora está libre.
                                                            <a href="${urlRedireccion}" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover ms-3">Ir a la vista de libres</a>
                                                        </div>
                                                    </div>
                                                `;

                                    const toastElement = document.createElement('div');
                                    toastElement.innerHTML = toastHTML;
                                    toastContainer.appendChild(toastElement.firstElementChild);

                                    const toast = new bootstrap.Toast(toastContainer.lastElementChild, {
                                        autohide: false // Para que no se oculte automáticamente
                                    });
                                    toast.show();
                                }
                            }
                        }
                    } else {
                        showToast(data.message, 'ERROR', 3000);
                    }
                } catch (error) {
                    console.error('Error al actualizar:', error);
                    showToast(data.message, 'ERROR', 3000);
                } finally {
                    saveButton.disabled = false;
                    saveButton.innerHTML = `<i class="fas fa-save me-2"></i>Guardar Cambios`;
                }
            }
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>