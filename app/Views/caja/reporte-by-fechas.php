<?php
include __DIR__ . '/../layout/header.php';
?>
<style>
    .card {
        border: none;
        border-radius: 10px;
    }

    .card-header {
        border-top-left-radius: 10px !important;
        border-top-right-radius: 10px !important;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
    }

    #reporte-tabla tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
    }

    .form-control {
        border-radius: 6px;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: ">";
    }
</style>
<div class="container-fluid py-4">


    <div class="alert alert-info mt-2" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reporte por fecha</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="/caja/" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-1"></i> Lista
                </a>
            </div>
        </div>
    </div>


    <!-- Tarjeta de filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">Filtrar Reporte</h6>
        </div>
        <div class="card-body">
            <form id="reporte-fechas-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="fecha-inicio" class="form-label fw-bold">Fecha de Inicio</label>
                        <input type="date" class="form-control form-control-lg" id="fecha-inicio" required>
                    </div>
                    <div class="col-md-5">
                        <label for="fecha-fin" class="form-label fw-bold">Fecha de Fin</label>
                        <input type="date" class="form-control form-control-lg" id="fecha-fin" required>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary btn-md" id="btn-generar">
                            <i class="fas fa-play me-2"></i>Generar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Resultados -->
    <div id="reporte-resultados-container" class="mt-4" style="display: none;">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">Resumen Diario de Pagos</h6>

                    <div>

                        <button type="button" class="btn btn-danger btn-sm" id="btn-pdf">
                            <i class="fas fa-file-pdf me-2"></i>Exportar
                        </button>

                        <button type="button" class="btn btn-success btn-sm" id="btn-excel">
                            <i class="bi bi-file-earmark-excel"></i></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="reporte-tabla">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Efectivo</th>
                                <th class="text-center">Yape</th>
                                <th class="text-center">Plin</th>
                                <th class="text-center">Transferencia</th>
                                <th class="text-center bg-primary">Total Diario</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr class="table-active fw-bold">
                                <td class="text-start">Totales:</td>
                                <td class="text-center" id="total-efectivo">S/ 0.00</td>
                                <td class="text-center" id="total-yape">S/ 0.00</td>
                                <td class="text-center" id="total-plin">S/ 0.00</td>
                                <td class="text-center" id="total-transferencia">S/ 0.00</td>
                                <td class="text-center bg-primary text-white" id="total-global">S/ 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="mensaje-inicial" class="card shadow d-flex justify-content-center align-items-center" style="height: 300px;">
    <div class="text-center text-muted">
        <i class="fas fa-search fs-1 mb-3 text-primary fw-bold"></i>
        <h4>Selecciona un rango de fechas y pulse en "Generar" para ver el reporte.</h4>
        <p>Los datos aparecerán aquí.</p>
    </div>
</div>


