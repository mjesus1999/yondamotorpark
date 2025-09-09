
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
                        <div class="input-group input-group-sm" style="width: 250px">
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
                                                        echo 'Pend. Sin pago';
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
                                                    <button class="btn btn-pagar btn-sm text-white bg-success" id="btn-pagar"
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


<!-- MODAL DE PAGOS -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content shadow-xl">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPagoLabel">
                    <i class="fas fa-cash-register me-2"></i> Procesar Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body ">
                <div class="alert alert-info py-2" role="alert">
                    <i class="fas fa-info-circle me-2"></i> <span id="numero-cuota" class="fw-bold">N° 25</span>
                </div>

                <form id="formPago" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card" style="min-height: 96%;">
                                <div class="card-body d-flex flex-column gap-5">
                                    <h6 class="card-title text-info fw-bold">
                                        <i class="fas fa-file-invoice-dollar me-2"></i> Detalles de la Deuda
                                    </h6>
                                    <div>
                                        <label class="form-label fw-bold small text-muted">Saldo Cuota</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-primary fs-5"><i class="fas fa-dollar-sign"></i></span>
                                            <input type="text" class="form-control form-control-lg bg-light border-0 fw-bold" value="" disabled id="detalle-cuota">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold small text-muted">Penalidad</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-danger fs-5"><i class="fas fa-exclamation-circle"></i></span>
                                            <input type="text" class="form-control form-control-lg bg-light border-0 fw-bold" disabled id="detalle-penalidad">
                                        </div>
                                    </div>
                                    <hr class="my-0">
                                    <div>
                                        <label class="form-label fw-bold small text-muted">Total Deuda</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0 text-success fs-5"><i class="fas fa-calculator"></i></span>
                                            <input type="text" class="form-control form-control-lg bg-light border-0 fw-bold text-success fs-5" disabled id="detalle-total-deuda">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-lg-8 d-flex flex-column">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-success fw-bold mb-3">
                                        <i class="fas fa-hand-holding-usd me-2"></i> Detalles del Pago
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-md-12 form-floating">
                                            <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" name="fechapago" id="fechapago">
                                            <label class="form-label small text-muted" for="fechapago">Fecha de Pago</label>
                                            <div class="invalid-feedback">La fecha de pago no puede estar vacía y no puede ser una fecha futura</div>
                                            <div class="valid-feedback">Fecha válida.</div>
                                        </div>
                                        <div class="col-md-12 form-floating" id="contenedor-tipo-pago">
                                            <select class="form-select" id="tipoPago" name="tipoPago"></select>
                                            <label class="form-label small text-muted" for="tipoPago">Tipo de Pago</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card" id="pagoCuota-group">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-warning mb-3">
                                        <i class="fas fa-coins text-warning me-2"></i> Pago de Cuota
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6 form-floating">
                                            <input type="text" class="form-control" id="amortizacionCuota" name="amortizacionCuota" placeholder="Monto de cuota">
                                            <label for="amortizacionCuota ">Monto de cuota</label>
                                            <div class="invalid-feedback" id="invalid-amortizacionCuota"></div>
                                            <div class="valid-feedback">Correcto.</div>
                                        </div>
                                        <div class="col-md-6 form-floating" id="group-medioPagoCuota">
                                            <select class="form-select" id="mediopago" name="mediopago">
                                                <option value="">Seleccione un medio de pago</option>
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Yape">Yape</option>
                                                <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                                <option value="Plin">Plin</option>
                                            </select>
                                            <label for="mediopago">Modalidad (cuota)</label>
                                        </div>
                                        <div class="col-md-6 form-floating" id="group-numTransaccionCuota">
                                            <input type="text" class="form-control numeros-transacciones" name="numerotransaccion" placeholder="N° de operación 6654.." id="numerotransaccion" maxlength="30" minlength="6" pattern="[0-9]{6,30}" title="Solo números, entre 6 y 30 dígitos">
                                            <label for="numerotransaccion">N° operación</label>
                                            <div class="invalid-feedback">Debe tener entre 6 y 30 dígitos numéricos.</div>
                                            <div class="valid-feedback">Número válido.</div>
                                        </div>
                                        <div class="col-md-6 form-floating" id="group-comprobanteCuota">
                                            <input id="comprobanteCuota" class="form-control" type="file" name="comprobanteCuota" accept="image/*,.pdf">
                                            <label for="comprobanteCuota" class="form-label small text-muted">Comprobante de Cuota</label>
                                            <div class="invalid-feedback">Ingrese un comprobante.</div>
                                            <div class="valid-feedback">Comprobante válido</div>
                                        </div>
                                        <div class="col-md-12 form-floating select-cuentas hidden" id="group-idcuentapago">
                                            <select class="form-select" id="idcuentapago" name="idcuentapago"></select>
                                            <label for="idcuentapago">N° cuenta</label>
                                            <div class="invalid-feedback">Seleccione un número de cuenta.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card" id="pagoPenalidad-group">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-danger mb-3">
                                        <i class="fas fa-gavel text-danger me-2"></i> Pago de Penalidad
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6 form-floating" >
                                            <input type="text" class="form-control" id="amortizacionPenalidad" name="amortizacionPenalidad">
                                            <label class="form-label small text-muted" for="amortizacionPenalidad">Monto</label>
                                        </div>
                                        <div class="col-md-6 form-floating" >
                                            <input id="comprobantePenalidad" class="form-control" type="file" name="comprobantePenalidad" accept="image/*,.pdf">
                                            <label class="form-label small text-muted" id="comprobantePenalidad">Comprobante de Penalidad</label>
                                            <div class="invalid-feedback">Ingrese un comprobante.</div>
                                            <div class="valid-feedback">Comprobante válido</div>
                                        </div>
                                        <div class="col-md-6 form-floating" id="group-medioPagoPenalidad">
                                            <select class="form-select" id="mediopagopenalidad" name="mediopagopenalidad">
                                                <option value="">Seleccione un medio de pago</option>
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Yape">Yape</option>
                                                <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                                <option value="Plin">Plin</option>
                                            </select>
                                            <label class="form-label small text-muted" for="mediopagopenalidad">Modalidad</label>
                                        </div>
                                        <div class="col-md-6 form-floating" id="group-numTransaccionPenalidad">
                                            <input type="text" class="form-control numeros-transacciones" name="numeroTransaccionPenalidad" placeholder="N° de operación 6654.." id="numerotransaccion-penalidad" maxlength="30" minlength="6" pattern="[0-9]{6,30}">
                                            <label class="form-label small text-muted" for="numerotransaccion-penalidad">N° operación</label>
                                            <div class="invalid-feedback">Debe tener entre 6 y 30 dígitos numéricos.</div>
                                            <div class="valid-feedback">Número válido.</div>
                                        </div>
                                        <div class="col-md-12 hidden form-floating" id="group-select-numero-cuenta">
                                            <select class="form-select" id="idcuentapagopenalidad" name="idcuentapagopenalidad"></select>
                                            <label class="form-label small text-muted" for="idcuentapagopenalidad">N° cuenta</label>
                                            <div class="invalid-feedback">Seleccione un número de cuenta.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-primary fw-bold">
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
            <div class="modal-footer d-flex justify-content-end border-top ">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" defer></script>


<script src="/assets/js/cronograma-pagos/cronograma.js" type="module" defer></script>
<?php include __DIR__ . '/../layout/footer.php'; ?>