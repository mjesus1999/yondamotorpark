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
                        <li class="breadcrumb-item active" aria-current="page">Vencidos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/Cobranza" class="btn btn-sm btn-outline-primary">Volver</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <!-- BÚSQUEDA Y CONTROLES (escritorio) -->
            <div class="table-responsive d-none d-md-block">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="busqueda-global" class="form-control" placeholder="Buscar ....">
                    </div>
                </div>
            </div>

            <!-- TABULATOR (escritorio) -->
            <div id="tabla-vencidos-tabulator" class="table-responsive d-none d-md-block">
                <div class="text-center py-5" id="spinner-vencidos">
                    <div class="spinner-border text-primary" role="status"><span
                            class="visually-hidden">Cargando...</span></div>
                    <p class="mt-2">Cargando contratos vencidos...</p>
                </div>
            </div>

            <!-- ACORDEÓN (móvil) -->
            <div id="acordeonVencidos" class="d-block d-md-none">
                <!-- Se llenará dinámicamente -->
            </div>

            <!-- Mensaje vacío -->
            <div id="mensaje-vacio" class="alert alert-warning d-none mt-3">No hay contratos vencidos.</div>

            <!-- Total -->
            <div class="mt-3 text-end fw-bold">
                <i class="fas fa-calculator me-2"></i>Total deuda: <span id="total-deuda" class="text-danger">S/.
                    0.00</span>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>

