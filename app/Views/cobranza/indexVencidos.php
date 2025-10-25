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
                <a href="#" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-arrow-clockwise me-1"></i> Actualizar</a>
                <a href="/Cobranza" class="btn btn-sm btn-outline-primary">Volver</a>
            </div>
        </div>
    </div>

    <div class="card">

        <!-- <div class="card-header d-none d-md-flex justify-content-between align-items-center">
            <div>
                <div class="btn-group">
                    <button class="btn btn-success btn-sm btn-exportar-excel" onclick="exportarVencidosExcel(false)">
                        <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
                    </button>
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Opciones</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault(); exportarVencidosExcel(false)">
                                <i class="bi bi-list-ul me-2"></i>Exportar Todos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault(); exportarVencidosExcel(true)">
                                <i class="bi bi-funnel me-2"></i>Exportar Solo Filtrados
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <span class="dropdown-item-text text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                El filtrado respeta las cuotas vencidas seleccionadas
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div>
                <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="1">
                    <i class="bi bi-funnel me-1"></i>1 vencida (0)
                </button>
                <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="2">
                    <i class="bi bi-funnel me-1"></i>2 vencidas (0)
                </button>
                <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="3">
                    <i class="bi bi-funnel me-1"></i>3 vencidas (0)
                </button>
                <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="4+">
                    <i class="bi bi-funnel me-1"></i>4 a + vencidas (0)
                </button>
                <button class="btn btn-warning btn-sm btn-filtro-tramo active" data-tramo="todos">
                    <i class="bi bi-list-ul me-1"></i>Todos
                </button>
            </div>
        </div> -->
        <div class="card-header d-none d-md-flex justify-content-end">

            <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="1">
                <i class="bi bi-funnel me-1"></i>1 vencida (0)
            </button>
            <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="2">
                <i class="bi bi-funnel me-1"></i>2 vencidas (0)
            </button>
            <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="3">
                <i class="bi bi-funnel me-1"></i>3 vencidas (0)
            </button>
            <button class="btn btn-outline-primary btn-sm me-2 btn-filtro-tramo" data-tramo="4+">
                <i class="bi bi-funnel me-1"></i>4 a + vencidas (0)
            </button>
            <button class="btn btn-warning btn-sm btn-filtro-tramo active" data-tramo="todos">
                <i class="bi bi-list-ul me-1"></i>Todos
            </button>
        </div>

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
    /* function cargarVencidos() {
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
    } */

    let tablaGlobal = null;

    function cargarVencidos() {
        return new Promise((resolve, reject) => {
            fetch('/Cobranza/getVencidos', { cache: 'no-store' })
                .then(async response => {
                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        if (response.ok && data.success) {
                            const fechaDatos = data.fecha_datos || '';
                            if (fechaDatos) {
                                console.log('Datos del:', fechaDatos);
                                mostrarFechaDatos(fechaDatos);
                            }
                            resolve(data.data || []);
                        } else {
                            reject(new Error(data.message || 'Error al cargar datos'));
                        }
                    } catch (err) {
                        console.error('Respuesta no JSON:', text);
                        reject(new Error('Respuesta inválida del servidor'));
                    }
                })
                .catch(error => reject(error));
        });
    }

    function actualizarDatos() {
        const btnActualizar = document.querySelector('.btn-outline-success');
        if (btnActualizar) {
            btnActualizar.disabled = true;
            btnActualizar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Actualizando...';
        }

        fetch('/Cobranza/actualizarVencidos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('Datos actualizados correctamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(data.message || 'Error al actualizar', 'danger');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                mostrarNotificacion('Error al actualizar los datos', 'danger');
            })
            .finally(() => {
                if (btnActualizar) {
                    btnActualizar.disabled = false;
                    btnActualizar.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Actualizar';
                }
            });
    }

    function mostrarFechaDatos(fecha) {
        const alertInfo = document.querySelector('.alert-info .row');
        if (alertInfo) {
            // Crear fecha en zona horaria de Perú (UTC-5)
            const fechaUTC = new Date(fecha + ' UTC');
            const fechaPeru = new Date(fechaUTC.getTime() - (5 * 60 * 60 * 1000));

            // Formatear manualmente
            const dia = String(fechaPeru.getUTCDate()).padStart(2, '0');
            const mes = String(fechaPeru.getUTCMonth() + 1).padStart(2, '0');
            const anio = fechaPeru.getUTCFullYear();
            const horas = String(fechaPeru.getUTCHours()).padStart(2, '0');
            const minutos = String(fechaPeru.getUTCMinutes()).padStart(2, '0');
            const segundos = String(fechaPeru.getUTCSeconds()).padStart(2, '0');

            const fechaFormateada = `${dia}/${mes}/${anio} ${horas}:${minutos}:${segundos}`;

            const badge = document.createElement('span');
            badge.className = 'badge bg-info ms-2';
            badge.textContent = `Datos de: ${fechaFormateada}`;
            alertInfo.querySelector('.col-md-6')?.appendChild(badge);
        }
    }

    function verificarActualizacionAutomatica() {
        fetch('/Cobranza/verificarActualizacion')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.necesita_actualizacion) {
                    console.log('Datos desactualizados, actualizando...');
                    actualizarDatos();
                }
            })
            .catch(err => console.error('Error verificando actualización:', err));
    }

    function filtrarPorTramo(tramo) {
        if (!tablaGlobal) return;

        // Remover clase active de todos los botones
        document.querySelectorAll('.btn-filtro-tramo').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-outline-primary');
        });

        // Agregar clase active al botón clickeado
        const btnActivo = document.querySelector(`[data-tramo="${tramo}"]`);
        if (btnActivo) {
            btnActivo.classList.add('active');
            btnActivo.classList.remove('btn-outline-primary');
            btnActivo.classList.add('btn-warning');
        }

        // Aplicar filtro
        if (tramo === 'todos') {
            tablaGlobal.clearFilter();
        } else if (tramo === '1') {
            tablaGlobal.setFilter("cuotas_vencidas", "=", 1);
        } else if (tramo === '2') {
            tablaGlobal.setFilter("cuotas_vencidas", "=", 2);
        } else if (tramo === '3') {
            tablaGlobal.setFilter("cuotas_vencidas", "=", 3);
        } else if (tramo === '4+') {
            tablaGlobal.setFilter("cuotas_vencidas", ">=", 4);
        }

        // Actualizar contadores
        actualizarContadores();
    }

    function actualizarContadores() {
        if (!tablaGlobal) return;

        const datosCompletos = tablaGlobal.getData();

        const count1 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 1).length;
        const count2 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 2).length;
        const count3 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 3).length;
        const count4 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) >= 4).length;

        // Validar que los botones existan antes de actualizar
        const btn1 = document.querySelector('[data-tramo="1"]');
        const btn2 = document.querySelector('[data-tramo="2"]');
        const btn3 = document.querySelector('[data-tramo="3"]');
        const btn4 = document.querySelector('[data-tramo="4+"]');

        if (btn1) btn1.innerHTML = `<i class="bi bi-funnel me-1"></i>1 vencida (${count1})`;
        if (btn2) btn2.innerHTML = `<i class="bi bi-funnel me-1"></i>2 vencidas (${count2})`;
        if (btn3) btn3.innerHTML = `<i class="bi bi-funnel me-1"></i>3 vencidas (${count3})`;
        if (btn4) btn4.innerHTML = `<i class="bi bi-funnel me-1"></i>4 a + vencidas (${count4})`;
    }

    /* function actualizarContadores() {
        if (!tablaGlobal) return;

        const datosCompletos = tablaGlobal.getData();

        const count1 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 1).length;
        const count2 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 2).length;
        const count3 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) === 3).length;
        const count4 = datosCompletos.filter(r => parseInt(r.cuotas_vencidas) >= 4).length;

        document.querySelector('[data-tramo="1"]').innerHTML = 
            `<i class="bi bi-funnel me-1"></i>1 vencida (${count1})`;
        document.querySelector('[data-tramo="2"]').innerHTML = 
            `<i class="bi bi-funnel me-1"></i>2 vencidas (${count2})`;
        document.querySelector('[data-tramo="3"]').innerHTML = 
            `<i class="bi bi-funnel me-1"></i>3 vencidas (${count3})`;
        document.querySelector('[data-tramo="4+"]').innerHTML = 
            `<i class="bi bi-funnel me-1"></i>4 a + vencidas (${count4})`;
    } */

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

        verificarActualizacionAutomatica();

        // Event listener para botón actualizar
        const btnActualizar = document.querySelector('.btn-outline-success');
        if (btnActualizar) {
            btnActualizar.addEventListener('click', (e) => {
                e.preventDefault();
                actualizarDatos();
            });
        }

        document.querySelectorAll('.btn-filtro-tramo').forEach(btn => {
            btn.addEventListener('click', function () {
                const tramo = this.dataset.tramo;
                filtrarPorTramo(tramo);
            });
        });

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
                /* acordeon.innerHTML = '<div class="text-center py-3">No hay datos para mostrar.</div>'; */
                totalDeudaEl.textContent = formatMoneda(0);
                return;
            }

            // calcular total deuda
            const totalDeuda = datos.reduce((s, r) => s + (parseFloat(r.deuda_vencida) || 0), 0);
            totalDeudaEl.textContent = formatMoneda(totalDeuda);

            /* console.log(datos); */

            tablaGlobal = new Tabulator("#tabla-vencidos-tabulator", {
                data: datos,
                layout: "fitDataFill",
                pagination: "local",
                paginationSize: 15,
                paginationSizeSelector: [10, 15, 25, 50],
                movableRows: false,
                reactiveData: false,
                /* dataLoaded: function () {
                    actualizarContadores(); // ← Se ejecuta DESPUÉS de cargar los datos
                }, */
                columns: [
                    { title: "#", formatter: "rownum", width: 45, hozAlign: "center" },
                    { title: "Cliente", field: "cliente", width: 280, tooltip: true },
                    { title: "Direccion", field: "ubicacion_cliente", width: 360, tooltip: true },
                    { title: "Telefono", field: "telefono", width: 100, tooltip: true, hozAlign: "center" },
                    { title: "N° Doc", field: "documento", width: 90, tooltip: true, hozAlign: "center" },
                    { title: "Vehículo", field: "vehiculo", width: 300, tooltip: true },
                    { title: "Tienda", field: "tienda", width: 100, tooltip: true, hozAlign: "center" },
                    { title: "Cuotas T.", field: "cuotas_totales", with: 100, hozAlign: "center", tooltip: true },
                    {
                        title: "C. Venc.", field: "cuotas_vencidas", width: 100, hozAlign: "center", tooltip: true,
                        formatter: function (cell) {
                            const val = cell.getValue();
                            let badge = 'badge bg-secondary';
                            if (val >= 4) badge = 'badge bg-danger';
                            else if (val === 3) badge = 'badge bg-warning';
                            else if (val === 2) badge = 'badge bg-info';
                            else if (val === 1) badge = 'badge bg-primary';
                            return `<span class="${badge}">${val}</span>`;
                        }
                    },
                    { title: "Monto Cuota", field: "monto_primera_vencida", width: 140, hozAlign: "right", tooltip: true, formatter: function (cell) { return formatMoneda(cell.getValue()); } },
                    { title: "Deuda", field: "deuda_vencida", width: 120, hozAlign: "right", tooltip: true, formatter: function (cell) { const v = parseFloat(cell.getValue()) || 0; return `<span class="${v > 0 ? 'text-danger fw-bold' : ''}">${formatMoneda(v)}</span>`; } },
                    { title: "Estado de pagos", field: "estado_pagos", width: 150, tooltip: true, hozAlign: "center" },
                    { title: "Cuota P.", field: "cuotas_pagadas", width: 100, hozAlign: "center", tooltip: true },
                    {
                        title: "Reporte",
                        hozAlign: "center",
                        headerSort: false,
                        width: 150,
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

            setTimeout(() => {
                actualizarContadores();
            }, 100);

            const btnTodos = document.querySelector('[data-tramo="todos"]');
            if (btnTodos) {
                btnTodos.classList.add('active');
                btnTodos.classList.remove('btn-primary');
                btnTodos.classList.add('btn-warning');
            }
            /* actualizarContadores(); */

            // busqueda global
            const searchInput = document.getElementById("busqueda-global");
            if (searchInput) {
                searchInput.addEventListener("keyup", function (e) {
                    const value = e.target.value.trim();
                    if (value === "") {
                        tablaGlobal.clearFilter();
                        return;
                    }
                    tablaGlobal.setFilter([
                        [
                            { field: "cliente", type: "like", value: value },
                            /* { field: "ndocumento", type: "like", value: value }, */
                            { field: "telefono", type: "like", value: value },
                            { field: "vehiculo", type: "like", value: value },
                            { field: "tienda", type: "like", value: value },
                            { field: "ubicacion_cliente", type: "like", value: value }
                        ]
                    ]);
                });
            }

            contTabla.addEventListener('click', function (e) {
                const atrasadoBtn = e.target.closest('.btn-pdf-atrasado');
                const recojoBtn = e.target.closest('.btn-pdf-recojo');

                if (atrasadoBtn) {
                    const contrato = atrasadoBtn.dataset.contrato;
                    // CAMBIO: URL limpia sin parámetros query
                    window.open(`/reportesAtrasado/${contrato}`, '_blank');
                }

                if (recojoBtn) {
                    const contrato = recojoBtn.dataset.contrato;
                    // CAMBIO: URL limpia sin parámetros query
                    window.open(`/reportesRecojo/${contrato}`, '_blank');
                }
            });
            /* // Delegación de eventos para botones dentro de Tabulator (Reportes)
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
            }); */

            //Construir acordeón para móvil
            const gruposHtml = [];
            let contador = 1;
            datos.forEach(row => {
                const id = `vencido-${row.idcontrato}`;

                // Badge color según cuotas vencidas
                let badgeColor = 'secondary';
                const cv = parseInt(row.cuotas_vencidas);
                if (cv >= 4) badgeColor = 'danger';
                else if (cv === 3) badgeColor = 'warning';
                else if (cv === 2) badgeColor = 'info';
                else if (cv === 1) badgeColor = 'primary';

                const item = `
                    <div class="accordion-item mb-2 shadow-sm">
                        <h2 class="accordion-header" id="heading-${id}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${id}" aria-expanded="false" aria-controls="collapse-${id}">
                                <span><i class="bi bi-person-circle me-2 text-primary"></i> ${escapeHtml(row.cliente)} 
                                <span class="badge bg-${badgeColor} ms-2">${cv} venc.</span></span>
                            </button>
                        </h2>
                        <div id="collapse-${id}" class="accordion-collapse collapse" aria-labelledby="heading-${id}" data-bs-parent="#acordeonVencidos">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>#:</strong> ${contador++}</li>
                                    <li class="list-group-item"><strong>Telefono:</strong> ${escapeHtml(row.telefono)}</li>
                                    <li class="list-group-item"><strong>N° Documento:</strong> ${escapeHtml(row.documento)}</li>
                                    <li class="list-group-item"><strong>Vehículo:</strong> ${escapeHtml(row.vehiculo)}</li>
                                    <li class="list-group-item"><strong>Tienda:</strong> <span class="badge bg-primary">${escapeHtml(row.tienda)}</span></li>
                                    <li class="list-group-item"><strong>Cuotas Totales:</strong> ${escapeHtml(row.cuotas_totales)}</li>
                                    <li class="list-group-item"><strong>Cuotas Vencidas:</strong> <span class="badge bg-${badgeColor}">${cv}</span></li>
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

            acordeon.addEventListener('click', function (e) {
                const atrasadoBtn = e.target.closest('.btn-pdf-atrasado');
                const recojoBtn = e.target.closest('.btn-pdf-recojo');

                if (atrasadoBtn) {
                    const contrato = atrasadoBtn.dataset.contrato;
                    // CAMBIO: URL limpia sin parámetros query
                    window.open(`/reportesAtrasado/${contrato}`, '_blank');
                }

                if (recojoBtn) {
                    const contrato = recojoBtn.dataset.contrato;
                    // CAMBIO: URL limpia sin parámetros query
                    window.open(`/reportesRecojo/${contrato}`, '_blank');
                }
            });

            /* // Delegación para botones móviles:
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
            }); */

        } catch (err) {
            console.error('Error al cargar vencidos:', err);
            if (spinner) spinner.remove();
            mensajeVacio.classList.remove('d-none');
            mostrarNotificacion('Error al cargar los contratos vencidos', 'danger');
        }
    });
</script>