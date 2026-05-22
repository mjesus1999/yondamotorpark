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
                            <i class="bi bi-file-earmark-excel"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="reporte-tabla">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Efectivo</th>
                                    <th class="text-center">Yape</th>
                                    <th class="text-center">Plin</th>
                                    <th class="text-center">Transferencia</th>
                                    <th class="text-center">Interbancario</th>
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
                                    <td class="text-center" id="total-interbancario">S/ 0.00</td>
                                    <td class="text-center bg-primary text-white" id="total-global">S/ 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="d-block d-md-none">
                    <div class="accordion" id="acordeonReporte">
                        <div class="accordion-item mt-4">
                            <h2 class="accordion-header" id="heading-totales">
                                <button class="accordion-button collapsed bg-primary text-white fw-bold" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse-totales"
                                    aria-expanded="false"
                                    aria-controls="collapse-totales">
                                    Totales
                                    <span class="ms-auto badge bg-light text-primary">
                                        S/ <span id="total-global-acordion">0.00</span>
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse-totales"
                                class="accordion-collapse collapse"
                                aria-labelledby="heading-totales"
                                data-bs-parent="#acordeonReporte">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Total Efectivo:</strong> <span id="total-efectivo-acordion">S/ 0.00</span></li>
                                        <li class="list-group-item"><strong>Total Yape:</strong> <span id="total-yape-acordion">S/ 0.00</span></li>
                                        <li class="list-group-item"><strong>Total Plin:</strong> <span id="total-plin-acordion">S/ 0.00</span></li>
                                        <li class="list-group-item"><strong>Total Transferencia:</strong> <span id="total-transferencia-acordion">S/ 0.00</span></li>
                                        <li class="list-group-item"><strong>Total Interbancario:</strong> <span id="total-interbancario-acordion">S/ 0.00</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
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
<script src="/assets/js/logoBase64.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const form = document.getElementById('reporte-fechas-form');
        const resultadosContainer = document.getElementById('reporte-resultados-container');
        const tbody = document.querySelector('#reporte-tabla tbody');
        const acordeonContainer = document.querySelector('#acordeonReporte'); 

        // Elementos de totales de la tabla
        const totalGlobalEl = document.getElementById('total-global');
        const totalEfectivoEl = document.getElementById('total-efectivo');
        const totalYapeEl = document.getElementById('total-yape');
        const totalPlinEl = document.getElementById('total-plin');
        const totalTransferenciaEl = document.getElementById('total-transferencia');
        const totalInterbancarioEl = document.getElementById('total-interbancario');

        // Elementos de totales del acordeón
        const totalGlobalAcordionEl = document.getElementById('total-global-acordion');
        const totalEfectivoAcordionEl = document.getElementById('total-efectivo-acordion');
        const totalYapeAcordionEl = document.getElementById('total-yape-acordion');
        const totalPlinAcordionEl = document.getElementById('total-plin-acordion');
        const totalTransferenciaAcordionEl = document.getElementById('total-transferencia-acordion');
        const totalInterbancarioAcordionEl = document.getElementById('total-interbancario-acordion');

        const btnGenerar = document.getElementById('btn-generar');
        const btnPDF = document.getElementById('btn-pdf');
        const btnExcel = document.querySelector('#btn-excel');
        const mensajeModal = new bootstrap.Modal(document.getElementById('mensajeModal'));
        const mensajeInicial = document.querySelector('#mensaje-inicial');

        let reporteData = null;

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
            tbody.innerHTML = '';

            limpiarTotales();

            try {
                const url = `/api/reporte/by/fecha?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
                const response = await fetch(url);

                if (!response.ok) {
                    const errorData = await response.json();
                    mostrarMensaje(errorData.message, 'Error');
                    mensajeInicial.classList.remove('d-none');
                    mensajeInicial.classList.add('d-flex');
                    return;
                }

                const data = await response.json();
                reporteData = data.data;
                mostrarReporteEnTabla(reporteData);

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
            if (reporteData) {
                generarReporteExcel(reporteData, formatearFecha);
            } else {
                mostrarMensaje('No hay datos para exportar a Excel. Por favor, genera un reporte primero.', 'Advertencia');
            }
        });

        function mostrarReporteEnTabla(datos) {
            let totalGlobal = 0;
            let totalEfectivo = 0;
            let totalYape = 0;
            let totalPlin = 0;
            let totalTransferencia = 0;
            let totalInterbancario = 0;

            resultadosContainer.style.display = 'block';
            tbody.innerHTML = '';

            const dataItemsAcordeon = Array.from(acordeonContainer.children).slice(0, -1);
            dataItemsAcordeon.forEach(item => item.remove());

            if (datos && datos.length > 0) {
                mensajeInicial.classList.remove('d-flex');
                mensajeInicial.classList.add('d-none');

                datos.forEach((row, index) => {
                    const interbancario = parseFloat(row.total_interbancario || 0);
                    const totalDiario = parseFloat(row.total_efectivo) + parseFloat(row.total_yape) + parseFloat(row.total_plin) + parseFloat(row.total_transferencia) + interbancario;
                    totalGlobal += totalDiario;
                    totalEfectivo += parseFloat(row.total_efectivo);
                    totalYape += parseFloat(row.total_yape);
                    totalPlin += parseFloat(row.total_plin);
                    totalTransferencia += parseFloat(row.total_transferencia);
                    totalInterbancario += interbancario;

                    // Crear la fila de la tabla
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                <td class="text-center">${formatearFecha(row.dia)}</td>
                <td class="text-end">S/ ${formatearMoneda(row.total_efectivo)}</td>
                <td class="text-end">S/ ${formatearMoneda(row.total_yape)}</td>
                <td class="text-end">S/ ${formatearMoneda(row.total_plin)}</td>
                <td class="text-end">S/ ${formatearMoneda(row.total_transferencia)}</td>
                <td class="text-end">S/ ${formatearMoneda(interbancario)}</td>
                <td class="text-end fw-bold">S/ ${formatearMoneda(totalDiario)}</td>
            `;
                    tbody.appendChild(newRow);

                    // Crear el item del acordeón
                    const acordeonItem = document.createElement('div');
                    acordeonItem.classList.add('accordion-item', 'mb-2', 'shadow-sm');
                    acordeonItem.innerHTML = `
                <h2 class="accordion-header" id="heading-${index}">
                    <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse-${index}"
                        aria-expanded="false"
                        aria-controls="collapse-${index}">
                        <span class="fw-bold">Fecha: ${formatearFecha(row.dia)}</span>
                        <span class="ms-auto badge bg-primary">
                            Total: S/ ${formatearMoneda(totalDiario)}
                        </span>
                    </button>
                </h2>
                <div id="collapse-${index}"
                    class="accordion-collapse collapse"
                    aria-labelledby="heading-${index}"
                    data-bs-parent="#acordeonReporte">
                    <div class="accordion-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Efectivo:</strong> S/ ${formatearMoneda(row.total_efectivo)}</li>
                            <li class="list-group-item"><strong>Yape:</strong> S/ ${formatearMoneda(row.total_yape)}</li>
                            <li class="list-group-item"><strong>Plin:</strong> S/ ${formatearMoneda(row.total_plin)}</li>
                            <li class="list-group-item"><strong>Transferencia:</strong> S/ ${formatearMoneda(row.total_transferencia)}</li>
                            <li class="list-group-item"><strong>Interbancario:</strong> S/ ${formatearMoneda(interbancario)}</li>
                            <li class="list-group-item bg-body fw-bold"><strong>Total Diario:</strong> S/ ${formatearMoneda(totalDiario)}</li>
                        </ul>
                    </div>
                </div>
            `;

                    acordeonContainer.insertBefore(acordeonItem, acordeonContainer.lastElementChild);
                });

                // Actualizar los totales de la tabla
                totalGlobalEl.textContent = `S/ ${formatearMoneda(totalGlobal)}`;
                totalEfectivoEl.textContent = `S/ ${formatearMoneda(totalEfectivo)}`;
                totalYapeEl.textContent = `S/ ${formatearMoneda(totalYape)}`;
                totalPlinEl.textContent = `S/ ${formatearMoneda(totalPlin)}`;
                totalTransferenciaEl.textContent = `S/ ${formatearMoneda(totalTransferencia)}`;
                if (totalInterbancarioEl) totalInterbancarioEl.textContent = `S/ ${formatearMoneda(totalInterbancario)}`;

                // Actualizar los totales del acordeón
                if (totalGlobalAcordionEl) totalGlobalAcordionEl.textContent = `${formatearMoneda(totalGlobal)}`;
                if (totalEfectivoAcordionEl) totalEfectivoAcordionEl.textContent = `S/ ${formatearMoneda(totalEfectivo)}`;
                if (totalYapeAcordionEl) totalYapeAcordionEl.textContent = `S/ ${formatearMoneda(totalYape)}`;
                if (totalPlinAcordionEl) totalPlinAcordionEl.textContent = `S/ ${formatearMoneda(totalPlin)}`;
                if (totalTransferenciaAcordionEl) totalTransferenciaAcordionEl.textContent = `S/ ${formatearMoneda(totalTransferencia)}`;
                if (totalInterbancarioAcordionEl) totalInterbancarioAcordionEl.textContent = `S/ ${formatearMoneda(totalInterbancario)}`;

                btnPDF.style.display = 'inline-block';

            } else {
                mensajeInicial.classList.add('d-flex');
                limpiarTotales();
                btnPDF.style.display = 'none';
            }
        }

        function limpiarTotales() {
            totalGlobalEl.textContent = 'S/ 0.00';
            totalEfectivoEl.textContent = 'S/ 0.00';
            totalYapeEl.textContent = 'S/ 0.00';
            totalPlinEl.textContent = 'S/ 0.00';
            totalTransferenciaEl.textContent = 'S/ 0.00';
            if (totalInterbancarioEl) totalInterbancarioEl.textContent = 'S/ 0.00';

            // Limpiar los totales del acordeón
            if (totalGlobalAcordionEl) totalGlobalAcordionEl.textContent = '0.00';
            if (totalEfectivoAcordionEl) totalEfectivoAcordionEl.textContent = 'S/ 0.00';
            if (totalYapeAcordionEl) totalYapeAcordionEl.textContent = 'S/ 0.00';
            if (totalPlinAcordionEl) totalPlinAcordionEl.textContent = 'S/ 0.00';
            if (totalTransferenciaAcordionEl) totalTransferenciaAcordionEl.textContent = 'S/ 0.00';
            if (totalInterbancarioAcordionEl) totalInterbancarioAcordionEl.textContent = 'S/ 0.00';
        }

        


        

        async function generarReporteExcel(data, formatearFecha) {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Reporte de Pagos');


            const headerStyle = {
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
                },
                fill: {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: {
                        argb: 'D3D3D3'
                    }
                }
            };

            const currencyStyle = {
                numFmt: '"S/"#,##0.00',
                font: {
                    size: 11
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

            const dateStyle = {
                font: {
                    size: 11
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

            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;

            worksheet.mergeCells('A1:F1');
            const titleRow = worksheet.getRow(1);
            titleRow.getCell(1).value = `Reporte de Pagos del ${formatearFecha(fechaInicio)} al ${formatearFecha(fechaFin)}`;
            titleRow.getCell(1).font = {
                bold: true,
                size: 16
            };
            titleRow.getCell(1).alignment = {
                horizontal: 'center'
            };

            worksheet.addRow([]);
            const columns = ['Fecha', 'Efectivo', 'Yape', 'Plin', 'Transferencia', 'Interbancario', 'Total Diario'];
            worksheet.addRow(columns).eachCell(cell => {
                Object.assign(cell, headerStyle);
            });

            let totalEfectivo = 0;
            let totalYape = 0;
            let totalPlin = 0;
            let totalTransferencia = 0;
            let totalInterbancario = 0;
            let totalGlobal = 0;

            data.forEach(row => {
                const interbancario = parseFloat(row.total_interbancario || 0);
                const totalDiario = parseFloat(row.total_efectivo) + parseFloat(row.total_yape) + parseFloat(row.total_plin) + parseFloat(row.total_transferencia) + interbancario;

                const newRow = [

                    formatearFecha(row.dia),
                    parseFloat(row.total_efectivo),
                    parseFloat(row.total_yape),
                    parseFloat(row.total_plin),
                    parseFloat(row.total_transferencia),
                    interbancario,
                    totalDiario
                ];
                const addedRow = worksheet.addRow(newRow);

                addedRow.getCell(1).style = dateStyle;
                addedRow.eachCell((cell, colNumber) => {
                    if (colNumber > 1) {
                        cell.style = currencyStyle;
                    }
                });

                totalEfectivo += parseFloat(row.total_efectivo);
                totalYape += parseFloat(row.total_yape);
                totalPlin += parseFloat(row.total_plin);
                totalTransferencia += parseFloat(row.total_transferencia);
                totalInterbancario += interbancario;
                totalGlobal += totalDiario;
            });

            const yellowFill = {
                type: 'pattern',
                pattern: 'solid',
                fgColor: {
                    argb: 'FFFFFF00'
                }
            };

            const totalsRow = worksheet.addRow(['Totales:', totalEfectivo, totalYape, totalPlin, totalTransferencia, totalInterbancario, totalGlobal]);
            totalsRow.getCell(1).style = {
                font: {
                    bold: true
                },
                fill: yellowFill
            };
            totalsRow.eachCell((cell, colNumber) => {
                if (colNumber > 1) {
                    cell.style = {
                        ...currencyStyle,
                        font: {
                            bold: true
                        },
                        fill: yellowFill
                    };
                }
            });

            worksheet.columns.forEach(column => {
                let maxLen = 0;
                column.eachCell({
                    includeEmpty: true
                }, cell => {
                    const cellLength = cell.value ? cell.value.toString().length : 0;
                    if (cellLength > maxLen) {
                        maxLen = cellLength;
                    }
                });
                column.width = maxLen + 1;
            });

            const fileName = `reporte-pagos(${(formatearFecha(fechaInicio))}/${formatearFecha(fechaFin)}).xlsx`;
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = fileName;
            a.click();
            window.URL.revokeObjectURL(url);
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
            const logo = window.logoBase64 || '';



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