<script>
    function cargarVencidos() {
        return new Promise((resolve, reject) => {
            fetch('/Cobranza/getVencidos', { cache: 'no-store' })
                .then(async response => {
                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        if (response.ok) {
                            if (data && data.success) {
                                resolve(data.data || []);
                            } else {
                                reject(new Error(data.message || 'Respuesta inválida del servidor'));
                            }
                        } else {
                            reject(new Error(data && data.message ? `Error ${response.status}: ${data.message}` : `Error HTTP ${response.status}`));
                        }
                    } catch (err) {
                        console.error('Respuesta no JSON recibida:', text);
                        reject(new Error('Respuesta inválida del servidor (no JSON). Revisa el endpoint.'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    reject(error);
                });
        });
    }

    function formatMoneda(valor) {
        const n = parseFloat(valor) || 0;
        return 'S/. ' + n.toFixed(2);
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function mostrarNotificacion(mensaje, tipo = 'info') {
        const nt = document.createElement('div');
        nt.className = `alert alert-${tipo === 'success' ? 'success' : tipo === 'warning' ? 'warning' : 'danger'} position-fixed`;
        nt.style.cssText = `top:20px; right:20px; z-index:9999; min-width:260px;`;
        nt.innerHTML = `<div class="d-flex align-items-center"><i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i><div>${mensaje}</div><button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button></div>`;
        document.body.appendChild(nt);
        setTimeout(() => nt.remove(), 5000);
    }

    //Inicializar
    document.addEventListener('DOMContentLoaded', async () => {
        const contTabla = document.getElementById('tabla-vencidos-tabulator');
        const spinner = document.getElementById('spinner-vencidos');
        const mensajeVacio = document.getElementById('mensaje-vacio');
        const totalDeudaEl = document.getElementById('total-deuda');
        const acordeon = document.getElementById('acordeonVencidos');

        try {
            const datos = await cargarVencidos();

            // ocultar spinner
            if (spinner) spinner.remove();

            if (!Array.isArray(datos) || datos.length === 0) {
                mensajeVacio.classList.remove('d-none');
                //acordeón en móvil vacío
                acordeon.innerHTML = '<div class="text-center py-3">No hay datos para mostrar.</div>';
                totalDeudaEl.textContent = formatMoneda(0);
                return;
            }

            // calcular total deuda
            const totalDeuda = datos.reduce((s, r) => s + (parseFloat(r.deuda_vencida) || 0), 0);
            totalDeudaEl.textContent = formatMoneda(totalDeuda);

            /* console.log(datos); */

            const tabla = new Tabulator("#tabla-vencidos-tabulator", {
                data: datos,
                layout: "fitColumns",
                pagination: "local",
                paginationSize: 15,
                paginationSizeSelector: [10, 15, 25, 50],
                movableRows: false,
                reactiveData: false,
                columns: [
                    { title: "#", formatter: "rownum", width: 50 },
                    { title: "Cliente", field: "cliente", widthGrow: 8, tooltip: true },
                    { title: "Telefono", field: "telefono", widthGrow: 3, tooltip: true },
                    { title: "N° Doc", field: "documento", widthGrow: 3, tooltip: true },
                    { title: "Vehículo", field: "vehiculo", widthGrow: 6, tooltip: true },
                    { title: "Tienda", field: "tienda", widthGrow: 3, tooltip: true },
                    { title: "Cuotas T.", field: "cuotas_totales", widthGrow: 3, hozAlign: "center" },
                    { title: "Monto Cuota", field: "monto_primera_vencida", widthGrow: 3, hozAlign: "right", formatter: function (cell) { return formatMoneda(cell.getValue()); } },
                    { title: "Deuda", field: "deuda_vencida", widthGrow: 3, hozAlign: "right", formatter: function (cell) { const v = parseFloat(cell.getValue()) || 0; return `<span class="${v > 0 ? 'text-danger fw-bold' : ''}">${formatMoneda(v)}</span>`; } },
                    { title: "Estado de pagos", field: "estado_pagos", widthGrow: 4, tooltip: true },
                    { title: "Cuota P.", field: "cuotas_pagadas", widthGrow: 3, hozAlign: "center" },
                    {
                        title: "Reporte",
                        hozAlign: "center",
                        headerSort: false,
                        widthGrow: 2,
                        formatter: function (cell) {
                            const data = cell.getRow().getData();
                            return `
                                <button class="btn btn-sm btn-danger btn-pdf-atrasado" data-contrato="${data.idcontrato}" title="Notificar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-pdf-recojo ms-1" data-contrato="${data.idcontrato}" title="Recojo PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            `;
                        }
                    }
                ],

                locale: true,
                langs: {
                    "es-es": {
                        "pagination": {
                            "first": "<<",
                            "last": ">>",
                            "prev": "<",
                            "next": ">",
                        }
                    }
                },
            });

            // busqueda global
            const searchInput = document.getElementById("busqueda-global");
            if (searchInput) {
                searchInput.addEventListener("keyup", function (e) {
                    const value = e.target.value.trim();
                    if (value === "") {
                        tabla.clearFilter();
                        return;
                    }
                    tabla.setFilter([
                        [
                            { field: "cliente", type: "like", value: value },
                            /* { field: "ndocumento", type: "like", value: value }, */
                            { field: "telefono", type: "like", value: value },
                            { field: "vehiculo", type: "like", value: value },
                            { field: "tienda", type: "like", value: value }
                        ]
                    ]);
                });
            }

            // Delegación de eventos para botones dentro de Tabulator (Reportes)
            contTabla.addEventListener('click', function (e) {
                const atrasadoBtn = e.target.closest('.btn-pdf-atrasado');
                const recojoBtn = e.target.closest('.btn-pdf-recojo');

                if (atrasadoBtn) {
                    const contrato = atrasadoBtn.dataset.contrato;
                    window.open('/reportesAtrasado?contrato=' + encodeURIComponent(contrato), '_blank');
                }

                if (recojoBtn) {
                    const contrato = recojoBtn.dataset.contrato;
                    window.open('/reportesRecojo?contrato=' + encodeURIComponent(contrato), '_blank');
                }
            });

            //Construir acordeón para móvil
            const gruposHtml = [];
            let contador = 1;
            datos.forEach(row => {
                const id = `vencido-${row.idcontrato}`;
                const item = `
                    <div class="accordion-item mb-2 shadow-sm">
                        <h2 class="accordion-header" id="heading-${id}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${id}" aria-expanded="false" aria-controls="collapse-${id}">
                                <span><i class="bi bi-person-circle me-2 text-primary"></i> ${escapeHtml(row.cliente)}</span>
                            </button>
                        </h2>
                        <div id="collapse-${id}" class="accordion-collapse collapse" aria-labelledby="heading-${id}" data-bs-parent="#acordeonVencidos">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>#:</strong> ${contador++}</li>
                                    <li class="list-group-item"><strong>Telefono:</strong> ${escapeHtml(row.telefono)}</li>
                                    <li class="list-group-item"><strong>Vehículo:</strong> ${escapeHtml(row.vehiculo)}</li>
                                    <li class="list-group-item"><strong>Tienda:</strong> <span class="badge bg-primary">${escapeHtml(row.tienda)}</span></li>
                                    <li class="list-group-item"><strong>Cuotas T.:</strong> ${escapeHtml(row.cuotas_totales)}</li>
                                    <li class="list-group-item"><strong>Monto Cuota:</strong> ${formatMoneda(row.monto_primera_vencida)}</li>
                                    <li class="list-group-item"><strong>Deuda:</strong> <span class="${(parseFloat(row.deuda_vencida) || 0) > 0 ? 'text-danger fw-bold' : ''}">${formatMoneda(row.deuda_vencida)}</span></li>
                                    <li class="list-group-item"><strong>Estado pagos:</strong> ${escapeHtml(row.estado_pagos)}</li>
                                    <li class="list-group-item d-flex gap-2">
                                        <button class="btn btn-sm btn-danger w-100 btn-pdf-atrasado" data-contrato="${row.idcontrato}">Notificar PDF</button>
                                        <button class="btn btn-sm btn-danger w-100 btn-pdf-recojo" data-contrato="${row.idcontrato}">Recojo PDF</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
                gruposHtml.push(item);
            });
            acordeon.innerHTML = `<div class="accordion" id="acordeonVencidos">${gruposHtml.join('')}</div>`;

            // Delegación para botones móviles:
            acordeon.addEventListener('click', function (e) {
                const atrasadoBtn = e.target.closest('.btn-pdf-atrasado');
                const recojoBtn = e.target.closest('.btn-pdf-recojo');
                if (atrasadoBtn) {
                    const contrato = atrasadoBtn.dataset.contrato;
                    window.open('/reportesAtrasado?contrato=' + encodeURIComponent(contrato), '_blank');
                }
                if (recojoBtn) {
                    const contrato = recojoBtn.dataset.contrato;
                    window.open('/reportesRecojo?contrato=' + encodeURIComponent(contrato), '_blank');
                }
            });

        } catch (err) {
            console.error('Error al cargar vencidos:', err);
            if (spinner) spinner.remove();
            mensajeVacio.classList.remove('d-none');
            mostrarNotificacion('Error al cargar los contratos vencidos', 'danger');
        }
    });
</script>