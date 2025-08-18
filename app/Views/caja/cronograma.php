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
                <button class="btn btn-danger btn-sm ms-2" id="btn-pdf">
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
                    <div class="table-responsive" id="area-pdf">
                        <table class="table table-hover cronograma-table mb-0" id="tabla-cronograma">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Vencimiento</th>
                                    <th> Interés</th>
                                    <th> Abono Capital</th>
                                    <th></i> Valor Cuota</th>
                                    <th>Amortización</th>
                                    <th>Restante</th>
                                    <th></i> Saldo Capital</th>
                                    <th width="188">Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-body" data-total-items="<?= count($cronograma) ?>">
                                <?php if (empty($cronograma)) : ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No hay datos para mostrar</td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $hoy = new DateTimeImmutable('today');
                                    $cuotaHabilitada = false;

                                    foreach ($cronograma as $index => $fila) {
                                        $fecha_cuota = new DateTimeImmutable($fila['fechapago']);
                                        $fecha_formateada = $fecha_cuota->format('d/m/Y');
                                        $estado = strtolower(trim($fila['estado']));
                                        $amortizacion = floatval($fila['amortizacion']);

                                        $dias_restantes = $hoy->diff($fecha_cuota)->days;
                                        $texto_vencimiento = '';
                                        $clase_estado = '';
                                        $icono = '';
                                        $clase_fila = '';

                                        $fila_deshabilitada = false;
                                        $es_sin_pago = ($amortizacion == 0);

                                        // Lógica para asignar las clases y el texto
                                        if ($estado === 'pagado') {
                                            $clase_fila = 'bg-success table-success';
                                            $clase_estado = 'estado-pagado';
                                            $icono = 'fa-check-circle';
                                            $texto_vencimiento = 'Pagado';
                                            $fila_deshabilitada = true;
                                        } elseif (!$cuotaHabilitada) {
                                            // Primera cuota pendiente habilitada
                                            if ($hoy > $fecha_cuota) {
                                                $dias_vencido = $fecha_cuota->diff($hoy)->days;
                                                $texto_vencimiento = "Venció hace $dias_vencido días";
                                                $clase_estado = 'estado-vencido';
                                                $clase_fila = 'bg-danger table-danger';
                                                $icono = 'fa-exclamation-triangle';
                                            } else {
                                                $dias_faltantes = $hoy->diff($fecha_cuota)->days;
                                                $texto_vencimiento = "Vence en $dias_faltantes días";
                                                $clase_estado = ($es_sin_pago ? 'estado-sin-pago' : 'estado-pendiente');
                                                $icono = ($es_sin_pago ? 'fa-exclamation-triangle' : 'fa-clock');
                                                $clase_fila = 'fila-pendiente-pago table-warning';
                                            }

                                            $fila_deshabilitada = false;
                                            $cuotaHabilitada = true; // Solo esta cuota se habilita
                                        } else {
                                            // Futuras cuotas
                                            $clase_fila = 'fila-deshabilitada';
                                            $clase_estado = 'estado-pendiente';
                                            $icono = 'fa-lock';

                                            if ($hoy > $fecha_cuota) {
                                                $dias_vencido = $fecha_cuota->diff($hoy)->days;
                                                $texto_vencimiento = "Venció hace $dias_vencido días";
                                                $clase_estado = 'estado-vencido';
                                                $clase_fila = 'bg-danger table-danger';
                                                $icono = 'fa-exclamation-triangle';
                                            } else {
                                                $dias_faltantes = $hoy->diff($fecha_cuota)->days;
                                                $texto_vencimiento = "Vence en $dias_faltantes días";
                                            }
                                            $fila_deshabilitada = true;
                                        }

                                        $style_display = ($index < 10) ? '' : 'style="display:none;"';
                                        $data_page = ceil(($index + 1) / 10);
                                    ?>

                                        <tr data-page="<?= $data_page ?>"
                                            <?= $style_display ?>
                                            class="<?= $clase_fila ?>"
                                            <?= $fila_deshabilitada ? 'data-disabled="true"' : '' ?>>

                                            <td>
                                                <span class="badge <?= $estado === 'pagado' ? 'bg-light text-success' : ($es_sin_pago ? 'bg-secondary' : 'bg-primary') ?> badge-cuota">
                                                    <?= $fila['numcuota'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?= $fecha_formateada ?></span>
                                                    <small class="<?= $clase_estado === 'estado-vencido' ? 'text-danger fw-bold' : ($estado === 'pagado' ? 'text-success fw-bold' : 'text-muted') ?>">
                                                        <?= $texto_vencimiento ?>
                                                    </small>
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
                                                    <i class="fas <?= $icono ?> me-1"></i>
                                                    <?php
                                                    if ($estado === 'pagado') {
                                                        echo 'Pagado';
                                                    } elseif ($clase_estado === 'estado-vencido') {
                                                        echo 'Vencido';
                                                    } elseif ($es_sin_pago) {
                                                        echo 'Pendiente - sin pago';
                                                    } else {
                                                        echo 'Pendiente';
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($estado === 'pagado'): ?>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <span class="badge bg-light text-success fs-6 px-3 py-2">
                                                            <i class="fas fa-check-circle me-1"></i> Completado
                                                        </span>
                                                    </div>
                                                <?php elseif ($fila_deshabilitada): ?>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <span class="badge bg-light text-muted fs-6 px-3 py-2">
                                                            <i class="fas fa-lock me-1"></i> Bloqueado
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <button class="btn btn-pagar btn-sm text-white" id="btn-pagar"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalPago"
                                                        data-cuota="<?= $fila['numcuota'] ?>"
                                                        data-idcronograma="<?= $fila['idcronograma'] ?>"
                                                        data-valorcuota="<?= $fila['valorcuota'] ?>"
                                                        data-penalidad="<?= $fila['penalidad'] ?>"
                                                        data-saldo-cuota="<?= $fila['saldocuota_pendiente'] ?>"
                                                        data-saldorestante="<?= $fila['saldorestante'] ?>"
                                                        data-saldo-penalidad="<?= $fila['penalidad_pendiente'] ?>">
                                                        <i class="fa-solid fa-dollar-sign me-1"></i> Pagar
                                                    </button>
                                                <?php endif; ?>
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


<!-- MODAL DE PAGOS  -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" style="max-width: 750px;">
        <div class="modal-content shadow-lg">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPagoLabel">
                    <i class="fas fa-cash-register me-2"></i> Procesar Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4 ">
                <div class="alert alert-info py-2" role="alert">
                    <i class="fas fa-info-circle me-2"></i> <span id="numero-cuota" class="fw-bold">N° 25</span>
                </div>

                <form id="formPago" enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- Detalles de la Deuda  -->
                        <div class="col-md-5 col-lg-4">
                            <div class="card h-70">
                                <div class="card-body">
                                    <h6 class="card-title text-info fw-bold mb-3">
                                        <i class="fas fa-file-invoice-dollar me-2"></i> Detalles de la Deuda
                                    </h6>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold small text-muted">Saldo Cuota</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-primary fs-5"><i class="fas fa-dollar-sign"></i></span>
                                            <input type="text" class="form-control form-control-lg bg-light border-0 fw-bold" value="" disabled id="detalle-cuota">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-bold small text-muted">Penalidad</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-danger fs-5"><i class="fas fa-exclamation-circle"></i></span>
                                            <input type="text" class="form-control form-control-lg bg-light border-0 fw-bold" disabled id="detalle-penalidad">
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="mb-1">
                                        <label class="form-label fw-bold small text-muted">Total Deuda</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-success fs-5"><i class="fas fa-calculator"></i></span>
                                            <input type="text" class="form-control form-control-lg bg-light border-0 fw-bold text-success fs-5" disabled id="detalle-total-deuda">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles de Pago & Método de Pago -->
                        <div class="col-md-7 col-lg-8 d-flex flex-column gap-4">
                            <!-- Card de Detalles del Pago -->
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-success fw-bold mb-3">
                                        <i class="fas fa-hand-holding-usd me-2"></i> Detalles del Pago
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6" id="contenedor-tipo-pago">
                                            <label class="form-label small text-muted">Tipo de Pago</label>
                                            <select class="form-select" id="tipoPago" name="tipoPago"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Fecha de Pago</label>
                                            <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" name="fechapago" id="fechapago">
                                            <div class="invalid-feedback">La fecha de pago no puede estar vacía y no puede ser una fecha futura</div>
                                            <div class="valid-feedback">Fecha válida.</div>
                                        </div>
                                    </div>

                                    <div id="pagoCuota-group" class="col-12 mt-4">
                                        <hr class="my-3">
                                        <h6 class="fw-bold text-muted small"><i class="fas fa-coins text-warning me-2"></i> Pago de Cuota</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Monto de cuota</label>
                                                <input type="text" class="form-control" id="amortizacionCuota" name="amortizacionCuota">
                                                <div class="invalid-feedback" id="invalid-amortizacionCuota"></div>
                                                <div class="valid-feedback">Correcto.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Comprobante de Cuota</label>
                                                <input id="comprobanteCuota" class="form-control" type="file" name="comprobanteCuota" accept="image/*,.pdf">
                                            </div>
                                        </div>
                                    </div>

                                    <div id="pagoPenalidad-group" class="col-12 mt-4">
                                        <hr class="my-3">
                                        <h6 class="fw-bold text-muted small"><i class="fas fa-gavel text-danger me-2"></i> Pago de Penalidad</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Monto de penalidad</label>
                                                <input type="text" class="form-control" id="amortizacionPenalidad" name="amortizacionPenalidad">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted">Comprobante de Penalidad</label>
                                                <input id="comprobantePenalidad" class="form-control" type="file" name="comprobantePenalidad" accept="image/*,.pdf">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card de Método de Pago -->
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-success fw-bold mb-3">
                                        <i class="fas fa-credit-card me-2"></i> Método de Pago
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Modalidad</label>
                                            <select class="form-select" id="mediopago" name="mediopago">
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Yape">Yape</option>
                                                <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                                <option value="Plin">Plin</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3 hidden select-cuentas">
                                            <label class="form-label small text-muted">N°cuenta</label>
                                            <select class="form-select" id="idcuentapago" name="idcuentapago"></select>
                                        </div>
                                        <div class="col-md-6" id="group-numTransaccionCuota">
                                            <label class="form-label small text-muted">N° operación(Cuota)</label>
                                            <input type="text" class="form-control numeros-transacciones" name="numerotransaccion" placeholder="N° de operación 6654.." id="numerotransaccion" maxlength="30" minlength="6" pattern="[0-9]{6,30}"
                                                title="Solo números, entre 6 y 30 dígitos">
                                            <div class="invalid-feedback">Debe tener entre 6 y 30 dígitos numéricos.</div>
                                            <div class="valid-feedback">Número válido.</div>
                                        </div>
                                        <div class="hidden col-md-6" id="group-numTransaccionPenalidad">
                                            <label class="form-label small text-muted">N° operación(Penalidad)</label>
                                            <input type="text" class="form-control numeros-transacciones" name="numeroTransaccionPenalidad" placeholder="N° de operación 6654.." id="numerotransaccion-penalidad" maxlength="30" minlength="6" pattern="[0-9]{6,30}">
                                             <div class="invalid-feedback">Debe tener entre 6 y 30 dígitos numéricos.</div>
                                            <div class="valid-feedback">Número válido.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-warning fw-bold mb-3">
                                        <i class="fas fa-sticky-note me-2"></i> Observaciones
                                    </h6>
                                    <textarea class="form-control" rows="3" placeholder="Ingresa cualquier observación..." id="observacion" name="observacion"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer d-flex justify-content-end p-3 border-top">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-success px-4" id="btn-confirmar-pago" form="formPago">
                    <i class="fas fa-check-circle me-1"></i> Confirmar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<!-- USADO PARA GENERAR EL PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="/assets/js/cronograma.js" type="module"></script>
<!-- 
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ========================
        // 1. VARIABLES
        // ========================
        const config = {
            itemsPerPage: 10,
            totalItems: <?= count($cronograma) ?>,
        };
        config.totalPages = Math.ceil(config.totalItems / config.itemsPerPage);
        let currentPage = parseInt(localStorage.getItem('pageCronograma') || '1');
        let idCronogramaSeleccionado = null;
        let valorCuotaDeuda = 0;
        let valorPenalidadDeuda = 0;

        const TIPOS_PAGO = {
            soloCuota: 'soloCuota',
            soloPenalidad: 'soloPenalidad',
            ambas: 'ambas'
        };

        const MEDIOS_PAGO = {
            efectivo: 'Efectivo',
            yape: 'Yape',
            transferenciaBancaria: 'Transferencia Bancaria',
            plin: 'Plin'
        };

        const elements = {
            tablaBody: document.getElementById('tabla-body'),
            infoPaginacion: document.getElementById('info-paginacion'),
            paginacion: document.getElementById('paginacion'),
            inputBuscar: document.getElementById('inputBuscar'),
            btnPdf: document.getElementById('btn-pdf'),
            btnConfirmarPago: document.getElementById('btn-confirmar-pago'),
            modalPago: new bootstrap.Modal(document.getElementById('modalPago')),
            formPago: document.getElementById('formPago'),
            numeroCuotaDisplay: document.getElementById('numero-cuota'),
            detalleCuotaInput: document.getElementById('detalle-cuota'),
            detallePenalidadInput: document.getElementById('detalle-penalidad'),
            detalleTotalDeudaInput: document.getElementById('detalle-total-deuda'),
            tipoPagoSelect: document.getElementById('tipoPago'),
            contenedorTipoPago: document.getElementById('contenedor-tipo-pago'),
            pagoCuotaGroup: document.getElementById('pagoCuota-group'),
            pagoPenalidadGroup: document.getElementById('pagoPenalidad-group'),
            amortizacionCuotaInput: document.getElementById('amortizacionCuota'),
            amortizacionPenalidadInput: document.getElementById('amortizacionPenalidad'),
            comprobanteCuotaInput: document.getElementById('comprobanteCuota'),
            comprobantePenalidadInput: document.getElementById('comprobantePenalidad'),
            medioPagoSelect: document.getElementById('mediopago'),
            selectCuentas: document.querySelector('.select-cuentas'),
            numeroCuentaSelect: document.getElementById('idcuentapago'),
            numeroTransaccionInput: document.getElementById('numerotransaccion'),
            fechaPagoInput: document.getElementById('fechapago'),
            observacionInput: document.getElementById('observacion'),
            contenedorInputMontoCuota: document.getElementById('contenedor-monto-cuota')
        };


        // ========================
        // 2. FUNCIONES 
        // ========================

        /**
         * Gestiona la paginación de la tabla.
         * @param {number} page La página a mostrar.
         */
        function showPage(page) {
            currentPage = page;
            localStorage.setItem('pageCronograma', currentPage);

            const rows = Array.from(elements.tablaBody.querySelectorAll('tr'));
            rows.forEach(row => row.style.display = 'none');

            const startIndex = (currentPage - 1) * config.itemsPerPage;
            const endIndex = Math.min(startIndex + config.itemsPerPage, config.totalItems);

            for (let i = startIndex; i < endIndex; i++) {
                if (rows[i]) rows[i].style.display = '';
            }

            updatePagina(startIndex, endIndex);
        }

        /**
         * Actualiza los elementos de la interfaz de usuario de paginación.
         * @param {number} startItem Índice inicial del elemento.
         * @param {number} endItem Índice final del elemento.
         */
        function updatePagina(startItem, endItem) {
            elements.infoPaginacion.innerHTML = `Mostrando <span class="fw-bold">${startItem + 1}-${endItem}</span> de <span class="fw-bold">${config.totalItems}</span> cuotas`;

            const pageItems = elements.paginacion.querySelectorAll('.page-item');
            pageItems.forEach(item => item.classList.remove('active'));

            const activeLink = elements.paginacion.querySelector(`.page-link[data-page="${currentPage}"]`);
            if (activeLink) activeLink.parentElement.classList.add('active');

            document.getElementById('prev-page').classList.toggle('disabled', currentPage === 1);
            document.getElementById('next-page').classList.toggle('disabled', currentPage === config.totalPages);

            if (currentPage < config.totalPages) {
                // Se puede ir a la siguiente página
                document.getElementById('next-page').querySelector('.page-link').dataset.page = currentPage + 1;
            }
            if (currentPage > 1) {
                // Se puede ir a la página anterior
                document.getElementById('prev-page').querySelector('.page-link').dataset.page = currentPage - 1;
            }
        }

        /**
         * Muestra u oculta los campos de pago en el modal según el tipo de pago seleccionado.
         */
        function updateSelectTipoPago() {
            const tipoPago = elements.tipoPagoSelect.value;
            const isCuotaVisible = tipoPago === TIPOS_PAGO.soloCuota || tipoPago === TIPOS_PAGO.ambas;
            const isPenalidadVisible = tipoPago === TIPOS_PAGO.soloPenalidad || tipoPago === TIPOS_PAGO.ambas;

            elements.pagoCuotaGroup.classList.toggle('d-none', !isCuotaVisible);
            elements.pagoPenalidadGroup.classList.toggle('d-none', !isPenalidadVisible);

            elements.amortizacionCuotaInput.disabled = !isCuotaVisible;
            elements.amortizacionPenalidadInput.disabled = !isPenalidadVisible;

            // La penalidad se paga completa, se hace de solo lectura
            if (isPenalidadVisible) {
                elements.amortizacionPenalidadInput.setAttribute('readonly', true);
                elements.amortizacionPenalidadInput.style.backgroundColor = '#f8f9fa';
            } else {
                elements.amortizacionPenalidadInput.removeAttribute('readonly');
                elements.amortizacionPenalidadInput.style.backgroundColor = '';
            }
        }


        /**
         * Carga las cuentas bancarias desde la API y las llena en el select.
         */
        async function cargarCuentasBancarias() {
            try {
                const res = await fetch('/api/numcuentaspagos');
                const data = await res.json();
                elements.numeroCuentaSelect.innerHTML = '<option value="">Selecciona una cuenta</option>';
                data.forEach(cuenta => {
                    elements.numeroCuentaSelect.innerHTML += `<option value="${cuenta.idcuentapago}">${cuenta.nombrecuenta}</option>`;
                });
            } catch (error) {
                console.error('Error al cargar cuentas:', error);
                showToast('Error al cargar cuentas bancarias.', 'ERROR', 2000);
            }
        }

        // Función para validar la longitud del número de transacción
        function validarNumeroTransaccion() {
            const numeroTransaccion = elements.numeroTransaccionInput.value.trim();
            const regex = /^\d{6,30}$/;
            return regex.test(numeroTransaccion);
        }

        function validarAmortizacionCuota(valorDeuda) {
            const monto = parseFloat(elements.amortizacionCuotaInput.value);


            if (isNaN(monto) || monto <= 0) {
                return false;
            }

            if (monto > valorDeuda) {
                return false;
            }

            return true;
        }

        function fechaVacia() {
            return !elements.fechaPagoInput.value;
        }


        function fechaEsFutura() {
            const fechaPagoValue = elements.fechaPagoInput.value;
            const fechaPagoDate = new Date(fechaPagoValue);
            const hoy = new Date();

            // Formatea ambas fechas a YYYY-MM-DD para una comparación precisa
            const fechaPagoFormato = fechaPagoDate.toISOString().slice(0, 10);
            const hoyFormato = hoy.toISOString().slice(0, 10);

            return fechaPagoFormato > hoyFormato;
        }

        /**
         * Valida los datos del formulario antes del envío.
         * @returns {boolean} True si los datos son válidos, de lo contrario, false.
         */
        function validarForm() {
            elements.numeroTransaccionInput.classList.remove('is-valid', 'is-invalid');
            if (!idCronogramaSeleccionado) {
                showToast('No se seleccionó ninguna cuota.', 'INFO', 1200);
                return false;
            }

            const tipoPago = elements.tipoPagoSelect.value;
            const amortizacionCuota = parseFloat(elements.amortizacionCuotaInput.value) || 0;
            const amortizacionPenalidad = parseFloat(elements.amortizacionPenalidadInput.value) || 0;

            // Lógica de validación
            const isCuota = tipoPago === TIPOS_PAGO.soloCuota || (tipoPago === TIPOS_PAGO.ambas && amortizacionCuota > 0);
            const isPenalidad = tipoPago === TIPOS_PAGO.soloPenalidad || (tipoPago === TIPOS_PAGO.ambas && amortizacionPenalidad > 0);

            if (isCuota) {
                const monto = parseFloat(elements.amortizacionCuotaInput.value);
                if (monto <= 0) {
                    showToast('Monto de cuota inválido.', 'INFO', 1200);
                    elements.amortizacionCuotaInput.classList.add('is-invalid');
                    return false;
                }

                if (!validarAmortizacionCuota(valorCuotaDeuda)) {
                    showToast(`La amortización no puede ser mayor a S/ ${valorCuotaDeuda.toFixed(2)}.`, 'INFO', 1200);
                    elements.amortizacionCuotaInput.classList.add('is-invalid');
                    return false;
                }
            }
            if (isPenalidad && (amortizacionPenalidad <= 0 || amortizacionPenalidad !== parseFloat(valorPenalidadDeuda))) {
                showToast(amortizacionPenalidad <= 0 ? 'Monto de penalidad inválido.' : `La penalidad debe pagarse completa: S/ ${valorPenalidadDeuda.toFixed(2)}.`, 'INFO', 1200);
                return false;
            }
            if (isCuota && elements.comprobanteCuotaInput.files.length === 0) {
                showToast('Debe adjuntar el comprobante de la cuota.', 'INFO', 1200);
                return false;
            }
            if (isPenalidad && elements.comprobantePenalidadInput.files.length === 0) {
                showToast('Debe adjuntar el comprobante de la penalidad.', 'INFO', 1200);
                return false;
            }


            if (elements.medioPagoSelect.value === MEDIOS_PAGO.transferenciaBancaria) {
                if (!elements.numeroCuentaSelect.value) {
                    showToast('Debes seleccionar una cuenta bancaria.', 'INFO', 1200);
                    return false;
                }

                const numeroTransaccion = elements.numeroTransaccionInput.value.trim();
                if (numeroTransaccion === '') {
                    showToast('El número de operación es obligatorio para transferencias.', 'INFO', 1500);
                    elements.numeroTransaccionInput.classList.add('is-invalid');
                    return false;
                }

                if (!validarNumeroTransaccion()) {
                    showToast('El número de operación es inválido. Debe contener solo números y tener entre 6 y 30 dígitos.', 'INFO', 2000);
                    elements.numeroTransaccionInput.classList.add('is-invalid');
                    return false;
                }
                elements.numeroTransaccionInput.classList.add('is-valid');
            }

            if (fechaVacia()) {
                showToast('La fecha de pago es obligatoria.', 'INFO', 1200);
                elements.fechaPagoInput.classList.add('is-invalid');
                return false;
            }

            if (fechaEsFutura()) {
                showToast('La fecha de pago no puede ser mayor a la actual.', 'INFO', 1200);
                elements.fechaPagoInput.classList.add('is-invalid');
                return false;
            }

            return true;
        }


        elements.numeroTransaccionInput.addEventListener('input', () => {
            elements.numeroTransaccionInput.classList.remove('is-valid', 'is-invalid');
            if (elements.numeroTransaccionInput.value.trim().length > 0) {
                if (validarNumeroTransaccion()) {
                    elements.numeroTransaccionInput.classList.add('is-valid');
                } else {
                    elements.numeroTransaccionInput.classList.add('is-invalid');
                }
            }
        });


        elements.amortizacionCuotaInput.addEventListener('input', () => {
            elements.amortizacionCuotaInput.classList.remove('is-valid', 'is-invalid');
            const monto = parseFloat(elements.amortizacionCuotaInput.value);

            // Valida si es un número y > 0
            if (!isNaN(monto) && monto > 0) {
                elements.amortizacionCuotaInput.classList.add('is-valid');
            } else if (elements.amortizacionCuotaInput.value.trim().length > 0) {
                // Muestra como inválido si el usuario está escribiendo un valor incorrecto
                elements.amortizacionCuotaInput.classList.add('is-invalid');
            }
        });

        elements.fechaPagoInput.addEventListener('change', () => {
            elements.fechaPagoInput.classList.remove('is-valid', 'is-invalid');

            if (fechaVacia() || fechaEsFutura()) {
                elements.fechaPagoInput.classList.add('is-invalid');
            } else {
                elements.fechaPagoInput.classList.add('is-valid');
            }
        });

        /**
         * Envía los datos del formulario
         */
        async function submitForm() {
            if (!confirm('¿Seguro de registrar el pago?')) return;
            elements.btnConfirmarPago.disabled = true;
            elements.btnConfirmarPago.classList.add('disabled', 'opacity-75');
            elements.btnConfirmarPago.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';

            const formData = new FormData(elements.formPago);
            formData.append('idcronograma', idCronogramaSeleccionado);
            if (elements.medioPagoSelect.value === MEDIOS_PAGO.transferenciaBancaria) {
                formData.append('idcuentapago', elements.numeroCuentaSelect.value);
            }

            try {
                const res = await fetch('/pago/cronograma', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showToast('Pago registrado correctamente.', 'SUCCESS', 1200);
                    elements.modalPago.hide();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.message || 'Error al registrar el pago.', 'WARNING', 2000);
                }
            } catch (err) {
                console.error('Error de red o del servidor:', err);
                showToast('Error de red o del servidor. Intente nuevamente.', 'ERROR', 2000);
            } finally {
                elements.btnConfirmarPago.disabled = false;
                elements.btnConfirmarPago.classList.remove('disabled', 'opacity-75');
                elements.btnConfirmarPago.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirmar Pago';
            }
        }

        /**
         * Filtra las filas de la tabla por el término de búsqueda.
         * @param {string} searchTerm El término de búsqueda.
         */
        function filterTable(searchTerm) {
            const rows = elements.tablaBody.querySelectorAll('tr');
            let found = false;
            rows.forEach(row => {
                const cuotaNum = row.cells[0].textContent.trim().toLowerCase();
                const showRow = cuotaNum.includes(searchTerm);
                row.style.display = showRow ? '' : 'none';
                if (showRow) found = true;
            });

            if (searchTerm === '') {
                showPage(currentPage);
            } else {
                elements.infoPaginacion.innerHTML = found ?
                    `Mostrando resultados para: <span class="fw-bold">${searchTerm}</span>` :
                    `No se encontraron resultados para: <span class="fw-bold">${searchTerm}</span>`;
            }
        }

        // ========================
        // 3. EVENT LISTENERS
        // ========================

        // Inicialización de la tabla
        showPage(currentPage);

        // Click en botones de pagar
        elements.tablaBody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-pagar');
            if (!btn) return;

            const saldoCuota = parseFloat(btn.dataset.saldoCuota) || 0;
            const saldoPenalidad = parseFloat(btn.dataset.saldoPenalidad) || 0;

            idCronogramaSeleccionado = btn.dataset.idcronograma;
            valorCuotaDeuda = saldoCuota;
            valorPenalidadDeuda = saldoPenalidad;

            elements.amortizacionCuotaInput.value = saldoCuota.toFixed(2);
            elements.amortizacionPenalidadInput.value = saldoPenalidad.toFixed(2);
            elements.detalleCuotaInput.value = saldoCuota.toFixed(2);
            elements.detallePenalidadInput.value = saldoPenalidad.toFixed(2);
            elements.detalleTotalDeudaInput.value = (saldoCuota + saldoPenalidad).toFixed(2);
            elements.numeroCuotaDisplay.textContent = `Está a punto de registrar el pago de la cuota N° ${btn.dataset.cuota}`;

            const mostrarSoloPenalidad = saldoCuota === 0 && saldoPenalidad > 0;
            const mostrarSoloCuota = saldoCuota > 0 && saldoPenalidad === 0;
            const mostrarSelectCompleto = saldoCuota > 0 && saldoPenalidad > 0;

            elements.contenedorTipoPago.classList.toggle('hidden', !mostrarSelectCompleto);

            if (mostrarSelectCompleto) {
                elements.tipoPagoSelect.innerHTML = `
            <option value='${TIPOS_PAGO.ambas}'>Cuota y Penalidad</option>
            <option value='${TIPOS_PAGO.soloCuota}'>Solo Cuota</option>
            <option value='${TIPOS_PAGO.soloPenalidad}'>Solo Penalidad</option>
        `;
                elements.tipoPagoSelect.value = TIPOS_PAGO.ambas;
            } else if (mostrarSoloPenalidad) {
                elements.tipoPagoSelect.innerHTML = `<option value='${TIPOS_PAGO.soloPenalidad}'>Solo Penalidad</option>`;
                elements.tipoPagoSelect.value = TIPOS_PAGO.soloPenalidad;
            } else if (mostrarSoloCuota) {
                elements.tipoPagoSelect.innerHTML = `<option value='${TIPOS_PAGO.soloCuota}'>Solo Cuota</option>`;
                elements.tipoPagoSelect.value = TIPOS_PAGO.soloCuota;
            }

            updateSelectTipoPago();
        });


        // Eventos del modal de pago
        elements.tipoPagoSelect.addEventListener('change', updateSelectTipoPago);

        elements.medioPagoSelect.addEventListener('change', (e) => {
            const isTransferencia = e.target.value === MEDIOS_PAGO.transferenciaBancaria;
            elements.selectCuentas.classList.toggle('hidden', !isTransferencia);
            if (isTransferencia) {
                cargarCuentasBancarias();
            } else {
                elements.numeroCuentaSelect.innerHTML = '';
            }
        });

        elements.formPago.addEventListener('submit', (e) => {
            e.preventDefault();
            if (validarForm()) {
                submitForm();

            }
        });



        // Eventos de paginación y búsqueda
        elements.paginacion.addEventListener('click', function(e) {
            const target = e.target.closest('.page-link');
            if (!target || target.parentElement.classList.contains('disabled')) return;
            e.preventDefault();
            const newPage = parseInt(target.dataset.page);
            if (newPage && newPage !== currentPage) {
                showPage(newPage);
            }
        });

        elements.inputBuscar.addEventListener('input', (e) => {
            filterTable(e.target.value.trim().toLowerCase());
        });

        // Evento para limpiar la paginación al cambiar de vista
        document.querySelector('a[href="/caja/"]').addEventListener('click', function() {
            localStorage.removeItem('pageCronograma');
            elements.inputBuscar.value = '';
            showPage(1);
        });

        // Generar PDF
        elements.btnPdf.addEventListener('click', () => {
            showToast('GENERANDO EL PDF.....', 'INFO', 2200);
            setTimeout(() => {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF('landscape', 'mm', 'a4');

                doc.setFontSize(18);
                doc.text("Cronograma de Pagos", doc.internal.pageSize.getWidth() / 2, 15, {
                    align: 'center'
                });

                const fechaHora = new Date().toLocaleDateString();
                doc.setFontSize(11);
                doc.text(`FECHA: ${fechaHora}`, doc.internal.pageSize.getWidth() - 10, 22, {
                    align: 'right'
                });

                const head = [
                    ["#", "Fecha Vencimiento", "Interés", "Abono Capital", "Valor Cuota", "Amortización", "Restante", "Saldo Capital", "Estado"]
                ];
                const rows = Array.from(elements.tablaBody.querySelectorAll('tr')).map(tr => {
                    const tds = tr.querySelectorAll('td');
                    return [
                        tds[0].innerText.trim(),
                        tds[1].querySelector('span.fw-bold')?.innerText.trim() || '',
                        tds[2].innerText.trim(),
                        tds[3].innerText.trim(),
                        tds[4].innerText.trim(),
                        tds[5].innerText.trim(),
                        tds[6].innerText.trim(),
                        tds[7].innerText.trim(),
                        tds[8].innerText.trim().split('-')[0]
                    ];
                }).filter(row => row.length === 9);

                doc.autoTable({
                    head,
                    body: rows,
                    startY: 25,
                    styles: {
                        fontSize: 10,
                        halign: 'center'
                    },
                    headStyles: {
                        fillColor: [200, 200, 200],
                        textColor: 20,
                        fontStyle: 'bold'
                    },
                    margin: {
                        left: 10,
                        right: 10
                    }
                });

                doc.save('cronograma_pagos.pdf');
                showToast('PDF GENERADO', 'SUCCESS', 2200);
            }, 3000);
        });

        // localStorage.removeItem('currentPage');
    });
</script> -->
<?php include __DIR__ . '/../layout/footer.php'; ?>