<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    .accordion-button[aria-expanded="true"] {
        background-color: #0d6efd !important;
        color: white !important;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3) !important;
        border-color: #0d6efd !important;
    }

    .accordion-button[aria-expanded="true"]:hover {
        background-color: #0b5ed7 !important;
        border-color: #0b5ed7 !important;
    }

    #totalpagado {
        background-color: chartreuse;
        box-shadow: 5px 5px #8dbfa8ff;
        animation: mover-y-cambiar alternate infinite 3s;
    }

    #totalpagado:hover {
        cursor: pointer;
        transition-duration: 0.5s;
        background: #68d424ff;
        scale: 1.08;
    }

    #total-oc {
        background-color: #3cbddaff;
        box-shadow: 5px 5px #71d0e6ff;
        animation: mover-y-cambiar alternate infinite 3s;
    }

    #total-oc:hover {
        transition-duration: 0.5s;
        cursor: pointer;
        background-color: #11c0e8e9;
        scale: 1.08;

    }

    #deuda-total {
        background-color: #fdc51eff;
        box-shadow: 5px 5px #ffd864ff;
        animation: mover-y-cambiar alternate infinite 3s;
    }

    #deuda-total:hover {
        transition: 0.5s;
        scale: 1.08;

        cursor: pointer;
        background-color: #dea700ff;

    }



    #saldo-pendiente {
        background-color: #ff2d2dff;
        box-shadow: 5px 5px #e9afafff;
        animation: mover-y-cambiar alternate infinite 3s;

    }

    #saldo-pendiente:hover {
        transition: 0.5s;
        background-color: #bf0202ff;
        scale: 1.08;
        cursor: pointer;

    }


    @keyframes mover-y-cambiar {
        0% {
            transform: translateX(0);
        }

        50% {
            transform: translateX(15px);
        }

        100% {
            transform: translateX(0);
        }
    }

    @media (max-width: 767px) {

        .table-responsive table,
        .table-responsive thead,
        .table-responsive tbody,
        .table-responsive th,
        .table-responsive td,
        .table-responsive tr {
            display: block;
        }

        .table-responsive thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        .table-responsive td {
            border: none;
            position: relative;
            padding-left: 50%;
            text-align: right;
        }

        .table-responsive td:before {
            content: attr(data-label);
            position: absolute;
            left: 6px;
            font-weight: bold;
            text-align: left;
            white-space: nowrap;
        }
    }
</style>