<!-- Modal para mensajes -->
<div class="modal fade" id="mensajeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mensajeModalTitulo">Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="mensajeModalCuerpo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const form = document.getElementById('reporte-fechas-form');
        const resultadosContainer = document.getElementById('reporte-resultados-container');
        const tbody = document.querySelector('#reporte-tabla tbody');
        const totalGlobalEl = document.getElementById('total-global');
        const totalEfectivoEl = document.getElementById('total-efectivo');
        const totalYapeEl = document.getElementById('total-yape');
        const totalPlinEl = document.getElementById('total-plin');
        const totalTransferenciaEl = document.getElementById('total-transferencia');
        const btnGenerar = document.getElementById('btn-generar');
        const btnPDF = document.getElementById('btn-pdf');
        const btnExcel = document.querySelector('#btn-excel');
        const mensajeModal = new bootstrap.Modal(document.getElementById('mensajeModal'));
        const mensajeInicial = document.querySelector('#mensaje-inicial');

        const tabla = document.querySelector('#reporte-tabla');

        // Establecer fechas por defecto 
        const hoy = new Date();
        const hace7Dias = new Date();
        hace7Dias.setDate(hoy.getDate() - 7);

        document.getElementById('fecha-inicio').value = hace7Dias.toISOString().split('T')[0];
        document.getElementById('fecha-fin').value = hoy.toISOString().split('T')[0];

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;

            if (!fechaInicio || !fechaFin) {
                mostrarMensaje('Por favor, selecciona una fecha de inicio y una fecha de fin.', 'Información');
                return;
            }

            if (fechaInicio > fechaFin) {
                mostrarMensaje('La fecha de inicio no puede ser mayor a la fecha de fin.', 'Error');
                return;
            }

            btnGenerar.disabled = true;
            btnGenerar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...';
            btnPDF.style.display = 'none';
            resultadosContainer.style.display = 'none';

            // Limpia la tabla y los totales
            tbody.innerHTML = '';
            limpiarTotales();


            try {
                const url = `/api/reporte/by/fecha?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
                const response = await fetch(url);

                if (!response.ok) {
                    const errorData = await response.json();
                    // console.error('Error del servidor:', errorData.message);
                    mostrarMensaje(errorData.message, 'Error');
                    mensajeInicial.classList.remove('d-none');
                    mensajeInicial.classList.add('d-flex');
                    return;
                }

                const data = await response.json();
                mostrarReporteEnTabla(data.data);

            } catch (error) {
                console.error('Error en la solicitud:', error);
                mostrarMensaje('Ocurrió un error al conectar con el servidor.', 'Error');
                mensajeInicial.classList.remove('d-none');
                mensajeInicial.classList.add('d-flex');
            } finally {
                btnGenerar.disabled = false;
                btnGenerar.innerHTML = '<i class="fas fa-play me-2"></i>Generar';
            }
        });

        btnPDF.addEventListener('click', () => {
            generarReportePDF();
        });

        btnExcel.addEventListener('click', () => {
            generarReporteExcel();
        })

        function mostrarReporteEnTabla(datos) {
            let totalGlobal = 0;
            let totalEfectivo = 0;
            let totalYape = 0;
            let totalPlin = 0;
            let totalTransferencia = 0;

            resultadosContainer.style.display = 'block';
            tbody.innerHTML = '';

            if (datos && datos.length > 0) {
                mensajeInicial.classList.remove('d-flex');
                mensajeInicial.classList.add('d-none');
                datos.forEach(row => {
                    const totalDiario = parseFloat(row.total_efectivo) + parseFloat(row.total_yape) + parseFloat(row.total_plin) + parseFloat(row.total_transferencia);
                    totalGlobal += totalDiario;
                    totalEfectivo += parseFloat(row.total_efectivo);
                    totalYape += parseFloat(row.total_yape);
                    totalPlin += parseFloat(row.total_plin);
                    totalTransferencia += parseFloat(row.total_transferencia);

                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                    <td class="text-center">${formatearFecha(row.dia)}</td>
                    <td class="text-end">S/ ${formatearMoneda(row.total_efectivo)}</td>
                    <td class="text-end">S/ ${formatearMoneda(row.total_yape)}</td>
                    <td class="text-end">S/ ${formatearMoneda(row.total_plin)}</td>
                    <td class="text-end">S/ ${formatearMoneda(row.total_transferencia)}</td>
                    <td class="text-end fw-bold">S/ ${formatearMoneda(totalDiario)}</td>
                `;
                    tbody.appendChild(newRow);
                });

                totalGlobalEl.textContent = `S/ ${formatearMoneda(totalGlobal)}`;
                totalEfectivoEl.textContent = `S/ ${formatearMoneda(totalEfectivo)}`;
                totalYapeEl.textContent = `S/ ${formatearMoneda(totalYape)}`;
                totalPlinEl.textContent = `S/ ${formatearMoneda(totalPlin)}`;
                totalTransferenciaEl.textContent = `S/ ${formatearMoneda(totalTransferencia)}`;
                btnPDF.style.display = 'inline-block';

            } else {
                mensajeInicial.classList.add('d-flex');

                // Limpia los totales
                limpiarTotales();
                btnPDF.style.display = 'none';
            }
        }


        // Nueva función para limpiar los totales
        function limpiarTotales() {
            totalGlobalEl.textContent = 'S/ 0.00';
            totalEfectivoEl.textContent = 'S/ 0.00';
            totalYapeEl.textContent = 'S/ 0.00';
            totalPlinEl.textContent = 'S/ 0.00';
            totalTransferenciaEl.textContent = 'S/ 0.00';
        }

        function generarReporteExcel() {

            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;

            let ws = XLSX.utils.table_to_sheet(tabla);
            // Creamos un libro de Excel y añadimos la hoja
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Hoja1");

            // Exportamos el archivo
            XLSX.writeFile(wb, `reporte-pagos(${formatearFecha(fechaInicio)}-${formatearFecha(fechaFin)}).xlsx`);
        }


        function formatearFecha(fecha) {
            const [year, month, day] = fecha.split('-');
            return `${day}/${month}/${year}`;
        }



        // Generar reporte PDF:
        function generarReportePDF() {
            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;

            function formatNumber(num) {
                let cleanNum = String(num).replace(/[^\d.-]/g, '');
                if (!cleanNum || cleanNum === '' || isNaN(cleanNum)) {
                    return '0.00';
                }
                return Number(cleanNum).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            const logo = "data:image/png;base64,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";


            
            function obtenerDatosDeTabla() {
                const tableData = [];
                const tableRows = tbody.querySelectorAll('tr');

                tableRows.forEach((row) => {
                    const cols = row.querySelectorAll('td');
                    if (cols.length > 1) {
                        const rowData = [{
                                text: cols[0].textContent,
                                alignment: 'center',
                                bold: true
                            },
                            {
                                text: `S/ ${formatNumber(cols[1].textContent)}`,
                                alignment: 'right'
                            },
                            {
                                text: `S/ ${formatNumber(cols[2].textContent)}`,
                                alignment: 'right'
                            },
                            {
                                text: `S/ ${formatNumber(cols[3].textContent)}`,
                                alignment: 'right'
                            },
                            {
                                text: `S/ ${formatNumber(cols[4].textContent)}`,
                                alignment: 'right'
                            },
                            {
                                text: `S/ ${formatNumber(cols[5].textContent)}`,
                                alignment: 'right',
                                bold: true,
                                fillColor: '#e6f2ff'
                            }
                        ];
                        tableData.push(rowData);
                    }
                });
                return tableData;
            }

            function obtenerDatosDeTotales() {
                return [
                    [{
                            text: 'Total Global',
                            style: 'tableHeaderTotales'
                        },
                        {
                            text: 'Total Efectivo',
                            style: 'tableHeaderTotales'
                        },
                        {
                            text: 'Total Yape',
                            style: 'tableHeaderTotales'
                        },
                        {
                            text: 'Total Plin',
                            style: 'tableHeaderTotales'
                        },
                        {
                            text: 'Total Transferencia',
                            style: 'tableHeaderTotales'
                        }
                    ],
                    [{
                            text: `S/ ${formatNumber(totalGlobalEl.textContent)}`,
                            alignment: 'center',
                            bold: true,
                            fillColor: '#f2f2f2'
                        },
                        {
                            text: `S/ ${formatNumber(totalEfectivoEl.textContent)}`,
                            alignment: 'center',
                            bold: true,
                            fillColor: '#f2f2f2'
                        },
                        {
                            text: `S/ ${formatNumber(totalYapeEl.textContent)}`,
                            alignment: 'center',
                            bold: true,
                            fillColor: '#f2f2f2'
                        },
                        {
                            text: `S/ ${formatNumber(totalPlinEl.textContent)}`,
                            alignment: 'center',
                            bold: true,
                            fillColor: '#f2f2f2'
                        },
                        {
                            text: `S/ ${formatNumber(totalTransferenciaEl.textContent)}`,
                            alignment: 'center',
                            bold: true,
                            fillColor: '#f2f2f2'
                        }
                    ]
                ];
            }

            const fechaActual = new Date().toLocaleDateString('es-ES');

            // Definición del documento PDF
            const documento = {
                pageSize: 'A4',
                pageOrientation: 'portrait',
                pageMargins: [40, 25, 25, 25], //  izquierdo, superior, derecho, inferior
                defaultStyle: {
                    fontSize: 7.2,
                },
                content: [{
                        columns: [{
                                image: logo,
                                width: 80,
                                alignment: 'left'
                            },
                            {
                                stack: [{
                                        text: 'YONDA & GRUPO HUARACA E.I.R.L',
                                    
                                        bold: true,
                                        color: '#2c3e50',
                                        alignment: 'right'
                                    },
                                    {
                                        text: 'RUC: 20609396866',
                                        
                                        margin: [0, 2, 0, 0],
                                        bold: true,
                                        alignment: 'right'
                                    },
                                ],
                                alignment: 'right'
                            }
                        ],
                        margin: [0, 0, 0, 10]
                    },
                    {
                        text: 'REPORTE DE INGRESOS POR PERIODO',
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 10],
                        decoration: 'underline'
                    },
                    {
                        style: 'summaryTable',
                        table: {
                            widths: ['*', '*'],
                            body: [
                                [{
                                    text: 'PERIODO DE TIEMPO',
                                    style: 'tableHeader'
                                }, {
                                    text: `${formatearFecha(fechaInicio)} - ${formatearFecha(fechaFin)}`,
                                    fillColor: 'yellow',
                                    bold: true,
                                    alignment: 'center'
                                }],
                            ]
                        },
                        layout: 'lightHorizontalLines',
                        margin: [0, 0, 0, 15]
                    },
                    {
                        text: 'RESUMEN DETALLADO',
                        style: 'sectionHeader',
                        margin: [0, 0, 0, 9]
                    },
                    {
                        style: 'tableDiario',
                        table: {
                            headerRows: 1,
                            widths: ['*', '*', '*', '*', '*', '*'],
                            body: [
                                [{
                                        text: 'Fecha',
                                        style: 'tableHeader'
                                    },
                                    {
                                        text: 'Efectivo',
                                        style: 'tableHeader'
                                    },
                                    {
                                        text: 'Yape',
                                        style: 'tableHeader'
                                    },
                                    {
                                        text: 'Plin',
                                        style: 'tableHeader'
                                    },
                                    {
                                        text: 'Transferencia',
                                        style: 'tableHeader'
                                    },
                                    {
                                        text: 'Total Diario',
                                        style: 'tableHeader'
                                    }
                                ],
                                ...obtenerDatosDeTabla()
                            ]
                        },
                        layout: {
                            fillColor: (rowIndex) => (rowIndex === 0) ? '#007bff' : (rowIndex % 2 === 0) ? '#f8f9fa' : null
                        }
                    },
                    {
                        text: 'TOTALES GENERALES',
                        style: 'sectionHeader',
                        margin: [0, 15, 0, 10]
                    },
                    {
                        style: 'tableTotales',
                        table: {
                            widths: ['*', '*', '*', '*', '*'],
                            body: obtenerDatosDeTotales()
                        },
                        layout: 'lightHorizontalLines'
                    }
                ],
                styles: {
                    subheader: {
                        bold: true,
                        
                        color: '#343a40'
                    },
                    sectionHeader: {
                        bold: true,
                        
                        color: '#212529'
                    },
                    tableHeader: {
                        bold: true,
                        
                        color: 'white',
                        alignment: 'center',
                        fillColor: '#007bff'
                    },
                    tableHeaderTotales: {
                        bold: true,
                        
                        color: 'white',
                        alignment: 'center',
                        fillColor: '#007bff'
                    },
                    tableDiario: {
                        margin: [0, 5, 0, 15],
                        
                        color: '#333333'
                    },
                    tableTotales: {
                        margin: [0, 5, 0, 15]
                        
                    },
                    summaryTable: {
                        color: '#333333'
                    }
                }
            };
            pdfMake.createPdf(documento).open();
        }


        function formatearMoneda(monto) {
            return new Intl.NumberFormat('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(parseFloat(monto) || 0);
        }


        function mostrarMensaje(mensaje, titulo = 'Mensaje') {
            document.getElementById('mensajeModalTitulo').textContent = titulo;
            document.getElementById('mensajeModalCuerpo').textContent = mensaje;
            mensajeModal.show();
        }
    });
</script>