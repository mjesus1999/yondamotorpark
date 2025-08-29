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


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js" defer></script>
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
            XLSX.writeFile(wb,`reporte-pagos(${formatearFecha(fechaInicio)}-${formatearFecha(fechaFin)}).xlsx`);
        }

        function generarReportePDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');
            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;

            doc.setFontSize(24);
            doc.setTextColor(52, 58, 64);
            doc.setFont('helvetica', 'bold');
            doc.text("Reporte de Pagos", 105, 20, null, null, 'center');

            doc.setFontSize(14);
            doc.setTextColor(108, 117, 125);
            doc.setFont('helvetica', 'normal');
            doc.text(`Desde: ${formatearFecha(fechaInicio)} hasta: ${formatearFecha(fechaFin)}`, 105, 30, null, null, 'center');

            doc.setFontSize(18);
            doc.setTextColor(33, 37, 41);
            doc.setFont('helvetica', 'bold');
            doc.text("Resumen Diario de Pagos", 14, 45);

            const tableData = [];
            const tableRows = tbody.querySelectorAll('tr');

            tableRows.forEach((row) => {
                const cols = row.querySelectorAll('td');
                // Verificamos si la fila no es el mensaje de "no hay datos"
                if (cols.length > 1) {
                    const rowData = [
                        cols[0].textContent,
                        cols[1].textContent,
                        cols[2].textContent,
                        cols[3].textContent,
                        cols[4].textContent,
                        cols[5].textContent
                    ];
                    tableData.push(rowData);
                }
            });

            doc.autoTable({
                head: [
                    ['Fecha', 'Efectivo', 'Yape', 'Plin', 'Transferencia', 'Total Diario']
                ],
                body: tableData,
                startY: 55,
                theme: 'striped',
                headStyles: {
                    fillColor: [0, 123, 255],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    halign: 'center'
                },
                bodyStyles: {
                    textColor: [51, 51, 51],
                    valign: 'middle'
                },
                columnStyles: {
                    0: {
                        halign: 'center',
                        fontStyle: 'bold'
                    },
                    1: {
                        halign: 'right'
                    },
                    2: {
                        halign: 'right'
                    },
                    3: {
                        halign: 'right'
                    },
                    4: {
                        halign: 'right'
                    },
                    5: {
                        halign: 'right',
                        fontStyle: 'bold',
                        fillColor: [235, 245, 255]
                    }
                },
                styles: {
                    fontSize: 10,
                    cellPadding: 3
                },
                didDrawPage: function(data) {
                    const pageHeight = doc.internal.pageSize.height;
                    const pageWidth = doc.internal.pageSize.width;
                    const yPos = pageHeight - 20;

                    doc.setDrawColor(108, 117, 125);
                    doc.setLineWidth(0.5);
                    doc.line(data.settings.margin.left, yPos, pageWidth - data.settings.margin.left, yPos);

                    doc.setFontSize(8);
                    doc.setFont('helvetica', 'normal');
                    doc.setTextColor(108, 117, 125);

                    const text1 = "Reporte generado automáticamente";
                    const text2 = `© ${new Date().getFullYear()} Sistema de Gestión de Caja`;
                    doc.text(text1, pageWidth / 2, yPos + 5, {
                        align: 'center'
                    });
                    doc.text(text2, pageWidth / 2, yPos + 10, {
                        align: 'center'
                    });

                    const xPosRight = pageWidth - data.settings.margin.left;
                    doc.text(`Página ${data.pageNumber} de ${doc.internal.getNumberOfPages()}`, xPosRight, yPos + 10, {
                        align: 'right'
                    });
                }
            });

            const finalY = doc.autoTable.previous.finalY + 20;

            doc.setFontSize(18);
            doc.setTextColor(33, 37, 41);
            doc.setFont('helvetica', 'bold');
            doc.text("Resumen de Totales", 14, finalY);

            const totalesHead = [
                ['Total Global', 'Total Efectivo', 'Total Yape', 'Total Plin', 'Total Transferencia']
            ];
            const totalesBody = [
                [
                    totalGlobalEl.textContent,
                    totalEfectivoEl.textContent,
                    totalYapeEl.textContent,
                    totalPlinEl.textContent,
                    totalTransferenciaEl.textContent
                ]
            ];

            doc.autoTable({
                head: totalesHead,
                body: totalesBody,
                startY: finalY + 5,
                theme: 'grid',
                headStyles: {
                    fillColor: [0, 123, 255],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    halign: 'center'
                },
                bodyStyles: {
                    textColor: [51, 51, 51],
                    halign: 'center',
                    fontStyle: 'bold',
                },
                styles: {
                    fontSize: 10,
                    cellPadding: 3,
                }
            });

            doc.save(`reporte_pagos_${fechaInicio}_a_${fechaFin}.pdf`);
        }

        function formatearMoneda(monto) {
            return new Intl.NumberFormat('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(parseFloat(monto) || 0);
        }
        function formatearFecha(fecha) {
            const [year, month, day] = fecha.split('-');
            return `${day}/${month}/${year}`;
        }

        function mostrarMensaje(mensaje, titulo = 'Mensaje') {
            document.getElementById('mensajeModalTitulo').textContent = titulo;
            document.getElementById('mensajeModalCuerpo').textContent = mensaje;
            mensajeModal.show();
        }
    });
</script>