<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .modern-table {
        font-size: 0.9rem;
    }

    .modern-table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        border: none;
    }

    .table-header-custom {
        background: linear-gradient(to right, #f8f9fa, #e9ecef);
        color: #495057;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .modern-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #e9ecef;
    }

    .modern-table tbody tr:hover {
        background-color: #f8f9ff !important;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .modern-table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }

    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    .bg-info-custom {
        background: linear-gradient(135deg, #17a2b8, #138496) !important;
        color: white !important;
        border: none;
    }

    .bg-success-custom {
        background: linear-gradient(135deg, #28a745, #1e7e34) !important;
        color: white !important;
        border: none;
    }

    .bg-primary-custom {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        color: white !important;
        border: none;
    }

    .bg-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        color: white !important;
        border: none;
    }

    .btn-sm {
        padding: 0.4rem 1rem;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }

    /* Estado vacío */
    .empty-state {
        padding: 2rem;
    }

    .empty-state i {
        opacity: 0.3;
    }


    .custom-pagination .page-link {
        border: 1px solid #dee2e6;
        color: #667eea;
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }

    .custom-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-color: #667eea;
        color: white;
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
    }

    .custom-pagination .page-link:hover:not(.disabled) {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .custom-pagination .page-item.disabled .page-link {
        opacity: 0.5;
    }

    .shadow-lg {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    }


    @media (max-width: 768px) {
        .modern-table {
            font-size: 0.8rem;
        }

        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.5rem;
        }

        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem !important;
        }

        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
        }
    }

    /* Animación para carga */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modern-table tbody tr {
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Scroll suave en tabla responsive */
    .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: #667eea #f1f1f1;
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #764ba2;
    }

    @media (max-width: 767.98px) {
        .table-responsive {
            display: none;
        }
    }
</style>


