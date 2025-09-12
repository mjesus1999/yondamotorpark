<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">Reporte Financiero por Concesionario</h5>
        </div>
        <div class="card-body">
            <form id="form-reporte" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label for="input-concesionario" class="form-label">Selecciona un Concesionario:</label>
                        <input class="form-control" list="listaConcesionarios" id="input-concesionario" placeholder="Escribe para buscar..." required>
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

            <div id="reporte-contenido" style="display: none;">
                <div class="row text-center mb-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 shadow-sm">
                            <h6 class="text-muted mb-1">Total OCs</h6>
                            <h4 id="total-ocs-val" class="text-primary">--</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 shadow-sm">
                            <h6 class="text-muted mb-1">Deuda Total</h6>
                            <h4 id="deuda-global-val" class="text-secondary">--</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 shadow-sm">
                            <h6 class="text-muted mb-1">Total Pagado</h6>
                            <h4 id="total-pagado-val" class="text-success">--</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded-3 shadow-sm">
                            <h6 class="text-muted mb-1">Saldo Pendiente</h6>
                            <h4 id="saldo-pendiente-val" class="text-danger">--</h4>
                        </div>
                    </div>
                </div>

                <div class="accordion" id="accordionOCs">
                </div>

                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-danger btn-lg" id="btn-generar-pdf">
                        <i class="bi bi-file-earmark-pdf"></i> Generar Reporte PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.71/vfs_fonts.js"></script>

