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
                            <tbody id="tabla-body">
                                <?php if (empty($cronograma)) : ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No hay datos para mostrar</td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $hoy = new DateTimeImmutable('today');
                                    $cuotaHabilitada = false; // Controlamos si ya habilitamos una cuota pendiente

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

                                        if ($estado === 'pagado') {
                                            $clase_fila = 'bg-success table-success';
                                            $clase_estado = 'estado-pagado';
                                            $icono = 'fa-check-circle';
                                            $texto_vencimiento = 'Pagado';
                                            $fila_deshabilitada = true;
                                        } elseif (!$cuotaHabilitada) {
                                            // Primera cuota pendiente habilitada (sin importar si amortización es 0 o no)
                                            $clase_fila = 'fila-pendiente-pago table-warning';
                                            $clase_estado = ($es_sin_pago ? 'estado-sin-pago' : 'estado-pendiente');
                                            $icono = ($es_sin_pago ? 'fa-exclamation-triangle' : 'fa-clock');

                                            if ($hoy > $fecha_cuota) {
                                                $dias_vencido = $fecha_cuota->diff($hoy)->days;
                                                $texto_vencimiento = "Venció hace $dias_vencido días";
                                                $clase_estado = 'estado-vencido';
                                                $icono = 'fa-exclamation-triangle';
                                            } else {
                                                $dias_faltantes = $hoy->diff($fecha_cuota)->days;
                                                $texto_vencimiento = "Vence en $dias_faltantes días";
                                            }

                                            $fila_deshabilitada = false;
                                            $cuotaHabilitada = true; // Solo esta cuota se habilita
                                        } else {
                                            // Futuras cuotas: deshabilitadas
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
                                                    <small class="<?= $estado === 'pagado' ? 'text-success fw-bold' : ($es_sin_pago ? 'text-danger fw-bold' : 'text-muted') ?>">
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
                                                    <button class="btn btn-pagar btn-sm text-white"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalPago"
                                                        data-cuota="<?= $fila['numcuota'] ?>"
                                                        data-idcronograma="<?= $fila['idcronograma'] ?>"
                                                        data-valorcuota="<?= $fila['valorcuota'] ?>"
                                                        data-penalidad="<?= $fila['penalidad'] ?>"
                                                        data-saldorestante="<?= $fila['saldorestante'] ?>">
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
                    <i class="fas fa-info-circle me-2"></i> <span id="numero-cuota"></span>
                </div>

                <form id="formPago" enctype="multipart/form-data">
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
                                            <input type="text" class="form-control bg-body-tertiary" id="amortizacion" name="amortizacion">
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Penalidad</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary"><i
                                                    class="fas fa-coins text-warning"></i></span>
                                            <input type="text" class="form-control bg-body-tertiary" id="pago-penalidad" name="pago-penalidad" disabled>
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
                                            <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" name="fechapago"
                                                id="fechapago">
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
                                        <select class="form-select" id="mediopago" name="mediopago">
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Yape">Yape</option>
                                            <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                            <option value="Plin">Plin</option>
                                        </select>
                                    </div>
                                    <div class="mb-2 hidden select-cuentas">
                                        <label class="form-label small text-muted">Número de cuenta</label>
                                        <select class="form-select"
                                            id="idcuentapago" name="idcuentapago">
                                            <option>Seleccione</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Número de Operación</label>
                                        <input type="text" class="form-control" name="numerotransaccion"
                                            placeholder="Opcional para transferencias" id="numerotransaccion">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Comprobante</label>
                                        <input id="comprobante" class="form-control" type="file" name="comprobante" placeholder="Seleccione un archivo" accept="image/*,.pdf">

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
                                        placeholder="Ingrese cualquier observación..." id="observacion" name="observacion"></textarea>
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>



<script>
    document.addEventListener('DOMContentLoaded', function() {



        // Variables globales
        const itemsPerPage = 10;
        const totalItems = <?= count($cronograma) ?>;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        let currentPage = 1;

        // Variables del FORM
        const numeroCuenta = document.querySelector('#idcuentapago');
        const medioPago = document.querySelector('#mediopago');
        const numeroTransaccion = document.querySelector('#numerotransaccion');
        const fechaPago = document.querySelector('#fechapago');
        const amortizacion = document.querySelector('#amortizacion');
        let pagoPenalidad = document.querySelector('#pago-penalidad');
        const comprobante = document.querySelector('#comprobante');
        const selectCuentas = document.querySelector('.select-cuentas');
        const observacion = document.querySelector('#observacion');
        const formPago = document.querySelector('#formPago');

        let inputPagopenalidad = null;
        let idCronogramaSeleccionado = null;
        let saldoRestante = null;








        medioPago.addEventListener('change', async (e) => {
            let valor = e.target.value;

            if (valor.toLowerCase() === 'transferencia bancaria') {
                selectCuentas.classList.remove('hidden');

                try {
                    const res = await fetch('/api/numcuentaspagos');
                    const data = await res.json();

                    numeroCuenta.innerHTML = '<option value="">Selecciona una cuenta</option>';

                    if (data.length > 0) {
                        data.forEach(e => {
                            numeroCuenta.innerHTML += `<option value="${e.idcuentapago}">${e.nombrecuenta}</option>`;
                        });
                    }

                } catch (error) {
                    console.error(error);
                }

            } else {
                selectCuentas.classList.add('hidden');
                numeroCuenta.innerHTML = '';
            }
        });


        formPago.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!idCronogramaSeleccionado) {
                showToast('No se seleccionó ninguna cuota. Intenta nuevamente.', 'INFO', 1200);
                return;
            }

            // Validar cuenta si es transferencia
            if (medioPago.value.toLowerCase() === 'transferencia bancaria' && numeroCuenta.value === '') {
                showToast('Debes seleccionar una cuenta bancaria', 'INFO', 1200);
                return;
            }

            const amortizacionValor = amortizacion.value;
            if (!amortizacionValor || isNaN(parseFloat(amortizacionValor)) || parseFloat(amortizacionValor) <= 0) {
                showToast('Ingresa una amortización válida', 'INFO', 1200);
                return;
            }

            if (parseFloat(amortizacionValor) > saldoRestante) {
                showToast(`La amortización no puede ser mayor al saldo restante de S/ ${saldoRestante.toFixed(2)}.`, 'INFO', 1200);
                return;
            }

            const formData = new FormData();
            formData.append('idcronograma', idCronogramaSeleccionado);
            if (medioPago.value === 'Transferencia Bancaria') {
                formData.append('idcuentapago', numeroCuenta.value);
            }
            formData.append('mediopago', medioPago.value);
            formData.append('numerotransaccion', numeroTransaccion.value ?? null);
            formData.append('fechapago', fechaPago.value);
            formData.append('amortizacion', amortizacion.value);
            formData.append('observacion', observacion.value);


            if (comprobante.files.length > 0) {
                formData.append('comprobante', comprobante.files[0]);
            }

            if (confirm('¿Seguro de registrar el pago?')) {
                try {
                    const res = await fetch('/pago/cronograma', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await res.json();


                    if (data.success) {
                        showToast('Pago registrado correctamente.', 'SUCCESS', 1200);

                        // Guardar la posición del scroll en localStorage
                        const scrollPosition = window.scrollY;
                        localStorage.setItem('scrollPosition', scrollPosition);

                        setTimeout(() => {
                            location.reload();
                        }, 1200);
                    } else {
                        showToast(data.message, 'WARNING', 1200);
                    }



                } catch (err) {
                    console.error('Error al enviar:', err);
                    showToast('Ocurrió un error al procesar el pago.', 'WARNING', 1200);
                }
            }


        });



        // RELLENAR EL FORMUALRIO CON LOS DATOS DE LA DB.
        document.querySelectorAll('.btn-pagar').forEach(btn => {
            btn.addEventListener('click', function() {
                const valorCuota = parseFloat(this.dataset.valorcuota);
                const abonoCapital = parseFloat(this.dataset.abonocapital);
                const penalidad = parseFloat(this.dataset.penalidad) || 0;
                saldoRestante = parseFloat(this.dataset.saldorestante);

                const totalDeuda = valorCuota + penalidad;

                document.getElementById('saldocuota').value = valorCuota.toFixed(2);
                document.getElementById('penalidad').value = penalidad.toFixed(2);
                document.getElementById('total-deuda').value = totalDeuda.toFixed(2);
                document.getElementById('amortizacion').value = '';
                document.getElementById('saldo-restante').value = saldoRestante.toFixed(2);
                idCronogramaSeleccionado = this.dataset.idcronograma;
                inputPagopenalidad = this.dataset.penalidad;
                pagoPenalidad.value = this.dataset.penalidad;

                document.querySelector('#numero-cuota').textContent = `Está a punto de registrar el pago de la cuota N°  ${this.dataset.cuota} `;
            });
        });




        // Deshabilitar completamente las filas pagadas
        function deshabilitarFilasPagadas() {
            const filasPagadas = document.querySelectorAll('tr.fila-pagada, tr[data-disabled="true"]');

            filasPagadas.forEach(fila => {
                // Prevenir todos los eventos en la fila
                fila.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }, true);

                fila.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }, true);

                // Deshabilitar botones dentro de la fila
                const botones = fila.querySelectorAll('button');
                botones.forEach(boton => {
                    boton.disabled = true;
                    boton.setAttribute('disabled', 'true');
                    boton.style.pointerEvents = 'none';
                    boton.style.opacity = '0.5';
                });


            });
        }

        // Ejecutar al cargar la página
        deshabilitarFilasPagadas();

        // Re-ejecutar después de la paginación.
        document.addEventListener('paginationComplete', deshabilitarFilasPagadas);



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




        document.getElementById('btn-pdf').addEventListener('click', () => {
            showToast('GENERANDO EL PDF.....', 'INFO', 3000);

            setTimeout(() => {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF('landscape', 'mm', 'a4');

                // Título
                doc.setFontSize(18);

                doc.text("Cronograma de Pagos", doc.internal.pageSize.getWidth() / 2, 15, {
                    align: 'center'
                });
                const fechaHora = new Date().toLocaleDateString();
                doc.setFontSize(11);
                doc.text(`FECHA: ${fechaHora} `, doc.internal.pageSize.getWidth() - 10, 22, {
                    align: 'right'
                });

                // Cabeceras en el PDF
                const head = [
                    [
                        "#", "Fecha Vencimiento", "Interés", "Abono Capital",
                        "Valor Cuota", "Amortización", "Restante", "Saldo Capital", "Estado"
                    ]
                ];

                const headStyles = {
                    fillColor: [200, 200, 200],
                    textColor: 20,
                    fontStyle: 'bold',
                    halign: 'center',
                    fontSize: 12,
                };

                // Extraer datos de la tabla original
                const rows = [];

                document.querySelectorAll('#tabla-body tr').forEach(tr => {
                    const tds = tr.querySelectorAll('td');

                    if (tds.length >= 9) {
                        const numCuota = tds[0].innerText.trim();
                        const fecha = tds[1].querySelector('span.fw-bold')?.innerText.trim() || ''; // Solo la fecha
                        const interes = tds[2].innerText.trim();
                        const abono = tds[3].innerText.trim();
                        const valorCuota = tds[4].innerText.trim();
                        const amortizacion = tds[5].innerText.trim();
                        const restante = tds[6].innerText.trim();
                        const saldo = tds[7].innerText.trim();
                        const estado = tds[8].innerText.trim();
                        const estadoFormateado = estado.split('-')[0];

                        rows.push([
                            numCuota, fecha, interes, abono, valorCuota, amortizacion, restante, saldo, estadoFormateado
                        ]);
                    }
                });

                // Crear tabla en PDF
                doc.autoTable({
                    head,
                    body: rows,
                    startY: 25,
                    styles: {
                        fontSize: 10,
                        halign: 'center',
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

                doc.save('cronograma_pagos.pdf');
                showToast('PDF GENERADO', 'SUCCESS', 3000);
            },3000);
        });




        showPage(1);
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>