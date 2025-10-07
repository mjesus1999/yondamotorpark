<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <!-- CABECERA -->
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none">Área de Cobranza</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Notificar a los Clientes
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/Cobranza" class="btn btn-outline-primary btn-sm">Volver</a>
            </div>
        </div>
    </div>

    <!-- TABLA DE DATOS -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- CABECERA DE LA TABLA -->
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Clientes por Notificar</h6>
                        <button class="btn btn-primary btn-sm" id="btnNotificarTodos">
                            <i class="fas fa-paper-plane me-1"></i>Notificar Todos
                        </button>
                    </div>
                </div>

                <!-- TABLA CON DATOS -->
                <div class="card-body">
                    <table class="table table-sm table-hover table-hover-yonda">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Vehículo</th>
                                <th>Tienda</th>
                                <th>Cuotas</th>
                                <th>Monto Cuota</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="tabla-clientes">
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando clientes...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR TELÉFONO -->
<div class="modal fade" id="modalEditarTelefono" tabindex="-1" aria-labelledby="modalEditarTelefonoLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditarTelefonoLabel">
                    <i class="fas fa-phone-alt me-2"></i>Actualizar Teléfono
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarTelefono">
                    <input type="hidden" id="idcontratoModal">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Cliente:</label>
                        <p class="text-muted mb-0" id="nombreClienteModal"></p>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="telefonoActual" class="form-label">Teléfono Actual</label>
                        <input type="text" class="form-control" id="telefonoActual" readonly>
                    </div> -->

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <div class="form-floating">
                                <input type="text" name="telefonoActual" class="form-control" id="telefonoActual"
                                    placeholder="Telefono Actual" readonly>
                                <label for="telefonoActual">Telefono Actual</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="form-floating">
                                <input type="text" name="telefonoNuevo" class="form-control" id="telefonoNuevo"
                                    maxlength="15" placeholder="Ingrese el nuevo número" autocomplete="off" required>
                                <label for="telefonoNuevo">Nuevo Teléfono <span class="text-danger">*</span></label>
                                <div class="form-text">Solo números, mínimo 9 dígitos</div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="mb-3">
                        <label for="telefonoNuevo" class="form-label">Nuevo Teléfono <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="telefonoNuevo" 
                               placeholder="Ingrese el nuevo número"
                               maxlength="15"
                               required>
                        <div class="form-text">Solo números, mínimo 9 dígitos</div>
                        <div class="invalid-feedback" id="errorTelefono">
                            Ingrese un número válido (mínimo 9 dígitos)
                        </div>
                    </div> -->

                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>El cambio se aplicará inmediatamente <!-- para futuras notificaciones --></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <!-- <i class="fas fa-times me-1"></i> -->Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-primary" id="btnGuardarTelefono">
                    <!-- <i class="fas fa-save me-1"></i> -->Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let modalEditarTelefono;
    let filaActual;

    // Función para cargar clientes próximos a vencer
    function cargarClientesProximosVencer() {
        return new Promise((resolve, reject) => {
            fetch('/Cobranza/getClientesNotificar')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resolve(data.data);
                    } else {
                        reject(new Error(data.message || 'Error al cargar clientes'));
                    }
                })
                .catch(error => reject(error));
        });
    }

    function validarTelefono(telefono) {
        const regex = /^[0-9]{9,15}$/;
        return regex.test(telefono.replace(/\s/g, ''));
    }

    function abrirModalTelefono(row) {
        filaActual = row;

        document.getElementById('idcontratoModal').value = row.dataset.idcontrato;
        document.getElementById('nombreClienteModal').textContent = row.dataset.cliente;
        document.getElementById('telefonoActual').value = row.dataset.telefono || 'Sin teléfono';
        document.getElementById('telefonoNuevo').value = '';
        document.getElementById('telefonoNuevo').classList.remove('is-invalid');

        modalEditarTelefono.show();
    }

    // Función para renderizar la tabla
    function renderizarTabla(clientes) {
        const tbody = document.getElementById('tabla-clientes');

        if (clientes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay clientes próximos a vencer</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        clientes.forEach((cliente, index) => {
            const telefono = cliente.telefono || 'N/A';
            const telefonoHtml = telefono !== 'N/A'
                ? `<a href="#" class="text-decoration-none link-telefono" title="Cambiar teléfono">
                       <i class="fas fa-phone-alt me-1"></i>${telefono}
                   </a>`
                : `<span class="text-muted">N/A</span>`;

            html += `
                <tr data-row="${index + 1}" 
                    data-idcontrato="${cliente.idcontrato}"
                    data-telefono="${telefono}"
                    data-cliente="${cliente.cliente}"
                    data-monto="${cliente.monto_cuota}"
                    data-fecha="${cliente.fecha_vencimiento}">
                    <td>${index + 1}</td>
                    <td>${cliente.cliente}</td>
                    <td>${telefonoHtml}</td>
                    <td>${cliente.vehiculo}</td>
                    <td>${cliente.local}</td>
                    <td>${cliente.cuotas_pagadas} de ${cliente.cuotas_totales}</td>
                    <td>S/. ${parseFloat(cliente.monto_cuota).toFixed(2)}</td>
                    <td>${cliente.fecha_vencimiento}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-secondary btn-estado">
                            <i class="fas fa-minus"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        // Agregar event listeners a los enlaces de teléfono
        document.querySelectorAll('.link-telefono').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const row = this.closest('tr');
                abrirModalTelefono(row);
            });
        });
    }

    // Función para enviar SMS individual
    async function enviarSms(row) {
        const btnEstado = row.querySelector('.btn-estado');

        const datosCliente = {
            idcontrato: row.dataset.idcontrato,
            telefono: row.dataset.telefono,
            cliente: row.dataset.cliente,
            monto_cuota: row.dataset.monto,
            fecha_vencimiento: row.dataset.fecha
        };

        try {
            const response = await fetch('/Cobranza/enviarSmsNotificacion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datosCliente)
            });

            const resultado = await response.json();

            if (resultado.success) {
                btnEstado.className = 'btn btn-sm btn-success btn-estado';
                btnEstado.innerHTML = '<i class="fas fa-check"></i>';
                btnEstado.title = 'Notificación enviada';
                return true;
            } else {
                btnEstado.className = 'btn btn-sm btn-danger btn-estado';
                btnEstado.innerHTML = '<i class="fas fa-times"></i>';
                btnEstado.title = 'Error al enviar';
                return false;
            }
        } catch (error) {
            console.error('Error al enviar SMS:', error);
            btnEstado.className = 'btn btn-sm btn-danger btn-estado';
            btnEstado.innerHTML = '<i class="fas fa-times"></i>';
            btnEstado.title = 'Error al enviar';
            return false;
        }
    }

    // Inicializar vista
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar modal
        modalEditarTelefono = new bootstrap.Modal(document.getElementById('modalEditarTelefono'));

        // Cargar clientes
        cargarClientesProximosVencer()
            .then(clientes => {
                renderizarTabla(clientes);
                console.log('Clientes cargados correctamente');
            })
            .catch(error => {
                console.error('Error al cargar clientes:', error);
                document.getElementById('tabla-clientes').innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="alert alert-danger mb-0" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error al cargar los datos. Por favor, recargue la página.
                            </div>
                        </td>
                    </tr>
                `;
            });

        // Validación en tiempo real
        document.getElementById('telefonoNuevo').addEventListener('input', function () {
            const valor = this.value.replace(/\s/g, '');
            if (valor && !validarTelefono(valor)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        // Guardar teléfono
        document.getElementById('btnGuardarTelefono').addEventListener('click', async function () {
            const telefonoActual = document.getElementById('telefonoActual').value.trim();
            const telefonoNuevo = document.getElementById('telefonoNuevo').value.trim();
            const idContrato = document.getElementById('idcontratoModal').value;

            if (!telefonoNuevo) {
                document.getElementById('telefonoNuevo').classList.add('is-invalid');
                return;
            }

            if (!validarTelefono(telefonoNuevo)) {
                document.getElementById('telefonoNuevo').classList.add('is-invalid');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';

            try {
                const response = await fetch('/Cobranza/actualizarTelefono', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        idcontrato: idContrato,
                        telefono_actual: telefonoActual,
                        telefono_nuevo: telefonoNuevo
                    })
                });

                const resultado = await response.json();

                if (resultado.success) {
                    // Actualizar en la fila actual
                    filaActual.dataset.telefono = telefonoNuevo;
                    const tdTelefono = filaActual.querySelector('td:nth-child(3)');
                    tdTelefono.innerHTML = `
                        <a href="#" class="text-decoration-none link-telefono" title="Cambiar teléfono">
                            <i class="fas fa-phone-alt me-1"></i>${telefonoNuevo}
                        </a>
                    `;

                    // Re-agregar event listener
                    tdTelefono.querySelector('.link-telefono').addEventListener('click', function (e) {
                        e.preventDefault();
                        const row = this.closest('tr');
                        abrirModalTelefono(row);
                    });

                    modalEditarTelefono.hide();

                    showToast(resultado.message || 'Teléfono actualizado correctamente', 'SUCCESS');
                } else {
                    showToast(resultado.message || 'Error al actualizar el teléfono', 'ERROR');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Error de conexión al actualizar el teléfono', 'ERROR');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Guardar Cambios';
            }
        });
    });

    // Botón Notificar Todos
    document.getElementById('btnNotificarTodos').addEventListener('click', async function () {
        const btn = this;
        const rows = document.querySelectorAll('tbody tr[data-row]');

        if (rows.length === 0) {
            showToast('No hay clientes para notificar', 'WARNING');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enviando...';

        for (const row of rows) {
            await enviarSms(row);
            await new Promise(resolve => setTimeout(resolve, 1000));
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Notificar Todos';

        showToast('Proceso de notificación completado', 'SUCCESS');
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>