<?php include __DIR__ . '/../layout/header.php'; ?>
<link rel="stylesheet" href="/assets/css/cronograma-contrato.css">

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert" style="border-left: 4px solid #3498db; border-radius: 0 8px 8px 0;">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cronograma de Pagos</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="/caja/" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-1"></i> Lista
                </a>
                <button class="btn btn-danger btn-sm ms-2" id="btnImprimir">
                    <i class="fa-regular fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
    </div>

    <!-- CRONOGRAMA -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-cronograma">
                <div class="card-header card-header-cronograma">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Cronograma de Pagos</h5>
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control" placeholder="Buscar cuota..." id="inputBuscar">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover cronograma-table mb-0" id="tabla-cronograma">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Vencimiento</th>
                                    <th><i class="fas fa-percentage me-1"></i> Interés</th>
                                    <th><i class="fas fa-piggy-bank me-1"></i> Abono Capital</th>
                                    <th><i class="fas fa-money-bill-wave me-1"></i> Valor Cuota</th>
                                    <th>Amortización</th>
                                    <th>Restante</th>
                                    <th><i class="fas fa-wallet me-1"></i> Saldo Capital</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-body">

                                <?php if (empty($cronograma)) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay datos para mostrar</td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $hoy = new DateTimeImmutable('today');
                                    foreach ($cronograma as $index => $fila) {
                                        $fecha_cuota = new DateTimeImmutable($fila['fechapago']);
                                        $fecha_formateada = $fecha_cuota->format('d/m/Y');

                                        $vencido = ($hoy > $fecha_cuota);
                                        $dias_restantes = $hoy->diff($fecha_cuota)->days;

                                        $estado = 'Pendiente';
                                        $clase_estado = 'estado-pendiente';
                                        $icono = 'fa-clock';
                                        $texto_vencimiento = "Vence en $dias_restantes días";

                                        $estado = $fila['estado'];
                                        $clase_estado = 'estado-pendiente';
                                        $icono = 'fa-clock';
                                        $texto_vencimiento = "Vence en $dias_restantes días";

                                        if ($estado === 'Pagado') {
                                            $clase_estado = 'estado-pagado';
                                            $icono = 'fa-check-circle';
                                            $texto_vencimiento = "Pagado";
                                        } elseif ($hoy > $fecha_cuota) {
                                            $clase_estado = 'estado-vencido';
                                            $icono = 'fa-exclamation-triangle';
                                            $texto_vencimiento = "Venció hace $dias_restantes días";
                                        }


                                        $style_display = ($index < 10) ? '' : 'style="display:none;"';
                                        $data_page = ceil(($index + 1) / 10);
                                    ?>
                                        <tr data-page="<?= $data_page ?>" <?= $style_display ?>>
                                            <td><span class="badge bg-primary badge-cuota"><?= $fila['numcuota'] ?></span></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?= $fecha_formateada ?></span>
                                                    <small class="text-muted"><?= $texto_vencimiento ?></small>
                                                </div>
                                            </td>
                                            <td>S/ <?= number_format($fila['interes'], 2) ?></td>
                                            <td>S/ <?= number_format($fila['abonocapital'], 2) ?></td>
                                            <td>S/ <?= number_format($fila['valorcuota'], 2) ?></td>
                                            <td><?= number_format($fila['amortizacion'], 2) ?></td>
                                            <td><?= number_format($fila['saldorestante'], 2) ?></td>
                                            <td>S/ <?= number_format($fila['saldocapital'], 2) ?></td>
                                            <td>
                                                <span class="<?= $clase_estado ?>">
                                                    <i class="fas <?= $icono ?> me-1"></i> <?= $estado ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-pagar btn-sm text-white"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalPago"
                                                    data-cuota="<?= $fila['numcuota'] ?>"
                                                    data-valorcuota="<?= $fila['valorcuota'] ?>"
                                                    data-abonocapital="<?= $fila['abonocapital'] ?>"
                                                    data-penalidad="<?= $fila['penalidad'] ?>">
                                                    <i class="fa-solid fa-dollar-sign me-1"></i> Pagar
                                                </button>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-body-tertiary text-body-secondary">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted" id="info-paginacion">
                            Mostrando <span class="fw-bold">1-10</span> de
                            <span class="fw-bold"><?= count($cronograma) ?></span> cuotas
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0" id="paginacion">
                                <li class="page-item disabled" id="prev-page">
                                    <a class="page-link" href="#" data-page="1" tabindex="-1" aria-disabled="true">Anterior</a>
                                </li>

                                <?php for ($p = 1; $p <= ceil(count($cronograma) / 10); $p++): ?>
                                    <li class="page-item <?= $p == 1 ? 'active' : '' ?>">
                                        <a class="page-link" href="#" data-page="<?= $p ?>"><?= $p ?></a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item" id="next-page">
                                    <a class="page-link" href="#" data-page="2">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>




<!-- Modal de Pago-->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="modalPagoLabel">
                    <i class="fas fa-cash-register me-2"></i> Procesar Pago
                </h5>
                <button type="button" class="btn-close btn-close-primary" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Está a punto de registrar el pago de la cuota seleccionada.
                </div>

                <form id="formPago">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Detalles de la Deuda
                                    </h6>
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Saldo Cuota</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary"><i
                                                    class="fas fa-dollar-sign text-primary"></i></span>
                                            <input type="text" class="form-control bg-body-tertiary" value="" disabled
                                                id="saldocuota">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Penalidad</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary"><i
                                                    class="fas fa-exclamation-circle text-danger"></i></span>
                                            <input type="text" class="form-control bg-body-tertiary" disabled
                                                id="penalidad">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label small text-muted">Total Deuda</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary"><i
                                                    class="fas fa-calculator text-success"></i></span>
                                            <input type="text" class="form-control bg-body-tertiary fw-bold " disabled
                                                id="total-deuda">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-hand-holding-usd me-2"></i>Detalles del Pago
                                    </h6>
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Amortización</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary"><i
                                                    class="fas fa-coins text-warning"></i></span>
                                            <input type="text" class="form-control bg-body-tertiary" id="amortizacion">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Saldo Restante</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary"><i
                                                    class="fas fa-wallet text-info"></i></span>
                                            <input type="text" class="form-control bg-body-tertiary" readonly
                                                id="saldo-restante">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label small text-muted">Fecha de Pago</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary"><i
                                                    class="far fa-calendar-alt text-secondary"></i></span>
                                            <input type="date" class="form-control" value="<?= date('Y-m-d') ?>"
                                                id="fecha-pago">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-info">
                                        <i class="fas fa-credit-card me-2"></i>Método de Pago
                                    </h6>
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Modalidad</label>
                                        <select class="form-select" id="metodo-pago">
                                            <option value="efectivo">Efectivo</option>
                                            <option value="yape">Yape</option>
                                            <option value="transferencia">Transferencia Bancaria</option>
                                            <option value="plin">Plin</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Número de Operación</label>
                                        <input type="text" class="form-control"
                                            placeholder="Opcional para transferencias" id="numoperacion">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-secondary h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-secondary">
                                        <i class="fas fa-sticky-note me-2"></i>Observaciones
                                    </h6>
                                    <textarea class="form-control" rows="4"
                                        placeholder="Ingrese cualquier observación..." id="observaciones"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success" id="btn-form-submit">
                            <i class="fas fa-check-circle me-1"></i> Confirmar Pago
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {



        // Variables globales
        const itemsPerPage = 10;
        const totalItems = <?= count($cronograma) ?>;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        let currentPage = 1;



        // RELLENAR EL FORMUALRIO CON LOS DATOS DE LA DB.
        document.querySelectorAll('.btn-pagar').forEach(btn => {
            btn.addEventListener('click', function() {
                const valorCuota = parseFloat(this.dataset.valorcuota);
                const abonoCapital = parseFloat(this.dataset.abonocapital);
                const penalidad = parseFloat(this.dataset.penalidad) || 0;


                // Calcular total deuda
                const totalDeuda = valorCuota + penalidad;

                // Llenar campos en el modal
                document.getElementById('saldocuota').value = valorCuota.toFixed(2);
                document.getElementById('penalidad').value = penalidad.toFixed(2);
                document.getElementById('total-deuda').value = totalDeuda.toFixed(2);
                document.getElementById('amortizacion').value = '';
                document.getElementById('saldo-restante').value = totalDeuda.toFixed(2);

            });
        });




        // ----------------------------------------------------------------------------------















        // Mostrar página específica
        function showPage(page) {
            currentPage = page;

            const rows = document.querySelectorAll('#tabla-body tr');
            rows.forEach(row => row.style.display = 'none');

            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, totalItems);

            for (let i = startIndex; i < endIndex; i++) {
                if (rows[i]) rows[i].style.display = '';
            }

            // Actualizar información de paginación
            const startItem = startIndex + 1;
            const endItem = endIndex;
            document.getElementById('info-paginacion').innerHTML =
                `Mostrando <span class="fw-bold">${startItem}-${endItem}</span> de <span class="fw-bold">${totalItems}</span> cuotas`;

            // Actualizar estado de los botones de paginación
            document.querySelectorAll('#paginacion .page-item').forEach(item => {
                item.classList.remove('active');
            });

            const activeLink = document.querySelector(`#paginacion .page-link[data-page="${page}"]`);
            if (activeLink) activeLink.parentElement.classList.add('active');

            document.getElementById('prev-page').classList.toggle('disabled', page === 1);
            document.getElementById('next-page').classList.toggle('disabled', page === totalPages);

            if (page < totalPages) {
                document.querySelector('#next-page .page-link').dataset.page = page + 1;
            }
            if (page > 1) {
                document.querySelector('#prev-page .page-link').dataset.page = page - 1;
            }
        }


        // Manejar clic en paginación
        document.getElementById('paginacion').addEventListener('click', function(e) {
            const target = e.target.closest('.page-link');
            if (!target || target.parentElement.classList.contains('disabled')) return;

            e.preventDefault();
            const newPage = parseInt(target.dataset.page);
            if (newPage && newPage !== currentPage) {
                showPage(newPage);
            }
        });




        // Búsqueda por número de cuota
        document.getElementById('inputBuscar').addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();

            if (searchTerm === '') {
                showPage(currentPage);
                return;
            }

            const rows = document.querySelectorAll('#tabla-body tr');
            let found = false;

            rows.forEach(row => {
                const cell = row.cells[0]; // Primera celda con el número si busco '1' - solo mostrara los que tienen '1' a la izquierda
                const cuotaNum = cell.textContent.trim().toLowerCase(); // Traer el texto
                console.log('CUOTANUM:', cuotaNum);

                if (cuotaNum.includes(searchTerm)) {
                    row.style.display = '';
                    found = true;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('info-paginacion').innerHTML = found ?
                `Mostrando resultados para: <span class="fw-bold">${searchTerm}</span>` :
                `No se encontraron resultados para: <span class="fw-bold">${searchTerm}</span>`;
        });


        document.getElementById('btnImprimir').addEventListener('click', function() {
            // Mostrar todas las filas
            document.querySelectorAll('#tabla-body tr').forEach(row => row.style.display = '');

            // Clonar tabla
            const originalTable = document.getElementById('tabla-cronograma');
            const cloneTable = originalTable.cloneNode(true);

            // Eliminar columna "Acciones"
            cloneTable.querySelectorAll('thead tr th:last-child').forEach(th => th.remove());
            cloneTable.querySelectorAll('tbody tr').forEach(tr => {
                tr.removeChild(tr.lastElementChild);
                tr.querySelectorAll('td').forEach(td => {
                    td.querySelectorAll('i').forEach(icon => icon.remove());
                });
            });

            // Quitar íconos en encabezados
            cloneTable.querySelectorAll('thead th').forEach(th => {
                th.querySelectorAll('i').forEach(icon => icon.remove());
            });


            // Aplicar estilo al clonado
            cloneTable.classList.remove('table-hover');
            cloneTable.classList.add('table', 'table-bordered', 'table-sm');

            // Aplicar estilos mejorados
            cloneTable.style.width = '100%';
            cloneTable.style.borderCollapse = 'collapse';
            cloneTable.querySelectorAll('th, td').forEach(cell => {
                cell.style.border = '1px solid #000';
                cell.style.padding = '6px 8px';
                cell.style.fontSize = '12px';
                cell.style.textAlign = 'center';
            });
            cloneTable.querySelectorAll('thead').forEach(thead => {
                thead.style.backgroundColor = '#f0f0f0';
            });

            // Crear contenedor limpio
            const cleanContainer = document.createElement('div');
            cleanContainer.style.padding = '30px';
            cleanContainer.innerHTML = `
            <div style="text-align:center; margin-bottom: 20px;">
                <h1 style="margin: 0; font-size: 20px;">Cronograma de Pagos</h1>
                <p style="margin: 5px 0; font-size: 12px;">Generado el: ${new Date().toLocaleString()}</p>
            </div>
        `;
            cleanContainer.appendChild(cloneTable);


            const options = {
                margin: 0.5,
                filename: 'cronograma_pagos.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 3
                },
                jsPDF: {
                    unit: 'in',
                    format: 'letter',
                    orientation: 'landscape'
                }
            };

            html2pdf().set(options).from(cleanContainer).save().then(() => {
                if (typeof showPage === 'function') showPage(1);
            });
        });


        showPage(1);
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>