<div class="container-fluid mt-5">



    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">

                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">OC en Proceso</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reporte por Concesionario</li>
                </ol>

            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <a href="/oc/listar/proceso" class="btn btn-sm btn-outline-primary">Mostrar lista</a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">Reporte Financiero por Concesionario</h5>
        </div>
        <div class="card-body">
            <form id="form-reporte" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label for="input-concesionario" class="form-label">Selecciona un Concesionario:</label>
                        <input class="form-control" list="listaConcesionarios" id="input-concesionario" placeholder="Escribe para buscar..." autocomplete="off" required>
                        <datalist id="listaConcesionarios"></datalist>
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0 d-grid">
                        <button type="submit" class="btn btn-primary" id="btn-buscar">
                            <i class="bi bi-search"></i> Generar Reporte
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="card" id="mensaje-inicial">
                <div class="card-body text-center text-muted">
                    <i class="bi bi-search m-1 fs-5"></i> Busca o selecciona un concesionario, las órdenes de compras del concesionario correspondiente se visualizarán aquí.
                </div>
            </div>


            <div id="reporte-contenido" style="display: none;">
                <div class="row text-center mb-4">
                    <div class="col-md-3">
                        <div class="p-3  fw-bold rounded-3" id="total-oc">
                            <h6 class="text-muted mb-1 fw-bold">Total OCs</h6>
                            <h4 id="total-ocs-val" class="text-white">--</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3  rounded-3" id="deuda-total">
                            <h6 class="text-muted mb-1 fw-bold">Deuda Total</h6>
                            <h4 id="deuda-global-val" class="text-white">--</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-3" id="totalpagado">
                            <h6 class="text-muted mb-1 fw-bold">Total Pagado</h6>
                            <h4 id="total-pagado-val" class="text-white">--</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3  rounded-3" id="saldo-pendiente">
                            <h6 class="text-muted mb-1 fw-bold">Saldo Pendiente</h6>
                            <h4 id="saldo-pendiente-val" class="text-white">--</h4>
                        </div>
                    </div>
                </div>

                <div class="accordion" id="accordionOCs">
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-danger btn-sm" id="btn-generar-pdf" disabled>
                            <i class="bi bi-file-earmark-pdf"></i> Generar Reporte PDF
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="btn-generar-excel" disabled>
                            <i class="bi bi-file-excel"></i> Generar Reporte EXCEL
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/vfs_fonts.js"></script>
<script src="/assets/js/logoBase64.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>
<script>
    const API_URL_CONCESIONARIOS = '/api/concesionariosOCProceso';
    const API_URL_REPORTE = '/api/reporteByConcesionario/';

    let concesionariosData = [];
    let reporteData = null;

    function formatNumber(num) {
        if (!num || isNaN(num)) {
            return '0.00';
        }
        return Number(num).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    async function cargarConcesionarios() {
        const listaConcesionarios = document.getElementById('listaConcesionarios');
        try {
            const response = await fetch(API_URL_CONCESIONARIOS);
            const data = await response.json();

            if (response.ok) {
                concesionariosData = data;
                listaConcesionarios.innerHTML = '';
                const nombresUnicos = [...new Set(data.map(item => item.nombrecomercial))];
                nombresUnicos.forEach((nombre) => {
                    const option = document.createElement('option');
                    option.value = nombre;
                    listaConcesionarios.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar los concesionarios:', error);
        }
    }

    /// Función para manejar el comportamiento personalizado del acordeón
    function initializeAccordionFunctionality() {
        const accordionOCs = document.getElementById('accordionOCs');

        // Agregar event listener para manejar clicks en los headers del acordeón
        accordionOCs.addEventListener('click', function(e) {
            // Buscar el botón del acordeón más cercano
            const button = e.target.closest('.accordion-button');
            if (!button) return;

            e.preventDefault(); // Prevenir el comportamiento por defecto

            const targetId = button.getAttribute('data-bs-target');
            const targetCollapse = document.querySelector(targetId);

            if (!targetCollapse) return;

            const isCurrentlyOpen = targetCollapse.classList.contains('show');
            const bsCollapse = bootstrap.Collapse.getInstance(targetCollapse);

            // Cerrar todos los acordeones abiertos y actualizar sus atributos
            const allCollapses = accordionOCs.querySelectorAll('.accordion-collapse');
            allCollapses.forEach(collapse => {
                if (collapse !== targetCollapse) {
                    const instance = bootstrap.Collapse.getInstance(collapse);
                    if (instance && collapse.classList.contains('show')) {
                        instance.hide();
                    }
                    // Encontrar el botón correspondiente y actualizar sus atributos
                    const collapseButton = accordionOCs.querySelector(`[data-bs-target="#${collapse.id}"]`);
                    if (collapseButton) {
                        collapseButton.setAttribute('aria-expanded', 'false');
                        collapseButton.classList.add('collapsed');
                    }
                }
            });

            // Alternar el acordeón clickeado y actualizar el aria-expanded
            if (isCurrentlyOpen) {
                if (bsCollapse) {
                    bsCollapse.hide();
                } else {
                    new bootstrap.Collapse(targetCollapse, {
                        toggle: false
                    }).hide();
                }
                // Actualizar atributos cuando se cierra
                button.setAttribute('aria-expanded', 'false');
                button.classList.add('collapsed');
            } else {
                if (bsCollapse) {
                    bsCollapse.show();
                } else {
                    new bootstrap.Collapse(targetCollapse, {
                        toggle: false
                    }).show();
                }
                // Actualizar atributos cuando se abre
                button.setAttribute('aria-expanded', 'true');
                button.classList.remove('collapsed');
            }
        });
    }


    document.addEventListener('DOMContentLoaded', () => {
        cargarConcesionarios();
    });

    document.getElementById('form-reporte').addEventListener('submit', async (e) => {
        e.preventDefault();

        const inputConcesionario = document.getElementById('input-concesionario').value.trim();
        const btnBuscar = document.getElementById('btn-buscar');
        const btnIcon = btnBuscar.querySelector('i');
        const reporteContenido = document.getElementById('reporte-contenido');
        const mensajeInicial = document.getElementById('mensaje-inicial');
        const btnPDF = document.getElementById('btn-generar-pdf');
        const btnExcel = document.getElementById('btn-generar-excel');

        // Deshabilita el botón y cambia el ícono a un spinner
        btnBuscar.disabled = true;
        btnIcon.className = 'spinner-border spinner-border-sm';
        // Elimina el texto temporalmente para evitar que se duplique
        const originalText = btnBuscar.childNodes[2].textContent;
        btnBuscar.childNodes[2].textContent = ' Cargando...';

        // Ocultar siempre antes de procesar
        reporteContenido.style.display = 'none';
        mensajeInicial.style.display = 'none';
        btnPDF.disabled = true;
        btnExcel.disabled = true;

        const concesionario = concesionariosData.find(c => c.nombrecomercial === inputConcesionario);

        if (!concesionario) {
            mensajeInicial.style.display = 'block';
            showToast("Por favor, selecciona un concesionario válido de la lista.", 'WARNING', 1350);
            btnBuscar.disabled = false;
            btnIcon.className = 'bi bi-search';
            btnBuscar.childNodes[2].textContent = originalText;
            return;
        }

        const concesionarioId = concesionario.idconcesionario;

        try {
            const response = await fetch(`${API_URL_REPORTE}${concesionarioId}`);
            const data = await response.json();

            if (!response.ok || !data) {
                mensajeInicial.style.display = 'block';
                showToast("No se encontraron datos para el concesionario seleccionado.", 'INFO', 1350);
                return;
            }

            reporteData = data;

            const resumen = data.resumenEjecutivo;
            document.getElementById('total-ocs-val').textContent = resumen.totalOCs;
            document.getElementById('deuda-global-val').textContent = `$${formatNumber(resumen.deudatotal)}`;
            document.getElementById('total-pagado-val').textContent = `$${formatNumber(resumen.totalpagado)}`;
            document.getElementById('saldo-pendiente-val').textContent = `$${formatNumber(resumen.saldopendiente)}`;

            const accordionOCs = document.getElementById('accordionOCs');
            accordionOCs.innerHTML = '';

            const ordenesCompra = {};

            // 1. Procesar vehículos primero para asegurar que todas las OCs se creen con su fecha de emisión.
            data.detalleVehiculos.forEach(vehiculo => {
                const id = vehiculo.idordencompra;
                if (!ordenesCompra[id]) {
                    ordenesCompra[id] = {
                        id: vehiculo.OCIdentificador,
                        fechaemision: vehiculo.fechaemision, // La fecha de emisión se toma de los datos del vehículo.
                        pagos: [],
                        vehiculos: []
                    };
                }
                if (!ordenesCompra[id].totalOC) {
                    ordenesCompra[id].totalOC = Number(vehiculo.totalOC);
                }
                ordenesCompra[id].vehiculos.push(vehiculo);
            });

            // 2. Procesar pagos y añadirlos a las OCs ya existentes.
            data.detallePagos.forEach(pago => {
                const id = pago.idordencompra;
                // Asegurarse de que la OC exista antes de añadir el pago
                if (ordenesCompra[id]) {
                    ordenesCompra[id].pagos.push(pago);
                }
            });

            const sortedOCs = Object.values(ordenesCompra).sort((a, b) => b.id.localeCompare(a.id));

            sortedOCs.forEach((oc, index) => {
                const totalOC = oc.totalOC || 0;
                const pagado = oc.pagos.reduce((sum, p) => sum + (Number(p.valordolares) || Number(p.montopagado)), 0);
                const saldo = totalOC - pagado;

                const accordionItem = document.createElement('div');
                accordionItem.className = 'accordion-item';
                accordionItem.innerHTML = `
                <h2 class="accordion-header" id="headingOC${index}">
                    <button class="accordion-button collapsed" type="button" data-bs-target="#collapseOC${index}" aria-expanded="false" aria-controls="collapseOC${index}">
                        <strong>OC: ${oc.id}</strong> - Emisión: ${oc.fechaemision} | Total: $${formatNumber(totalOC)} | Pagado: $${formatNumber(pagado)} | Saldo: <span class="text-danger">$${formatNumber(saldo)}</span>
                    </button>
                </h2>
                <div id="collapseOC${index}" class="accordion-collapse collapse" aria-labelledby="headingOC${index}">
                    <div class="accordion-body">
                        <h6 class="mb-2 fw-bold text-primary">Pagos Realizados</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>ID OC</th>
                                        <th>Fecha Pago</th>
                                        <th>Entidad</th>
                                        <th>N° Transacción</th>
                                        <th>Moneda</th>
                                        <th>Monto Pagado</th>
                                        <th>T. Cambio</th>
                                        <th>Valor Dólares</th>
                                    </tr>
                                </thead>
                                <tbody id="pagos-oc-${index}" style="font-size:13px"></tbody>
                            </table>
                        </div>
                        <h6 class="mt-4 mb-2 fw-bold text-primary">Vehículos de la OC</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Marca / Modelo</th>
                                        <th>Características</th>
                                        <th>Chasis</th>
                                        <th>Placa</th>
                                        <th>P Rotativa</th>
                                        <th>S. Motor</th>
                                        <th>Año</th>
                                        <th>Condición</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody id="vehiculos-oc-${index}" style="font-size:13px"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
                accordionOCs.appendChild(accordionItem);
                fillPagosTable(oc.pagos, `pagos-oc-${index}`);
                fillVehiculosTable(oc.vehiculos, `vehiculos-oc-${index}`);
            });


            initializeAccordionFunctionality();

            reporteContenido.style.display = 'block';
            btnPDF.disabled = false;
            btnExcel.disabled = false;

        } catch (error) {
            console.error('Error al generar el reporte:', error);
            mensajeInicial.style.display = 'block';
            showToast('Ocurrió un error al generar el reporte. Por favor, inténtalo de nuevo más tarde.', 'ERROR', 1350);
        } finally {
            btnBuscar.disabled = false;
            btnIcon.className = 'bi bi-search';
            btnBuscar.childNodes[2].textContent = originalText;
        }
    });





    function fillPagosTable(pagos, targetId) {
        const tableBody = document.getElementById(targetId);
        tableBody.innerHTML = '';
        if (pagos.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No se han registrado pagos para esta OC.</td></tr>`;
            return;
        }
        pagos.forEach(pago => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td data-label="ID OC">${pago.idordencompra}</td>
            <td data-label="Fecha Pago">${pago.fechapago}</td>
            <td data-label="Entidad">${pago.entidad}</td>
            <td data-label="N° Transacción">${pago.numtransaccion}</td>
            <td data-label="Moneda">${pago.moneda}</td>
            <td data-label="Monto Pagado">${pago.moneda === 'USD' ? '$' : 'S/'}${formatNumber(pago.montopagado)}</td>
            <td data-label="T. Cambio">${pago.tipocambio && !isNaN(pago.tipocambio) ? Number(pago.tipocambio).toFixed(2) : '-'}</td>
            <td data-label="Valor Dólares">${pago.valordolares ? '$' + formatNumber(pago.valordolares) : '-'}</td>
        `;
            tableBody.appendChild(row);
        });
    }

    function fillVehiculosTable(vehiculos, targetId) {
        const tableBody = document.getElementById(targetId);
        tableBody.innerHTML = '';
        if (vehiculos.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">No hay vehículos asociados a esta OC.</td></tr>`;
            return;
        }
        vehiculos.forEach(vehiculo => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td data-label="Marca / Modelo">${vehiculo.marcaymodelo}</td>
            <td data-label="Características">${vehiculo.caracteristicas}</td>
            <td data-label="Chasis">${vehiculo.chasis ?? '-'}</td>
            <td data-label="Placa">${vehiculo.placa ?? '-'}</td>
            <td data-label="P Rotativa">${vehiculo.placarotativa ?? '-'}</td>
            <td data-label="S. Motor">${vehiculo.seriemotor ?? '-'}</td>
            <td data-label="Año">${vehiculo.Anio}</td>
            <td data-label="Condición">${vehiculo.condicion}</td>
            <td data-label="Precio">$${formatNumber(vehiculo.preciocompra)}</td>
        `;
            tableBody.appendChild(row);
        });
    }






    document.getElementById('btn-generar-pdf').addEventListener('click', () => {
        if (!reporteData) {
            showToast('Por favor, genera un reporte primero', 'WARNING', 1300);
            return;
        }

        const inputConcesionario = document.getElementById('input-concesionario').value.trim();
        const concesionario = concesionariosData.find(c => c.nombrecomercial === inputConcesionario);

        if (!concesionario) {
            showToast("No se pudo obtener la información del concesionario para generar el PDF.", 'ERROR', 1350);
            return;
        }

        generarPDF(concesionario.nombrecomercial, reporteData);
    });








    function generarPDF(concesionarioNombre, data) {
        const logo = window.logoBase64;
        const resumen = data.resumenEjecutivo;
        const ordenesCompra = {};


        data.detalleVehiculos.forEach(vehiculo => {
            const id = vehiculo.idordencompra;
            if (!ordenesCompra[id]) {
                ordenesCompra[id] = {
                    id: vehiculo.OCIdentificador,
                    totalOC: Number(vehiculo.totalOC),
                    fechaemision: vehiculo.fechaemision,
                    pagos: [],
                    vehiculos: []
                };
            }
            ordenesCompra[id].vehiculos.push(vehiculo);
        });

        // Procesar los pagos y agregarlos a los objetos de OC existentes
        data.detallePagos.forEach(pago => {
            const id = pago.idordencompra;
            if (ordenesCompra[id]) {
                ordenesCompra[id].pagos.push(pago);
            } else {

                ordenesCompra[id] = {
                    id: pago.OCIdentificador,
                    totalOC: 0,
                    fechaemision: 'No disponible',
                    pagos: [pago],
                    vehiculos: []
                };
            }
        });

        // Calcular totales por OC
        Object.values(ordenesCompra).forEach(oc => {
            const totalPagado = oc.pagos.reduce((sum, p) => sum + (Number(p.valordolares) || Number(p.montopagado)), 0);
            oc.totalPagado = totalPagado;
            oc.saldo = oc.totalOC - totalPagado;
        });

        const sortedOCs = Object.values(ordenesCompra).sort((a, b) => b.id.localeCompare(a.id));
        const fechaReporte = new Date().toLocaleDateString('es-PE');

        function formatNumber(num) {
            if (num === null || isNaN(num)) return '0.00';
            return Number(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        const content = [
            // Encabezado Principal
            {
                columns: [{
                        image: logo,
                        width: 80,
                        alignment: 'left',
                        margin: [0, 0, 0, 0],
                    },
                    {
                        stack: [{
                                text: 'YONDA & GRUPO HUARACA E.I.R.L',
                                style: 'companyName'
                            },
                            {
                                text: 'RUC: 20609396866',
                                style: 'companyRuc'
                            },
                        ],
                        alignment: 'right',
                    },
                ],
                margin: [0, 0, 0, 20]
            },

            // Título del Reporte
            {
                text: 'REPORTE FINANCIERO DE CONCESIONARIO',
                style: 'mainTitle',
                margin: [0, 0, 0, 5],
            },
            {
                text: concesionarioNombre.toUpperCase(),
                style: 'dealerName',
                margin: [0, 0, 0, 15]
            },

            //  Resumen Ejecutivo General
            {
                text: 'RESUMEN EJECUTIVO',
                style: 'sectionTitle',
                margin: [0, 0, 0, 5],
            },
            {
                table: {
                    widths: ['*', '*', '*', '*'],
                    body: [
                        [{
                                text: 'Total OCs',
                                style: 'summaryHeader'
                            },
                            {
                                text: 'Deuda Total',
                                style: 'summaryHeader'
                            },
                            {
                                text: 'Total Pagado',
                                style: 'summaryHeader'
                            },
                            {
                                text: 'Saldo Pendiente',
                                style: 'summaryHeader'
                            }
                        ],
                        [{
                            text: resumen.totalOCs,
                            style: 'summaryValue'
                        }, {
                            text: `$${formatNumber(resumen.deudatotal)}`,
                            style: 'summaryValue'
                        }, {
                            text: `$${formatNumber(resumen.totalpagado)}`,
                            style: 'summaryValue',
                            color: '#28a745'
                        }, {
                            text: `$${formatNumber(resumen.saldopendiente)}`,
                            style: 'summaryValue',
                            color: '#dc3545'
                        }]
                    ]
                },
                layout: {
                    hLineWidth: function(i, node) {
                        return 0;
                    },
                    vLineWidth: function(i, node) {
                        return 0;
                    },
                    fillColor: function(i, node) {
                        return (i === 0) ? '#495057' : null;
                    },
                    paddingLeft: function(i, node) {
                        return 10;
                    },
                    paddingRight: function(i, node) {
                        return 10;
                    },
                    paddingTop: function(i, node) {
                        return 5;
                    },
                    paddingBottom: function(i, node) {
                        return 5;
                    }
                },
                margin: [0, 0, 0, 20]
            },
        ];

        // Detalle por Orden de Compra
        sortedOCs.forEach((oc, index) => {
            const contentOC = [
                // El pageBreak solo se aplica a partir de la segunda OC (índice 1)
                ...(index > 0 ? [{
                    text: '',
                    pageBreak: 'before'
                }] : []),
                {
                    // Agregar la fecha de emisión al título de la OC.
                    text: `ORDEN DE COMPRA: ${oc.id} | Emisión: ${oc.fechaemision || 'No disponible'}`,
                    style: 'ocTitle',
                    margin: [0, 10, 0, 5]
                },
                {
                    text: `Total: $ ${formatNumber(oc.totalOC)} | Pagado: $ ${formatNumber(oc.totalPagado)} | Saldo: $ ${formatNumber(oc.saldo)}`,
                    style: 'ocSummary',
                    margin: [0, 0, 0, 10]
                },

                // Detalle de Pagos
                {
                    text: 'DETALLE DE PAGOS',
                    style: 'subSectionTitle'
                },
                {
                    table: {
                        headerRows: 1,
                        widths: ['auto', 'auto', '*', 'auto', 'auto', 'auto', 'auto'],
                        body: [
                            [{
                                    text: 'Fecha Pago',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Entidad',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'N° Transacción',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Moneda',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Monto Pagado',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'T. Cambio',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Valor Dólares',
                                    style: 'tableHeader'
                                }
                            ],
                            ...(oc.pagos.length ? oc.pagos.map(pago => [
                                pago.fechapago || '-',
                                pago.entidad || '-',
                                pago.numtransaccion || '-',
                                pago.moneda || '-', {
                                    text: `${pago.moneda === 'USD' ? '$' : 'S/'}${formatNumber(pago.montopagado)}`,
                                    alignment: 'right'
                                }, {
                                    text: pago.tipocambio && !isNaN(pago.tipocambio) ? Number(pago.tipocambio).toFixed(2) : '-',
                                    alignment: 'right'
                                }, {
                                    text: pago.valordolares ? `$ ${formatNumber(pago.valordolares)}` : '-',
                                    alignment: 'right'
                                }
                            ]) : [
                                [{
                                    text: 'No se han registrado pagos para esta OC.',
                                    colSpan: 7,
                                    alignment: 'center',
                                    italics: true
                                }, {}, {}, {}, {}, {}, {}]
                            ])
                        ]
                    },
                    layout: 'lightHorizontalLines',
                    margin: [0, 5, 0, 20]
                },

                // Detalle de Vehículos
                {
                    text: 'DETALLE DE VEHÍCULOS',
                    style: 'subSectionTitle'
                },
                {
                    table: {
                        headerRows: 1,
                        widths: ['auto', '*', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto'],
                        body: [
                            [{
                                    text: 'Marca / Modelo',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Características',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Chasis',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Placa',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'P. Rotativa',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'S. Motor',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Año',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Condición',
                                    style: 'tableHeader'
                                },
                                {
                                    text: 'Precio',
                                    style: 'tableHeader'
                                }
                            ],
                            ...(oc.vehiculos.length ? oc.vehiculos.map(vehiculo => [
                                vehiculo.marcaymodelo || '-',
                                vehiculo.caracteristicas || '-', {
                                    text: vehiculo.chasis || '-'
                                },
                                vehiculo.placa || '-',
                                vehiculo.placarotativa || '-', {
                                    text: vehiculo.seriemotor || '-'
                                },
                                vehiculo.Anio?.toString() || '-', {
                                    text: (vehiculo.condicion || 'N/A').toUpperCase()
                                }, {
                                    text: `$ ${formatNumber(vehiculo.preciocompra)}`,
                                    alignment: 'right'
                                }
                            ]) : [
                                [{
                                    text: 'No hay vehículos asociados a esta OC.',
                                    colSpan: 9,
                                    alignment: 'center',
                                    italics: true
                                }, {}, {}, {}, {}, {}, {}, {}, {}]
                            ])
                        ]
                    },
                    layout: 'lightHorizontalLines',
                    margin: [0, 5, 0, 20]
                }
            ];
            content.push(...contentOC);
        });

        const docDefinition = {
            pageSize: 'A4',
            pageOrientation: 'landscape',
            pageMargins: [40, 25, 25, 25],
            defaultStyle: {
                fontSize: 7.6
            },
            footer: function(currentPage, pageCount) {
                return {
                    columns: [{
                            text: 'Documento confidencial',
                            style: 'headerFooterText',
                            alignment: 'left'
                        },
                        {
                            text: `Página ${currentPage} de ${pageCount}`,
                            style: 'headerFooterText',
                            alignment: 'right'
                        }
                    ],
                    margin: [40, 10]
                };
            },
            content: content,
            styles: {
                companyName: {
                    bold: true,
                    color: '#2c3e50',
                    alignment: 'right'
                },
                companyRuc: {
                    bold: true,
                    color: '#6c757d',
                    alignment: 'right',
                    margin: [0, 2, 0, 0]
                },
                mainTitle: {
                    bold: true,
                    alignment: 'center',
                    color: '#2c3e50'
                },
                dealerName: {
                    bold: true,
                    alignment: 'center',
                    color: '#007bff'
                },
                sectionTitle: {
                    bold: true,
                    color: '#495057',
                    margin: [0, 10, 0, 5]
                },
                subSectionTitle: {
                    bold: true,
                    color: '#495057',
                    margin: [0, 5, 0, 5]
                },
                ocTitle: {
                    bold: true,
                    color: '#495057'
                },
                ocSummary: {
                    bold: true,
                    alignment: 'center',
                    color: '#6c757d'
                },
                summaryHeader: {
                    bold: true,
                    alignment: 'center',
                    color: 'white',
                    fillColor: '#495057'
                },
                summaryValue: {
                    bold: true,
                    alignment: 'center'
                },
                tableHeader: {
                    bold: true,
                    alignment: 'center',
                    color: 'white',
                    fillColor: '#6c757d'
                },
                headerFooterText: {
                    fontSize: 7,
                    color: '#6c757d'
                },
            }
        };

        pdfMake.createPdf(docDefinition).open();
    }


    // EVENTO PARA GENERAR EL ARCHIVO EXCEL:
    document.getElementById("btn-generar-excel").addEventListener("click", async () => {

        if (!reporteData) {
            alert("Por favor, genera el reporte primero para tener los datos disponibles.");
            return;
        }

        const inputConcesionario = document.getElementById('input-concesionario').value.trim();
        const concesionario = concesionariosData.find(c => c.nombrecomercial === inputConcesionario);

        if (!concesionario) {
            alert("No se pudo obtener la información del concesionario.");
            return;
        }

        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet(`${concesionario.nombrecomercial}`);

        // 2. Definir estilos reutilizables.
        const titleStyle = {
            font: {
                bold: true,
                size: 16
            },
            alignment: {
                horizontal: 'center'
            }
        };
        const subtitleStyle = {
            font: {
                bold: true,
                size: 14
            },
            alignment: {
                horizontal: 'center'
            }
        };
        const ocTitleStyle = {
            font: {
                bold: true,
                size: 14
            },
            alignment: {
                horizontal: 'center'
            },
            fill: {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                    argb: 'FFFFFF00'
                }
            }
        };
        const headerStyle = {
            font: {
                bold: true,
                size: 11
            },
            fill: {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                    argb: 'FFE0E0E0'
                }
            },
            alignment: {
                vertical: 'middle',
                horizontal: 'center'
            },
            border: {
                top: {
                    style: 'thin'
                },
                left: {
                    style: 'thin'
                },
                bottom: {
                    style: 'thin'
                },
                right: {
                    style: 'thin'
                }
            }
        };
        const summaryHeaderStyle = {
            font: {
                bold: true,
                size: 12,
                color: {
                    argb: 'FFFFFFFF'
                }
            },
            fill: {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                    argb: 'FF495057'
                }
            },
            alignment: {
                horizontal: 'center'
            },
            border: {
                top: {
                    style: 'thin'
                },
                left: {
                    style: 'thin'
                },
                bottom: {
                    style: 'thin'
                },
                right: {
                    style: 'thin'
                }
            }
        };
        const summaryValueStyle = {
            font: {
                bold: true,
                size: 12
            },
            alignment: {
                horizontal: 'center'
            },
            border: {
                top: {
                    style: 'thin'
                },
                left: {
                    style: 'thin'
                },
                bottom: {
                    style: 'thin'
                },
                right: {
                    style: 'thin'
                }
            }
        };

        // 3. Agregar el título y la información general.
        worksheet.addRow(['REPORTE FINANCIERO DE CONCESIONARIO']).getCell(1).style = titleStyle;
        worksheet.mergeCells('A1:J1');
        worksheet.addRow([]);
        worksheet.addRow([`Concesionario:`, `${concesionario.nombrecomercial ?? '---'}`]);
        worksheet.addRow([`Fecha del Reporte:`, `${new Date().toLocaleDateString('es-PE')}`]);
        worksheet.addRow([]);

        // 4. Agregar el resumen ejecutivo.
        const resumen = reporteData.resumenEjecutivo;
        console.log('TOTAL OCS: ', resumen.totalOCs);
        worksheet.addRow(['RESUMEN EJECUTIVO']).getCell(1).style = subtitleStyle;
        worksheet.mergeCells('A' + worksheet.rowCount + ':D' + worksheet.rowCount);
        worksheet.addRow([]);

        const summaryHeaders = ['Total OCs', 'Deuda Total', 'Total Pagado', 'Saldo Pendiente'];
        const summaryValues = [
            parseInt(resumen.totalOCs, 10),
            parseFloat(resumen.deudatotal),
            parseFloat(resumen.totalpagado),
            parseFloat(resumen.saldopendiente)
        ];

        const summaryHeaderRow = worksheet.addRow(summaryHeaders);
        summaryHeaderRow.eachCell(cell => cell.style = summaryHeaderStyle);

        const summaryValueRow = worksheet.addRow(summaryValues);

        summaryValueRow.getCell(1).numFmt = '0';
        summaryValueRow.getCell(2).numFmt = '_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)';
        summaryValueRow.getCell(3).numFmt = '_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)';
        summaryValueRow.getCell(4).numFmt = '_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)';

        // 5. Agrupar datos por Orden de Compra para el detalle.
        const ordenesCompra = {};


        reporteData.detalleVehiculos.forEach(vehiculo => {
            const id = vehiculo.idordencompra;
            if (!ordenesCompra[id]) {
                ordenesCompra[id] = {
                    id: vehiculo.OCIdentificador,
                    fechaemision: vehiculo.fechaemision,
                    totalOC: Number(vehiculo.totalOC),
                    pagos: [],
                    vehiculos: []
                };
            }
            ordenesCompra[id].vehiculos.push(vehiculo);
        });

        // agregamos los pagos a las OCs ya creadas.
        reporteData.detallePagos.forEach(pago => {
            const id = pago.idordencompra;
            if (ordenesCompra[id]) {
                ordenesCompra[id].pagos.push(pago);
            }
        });

        // 6. Recorrer cada OC y agregar las tablas de pagos y vehículos.
        for (const oc of Object.values(ordenesCompra)) {
            worksheet.addRow([]);
            worksheet.addRow([]);

            // Título de la OC, con total, pagado y saldo.
            const totalPagado = oc.pagos.reduce((sum, pago) => sum + (pago.valordolares ? parseFloat(pago.valordolares) : 0), 0);
            const saldoPendiente = oc.totalOC - totalPagado;
            const ocTitleCell = worksheet.addRow([`DETALLE ORDEN DE COMPRA: ${oc.id} (Emitida: ${oc.fechaemision || '---'}) - Total: $${oc.totalOC.toFixed(2)} | Pagado: $${totalPagado.toFixed(2)} | Saldo: $${saldoPendiente.toFixed(2)}`]).getCell(1);


            ocTitleCell.style = ocTitleStyle;
            worksheet.mergeCells(`A${ocTitleCell.row}:I${ocTitleCell.row}`);

            worksheet.addRow([]);

            // Tabla de Pagos
            if (oc.pagos.length > 0) {
                const headersPagos = ["Fecha Pago", "Entidad", "N° Transacción", "Moneda", "Monto Pagado", "T. Cambio", "Valor Dólares"];
                const headerRowPagos = worksheet.addRow(headersPagos);
                headerRowPagos.eachCell(cell => cell.style = headerStyle);

                oc.pagos.forEach(pago => {
                    const row = worksheet.addRow([
                        pago.fechapago || '-',
                        pago.entidad || '-',
                        pago.numtransaccion || '-',
                        pago.moneda || '-',
                        pago.montopagado ? parseFloat(pago.montopagado) : 0,
                        pago.tipocambio && !isNaN(pago.tipocambio) ? parseFloat(pago.tipocambio) : '-',
                        pago.valordolares ? parseFloat(pago.valordolares) : 0
                    ]);
                    row.eachCell((cell, colNumber) => {
                        cell.border = {
                            top: {
                                style: 'thin'
                            },
                            left: {
                                style: 'thin'
                            },
                            bottom: {
                                style: 'thin'
                            },
                            right: {
                                style: 'thin'
                            }
                        };
                        if (colNumber === 5) {
                            cell.numFmt = pago.moneda === 'PEN' ? '_("S/"* #,##0.00_);_("S/"* (#,##0.00);_("S/"* "-"??_);_(@_)' : '_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)';
                            cell.alignment = {
                                horizontal: 'right'
                            };
                        }
                        if (colNumber === 7) {
                            cell.numFmt = '_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)';
                            cell.alignment = {
                                horizontal: 'right'
                            };
                        }
                    });
                });
            } else {
                worksheet.addRow(['No se han registrado pagos para esta OC.']).eachCell(cell => {
                    cell.font = {
                        italic: true
                    };
                    cell.alignment = {
                        horizontal: 'center'
                    };
                });
            }

            worksheet.addRow([]);

            // Tabla de Vehículos
            const headersVehiculos = ["Marca / Modelo", "Características", "Chasis", "Placa", "P. Rotativa", "S. Motor", "Año", "Estado", "Precio"];
            const headerRowVehiculos = worksheet.addRow(headersVehiculos);
            headerRowVehiculos.eachCell(cell => cell.style = headerStyle);

            oc.vehiculos.forEach(vehiculo => {
                const caracteristicas = vehiculo.caracteristicas ? vehiculo.caracteristicas.split(',') : ['-'];
                const row = worksheet.addRow([
                    vehiculo.marcaymodelo,
                    caracteristicas[0]?.trim() || '-',
                    vehiculo.chasis || '-',
                    vehiculo.placa || '-',
                    vehiculo.placarotativa || '-',
                    vehiculo.seriemotor || '-',
                    vehiculo.Anio || '-',
                    vehiculo.condicion || '-',
                    vehiculo.preciocompra ? parseFloat(vehiculo.preciocompra) : 0
                ]);
                row.eachCell((cell, colNumber) => {
                    cell.border = {
                        top: {
                            style: 'thin'
                        },
                        left: {
                            style: 'thin'
                        },
                        bottom: {
                            style: 'thin'
                        },
                        right: {
                            style: 'thin'
                        }
                    };
                    if (colNumber === 9) {
                        cell.numFmt = '_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)';
                        cell.alignment = {
                            horizontal: 'right'
                        };
                    }
                });
            });
        }

        // 7. Ajustar anchos de columna de forma más controlada.
        worksheet.columns.forEach((column, index) => {
            let maxLength = 0;
            column.eachCell({
                includeEmpty: true
            }, cell => {
                const columnLength = cell.value ? cell.value.toString().length : 10;
                if (columnLength > maxLength) {
                    maxLength = columnLength;
                }
            });
            column.width = Math.min(maxLength + 2, 23);
        });

        // 8. Escribir el archivo y descargarlo.
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Reporte-Financiero-${concesionario.nombrecomercial ?? '---'}(${new Date().toLocaleDateString('es-PE')}).xlsx`;
        a.click();
        window.URL.revokeObjectURL(url);
    });





    cargarConcesionarios();
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>