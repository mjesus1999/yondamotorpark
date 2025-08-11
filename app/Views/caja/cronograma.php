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
                <a href="/contratos/" class="btn btn-outline-primary btn-sm">
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
                                    <th><i class="fas fa-wallet me-1"></i> Saldo Capital</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-body">
                                <?php
                                $monto_prestamo = 40050;
                                $plazo_meses = 36;
                                $cuota_fija = 2196.00;
                                $saldo_capital_restante = $monto_prestamo;
                                $fecha_base = new DateTimeImmutable('2025-06-27');
                                $hoy = new DateTimeImmutable('today'); // Forzar la hora                                 
                                for ($i = 1; $i <= $plazo_meses; $i++) {
                                    $fecha_cuota = $fecha_base->modify("+$i month");
                                    $fecha_formateada = $fecha_cuota->format('d/m/Y');

                                    $vencido = ($hoy > $fecha_cuota);
                                    $dias_restantes = $hoy->diff($fecha_cuota)->days;

                                    $estado = 'Pendiente';
                                    $clase_estado = 'estado-pendiente';
                                    $icono = 'fa-clock';
                                    $texto_vencimiento = "Vence en $dias_restantes días";

                                    if ($vencido) {
                                        $estado = 'Vencido';
                                        $clase_estado = 'estado-vencido';
                                        $icono = 'fa-exclamation-triangle';
                                        $texto_vencimiento = "Venció hace $dias_restantes días";
                                    }

                                    $saldo_capital = $cuota_fija;
                                    $abono_capital = 0.00;
                                    $style_display = ($i <= 10) ? '' : 'style="display:none;"';
                                    $data_page = ceil($i / 10);

                                    echo "<tr data-page='$data_page' $style_display>
                                        <td><span class='badge bg-primary badge-cuota'>$i</span></td>
                                        <td>
                                            <div class='d-flex flex-column'>
                                                <span class='fw-bold'>$fecha_formateada</span>
                                                <small class='text-muted'>$texto_vencimiento</small>
                                            </div>
                                        </td>
                                        <td>S/ 0.00</td>
                                        <td>S/ " . number_format($abono_capital, 2) . "</td>
                                        <td>S/ " . number_format($cuota_fija, 2) . "</td>
                                        <td>S/ " . number_format($saldo_capital, 2) . "</td>
                                        <td>
                                            <span class='$clase_estado'>
                                                <i class='fas $icono me-1'></i> $estado
                                            </span>
                                        </td>
                                        <td>
                                            <button class='btn btn-pagar btn-sm text-white' data-bs-toggle='modal' data-bs-target='#modalPago' data-cuota='$i' id='btn-cuota'>
                                                <i class='fa-solid fa-dollar-sign me-1'></i> Pagar
                                            </button>
                                        </td>
                                    </tr>";
                                }


                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-body-tertiary text-body-secondary">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted" id="info-paginacion">
                            Mostrando <span class="fw-bold">1-10</span> de <span
                                class="fw-bold"><?= $plazo_meses ?></span> cuotas
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0" id="paginacion">
                                <li class="page-item disabled" id="prev-page">
                                    <a class="page-link" href="#" data-page="1" tabindex="-1" aria-disabled="true">Anterior</a>
                                </li>

                                <?php for ($p = 1; $p <= ceil($plazo_meses / 10); $p++): ?>
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
                                            <input type="text" class="form-control bg-body-tertiary" value="0.00" disabled
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
                                            <input type="text" class="form-control bg-body-tertiary" value="0.00" readonly
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
        const totalItems = 36;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        let currentPage = 1;
        let cuotaActual = null;
        let cuotas = {}; // Usaremos un objeto para almacenar el estado de cada cuota

        // Inicializar el objeto de cuotas
        document.querySelectorAll('#tabla-body tr').forEach((row, index) => {
            const numCuota = index + 1;
            const valorCuotaTexto = row.querySelector('td:nth-child(5)').textContent;
            const valorCuota = parseFloat(valorCuotaTexto.replace('S/ ', '').replace(',', ''));
            const fechaVencimientoTexto = row.querySelector('td:nth-child(2) .fw-bold').textContent;
            const [dia, mes, anio] = fechaVencimientoTexto.split('/');
            const fechaVencimiento = `${anio}-${mes.padStart(2, '0')}-${dia.padStart(2, '0')}`;

            cuotas[numCuota] = {
                id: numCuota,
                valorCuota,
                fechaVencimiento,
                abonoCapitalTotal: 0.00,
                penalidad: 0.00,
                pagado: false
            };
            // Calcular penalidad inicial si la cuota ya está vencida al cargar la página
            const diasVencimiento = calcularDiasVencimiento(fechaVencimiento);
            if (diasVencimiento > 3) {
                cuotas[numCuota].penalidad = 300.00;
                // Actualiza la columna de saldo capital en la tabla
                const saldoCapitalCell = row.querySelector('td:nth-child(6)');
                const nuevoSaldo = valorCuota + cuotas[numCuota].penalidad;
                saldoCapitalCell.innerHTML = `S/ ${nuevoSaldo.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`;
            }
            actualizarEstadoFila(row, cuotas[numCuota]);
        });

        // Función para calcular días de vencimiento
        function calcularDiasVencimiento(fechaVencimiento) {
            const hoy = new Date();
            const fechaVenc = new Date(fechaVencimiento);
            const diferencia = hoy.getTime() - fechaVenc.getTime();
            return Math.ceil(diferencia / (1000 * 3600 * 24));
        }

        // Función para actualizar el estado visual de una fila de la tabla
        function actualizarEstadoFila(fila, cuotaData) {
            const estadoCell = fila.querySelector('td:nth-child(7)');
            const btnPago = fila.querySelector('[id="btn-cuota"]');
            const abonoCapitalCell = fila.querySelector('td:nth-child(4)');

            let estadoHTML = '';

            if (cuotaData.pagado) {
                estadoHTML = `
        <span class="estado-pagado">
            <i class="fas fa-check-circle me-1"></i> Pagado
        </span>`;
                btnPago.disabled = true;
                btnPago.innerHTML = '<i class="fas fa-check me-1"></i> Pagado';
                btnPago.classList.remove('btn-pagar');
                btnPago.classList.add('btn-secondary');
            } else {
                const diasVencimiento = calcularDiasVencimiento(cuotaData.fechaVencimiento);
                if (diasVencimiento > 0) {
                    estadoHTML = `
            <span class="estado-vencido">
                <i class="fas fa-exclamation-triangle me-1"></i> Vencido
            </span>`;
                    btnPago.disabled = false;
                    btnPago.classList.remove('btn-secondary');
                    btnPago.classList.add('btn-pagar');
                    btnPago.innerHTML = '<i class="fa-solid fa-dollar-sign me-1"></i> Pagar';
                } else {
                    estadoHTML = `
            <span class="estado-pendiente">
                <i class="fas fa-clock me-1"></i> Pendiente
            </span>`;
                    btnPago.disabled = false;
                    btnPago.classList.remove('btn-secondary');
                    btnPago.classList.add('btn-pagar');
                    btnPago.innerHTML = '<i class="fa-solid fa-dollar-sign me-1"></i> Pagar';
                }
            }

            estadoCell.innerHTML = estadoHTML;

            //cambio visual de la fila, para que me lo marque cn un fondo que identifique si esta pagado o no:
            if (cuotaData.pagado) {
                fila.classList.add('table-success', 'fw-bold');
            } else {
                fila.classList.remove('table-success');
            }

            // Mostrar abono capital acumulado si existe
            if (cuotaData.abonoCapitalTotal > 0) {
                abonoCapitalCell.innerHTML = `S/ ${cuotaData.abonoCapitalTotal.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`;
            }
        }


        // Event listeners para botones de pago
        document.querySelectorAll('[id="btn-cuota"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                cuotaActual = parseInt(this.dataset.cuota);
                const datosCuota = cuotas[cuotaActual];

                // Llenar los campos del modal
                const saldoRestanteCapital = datosCuota.valorCuota - datosCuota.abonoCapitalTotal;
                const totalDeuda = saldoRestanteCapital + datosCuota.penalidad;

                document.getElementById('saldocuota').value = saldoRestanteCapital.toFixed(2);
                document.getElementById('penalidad').value = datosCuota.penalidad.toFixed(2);
                document.getElementById('total-deuda').value = totalDeuda.toFixed(2);
                document.getElementById('amortizacion').value = '';
                document.getElementById('saldo-restante').value = totalDeuda.toFixed(2);
                document.getElementById('fecha-pago').value = new Date().toISOString().split('T')[0];
            });
        });

        // Event listener para el cambio en el campo de amortización
        document.getElementById('amortizacion').addEventListener('input', function() {
            const nuevaAmortizacion = parseFloat(this.value) || 0;
            const datosCuota = cuotas[cuotaActual];
            const saldoRestanteCapital = datosCuota.valorCuota - datosCuota.abonoCapitalTotal;
            const totalDeuda = saldoRestanteCapital + datosCuota.penalidad;
            const nuevoSaldoRestante = totalDeuda - nuevaAmortizacion;
            document.getElementById('saldo-restante').value = Math.max(0, nuevoSaldoRestante).toFixed(2);

            // Validaciones en tiempo real
            if (nuevaAmortizacion > totalDeuda) {
                this.classList.add('is-invalid');
                document.getElementById('btn-form-submit').disabled = true;
            } else {
                this.classList.remove('is-invalid');
                document.getElementById('btn-form-submit').disabled = false;
            }
        });

        // Evento para el envío del formulario de pago
        document.getElementById('formPago').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!cuotaActual) {
                alert('Error: No se ha seleccionado una cuota válida');
                return;
            }

            // Confirmar si desea guardar el pago
            const confirmar = confirm('¿Está seguro que desea guardar este pago?');
            if (!confirmar) {
                return;
            }

            const nuevaAmortizacion = parseFloat(document.getElementById('amortizacion').value) || 0;
            const datosCuota = cuotas[cuotaActual];
            
            const fila = document.querySelector(`#tabla-body tr:nth-child(${cuotaActual})`);
            let saldoCapitalAnterior = datosCuota.valorCuota + datosCuota.penalidad - datosCuota.abonoCapitalTotal;

            // Validación
            const totalDeuda = (datosCuota.valorCuota - datosCuota.abonoCapitalTotal) + datosCuota.penalidad;
            if (nuevaAmortizacion > totalDeuda) {
                alert('El monto a amortizar no puede ser mayor al total adeudado (cuota + penalidad).');
                return;
            }

            // Desglosar el pago: penalidad primero, luego capital
            let montoRestante = nuevaAmortizacion;
            let pagoPenalidad = 0;
            let pagoCapital = 0;

            if (datosCuota.penalidad > 0) {
                if (montoRestante >= datosCuota.penalidad) {
                    pagoPenalidad = datosCuota.penalidad;
                    montoRestante -= datosCuota.penalidad;
                } else {
                    pagoPenalidad = montoRestante;
                    montoRestante = 0;
                }
            }

            pagoCapital = montoRestante;

            // Actualizar estado de cuota
            datosCuota.penalidad -= pagoPenalidad;
            datosCuota.abonoCapitalTotal += pagoCapital;
            datosCuota.amortizacion = document.querySelector('#amortizacion').value;
            datosCuota.observaciones = document.querySelector('#observaciones').value;
            datosCuota.fechaPago = document.querySelector('#fecha-pago').value;
            datosCuota.metodoPago = document.querySelector('#metodo-pago').value;
            datosCuota.numOperacion = document.querySelector('#numoperacion').value;


            if (datosCuota.abonoCapitalTotal >= datosCuota.valorCuota && datosCuota.penalidad <= 0) {
                datosCuota.pagado = true;
            }


            // Actualizar DOM
            fila.querySelector('td:nth-child(4)').innerHTML = `S/ ${datosCuota.abonoCapitalTotal.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`;
            const nuevoSaldo = Math.max(0, saldoCapitalAnterior - nuevaAmortizacion);
            fila.querySelector('td:nth-child(6)').innerHTML = `S/ ${nuevoSaldo.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`;

            actualizarEstadoFila(fila, datosCuota);

            const modal = bootstrap.Modal.getInstance(document.getElementById('modalPago'));
            modal.hide();
            this.reset();


            showToast('¡Pago realizado!','SUCCESS','1200');

            const objetoJson = JSON.stringify(datosCuota);
            console.log(objetoJson);
            
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