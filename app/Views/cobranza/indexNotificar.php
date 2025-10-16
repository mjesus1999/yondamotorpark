<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">

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
                <a href="/Cobranza" class="btn btn-sm btn-outline-primary">Volver</a>
            </div>
        </div>
    </div>

    <!-- TARJETA PRINCIPAL -->
    <div class="card">
        <div class="card-body">

            <!-- BUSCADOR Y BOTÓN (solo escritorio) -->
            <div class="d-none d-md-flex justify-content-between align-items-center mb-3">
                <div class="input-group w-50">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="busqueda-global" class="form-control" placeholder="Buscar...">
                </div>
                <button class="btn btn-primary btn-sm" id="btnNotificarTodos">
                    <i class="fas fa-paper-plane me-1"></i> Notificar Todos
                </button>
            </div>

            <!-- TABULATOR (escritorio) -->
            <div id="tabla-clientes-tabulator" class="table-responsive d-none d-md-block">
                <div class="text-center py-5" id="spinner-clientes">
                    <div class="spinner-border text-primary" role="status"><span
                            class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2">Cargando clientes...</p>
                </div>
            </div>

            <!-- ACORDEÓN (móvil) -->
            <div id="acordeonClientes" class="d-block d-md-none"></div>

            <!-- MENSAJE VACÍO -->
            <div id="mensaje-vacio" class="alert alert-warning d-none mt-3">No hay clientes para notificar.</div>
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
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <div class="form-floating">
                                <input type="text" id="telefonoActual" class="form-control" readonly>
                                <label for="telefonoActual">Teléfono Actual</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-floating">
                                <input type="text" id="telefonoNuevo" maxlength="15" class="form-control"
                                    placeholder="Nuevo Teléfono" autocomplete="off" required>
                                <label for="telefonoNuevo">Nuevo Teléfono <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>El cambio se aplicará inmediatamente.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm btn-primary" id="btnGuardarTelefono">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>