<script>
    // Datos estáticos que simulan la respuesta de la API
    const concesionariosData = {
        'KIA': {
            id: 1,
            nombre: 'KIA',
            resumenEjecutivo: {
                totalOCs: 2,
                deudaGlobal: 184740.00,
                totalPagosGlobal: 157789.47,
                saldoPendienteGlobal: 26950.53,
            },
            detallesOrdenes: [{
                id: 'OC-001-KIA',
                totalOC: 75000.00,
                pagado: 50000.00,
                saldo: 25000.00,
                avancePorcentaje: 66.67,
                totalVehiculos: 15,
                vehiculos: [{
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, ],
                pagos: [{
                    numero: 1,
                    fecha: '10/05/2025',
                    entidad: 'BBVA',
                    transaccion: '54321',
                    moneda: 'USD',
                    monto: 50000.00,
                    restante: 25000.00,
                    tipoCambio: null,
                    valorDolares: null,
                    observacion: 'Primer pago de la OC.'
                }]
            }, {
                id: '#2025-00058',
                totalOC: 109740.00,
                pagado: 107789.47,
                saldo: 1950.53,
                avancePorcentaje: 98.22,
                totalVehiculos: 3,
                vehiculos: [{
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'KIA',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, ],
                pagos: [{
                    numero: 1,
                    fecha: '12/09/2025 09:00',
                    entidad: 'BBVA',
                    transaccion: '457988588585',
                    moneda: 'USD',
                    monto: 45000.00,
                    restante: 64740.00,
                    tipoCambio: null,
                    valorDolares: null,
                    observacion: 'Pago realizado en dólares.'
                }, {
                    numero: 2,
                    fecha: '11/09/2025 09:02',
                    entidad: 'Interbank',
                    transaccion: '46464646464',
                    moneda: 'PEN',
                    monto: 50000.00,
                    restante: 51582.11,
                    tipoCambio: 3.80,
                    valorDolares: 13157.89,
                    observacion: 'Se realizó el pago en soles, un monto de S/50,000.'
                }, {
                    numero: 3,
                    fecha: '12/09/2025 12:46',
                    entidad: 'BCP',
                    transaccion: '737381919288',
                    moneda: 'PEN',
                    monto: 70000.00,
                    restante: 33161.05,
                    tipoCambio: 3.80,
                    valorDolares: 18421.05,
                    observacion: 'Pago de 70,000 soles'
                }, {
                    numero: 4,
                    fecha: '12/09/2025 03:08',
                    entidad: 'BBVA',
                    transaccion: '874646464',
                    moneda: 'USD',
                    monto: 10000.00,
                    restante: 23161.05,
                    tipoCambio: null,
                    valorDolares: null,
                    observacion: 'Pago en dólares'
                }, {
                    numero: 5,
                    fecha: '12/09/2025 13:17',
                    entidad: 'Interbank',
                    transaccion: '894646464646',
                    moneda: 'USD',
                    monto: 12000.00,
                    restante: 11161.05,
                    tipoCambio: null,
                    valorDolares: null,
                    observacion: ''
                }, {
                    numero: 6,
                    fecha: '12/09/2025 13:18',
                    entidad: 'Banco Falabella',
                    transaccion: '4646464646',
                    moneda: 'PEN',
                    monto: 30000.00,
                    restante: 3266.32,
                    tipoCambio: 3.80,
                    valorDolares: 7894.74,
                    observacion: 'Se pago en soles, se convirtió a dólares con el tipo de cambio 3.80'
                }, {
                    numero: 7,
                    fecha: '12/09/2025 16:14',
                    entidad: 'Interbank',
                    transaccion: '646464646464',
                    moneda: 'PEN',
                    monto: 5000.00,
                    restante: 1950.53,
                    tipoCambio: 3.80,
                    valorDolares: 1315.79,
                    observacion: 'SE pago en soles.'
                }]
            }]
        },
        'HYUNDAI': {
            id: 2,
            nombre: 'HYUNDAI',
            resumenEjecutivo: {
                totalOCs: 1,
                deudaGlobal: 50000.00,
                totalPagosGlobal: 0,
                saldoPendienteGlobal: 50000.00,
            },
            detallesOrdenes: [{
                id: 'OC-001-HYUNDAI',
                totalOC: 50000.00,
                pagado: 0,
                saldo: 50000.00,
                avancePorcentaje: 0,
                totalVehiculos: 10,
                vehiculos: [{
                    marca: 'HYUNDAI',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'HYUNDAI',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'HYUNDAI',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'HYUNDAI',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'HYUNDAI',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, {
                    marca: 'HYUNDAI',
                    modelo: 'Sportage',
                    caracteristicas: 'SUV/Full',
                    chasis: 'YFGJJFDDS',
                    placa: 'JFD-FDFDD',
                    placarotativa: '',
                    seriemotor: 'YFHJ-FDD',
                    anio: 2024,
                    estado: 'Nuevo'
                }, ],
                pagos: []
            }]
        }
    };

    // Función para formatear números
    function formatNumber(num) {
        if (!num || isNaN(num)) {
            return '0.00';
        }
        return Number(num).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Carga las opciones del datalist
    function cargarConcesionarios() {
        const listaConcesionarios = document.getElementById('listaConcesionarios');
        for (const nombre in concesionariosData) {
            const option = document.createElement('option');
            option.value = nombre;
            listaConcesionarios.appendChild(option);
        }
    }

    // Rellena la tabla de pagos para una OC específica
    function fillPagosTable(pagos, targetId) {
        const tableBody = document.getElementById(targetId);
        tableBody.innerHTML = '';
        if (pagos.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No se han registrado pagos para esta OC.</td></tr>`;
            return;
        }
        pagos.forEach(pago => {
            const row = `
                <tr>
                    <td>${pago.numero}</td>
                    <td>${pago.fecha}</td>
                    <td>${pago.entidad}</td>
                    <td>${pago.transaccion}</td>
                    <td>${pago.moneda}</td>
                    <td>${pago.moneda === 'USD' ? '$' : 'S/'}${formatNumber(pago.monto)}</td>
                    <td>${pago.moneda === 'USD' ? '$' : 'S/'}${formatNumber(pago.restante)}</td>
                    <td>${pago.tipoCambio ? pago.tipoCambio.toFixed(2) : '-'}</td>
                    <td>${pago.valorDolares ? '$' + formatNumber(pago.valorDolares) : '-'}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    // Rellena la tabla de vehículos para una OC específica
    function fillVehiculosTable(vehiculos, targetId) {
        const tableBody = document.getElementById(targetId);
        tableBody.innerHTML = '';
        if (vehiculos.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">No hay vehículos asociados a esta OC.</td></tr>`;
            return;
        }
        vehiculos.forEach(vehiculo => {
            const row = `
                <tr>
                    <td>${vehiculo.marca} / ${vehiculo.modelo}</td>
                    <td>${vehiculo.caracteristicas}</td>
                    <td>${vehiculo.chasis}</td>
                    <td>${vehiculo.placa}</td>
                    <td>${vehiculo.placarotativa}</td>
                    <td>${vehiculo.seriemotor}</td>
                    <td>${vehiculo.anio}</td>
                    <td>${vehiculo.estado}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }

    // Maneja el evento de envío del formulario
    document.getElementById('form-reporte').addEventListener('submit', (e) => {
        e.preventDefault();
        const inputConcesionario = document.getElementById('input-concesionario').value;
        const dataConcesionario = concesionariosData[inputConcesionario];

        if (!dataConcesionario) {
            alert("Concesionario no encontrado. Por favor, selecciona uno de la lista.");
            document.getElementById('reporte-contenido').style.display = 'none';
            return;
        }

        // Rellenar el resumen ejecutivo
        const resumen = dataConcesionario.resumenEjecutivo;
        document.getElementById('total-ocs-val').textContent = resumen.totalOCs;
        document.getElementById('deuda-global-val').textContent = `$${formatNumber(resumen.deudaGlobal)}`;
        document.getElementById('total-pagado-val').textContent = `$${formatNumber(resumen.totalPagosGlobal)}`;
        document.getElementById('saldo-pendiente-val').textContent = `$${formatNumber(resumen.saldoPendienteGlobal)}`;

        // Rellenar el acordeón con los detalles de cada OC
        const accordionOCs = document.getElementById('accordionOCs');
        accordionOCs.innerHTML = '';
        dataConcesionario.detallesOrdenes.forEach((oc, index) => {
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item';
            accordionItem.innerHTML = `
                <h2 class="accordion-header" id="headingOC${index}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOC${index}" aria-expanded="false" aria-controls="collapseOC${index}">
                        <strong>OC: ${oc.id}</strong> - Total: $${formatNumber(oc.totalOC)} | Pagado: $${formatNumber(oc.pagado)} | Saldo: <span class="text-danger">$${formatNumber(oc.saldo)}</span>
                    </button>
                </h2>
                <div id="collapseOC${index}" class="accordion-collapse collapse" aria-labelledby="headingOC${index}" data-bs-parent="#accordionOCs">
                    <div class="accordion-body">
                        <h6 class="mb-2">Pagos Realizados</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha Pago</th>
                                        <th>Entidad</th>
                                        <th>N° Transacción</th>
                                        <th>Moneda</th>
                                        <th>Monto Pagado</th>
                                        <th>Restante</th>
                                        <th>T. Cambio</th>
                                        <th>Valor Dólares</th>
                                    </tr>
                                </thead>
                                <tbody id="pagos-oc-${index}"></tbody>
                            </table>
                        </div>
                        <h6 class="mt-4 mb-2">Vehículos de la OC</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Marca / Modelo</th>
                                        <th>Características</th>
                                        <th>Chasis</th>
                                        <th>Placa</th>
                                        <th>Placa Rot.</th>
                                        <th>Serie Motor</th>
                                        <th>Año</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="vehiculos-oc-${index}"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            accordionOCs.appendChild(accordionItem);

            fillPagosTable(oc.pagos, `pagos-oc-${index}`);
            fillVehiculosTable(oc.vehiculos, `vehiculos-oc-${index}`);
        });

        document.getElementById('reporte-contenido').style.display = 'block';
    });

    // Maneja el botón de generar PDF
    document.getElementById('btn-generar-pdf').addEventListener('click', () => {
        const inputConcesionario = document.getElementById('input-concesionario').value;
        const dataConcesionario = concesionariosData[inputConcesionario];
        if (!dataConcesionario) {
            alert("Selecciona un concesionario para generar el PDF.");
            return;
        }
        const documento = generarReportePDF(dataConcesionario);
        pdfMake.createPdf(documento).open();
    });

    // Lógica para generar el PDF
    function generarReportePDF(data) {
        const {
            nombre,
            resumenEjecutivo,
            detallesOrdenes
        } = data;

        const content = [{
            text: `Reporte Financiero: ${nombre}`,
            style: 'header',
            alignment: 'center',
            margin: [0, 0, 0, 10]
        }, {
            columns: [{
                text: `Total de OCs: ${resumenEjecutivo.totalOCs}`,
                style: 'subheader'
            }, {
                text: `Deuda Global: $${formatNumber(resumenEjecutivo.deudaGlobal)}`,
                style: 'subheader'
            }, {
                text: `Total Pagado: $${formatNumber(resumenEjecutivo.totalPagosGlobal)}`,
                style: 'subheader'
            }, {
                text: `Saldo Pendiente: $${formatNumber(resumenEjecutivo.saldoPendienteGlobal)}`,
                style: 'subheader'
            }],
            margin: [0, 0, 0, 20]
        }];

        detallesOrdenes.forEach(oc => {
            content.push({
                text: `Detalle de OC: ${oc.id}`,
                style: 'sectionHeader',
                margin: [0, 10, 0, 5]
            });
            content.push({
                table: {
                    widths: ['auto', 'auto', 'auto', 'auto'],
                    body: [
                        [{
                            text: 'Total OC',
                            bold: true
                        }, {
                            text: 'Pagado',
                            bold: true
                        }, {
                            text: 'Saldo',
                            bold: true
                        }, {
                            text: 'Cantidad Vehículos',
                            bold: true
                        }],
                        [
                            `$${formatNumber(oc.totalOC)}`,
                            `$${formatNumber(oc.pagado)}`,
                            `$${formatNumber(oc.saldo)}`,
                            oc.totalVehiculos
                        ]
                    ]
                },
                layout: 'lightHorizontalLines',
                margin: [0, 0, 0, 10]
            });

            // Tabla de Pagos
            content.push({
                text: 'Pagos de la OC',
                style: 'itemHeader',
                margin: [0, 5, 0, 3]
            });
            content.push({
                table: {
                    widths: ['auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto'],
                    headerRows: 1,
                    body: [
                        [{
                            text: '#',
                            bold: true
                        }, {
                            text: 'Fecha Pago',
                            bold: true
                        }, {
                            text: 'Entidad',
                            bold: true
                        }, {
                            text: 'N° Transacción',
                            bold: true
                        }, {
                            text: 'Moneda',
                            bold: true
                        }, {
                            text: 'Monto Pagado',
                            bold: true
                        }, {
                            text: 'Restante',
                            bold: true
                        }, {
                            text: 'T. Cambio',
                            bold: true
                        }, {
                            text: 'Valor Dólares',
                            bold: true
                        }],
                        ...oc.pagos.map(p => [
                            p.numero,
                            p.fecha,
                            p.entidad,
                            p.transaccion,
                            p.moneda,
                            `${p.moneda === 'USD' ? '$' : 'S/'}${formatNumber(p.monto)}`,
                            `${p.moneda === 'USD' ? '$' : 'S/'}${formatNumber(p.restante)}`,
                            p.tipoCambio ? p.tipoCambio.toFixed(2) : '-',
                            p.valorDolares ? `$${formatNumber(p.valorDolares)}` : '-'
                        ])
                    ]
                },
                layout: 'lightHorizontalLines',
                margin: [0, 0, 0, 10]
            });

            // Tabla de Vehículos - Se agregaron las columnas faltantes
            content.push({
                text: 'Vehículos de la OC',
                style: 'itemHeader',
                margin: [0, 5, 0, 3]
            });
            content.push({
                table: {
                    widths: ['*', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto', 'auto'],
                    headerRows: 1,
                    body: [
                        [{
                            text: 'Marca / Modelo',
                            bold: true
                        }, {
                            text: 'Características',
                            bold: true
                        }, {
                            text: 'Chasis',
                            bold: true
                        }, {
                            text: 'Placa',
                            bold: true
                        }, {
                            text: 'Placa Rot.',
                            bold: true
                        }, {
                            text: 'Serie Motor',
                            bold: true
                        }, {
                            text: 'Año',
                            bold: true
                        }, {
                            text: 'Estado',
                            bold: true
                        }],
                        ...oc.vehiculos.map(v => [
                            `${v.marca} / ${v.modelo}`,
                            v.caracteristicas,
                            v.chasis,
                            v.placa,
                            v.placarotativa,
                            v.seriemotor,
                            v.anio,
                            v.estado
                        ])
                    ]
                },
                layout: 'lightHorizontalLines',
                margin: [0, 0, 0, 20]
            });
        });

        return {
            pageSize: 'A4',
            pageOrientation: 'landscape',
            pageMargins: [20, 20, 20, 20],
            defaultStyle: {
                fontSize: 8
            },
            content: content,
            styles: {
                header: {
                    bold: true,
                    fontSize: 16,
                    color: '#333'
                },
                subheader: {
                    bold: true,
                    fontSize: 10,
                    margin: [0, 5, 0, 5]
                },
                sectionHeader: {
                    bold: true,
                    fontSize: 12,
                    decoration: 'underline',
                    color: '#555'
                },
                itemHeader: {
                    bold: true,
                    fontSize: 10,
                    decoration: 'underline',
                    color: '#777'
                }
            }
        };
    }

    // Cargar los datos al inicio
    cargarConcesionarios();
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>