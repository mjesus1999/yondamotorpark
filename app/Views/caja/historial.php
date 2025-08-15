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
                                <th class="no-imprimir" width="150">Comprobante</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-body">
                            <?php if (empty($pagos)) : ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No hay pagos registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($pagos as $pago) : ?>
                                    <tr>
                                        <td class="text-muted"><?= htmlspecialchars($numeroFila++) ?></td>
                                        <td><?= htmlspecialchars($pago['numcuota']) ?></td>
                                        <td><?= htmlspecialchars($pago['fecha_vencimiento']) ?></td>
                                        <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
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
                                        <td class="text-center no-imprimir">
                                            <?php if (!empty($pago['comprobante'])): ?>
                                                <?php $esPdf = strtolower(pathinfo($pago['comprobante'], PATHINFO_EXTENSION)) === 'pdf'; ?>
                                                <?php if ($esPdf): ?>
                                                    <a href="<?= htmlspecialchars($pago['comprobante']) ?>" target="_blank"
                                                        class="btn btn-sm btn-danger">
                                                        <i class="fas fa-file-pdf me-1"></i> PDF
                                                    </a>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-sm btn-primary ver-comprobante-img"
                                                        data-img="<?= htmlspecialchars($pago['comprobante']) ?>">
                                                        <i class="fas fa-image me-1"></i> Comprobante
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>




<script>
    document.addEventListener('DOMContentLoaded', () => {

        const btnPDF = document.querySelector('#btn-pdf');
        const itemsPerPage = 10;
        const totalItems = <?= count($pagos) ?>;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        let currentPage = 1;

        btnPDF.addEventListener('click', async () => {
            showToast('GENERANDO EL PDF.....', 'INFO', 3000);

            // Espera 3 segundos
            await new Promise(resolve => setTimeout(resolve, 3000));

            await generarPDF();

            showToast('PDF GENERADO', 'SUCCESS', 3000);
        });

        async function generarPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape', 'mm', 'a4');

            // Título
            doc.setFontSize(18);
            doc.text("Historial de Pagos", doc.internal.pageSize.getWidth() / 2, 15, {
                align: 'center'
            });

            const fechaHora = new Date().toLocaleDateString();
            doc.setFontSize(11);
            doc.text(`FECHA: ${fechaHora}`, doc.internal.pageSize.getWidth() - 10, 22, {
                align: 'right'
            });

            // Cabeceras para el PDF
            const head = [
                [
                    "#", "N° Cuota", "Vencimiento", "Fecha pago",
                    "Amortización", "Saldo", "Medio", "Concepto","Transacción"
                ]
            ];

            const headStyles = {
                fillColor: [200, 200, 200],
                textColor: 20,
                fontStyle: 'bold',
                halign: 'center',
                fontSize: 12,
            };

            const areaPDF = document.querySelector('#area-pdf');
            const columnasOcultas = areaPDF.querySelectorAll('.no-imprimir');
            columnasOcultas.forEach(col => col.style.display = 'none');

            // Mostrar todas las filas ocultas por paginación
            const todasFilas = document.querySelectorAll('#tabla-body tr');
            todasFilas.forEach(fila => fila.style.display = '');

            // Extraer datos de todas las filas visibles
            const rows = [];
            todasFilas.forEach(tr => {
                const tds = tr.querySelectorAll('td');
                if (tds.length >= 9) {
                    const fila = [
                        tds[0].innerText.trim(), // #
                        tds[1].innerText.trim(), // N° Cuota
                        tds[2].innerText.trim(), // Vencimiento
                        tds[3].innerText.trim(), // Fecha pago
                        tds[4].innerText.trim(), // Amortización
                        tds[5].innerText.trim(), // Saldo
                        tds[6].innerText.trim(), // Medio
                        tds[7].innerText.trim(),
                        tds[8].innerText.trim(), // Transacción
                    ];
                    rows.push(fila);
                }
            });

            // Crear tabla en el PDF
            doc.autoTable({
                head,
                body: rows,
                startY: 25,
                styles: {
                    fontSize: 10,
                    halign: 'center'
                },
                headStyles: {
                    headStyles
                },
                margin: {
                    left: 10,
                    right: 10
                },

                showHead: 'everyPage',
                pageBreak: 'auto'
            });


            columnasOcultas.forEach(col => col.style.display = '');
            if (typeof showPage === 'function' && typeof currentPage !== 'undefined') {
                showPage(currentPage);
            }

            doc.save('historial_pagos.pdf');
        }



        // Mostrar página específica
        function showPage(page) {
            currentPage = page;
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
        if (totalItems > 0) showPage(1);


        // Manejar comprobantes
        document.querySelectorAll('.ver-comprobante-img').forEach(btn => {
            btn.addEventListener('click', function() {
                const imgSrc = this.dataset.img;
                console.log(imgSrc);
                const imgElement = document.getElementById('imagenComprobante');
                const downloadBtn = document.getElementById('descargarComprobante');

                imgElement.src = imgSrc;
                imgElement.style.display = 'block';
                downloadBtn.href = imgSrc;

                const modal = new bootstrap.Modal(document.getElementById('modalcomprobante'));
                modal.show();
            });
        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>