<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert" style="border-left: 4px solid #3498db; border-radius: 0 8px 8px 0;">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Historial de Pagos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="/caja/" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-1"></i> Lista
                </a>
                <button class="btn btn-danger btn-sm ms-2" id="btn-pdf">
                    <i class="fa-regular fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg">
        <div class="card-header bg-gradient-primary text-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Detalle de Pagos
                </h5>
                <span class="badge bg-white text-primary px-3 py-2">
                    Total: <?= count($pagos) ?> registros
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <div id="area-pdf">
                    <table class="table table-hover mb-0 modern-table" id="tabla-historial-pagos">
                        <thead class="table-header-custom border-bottom">
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th width="80">Cuota</th>
                                <th>Vencimiento</th>
                                <th>Fecha pago</th>
                                <th>Amortización</th>
                                <th>Saldo</th>
                                <th width="100">Medio</th>
                                <th>Concepto</th>
                                <th>Transacción</th>
                                <th class="text-center">Observaciones</th>
                                <th class="no-imprimir text-center" width="150">Voucher</th>
                                <th>Boleta / Factura</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-body">
                            <?php if (empty($pagos)) : ?>
                                <tr>
                                    <td colspan="12" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No hay pagos registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($pagos as $pago) : ?>
                                    <tr class="align-middle">
                                        <td class="text-center text-muted fw-semibold"><?= htmlspecialchars($numeroFila++) ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($pago['numcuota']) ?></td>
                                        <td>
                                            <span class="badge bg-info-custom px-3 py-2">
                                                <i class="far fa-calendar me-1"></i>
                                                <?= htmlspecialchars($pago['fecha_vencimiento']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-custom px-3 py-2">
                                                <i class="far fa-calendar-check me-1"></i>
                                                <?= htmlspecialchars($pago['fecha_pago']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold text-success"><?= htmlspecialchars($pago['amortizacion']) ?></td>
                                        <td class="fw-semibold text-body"><?= htmlspecialchars($pago['saldorestante'] ?? '') ?></td>
                                        <td>
                                            <span class="badge bg-success-custom px-3 py-2">
                                                <i class="fas fa-credit-card me-1"></i>
                                                <?= htmlspecialchars($pago['mediopago']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= trim($pago['tipo']) === 'Cuota' ? 'bg-primary-custom' : 'bg-danger-custom' ?> px-3 py-2">
                                                <i class="fas fa-tag me-1"></i>
                                                <?= htmlspecialchars($pago['tipo']) ?>
                                            </span>
                                        </td>
                                        <td class="text-muted">
                                            <small><?= htmlspecialchars($pago['numerotransaccion'] ?? '-------')  ?></small>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-info rounded-pill ver-observacion"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalObservacion"
                                                data-observacion="<?= htmlspecialchars($pago['observacion'] ?? 'Sin observaciones') ?>"
                                                title="Ver observación">
                                                <i class="fas fa-eye me-1"></i>Detalle
                                            </button>
                                        </td>
                                        <td class="no-imprimir text-center">
                                            <?php if (!empty($pago['comprobante'])): ?>
                                                <?php $esPdf = strtolower(pathinfo($pago['comprobante'], PATHINFO_EXTENSION)) === 'pdf'; ?>
                                                <?php $urlSegura = "/archivos/" . htmlspecialchars($pago['comprobante']); ?>
                                                <?php if ($esPdf): ?>
                                                    <a href="<?= $urlSegura ?>" target="_blank"
                                                        class="btn btn-sm btn-danger rounded-pill" title="Ver voucher">
                                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                                    </a>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill ver-comprobante-img"
                                                        data-img="<?= htmlspecialchars($urlSegura) ?>" title="Ver voucher">
                                                        <i class="fas fa-image me-1"></i>Imagen
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted px-3 py-2">Sin voucher</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($pago['enlace_pdf_nubefact'])): ?>
                                                <span class="bg-primary-custom rounded p-1">
                                                    <a class="text-white small"
                                                        href="<?= htmlspecialchars($pago['enlace_pdf_nubefact']) ?>"
                                                        target="_blank">
                                                        Comprobante
                                                    </a>
                                                </span>
                                            <?php else: ?>
                                                <span class="bg-primary-custom rounded p-1 text-white small">
                                                    Sin comprobante
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                </td>


            </div>
        </div>

        <!-- Pie de tabla con paginación -->
        <div class="card-footer bg-body border-top-0 py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted">
                    <i class="fas fa-list-ol me-2"></i>
                    Mostrando <span class="fw-bold text-primary">1-<?= min(10, count($pagos)) ?></span> de
                    <span class="fw-bold text-primary"><?= count($pagos) ?></span> registros
                </div>

                <nav aria-label="Paginación">
                    <ul class="pagination pagination-sm mb-0 custom-pagination" id="paginacion">
                        <li class="page-item disabled" id="prev-page">
                            <a class="page-link rounded-start" href="#" tabindex="-1">
                                <i class="fas fa-chevron-left fs-6"></i>
                            </a>
                        </li>

                        <?php for ($p = 1; $p <= ceil(count($pagos) / 15); $p++): ?>
                            <li class="page-item <?= $p == 1 ? 'active' : '' ?>">
                                <a class="page-link" href="#" data-page="<?= $p ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item" id="next-page">
                            <a class="page-link rounded-end" href="#">
                                <i class="fas fa-chevron-right fs-6"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>


    <div class="d-block d-md-none">
        <?php if (empty($pagos)) : ?>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-info-circle me-2"></i>No hay pagos registrados
            </div>
        <?php else: ?>
            <div class="accordion" id="acordeonPagos">
                <?php $numeroFila = 1; ?>
                <?php foreach ($pagos as $pago) : ?>
                    <div class="accordion-item mb-2 shadow-sm">
                        <h2 class="accordion-header" id="heading-<?= $pago['idpago'] ?>">
                            <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapsePago<?= $pago['idpago'] ?>"
                                aria-expanded="false"
                                aria-controls="collapsePago<?= $pago['idpago'] ?>">
                                <span class="fw-bold">
                                    Cuota #<?= htmlspecialchars($pago['numcuota']) ?>
                                </span>
                                <span class="ms-auto badge bg-primary">
                                    S/. <?= htmlspecialchars($pago['amortizacion']) ?>
                                </span>
                            </button>
                        </h2>
                        <div id="collapsePago<?= $pago['idpago'] ?>"
                            class="accordion-collapse collapse"
                            aria-labelledby="heading-<?= $pago['idpago'] ?>"
                            data-bs-parent="#acordeonPagos">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>#:</strong> <?= htmlspecialchars($numeroFila++) ?></li>
                                    <li class="list-group-item"><strong>Vencimiento:</strong> <span class="badge bg-info text-white"><?= htmlspecialchars($pago['fecha_vencimiento']) ?></span></li>
                                    <li class="list-group-item"><strong>Fecha pago:</strong> <span class="badge bg-info text-white"><?= htmlspecialchars($pago['fecha_pago']) ?></span></li>
                                    <li class="list-group-item"><strong>Saldo restante:</strong> <?= htmlspecialchars($pago['saldorestante'] ?? '0.00') ?></li>
                                    <li class="list-group-item"><strong>Medio de pago:</strong> <span class="badge bg-success text-white"><?= htmlspecialchars($pago['mediopago']) ?></span></li>
                                    <li class="list-group-item"><strong>Concepto:</strong> <span class="badge <?= trim($pago['tipo']) === 'Cuota' ? 'bg-primary text-white' : 'bg-danger text-white' ?>"><?= htmlspecialchars($pago['tipo']) ?></span></li>
                                    <li class="list-group-item"><strong>Transacción:</strong> <?= htmlspecialchars($pago['numerotransaccion'] ?? '----') ?></li>
                                    <li class="list-group-item">
                                        <strong>Observaciones:</strong>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ver-observacion mt-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalObservacion"
                                            data-observacion="<?= htmlspecialchars($pago['observacion'] ?? 'Sin observaciones') ?>" title="Ver observación">
                                            <i class="fas fa-eye"></i> Ver detalle
                                        </button>
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Comprobante:</strong>

                                        <?php if (!empty($pago['comprobante'])): ?>
                                            <?php
                                            $urlSegura = "/archivos/" . htmlspecialchars($pago['comprobante']);
                                            $extension = strtolower(pathinfo($pago['comprobante'], PATHINFO_EXTENSION));
                                            ?>

                                            <?php if ($extension === 'pdf'): ?>
                                                <a href="<?= $urlSegura ?>" target="_blank"
                                                    class="btn btn-sm btn-danger mt-1"
                                                    title="Ver comprobante">
                                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                                </a>

                                            <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-primary ver-comprobante-img mt-1"
                                                    data-img="<?= htmlspecialchars($urlSegura) ?>"
                                                    title="Ver comprobante">
                                                    <i class="fas fa-image me-1"></i>Voucher
                                                </button>

                                            <?php else: ?>

                                                <span class="badge bg-light text-muted">N/A</span>
                                            <?php endif; ?>

                                        <?php else: ?>

                                            <span class="badge bg-light text-muted">N/A</span>
                                        <?php endif; ?>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>


</div>

<!-- Modal para comprobantes -->
<div class="modal fade" id="modalcomprobante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i>Comprobante de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 60vh;">
                <div class="ratio ratio-1x1">

                    <img id="imagenComprobante" src="" alt="Comprobante"
                        class="img-fluid mx-auto d-block" style="display: none; max-height: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <a id="descargarComprobante" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download me-1"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE Observaciones -->
<div class="modal fade top" id="modalObservacion" tabindex="-1" aria-labelledby="modalObservacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm mt-0">
        <div class="modal-content">
            <div class="modal-header bg-info text-white fw-bold">
                <h5 class="modal-title" id="modalObservacionLabel">Observación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="contenidoObservacion">

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
    document.addEventListener('DOMContentLoaded', () => {

        const btnPDF = document.querySelector('#btn-pdf');
        const itemsPerPage = 15;
        const totalItems = <?= count($pagos) ?>;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        let currentPage = parseInt(localStorage.getItem('pageHistorialPago') || '1');
        const botonesObservacion = document.querySelectorAll('.ver-observacion');
        const contenidoModal = document.getElementById('contenidoObservacion');



        botonesObservacion.forEach((boton) => {
            boton.addEventListener('click', (e) => {
                const btn = e.target.closest('.ver-observacion');
                if (!btn) return;
                const observacion = btn.getAttribute('data-observacion');
                contenidoModal.textContent = observacion === null ? 'Sin observaciones' : observacion;
            });
        });


        btnPDF.addEventListener('click', async () => {

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


            // Extraer datos de la tabla de pagos original
            const todasFilas = document.querySelectorAll('#tabla-body tr');
            const rows = [];
            let totalAmortizacion = 0;
            let totalSaldo = 0;

            todasFilas.forEach(tr => {
                const tds = tr.querySelectorAll('td');
                if (tds.length >= 9) {
                    const fila = [{
                            text: tds[0].innerText.trim(),
                            alignment: 'center'
                        }, // #
                        {
                            text: tds[1].innerText.trim(),
                            alignment: 'center'
                        }, // N° Cuota
                        {
                            text: tds[2].innerText.trim(),
                            alignment: 'center'
                        }, // Vencimiento
                        {
                            text: tds[3].innerText.trim(),
                            alignment: 'center'
                        }, // Fecha pago
                        {
                            text: tds[4].innerText.trim(),
                            alignment: 'right'
                        }, // Amortización
                        {
                            text: tds[5].innerText.trim(),
                            alignment: 'right'
                        }, // Saldo
                        {
                            text: tds[6].innerText.trim(),
                            alignment: 'center'
                        }, // Medio
                        {
                            text: tds[7].innerText.trim(),
                            alignment: 'center'
                        }, // Concepto
                        {
                            text: tds[8].innerText.trim(),
                            alignment: 'center'
                        }, // Transacción
                    ];
                    rows.push(fila);

                    const amortizacion = parseFloat(tds[4].innerText.trim().replace(/S\/\s*/, '').replace(/,/g, ''));
                    const saldo = parseFloat(tds[5].innerText.trim().replace(/S\/\s*/, '').replace(/,/g, ''));
                    if (!isNaN(amortizacion)) {
                        totalAmortizacion += amortizacion;
                    }
                    if (!isNaN(saldo)) {
                        totalSaldo += saldo;
                    }
                }
            });

            // Agregar la fila de totales
            rows.push([{
                    text: 'TOTAL',
                    colSpan: 4,
                    alignment: 'center',
                    bold: true,
                    fillColor: '#fce300'
                }, {}, {}, {},
                {
                    text: `S/ ${formatNumber(totalAmortizacion)}`,
                    bold: true,
                    fillColor: '#fce300',
                    alignment: 'right'
                },
                {
                    text: `S/ ${formatNumber(totalSaldo)}`,
                    bold: true,
                    fillColor: '#fce300',
                    alignment: 'right'
                },
                {
                    text: '',
                    colSpan: 3,
                    fillColor: '#fce300'
                }, {}, {}
            ]);

            const documento = {
                pageSize: 'A4',
                pageOrientation: 'portrait',
                pageMargins: [40, 25, 25, 25],
                defaultStyle: {
                    fontSize: 6.8,
                },
                content: [{
                    columns: [{
                        image: logo,
                        width: 80,
                        alignment: 'left'
                    }, {
                        stack: [{
                            text: 'YONDA & GRUPO HUARACA E.I.R.L',
                            bold: true,
                            color: '#2c3e50'
                        }, {
                            text: 'RUC: 20609396866',
                            margin: [0, 2, 0, 0],
                            bold: true
                        }, ],
                        alignment: 'right',
                        margin: [10, 0, 0, 0]
                    }],
                    margin: [0, 0, 0, 10]
                }, {
                    text: 'HISTORIAL DE PAGOS',
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 0],
                    decoration: 'underline',
                    bold: true
                }, {
                    style: 'tableHistorial',
                    table: {
                        headerRows: 1,
                        widths: ['*', '*', '*', '*', '*', '*', 'auto', '*', '*'],
                        body: [
                            [{
                                text: '#',
                                style: 'tableHeader'
                            }, {
                                text: 'N° Cuota',
                                style: 'tableHeader'
                            }, {
                                text: 'Vencimiento',
                                style: 'tableHeader'
                            }, {
                                text: 'Fecha pago',
                                style: 'tableHeader'
                            }, {
                                text: 'Amortización',
                                style: 'tableHeader'
                            }, {
                                text: 'Saldo',
                                style: 'tableHeader'
                            }, {
                                text: 'Medio',
                                style: 'tableHeader'
                            }, {
                                text: 'Concepto',
                                style: 'tableHeader'
                            }, {
                                text: 'Transacción',
                                style: 'tableHeader'
                            }],
                            ...rows
                        ]
                    },
                    layout: {
                        hLineWidth: function(i, node) {
                            return (i === 0 || i === node.table.body.length) ? 0.8 : 0.8;
                        },
                        vLineWidth: function(i, node) {
                            return (i === 0 || i === node.table.widths.length) ? 0.8 : 0.8;
                        },
                        hLineColor: function(i, node) {
                            return (i === 0 || i === node.table.body.length) ? '#000' : '#000';
                        },
                        vLineColor: function(i, node) {
                            return (i === 0 || i === node.table.widths.length) ? '#000' : '#000';
                        },
                        fillColor: function(rowIndex) {
                            return rowIndex === 0 ? '#e0e0e0' : null;
                        }
                    }
                }],
                styles: {
                    subheader: {
                        bold: true,
                        alignment: 'center'
                    },
                    tableHeader: {
                        bold: true,
                        fillColor: '#e0e0e0',
                        alignment: 'center'
                    },
                    tableHistorial: {
                        margin: [0, 5, 0, 0]
                    }
                }
            };

            pdfMake.createPdf(documento).open();
        });



        // Mostrar página específica
        function showPage(page) {
            currentPage = page;
            localStorage.setItem('pageHistorialPago', page);
            const rows = document.querySelectorAll('#tabla-body tr:not(.no-results)');
            rows.forEach(row => row.style.display = 'none');

            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, totalItems);

            for (let i = startIndex; i < endIndex; i++) {
                if (rows[i]) rows[i].style.display = '';
            }

            // Actualizar información de paginación
            const startItem = startIndex + 1;
            const endItem = endIndex;
            document.querySelector('.card-footer .text-muted').innerHTML =
                `Mostrando <span class="fw-bold">${startItem}-${endItem}</span> de <span class="fw-bold">${totalItems}</span> registros`;

            // Actualizar paginación
            document.querySelectorAll('#paginacion .page-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`#paginacion .page-link[data-page="${page}"]`)?.parentElement.classList.add('active');

            document.getElementById('prev-page').classList.toggle('disabled', page === 1);
            document.getElementById('next-page').classList.toggle('disabled', page === totalPages);
        }


        // Manejar paginación
        document.getElementById('paginacion').addEventListener('click', function(e) {
            e.preventDefault();
            const target = e.target.closest('.page-link');
            if (!target || target.parentElement.classList.contains('disabled')) return;

            let newPage = currentPage;
            if (target.parentElement.id === 'prev-page') {
                newPage = currentPage - 1;
            } else if (target.parentElement.id === 'next-page') {
                newPage = currentPage + 1;
            } else {
                newPage = parseInt(target.dataset.page);
            }

            if (newPage !== currentPage) {
                showPage(newPage);
            }
        });

        // Mostrar primera página
        if (totalItems > 0) showPage(currentPage);

        localStorage.removeItem('page');



        // Manejar comprobantes
        document.querySelectorAll('.ver-comprobante-img').forEach(btn => {
            btn.addEventListener('click', function() {
                const imgSrc = this.dataset.img;
                const imgElement = document.getElementById('imagenComprobante');
                const downloadBtn = document.getElementById('descargarComprobante');

                // Asignar la URL segura al src de la imagen
                imgElement.src = imgSrc;
                imgElement.style.display = 'block';
                downloadBtn.href = imgSrc;

                const modal = new bootstrap.Modal(document.getElementById('modalcomprobante'));
                modal.show();
            });
        });


        // Evento para limpiar la paginación al cambiar de vista
        document.querySelector('a[href="/caja/"]').addEventListener('click', function() {
            localStorage.removeItem('pageHistorialPago');
            showPage(1);
        });
    });
</script>

