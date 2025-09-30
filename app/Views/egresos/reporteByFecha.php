<?php
include __DIR__ . '/../layout/header.php';
?>
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .card-header {
        border-top-left-radius: 10px !important;
        border-top-right-radius: 10px !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-top: none;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .table td {
        vertical-align: middle;
        padding: 0.75rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.15s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .form-control {
        border-radius: 6px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .accordion-button {
        font-weight: 600;
    }

    .accordion-button:not(.collapsed) {
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.125);
    }

    .accordion-button.bg-danger,
    .accordion-button.bg-success,
    .accordion-button.bg-info,
    .accordion-button.bg-primary {
        color: white !important;
    }

    .accordion-button.bg-danger:not(.collapsed) {
        background-color: #dc3545 !important;
    }

    .accordion-button.bg-success:not(.collapsed) {
        background-color: #198754 !important;
    }

    .accordion-button.bg-info:not(.collapsed) {
        background-color: #0dcaf0 !important;
    }

    .accordion-button.bg-primary:not(.collapsed) {
        background-color: #0d6efd !important;
    }

    /* Mejoras específicas para tablas */
    .table-responsive {
        border-radius: 0.35rem;
        overflow: hidden;
    }

    .table-bordered {
        border: 1px solid #e3e6f0;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #e3e6f0;
    }

    .table-danger th {
        background-color: #dc3545;
    }

    .table-success th {
        background-color: #198754;
    }

    .table-info th {
        background-color: #0dcaf0;
    }

    .table-primary th {
        background-color: #0d6efd;
    }


    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .section-header i {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }

    .table tfoot tr td {
        border-top: 2px solid;
        font-size: 1.1rem;
    }

    .table-danger tfoot tr td {
        border-color: #dc3545;
    }

    .table-success tfoot tr td {
        border-color: #198754;
    }

    #mensaje-inicial i {
        font-size: 4rem;
        opacity: 0.7;
    }


    .export-buttons {
        display: flex;
        gap: 0.5rem;
    }


    .comment-icon {
        cursor: pointer;
        transition: color 0.15s ease-in-out;
    }

    .comment-icon:hover {
        color: #0d6efd !important;
    }
</style>


<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-primary"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" class="text-primary">Egreso</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Reporte por fecha
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="/egreso" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-1"></i> Lista
                </a>
            </div>
        </div>
    </div>

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

    <div id="reporte-resultados-container" class="mt-4" style="display: none;">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">Resumen de Egresos</h6>
                    <div class="export-buttons">
                        <button type="button" class="btn btn-danger btn-sm" id="btn-pdf">
                            <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="btn-excel">
                            <i class="bi bi-file-earmark-excel me-2"></i> Exportar Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="d-none d-md-block" id="tablas-grandes-container">
                    <div class="mb-4" id="seccion-sin-comprobante" style="display: none;">
                        <div class="section-header">
                            <i class="bi bi-card-checklist text-danger fw-bold"></i>
                            <h5 class="text-danger mb-0 fw-bold">Egresos sin Comprobante</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="tabla-sin-comprobante">
                                <thead class="table-danger text-white">
                                    <tr>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Concepto</th>
                                        <th class="text-end">Monto</th>
                                        <th class="text-center">Registrador</th>
                                        <th class="text-center">Solicitante</th>
                                        <th class="text-center">Comentario</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-sin-comprobante">
                                </tbody>
                                <tfoot class="table-danger text-white fw-bold">
                                    <tr>
                                        <td colspan="2" class="text-end">Total:</td>
                                        <td class="text-end" id="total-sin-comprobante-tabla">S/ 0.00</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4" id="seccion-con-comprobante" style="display: none;">
                        <div class="section-header">
                            <i class="bi bi-card-checklist text-success fw-bold"></i>
                            <h5 class="text-success mb-0 fw-bold">Egresos con Comprobante</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="tabla-con-comprobante">
                                <thead class="table-success text-white">
                                    <tr>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Concepto</th>
                                        <th class="text-end">Monto</th>
                                        <th class="text-center">Registrador</th>
                                        <th class="text-center">Solicitante</th>
                                        <th class="text-center">Comentario</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-con-comprobante">
                                </tbody>
                                <tfoot class="table-success text-white fw-bold">
                                    <tr>
                                        <td colspan="2" class="text-end">Total:</td>
                                        <td class="text-end" id="total-con-comprobante-tabla">S/ 0.00</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4" id="seccion-por-concepto" style="display: none;">
                        <div class="section-header">
                            <i class="bi bi-graph-up text-info fw-bold"></i>
                            <h5 class="text-info mb-0 fw-bold">Resumen por Concepto</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="reporte-concepto-tabla">
                                <thead class="table-info text-white">
                                    <tr>
                                        <th class="text-center">Concepto</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-concepto">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4" id="seccion-totales-generales" style="display: none;">
                        <div class="section-header">
                            <i class="bi bi-calculator text-primary fw-bold"></i>
                            <h5 class="text-primary mb-0 fw-bold">Totales Generales</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="tabla-totales-generales">
                                <thead class="table-primary text-white">
                                    <tr>
                                        <th class="text-center">Total sin Comprobante</th>
                                        <th class="text-center">Total con Comprobante</th>
                                        <th class="text-center">Total General</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center fw-bold" id="total-sin-comprobante">S/ 0.00</td>
                                        <td class="text-center fw-bold" id="total-con-comprobante">S/ 0.00</td>
                                        <td class="text-center fw-bold" id="total-global">S/ 0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-block d-md-none accordion" id="acordeonReportePadre">
                </div>
            </div>
        </div>
    </div>

    <div id="mensaje-inicial" class="card shadow d-flex justify-content-center align-items-center" style="height: 300px;">
        <div class="text-center text-muted">
            <i class="fas fa-search fs-1 mb-3 text-primary fw-bold"></i>
            <h4>Selecciona un rango de fechas y pulsa en "Generar" para ver el reporte.</h4>
            <p>Los datos aparecerán aquí.</p>
        </div>
    </div>

    <div class="modal fade" id="comentarioModal" tabindex="-1" aria-labelledby="comentarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="comentarioModalLabel">Detalle del Comentario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="comentarioModalCuerpo"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

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

        const tbodySinComprobante = document.getElementById('tbody-sin-comprobante');
        const tbodyConComprobante = document.getElementById('tbody-con-comprobante');
        const tbodyConcepto = document.getElementById('tbody-concepto');

        const seccionSinComprobante = document.getElementById('seccion-sin-comprobante');
        const seccionConComprobante = document.getElementById('seccion-con-comprobante');
        const seccionPorConcepto = document.getElementById('seccion-por-concepto');
        const seccionTotalesGenerales = document.getElementById('seccion-totales-generales');

        const totalSinComprobanteTabla = document.getElementById('total-sin-comprobante-tabla');
        const totalConComprobanteTabla = document.getElementById('total-con-comprobante-tabla');
        const totalSinComprobanteEl = document.getElementById('total-sin-comprobante');
        const totalConComprobanteEl = document.getElementById('total-con-comprobante');
        const totalGlobalEl = document.getElementById('total-global');

        const acordeonContainer = document.querySelector('#acordeonReportePadre');
        const comentarioModal = new bootstrap.Modal(document.getElementById('comentarioModal'));
        const comentarioModalCuerpo = document.getElementById('comentarioModalCuerpo');

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

        
        document.body.addEventListener('click', (e) => {
            const btnComentario = e.target.closest('button[data-comentario]');
            const linkComentario = e.target.closest('.ver-comentario-link[data-comentario]');

            if (btnComentario) {
                const comentario = btnComentario.getAttribute('data-comentario');
                comentarioModalCuerpo.textContent = comentario;
                comentarioModal.show();
            } else if (linkComentario) {
                e.preventDefault();
                const comentario = linkComentario.getAttribute('data-comentario');
                comentarioModalCuerpo.textContent = comentario;
                comentarioModal.show();
            }
        });

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
            btnExcel.style.display = 'none';
            resultadosContainer.style.display = 'none';
            limpiarTablasYAcordeones();
            limpiarTotales();

            try {
                const url = `/api/egreso/reporteByFecha?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
                const response = await fetch(url);
                const data = await response.json();

                if (!response.ok) {
                    mostrarMensaje(data.message, 'Error');
                    mensajeInicial.classList.remove('d-none');
                    mensajeInicial.classList.add('d-flex');
                    return;
                }

                reporteData = data;
                mostrarReporte(reporteData);

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

        function limpiarTablasYAcordeones() {
            tbodySinComprobante.innerHTML = '';
            tbodyConComprobante.innerHTML = '';
            tbodyConcepto.innerHTML = '';
            acordeonContainer.innerHTML = '';
        }

        function limpiarTotales() {
            totalSinComprobanteTabla.textContent = 'S/ 0.00';
            totalConComprobanteTabla.textContent = 'S/ 0.00';
            totalSinComprobanteEl.textContent = 'S/ 0.00';
            totalConComprobanteEl.textContent = 'S/ 0.00';
            totalGlobalEl.textContent = 'S/ 0.00';
        }

        function mostrarReporte(datos) {
            resultadosContainer.style.display = 'block';
            mensajeInicial.classList.remove('d-flex');
            mensajeInicial.classList.add('d-none');

            mostrarReporteEnTablaYAcordeon(datos);

            const hayDatos = (datos.egresoSinComprobante.length > 0 || datos.egresoConComprobante.length > 0 || datos.egresoByConcepto.length > 0);
            if (hayDatos) {
                btnPDF.style.display = 'inline-block';
                btnExcel.style.display = 'inline-block';
            } else {
                mensajeInicial.classList.add('d-flex');
                btnPDF.style.display = 'none';
                btnExcel.style.display = 'none';
            }
        }

        function mostrarReporteEnTablaYAcordeon(datos) {
            limpiarTablasYAcordeones();
            limpiarTotales();

            let totalSinComprobante = 0;
            let totalConComprobante = 0;

            seccionSinComprobante.style.display = 'none';
            seccionConComprobante.style.display = 'none';
            seccionPorConcepto.style.display = 'none';
            seccionTotalesGenerales.style.display = 'none';

            if (datos && (datos.egresoSinComprobante.length > 0 || datos.egresoConComprobante.length > 0 || datos.egresoByConcepto.length > 0)) {

                if (datos.egresoSinComprobante && datos.egresoSinComprobante.length > 0) {
                    seccionSinComprobante.style.display = 'block';

                    datos.egresoSinComprobante.forEach((egreso) => {
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td class="text-center">${formatearFecha(egreso.creado.split(' ')[0])}</td>
                            <td>${egreso.concepto}</td>
                            <td class="text-end">S/ ${formatearMoneda(egreso.monto)}</td>
                            <td>${egreso.registrador}</td>
                            <td>${egreso.solicitante}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" data-comentario="${egreso.comentario || 'Sin comentario'}"><i class="fas fa-comment-dots" title="Ver comentario"></i></button>
                            </td>
                        `;
                        tbodySinComprobante.appendChild(newRow);
                        totalSinComprobante += parseFloat(egreso.monto);
                    });
                    totalSinComprobanteTabla.textContent = `S/ ${formatearMoneda(totalSinComprobante)}`;

                    const accordionItemHtml = `
                        <div class="accordion-item mb-2 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-danger text-white fw-bold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse-sin-comprobante" 
                                    aria-expanded="false" data-bs-parent="#acordeonReportePadre">
                                    <span class="fw-bold">Egresos sin Comprobante</span>
                                    <span class="ms-auto badge bg-light text-danger">
                                        S/ ${formatearMoneda(totalSinComprobante)}
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse-sin-comprobante" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        ${datos.egresoSinComprobante.map(egreso => `
                                            <li class="list-group-item">
                                                <strong>Fecha:</strong> ${formatearFecha(egreso.creado.split(' ')[0])}<br>
                                                <strong>Concepto:</strong> ${egreso.concepto}<br>
                                                <strong>Monto:</strong> S/ ${formatearMoneda(egreso.monto)}<br>
                                                <strong>Registrador:</strong> ${egreso.registrador}<br>
                                                <strong>Solicitante:</strong> ${egreso.solicitante}<br>
                                                <strong>Comentario:</strong> <a href="#" class="text-primary ver-comentario-link" data-comentario="${egreso.comentario || 'Sin comentario'}">Ver detalle</a>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                    acordeonContainer.insertAdjacentHTML('beforeend', accordionItemHtml);
                }

                if (datos.egresoConComprobante && datos.egresoConComprobante.length > 0) {
                    seccionConComprobante.style.display = 'block';

                    datos.egresoConComprobante.forEach((egreso) => {
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td class="text-center">${formatearFecha(egreso.creado.split(' ')[0])}</td>
                            <td>${egreso.concepto}</td>
                            <td class="text-end">S/ ${formatearMoneda(egreso.monto)}</td>
                            <td>${egreso.registrador}</td>
                            <td>${egreso.solicitante}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" data-comentario="${egreso.comentario || 'Sin comentario'}"><i class="fas fa-comment-dots" title="Ver comentario"></i></button>
                            </td>
                        `;
                        tbodyConComprobante.appendChild(newRow);
                        totalConComprobante += parseFloat(egreso.monto);
                    });
                    totalConComprobanteTabla.textContent = `S/ ${formatearMoneda(totalConComprobante)}`;

                    const accordionItemHtml = `
                        <div class="accordion-item mb-2 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-success text-white fw-bold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse-con-comprobante" 
                                    aria-expanded="false" data-bs-parent="#acordeonReportePadre">
                                    <span class="fw-bold">Egresos con Comprobante</span>
                                    <span class="ms-auto badge bg-light text-success">
                                        S/ ${formatearMoneda(totalConComprobante)}
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse-con-comprobante" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        ${datos.egresoConComprobante.map(egreso => `
                                            <li class="list-group-item">
                                                <strong>Fecha:</strong> ${formatearFecha(egreso.creado.split(' ')[0])}<br>
                                                <strong>Concepto:</strong> ${egreso.concepto}<br>
                                                <strong>Monto:</strong> S/ ${formatearMoneda(egreso.monto)}<br>
                                                <strong>Registrador:</strong> ${egreso.registrador}<br>
                                                <strong>Solicitante:</strong> ${egreso.solicitante}<br>
                                                <strong>Comentario:</strong> <a href="#" class="text-primary ver-comentario-link" data-comentario="${egreso.comentario || 'Sin comentario'}">Ver detalle</a>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                    acordeonContainer.insertAdjacentHTML('beforeend', accordionItemHtml);
                }

                if (datos.egresoByConcepto && datos.egresoByConcepto.length > 0) {
                    seccionPorConcepto.style.display = 'block';

                    datos.egresoByConcepto.forEach(item => {
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td>${item.concepto}</td>
                            <td class="text-end fw-bold">S/ ${formatearMoneda(item.total_por_concepto)}</td>
                        `;
                        tbodyConcepto.appendChild(newRow);
                    });

                    const accordionItemHtml = `
                        <div class="accordion-item mb-2 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-info text-white fw-bold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse-resumen" 
                                    aria-expanded="false" data-bs-parent="#acordeonReportePadre">
                                    Resumen por Concepto
                                </button>
                            </h2>
                            <div id="collapse-resumen" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        ${datos.egresoByConcepto.map(item => `
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>${item.concepto}</span>
                                                <span class="badge bg-primary rounded-pill">S/ ${formatearMoneda(item.total_por_concepto)}</span>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                    acordeonContainer.insertAdjacentHTML('beforeend', accordionItemHtml);
                }

                if (datos.egresoGeneral && datos.egresoGeneral.length > 0) {
                    const egresoGeneral = datos.egresoGeneral[0];
                    seccionTotalesGenerales.style.display = 'block';

                    totalSinComprobanteEl.textContent = `S/ ${formatearMoneda(egresoGeneral.total_sin_comprobante)}`;
                    totalConComprobanteEl.textContent = `S/ ${formatearMoneda(egresoGeneral.total_con_comprobante_cargado)}`;
                    totalGlobalEl.textContent = `S/ ${formatearMoneda(egresoGeneral.gran_total)}`;

                    const totalGeneralAcordeonHtml = `
                        <div class="accordion-item mb-2 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-primary text-white fw-bold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse-totales-generales" 
                                    aria-expanded="false" data-bs-parent="#acordeonReportePadre">
                                    Totales Generales
                                    <span class="ms-auto badge bg-light text-primary">
                                        S/ ${formatearMoneda(egresoGeneral.gran_total)}
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse-totales-generales" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Total sin Comprobante:</strong> S/ ${formatearMoneda(egresoGeneral.total_sin_comprobante)}</li>
                                        <li class="list-group-item"><strong>Total con Comprobante:</strong> S/ ${formatearMoneda(egresoGeneral.total_con_comprobante_cargado)}</li>
                                        <li class="list-group-item bg-body fw-bold"><strong>Gran Total:</strong> S/ ${formatearMoneda(egresoGeneral.gran_total)}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;
                    acordeonContainer.insertAdjacentHTML('beforeend', totalGeneralAcordeonHtml);
                }
            } else {
                mensajeInicial.classList.add('d-flex');
                btnPDF.style.display = 'none';
                btnExcel.style.display = 'none';
            }
        }







        resultadosContainer.addEventListener('click', (e) => {
            if (e.target.parentElement.classList.contains('ver-comentario-link')) {
                const comentario = e.target.getAttribute('data-comentario') || e.target.parentElement.getAttribute('data-comentario');
                comentarioModalCuerpo.textContent = comentario || 'Sin comentario.';
                comentarioModal.show();
            }
        });







        btnPDF.addEventListener('click', () => {
            if (reporteData) {
                generarReportePDF(reporteData);
            }
        });

        btnExcel.addEventListener('click', () => {
            if (reporteData) {
                generarReporteExcel(reporteData, formatearFecha);
            }
        });




        function formatearFecha(fecha) {
            const [year, month, day] = fecha.split('-');
            return `${day}/${month}/${year}`;
        }

        function formatearMoneda(monto) {
            return new Intl.NumberFormat('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(parseFloat(monto) || 0);
        }

        function mostrarMensaje(mensaje, titulo = 'Mensaje') {
            const mensajeModalCuerpo = document.getElementById('mensajeModalCuerpo');
            const mensajeModalTitulo = document.getElementById('mensajeModalTitulo');
            mensajeModalTitulo.textContent = titulo;
            mensajeModalCuerpo.textContent = mensaje;
            mensajeModal.show();
        }


        async function generarReporteExcel(data, formatearFecha) {
            const workbook = new ExcelJS.Workbook();
            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;

            // Hoja de egresos sin comprobante
            if (data.egresoSinComprobante && data.egresoSinComprobante.length > 0) {
                const wsSin = workbook.addWorksheet('Sin Comprobante');
                wsSin.columns = [{
                    header: 'Fecha',
                    key: 'fecha'
                }, {
                    header: 'Concepto',
                    key: 'concepto'
                }, {
                    header: 'Monto',
                    key: 'monto'
                }, {
                    header: 'Registrador',
                    key: 'registrador'
                }, {
                    header: 'Solicitante',
                    key: 'solicitante'
                }];
                data.egresoSinComprobante.forEach(egreso => {
                    wsSin.addRow({
                        fecha: formatearFecha(egreso.creado.split(' ')[0]),
                        concepto: egreso.concepto,
                        monto: parseFloat(egreso.monto),
                        registrador: egreso.registrador,
                        solicitante: egreso.solicitante
                    });
                });
                wsSin.addRow(['Total:', '', parseFloat(data.egresoGeneral[0].total_sin_comprobante)]);
            }

            // Hoja de egresos con comprobante
            if (data.egresoConComprobante && data.egresoConComprobante.length > 0) {
                const wsCon = workbook.addWorksheet('Con Comprobante');
                wsCon.columns = [{
                    header: 'Fecha',
                    key: 'fecha'
                }, {
                    header: 'Concepto',
                    key: 'concepto'
                }, {
                    header: 'Monto',
                    key: 'monto'
                }, {
                    header: 'Registrador',
                    key: 'registrador'
                }, {
                    header: 'Solicitante',
                    key: 'solicitante'
                }];
                data.egresoConComprobante.forEach(egreso => {
                    wsCon.addRow({
                        fecha: formatearFecha(egreso.creado.split(' ')[0]),
                        concepto: egreso.concepto,
                        monto: parseFloat(egreso.monto),
                        registrador: egreso.registrador,
                        solicitante: egreso.solicitante
                    });
                });
                wsCon.addRow(['Total:', '', parseFloat(data.egresoGeneral[0].total_con_comprobante_cargado)]);
            }

            // Hoja de resumen por concepto
            if (data.egresoByConcepto && data.egresoByConcepto.length > 0) {
                const wsConcepto = workbook.addWorksheet('Por Concepto');
                wsConcepto.columns = [{
                    header: 'Concepto',
                    key: 'concepto'
                }, {
                    header: 'Total',
                    key: 'total'
                }];
                data.egresoByConcepto.forEach(item => {
                    wsConcepto.addRow({
                        concepto: item.concepto,
                        total: parseFloat(item.total_por_concepto)
                    });
                });
            }

            // Hoja de totales generales
            if (data.egresoGeneral && data.egresoGeneral.length > 0) {
                const wsTotales = workbook.addWorksheet('Totales Generales');
                const egresoGeneral = data.egresoGeneral[0];
                wsTotales.addRow(['Total sin Comprobante', parseFloat(egresoGeneral.total_sin_comprobante)]);
                wsTotales.addRow(['Total con Comprobante', parseFloat(egresoGeneral.total_con_comprobante_cargado)]);
                wsTotales.addRow(['Gran Total', parseFloat(egresoGeneral.gran_total)]);
            }

            workbook.xlsx.writeBuffer().then(buffer => {
                const blob = new Blob([buffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `reporte-egresos-${formatearFecha(fechaInicio)}-${formatearFecha(fechaFin)}.xlsx`;
                a.click();
                window.URL.revokeObjectURL(url);
            });
        }


        async function generarReportePDF(data) {
            const egresoGeneral = data.egresoGeneral[0];
            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;
            const logo = window.logoBase64 || '';

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

            let content = [];

            // Sección de encabezado
            content.push({
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
            }, {
                text: 'REPORTE DE EGRESOS POR PERIODO',
                style: 'subheader',
                alignment: 'center',
                fontSize:13.2,
                margin: [0, 0, 0, 10],
                decoration: 'underline'
            }, {
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
            });

            // Sección de egresos sin comprobante
            if (data.egresoSinComprobante && data.egresoSinComprobante.length > 0) {
                content.push({
                    text: 'Egresos sin Comprobante',
                    style: 'sectionHeader',
                    fontSize:8.2,
                    margin: [0, 0, 0, 9]
                }, {
                    style: 'tableContent',
                    table: {
                        headerRows: 1,
                        widths: ['auto', '*', 'auto', 'auto', 'auto'],
                        body: [
                            [{
                                text: 'Fecha',
                                style: 'tableHeaderRed'
                            }, {
                                text: 'Concepto',
                                style: 'tableHeaderRed'
                            }, {
                                text: 'Monto',
                                style: 'tableHeaderRed'
                            }, {
                                text: 'Registrador',
                                style: 'tableHeaderRed'
                            }, {
                                text: 'Solicitante',
                                style: 'tableHeaderRed'
                            }],
                            ...data.egresoSinComprobante.map(egreso => ([{
                                    text: formatearFecha(egreso.creado.split(' ')[0]),
                                    alignment: 'center'
                                },
                                {
                                    text: egreso.concepto,
                                    alignment: 'left'
                                },
                                {
                                    text: `S/ ${formatNumber(egreso.monto)}`,
                                    alignment: 'right'
                                },
                                {
                                    text: egreso.registrador,
                                    alignment: 'center'
                                },
                                {
                                    text: egreso.solicitante,
                                    alignment: 'center'
                                }
                            ]))
                        ]
                    },
                    layout: {
                        fillColor: (rowIndex) => (rowIndex === 0) ? '#dc3545' : null
                    }
                });
            }

            // Sección de egresos con comprobante
            if (data.egresoConComprobante && data.egresoConComprobante.length > 0) {
                content.push({
                    text: 'Egresos con Comprobante',
                    style: 'sectionHeader',
                    fontSize:8.2,
                    margin: [0, 15, 0, 9]
                }, {
                    style: 'tableContent',
                    table: {
                        headerRows: 1,
                        widths: ['auto', '*', 'auto', 'auto', 'auto'],
                        body: [
                            [{
                                text: 'Fecha',
                                style: 'tableHeaderGreen'
                            }, {
                                text: 'Concepto',
                                style: 'tableHeaderGreen'
                            }, {
                                text: 'Monto',
                                style: 'tableHeaderGreen'
                            }, {
                                text: 'Registrador',
                                style: 'tableHeaderGreen'
                            }, {
                                text: 'Solicitante',
                                style: 'tableHeaderGreen'
                            }],
                            ...data.egresoConComprobante.map(egreso => ([{
                                    text: formatearFecha(egreso.creado.split(' ')[0]),
                                    alignment: 'center'
                                },
                                {
                                    text: egreso.concepto,
                                    alignment: 'left'
                                },
                                {
                                    text: `S/ ${formatNumber(egreso.monto)}`,
                                    alignment: 'right'
                                },
                                {
                                    text: egreso.registrador,
                                    alignment: 'center'
                                },
                                {
                                    text: egreso.solicitante,
                                    alignment: 'center'
                                }
                            ]))
                        ]
                    },
                    layout: {
                        fillColor: (rowIndex) => (rowIndex === 0) ? '#28a745' : null
                    }
                });
            }

            // Sección de resumen por concepto
            if (data.egresoByConcepto && data.egresoByConcepto.length > 0) {
                content.push({
                    text: 'Resumen por Concepto',
                    fontSize:8.2,
                    style: 'sectionHeader',
                    margin: [0, 15, 0, 9]
                }, {
                    style: 'tableConcepto',
                    table: {
                        headerRows: 1,
                        widths: ['*', '*'],
                        body: [
                            [{
                                text: 'Concepto',
                                style: 'tableHeaderConcepto'
                            }, {
                                text: 'Total',
                                style: 'tableHeaderConcepto'
                            }],
                            ...data.egresoByConcepto.map(item => ([{
                                    text: item.concepto,
                                    alignment: 'left'
                                },
                                {
                                    text: `S/ ${formatNumber(item.total_por_concepto)}`,
                                    alignment: 'right'
                                }
                            ]))
                        ]
                    },
                    layout: {
                        fillColor: (rowIndex) => (rowIndex === 0) ? '#ADD8E6' : null
                    }
                });
            }

            // Sección de totales generales
            if (data.egresoGeneral && data.egresoGeneral.length > 0) {
                content.push({
                    text: 'Totales Generales',
                    fontSize:8.2,
                    style: 'sectionHeader',
                    margin: [0, 15, 0, 10]
                }, {
                    style: 'tableTotales',
                    table: {
                        widths: ['*', '*'],
                        body: [
                            [{
                                text: 'Total General',
                                style: 'tableHeaderTotales'
                            }, {
                                text: `S/ ${formatNumber(egresoGeneral.gran_total)}`,
                                alignment: 'center',
                                bold: true,
                                fillColor: '#f2f2f2'
                            }],
                            [{
                                text: 'Total Sin Comprobante',
                                style: 'tableHeaderTotales'
                            }, {
                                text: `S/ ${formatNumber(egresoGeneral.total_sin_comprobante)}`,
                                alignment: 'center',
                                bold: true,
                                fillColor: '#f2f2f2'
                            }],
                            [{
                                text: 'Total Con Comprobante',
                                style: 'tableHeaderTotales'
                            }, {
                                text: `S/ ${formatNumber(egresoGeneral.total_con_comprobante_cargado)}`,
                                alignment: 'center',
                                bold: true,
                                fillColor: '#f2f2f2'
                            }]
                        ]
                    },

                });
            }

            const documento = {
                pageSize: 'A4',
                pageOrientation: 'portrait',
                pageMargins: [40, 25, 25, 25],
                defaultStyle: {
                    fontSize: 7.2
                },
                content: content,
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
                        fillColor: '#343a40'
                    },
                    tableHeaderRed: {
                        bold: true,
                        color: 'white',
                        alignment: 'center',
                        fillColor: '#dc3545'
                    },
                    tableHeaderGreen: {
                        bold: true,
                        color: 'white',
                        alignment: 'center',
                        fillColor: '#28a745'
                    },
                    tableHeaderConcepto: {
                        bold: true,
                        color: 'black',
                        alignment: 'center',
                        fillColor: '#ADD8E6'
                    },
                    tableHeaderTotales: {
                        bold: true,
                        color: 'white',
                        alignment: 'center',
                        fillColor: '#dc3545'
                    },
                    tableContent: {
                        margin: [0, 5, 0, 15],
                        color: '#333333'
                    },
                    tableConcepto: {
                        margin: [0, 5, 0, 15]
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


    });
</script>