<script>
    let modalEditarTelefono;
    let filaActual;

    //Funciones auxiliares
    function escapeHtml(t) {
        return t ? t.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m])) : '';
    }
    function formatMoneda(v) {
        return 'S/. ' + (parseFloat(v) || 0).toFixed(2);
    }
    function validarTelefono(tel) {
        return /^[0-9]{9,15}$/.test(tel.replace(/\s/g, ''));
    }
    function mostrarToast(mensaje, tipo = 'info') {
        const nt = document.createElement('div');
        nt.className = `alert alert-${tipo === 'success' ? 'success' : tipo === 'warning' ? 'warning' : 'danger'} position-fixed`;
        nt.style.cssText = `top:20px; right:20px; z-index:9999; min-width:260px;`;
        nt.innerHTML = `<div class="d-flex align-items-center"><i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i><div>${mensaje}</div><button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button></div>`;
        document.body.appendChild(nt);
        setTimeout(() => nt.remove(), 5000);
    }

    //Cargar clientes
    async function cargarClientes() {
        const res = await fetch('/Cobranza/getClientesNotificar', { cache: 'no-store' });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Error al cargar datos');
        return data.data || [];
    }

    //Enviar SMS
    async function enviarSms(cliente) {
        const res = await fetch('/Cobranza/enviarSmsNotificacion', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(cliente)
        });
        return res.json();
    }

    //Inicializar vista
    document.addEventListener('DOMContentLoaded', async () => {
        const spinner = document.getElementById('spinner-clientes');
        const mensajeVacio = document.getElementById('mensaje-vacio');
        const acordeon = document.getElementById('acordeonClientes');

        modalEditarTelefono = new bootstrap.Modal(document.getElementById('modalEditarTelefono'));

        try {
            const datos = await cargarClientes();
            if (spinner) spinner.remove();

            if (!Array.isArray(datos) || datos.length === 0) {
                mensajeVacio.classList.remove('d-none');
                acordeon.innerHTML = '<div class="text-center py-3">No hay datos para mostrar.</div>';
                return;
            }

            //TABULATOR
            const tabla = new Tabulator("#tabla-clientes-tabulator", {
                data: datos,
                layout: "fitColumns",
                pagination: "local",
                paginationSize: 15,
                paginationSizeSelector: [10, 15, 25, 50],
                columns: [
                    { title: "#", formatter: "rownum", width: 50 },
                    { title: "Cliente", field: "cliente", widthGrow: 6, tooltip: true },
                    {
                        title: "Teléfono", field: "telefono", widthGrow: 3, formatter: (cell) => {
                            const val = cell.getValue() || 'N/A';
                            const data = cell.getRow().getData();
                            return val !== 'N/A'
                                ? ` <a href="#" class="text-decoration-none link-telefono" data-id="${data.idcontrato}" data-cliente="${escapeHtml(data.cliente)}" data-tel="${val}">
                                        <i class="fas fa-phone-alt me-1"></i>${escapeHtml(val)}
                                    </a>`
                                : `<span class="text-muted">N/A</span>`;
                        }
                    },
                    { title: "Vehículo", field: "vehiculo", widthGrow: 4 },
                    { title: "Tienda", field: "local", widthGrow: 3 },
                    {
                        title: "Cuotas", field: "cuotas_pagadas", widthGrow: 3, formatter: (cell) => {
                            const d = cell.getRow().getData();
                            return `${d.cuotas_pagadas} / ${d.cuotas_totales}`;
                        }
                    },
                    { title: "Monto Cuota", field: "monto_cuota", widthGrow: 3, hozAlign: "right", formatter: c => formatMoneda(c.getValue()) },
                    { title: "Fecha Venc.", field: "fecha_vencimiento", widthGrow: 3 },
                    {
                        title: "Acciones", hozAlign: "center", headerSort: false, widthGrow: 2,
                        formatter: (cell) => `<button class="btn btn-sm btn-secondary btn-sms"><i class="fas fa-paper-plane"></i></button>`
                    }
                ]
            });

            //Filtro de busqueda
            const searchInput = document.getElementById("busqueda-global");
            if (searchInput) {
                searchInput.addEventListener("keyup", function (e) {
                    const val = e.target.value.trim();
                    if (!val) tabla.clearFilter();
                    else tabla.setFilter([
                        [
                            { field: "cliente", type: "like", value: val },
                            { field: "telefono", type: "like", value: val },
                            { field: "vehiculo", type: "like", value: val },
                            { field: "local", type: "like", value: val }
                        ]
                    ]);
                });
            }

            //Eventos dentro de la tabla
            document.getElementById('tabla-clientes-tabulator').addEventListener('click', async (e) => {
                const telLink = e.target.closest('.link-telefono');
                const btnSms = e.target.closest('.btn-sms');

                if (telLink) {
                    const id = telLink.dataset.id;
                    document.getElementById('idcontratoModal').value = id;
                    document.getElementById('nombreClienteModal').textContent = telLink.dataset.cliente;
                    document.getElementById('telefonoActual').value = telLink.dataset.tel;
                    document.getElementById('telefonoNuevo').value = '';
                    modalEditarTelefono.show();
                }

                if (btnSms) {
                    const row = tabla.getRowFromElement(btnSms.closest('.tabulator-row'));
                    const data = row.getData();
                    btnSms.disabled = true;
                    btnSms.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    const result = await enviarSms(data);
                    btnSms.className = `btn btn-sm ${result.success ? 'btn-success' : 'btn-danger'} btn-sms`;
                    btnSms.innerHTML = `<i class="fas fa-${result.success ? 'check' : 'times'}"></i>`;
                    btnSms.title = result.message || '';
                }
            });

            //Acordeon movil
            let html = '';
            let i = 1;

            datos.forEach(c => {
                const id = `cliente-${c.idcontrato}`;
                html += `
                    <div class="accordion-item mb-2 shadow-sm">
                        <h2 class="accordion-header" id="heading-${id}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${id}" aria-expanded="false">
                                <i class="bi bi-person-circle me-2 text-primary"></i>${escapeHtml(c.cliente)}
                            </button>
                        </h2>
                        <div id="collapse-${id}" class="accordion-collapse collapse" data-bs-parent="#acordeonClientes">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>#:</strong> ${i++}</li>
                                    <li class="list-group-item"><strong>Teléfono:</strong> ${c.telefono || 'N/A'}</li>
                                    <li class="list-group-item"><strong>Vehículo:</strong> ${escapeHtml(c.vehiculo)}</li>
                                    <li class="list-group-item"><strong>Tienda:</strong> ${escapeHtml(c.local)}</li>
                                    <li class="list-group-item"><strong>Cuotas:</strong> ${c.cuotas_pagadas} / ${c.cuotas_totales}</li>
                                    <li class="list-group-item"><strong>Monto Cuota:</strong> ${formatMoneda(c.monto_cuota)}</li>
                                    <li class="list-group-item"><strong>Fecha Vencimiento:</strong> ${escapeHtml(c.fecha_vencimiento)}</li>
                                    <li class="list-group-item d-flex gap-2">
                                        <button class="btn btn-sm btn-secondary w-50 btn-sms" data-id="${c.idcontrato}"><i class="fas fa-paper-plane"></i></button>
                                        <button class="btn btn-sm btn-primary w-50 btn-editar" data-id="${c.idcontrato}" data-cliente="${escapeHtml(c.cliente)}" data-tel="${c.telefono || ''}"><i class="fas fa-edit"></i></button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
            acordeon.innerHTML = `<div class="accordion">${html}</div>`;

            // Eventos moviles
            acordeon.addEventListener('click', async (e) => {
                const btnSms = e.target.closest('.btn-sms');
                const btnEdit = e.target.closest('.btn-editar');

                if (btnEdit) {
                    document.getElementById('idcontratoModal').value = btnEdit.dataset.id;
                    document.getElementById('nombreClienteModal').textContent = btnEdit.dataset.cliente;
                    document.getElementById('telefonoActual').value = btnEdit.dataset.tel || 'Sin teléfono';
                    document.getElementById('telefonoNuevo').value = '';
                    modalEditarTelefono.show();
                }

                if (btnSms) {
                    btnSms.disabled = true;
                    btnSms.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    const contrato = btnSms.dataset.id;
                    const cliente = datos.find(d => d.idcontrato == contrato);
                    const result = await enviarSms(cliente);
                    btnSms.className = `btn btn-sm ${result.success ? 'btn-success' : 'btn-danger'} btn-sms`;
                    btnSms.innerHTML = `<i class="fas fa-${result.success ? 'check' : 'times'}"></i>`;
                }
            });

            //Boton Notificar Todos
            document.getElementById('btnNotificarTodos').addEventListener('click', async function () {
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enviando...';
                for (const c of datos) {
                    await enviarSms(c);
                    await new Promise(r => setTimeout(r, 1000));
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Notificar Todos';
                mostrarToast('Proceso completado', 'success');
            });

            //Guardar telefono
            document.getElementById('btnGuardarTelefono').addEventListener('click', async () => {
                const id = document.getElementById('idcontratoModal').value;
                const telNuevo = document.getElementById('telefonoNuevo').value.trim();
                const telActual = document.getElementById('telefonoActual').value.trim();

                if (!validarTelefono(telNuevo)) {
                    document.getElementById('telefonoNuevo').classList.add('is-invalid');
                    return;
                }

                const res = await fetch('/Cobranza/actualizarTelefono', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ idcontrato: id, telefono_actual: telActual, telefono_nuevo: telNuevo })
                });

                const result = await res.json();
                if (result.success) {
                    modalEditarTelefono.hide();
                    mostrarToast('Teléfono actualizado correctamente', 'success');
                    location.reload();
                } else mostrarToast(result.message || 'Error al actualizar', 'danger');
            });

        } catch (err) {
            console.error(err);
            if (spinner) spinner.remove();
            mensajeVacio.classList.remove('d-none');
            mostrarToast('Error al cargar clientes', 'danger');
        }
    });
</script>