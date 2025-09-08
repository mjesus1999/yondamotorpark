<?php include __DIR__ . '/../layout/header.php'; ?>


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

    <!-- Tarjeta principal -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold ">Detalle de Pagos</h5>
                <span class="badge bg-primary">
                    Total: <?= count($pagos) ?> registros
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <div id="area-pdf">
                    <table class="table table-hover table-striped mb-0 bg-body-tertiary" id="tabla-historial-pagos">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th width="80">N° Cuota</th>
                                <th>Vencimiento</th>
                                <th>Fecha pago</th>
                                <th>Amortización</th>
                                <th>Saldo</th>
                                <th width="100">Medio</th>
                                <th>Concepto</th>
                                <th>Transacción</th>
                                <th>Observaciones</th>
                                <th class="no-imprimir" width="150">Comprobante</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-body">
                            <?php if (empty($pagos)) : ?>
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No hay pagos registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($pagos as $pago) : ?>
                                    <tr>
                                        <td class="text-muted"><?= htmlspecialchars($numeroFila++) ?></td>
                                        <td><?= htmlspecialchars($pago['numcuota']) ?></td>
                                        <td>
                                            <span class="badge bg-info text-white">
                                                <?= htmlspecialchars($pago['fecha_vencimiento']) ?>
                                            </span>
                                        </td>

                                        <td> <span class="badge bg-info text-white">
                                                <?= htmlspecialchars($pago['fecha_pago']) ?>
                                            </span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($pago['amortizacion']) ?></td>
                                        <td class=""><?= htmlspecialchars($pago['saldorestante'] ?? '') ?></td>
                                        <td>
                                            <span class="badge bg-success text-white">
                                                <?= htmlspecialchars($pago['mediopago']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= trim($pago['tipo']) === 'Cuota' ? 'bg-primary text-white' : 'bg-danger text-white' ?>">

                                                <?= htmlspecialchars($pago['tipo']) ?>

                                            </span>
                                        </td>

                                        <td class="text-muted"><?= htmlspecialchars($pago['numerotransaccion'] ?? 'N/A')  ?></td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary ver-observacion"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalObservacion"
                                                data-observacion="<?= htmlspecialchars($pago['observacion'] ?? 'Sin observaciones') ?>" title="Ver observación">
                                                <i class="fas fa-eye"></i> Detalle
                                            </button>
                                        </td>

                                        <td class="no-imprimir">
                                            <?php if (!empty($pago['comprobante'])): ?>
                                                <?php $esPdf = strtolower(pathinfo($pago['comprobante'], PATHINFO_EXTENSION)) === 'pdf'; ?>
                                                <?php
                                                $urlSegura = "/archivos/" . htmlspecialchars($pago['comprobante']);
                                                ?>
                                                <?php if ($esPdf): ?>
                                                    <a href="<?= $urlSegura ?>" target="_blank"
                                                        class="btn btn-sm btn-danger" title="Ver comprobante">
                                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                                    </a>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-sm btn-primary ver-comprobante-img"
                                                        data-img="<?= htmlspecialchars($urlSegura) ?>" title="Ver comprobante">
                                                        <i class="fas fa-image me-1"></i>Comprobante
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pie de tabla con paginación -->
        <div class="card-footer bg-white border-top bg-body-tertiary">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="mb-2 mb-md-0 text-muted">
                    Mostrando <span class="fw-bold">1-<?= min(10, count($pagos)) ?></span> de
                    <span class="fw-bold"><?= count($pagos) ?></span> registros
                </div>

                <nav aria-label="Paginación">
                    <ul class="pagination pagination-sm mb-0" id="paginacion">
                        <li class="page-item disabled" id="prev-page">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <?php for ($p = 1; $p <= ceil(count($pagos) / 10); $p++): ?>
                            <li class="page-item <?= $p == 1 ? 'active' : '' ?>">
                                <a class="page-link" href="#" data-page="<?= $p ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item" id="next-page">
                            <a class="page-link" href="#">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal para comprobantes -->
<div class="modal fade" id="modalcomprobante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" defer></script>


<script>
    document.addEventListener('DOMContentLoaded', () => {

        const btnPDF = document.querySelector('#btn-pdf');
        const itemsPerPage = 10;
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


            const logo = "data:image/png;base64,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";


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
                    fontSize: 7.2,
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
                    margin: [0, 0, 0, 10],
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
                console.log("Ruta segura del comprobante:", imgSrc); // Para depuración

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

    // localStorage.removeItem('currentPage');
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>