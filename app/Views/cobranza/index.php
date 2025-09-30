<!-- app/views/cobranza/index.php -->
<?php include __DIR__ . '/../layout/header.php'; ?>

<head>

<body>

    <div class="container-fluid">

        <!-- CABECERA -->
        <div class="alert alert-info mt-2" role="alert">
            <div class="row">
                <nav class="col-md-6 d-flex align-items-center justify-content-start">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Area de cobranza</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"> Gestion de clientes con Deudas
                                pendientes</li>
                        </ol>
                    </nav>
                </nav>
                <div class="col-md-6 text-end">
                    <button class="btn btn-secondary btn-sm" id="btnVolver" onclick="volverALista()"
                        style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i>Volver a la Lista
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadisticas principales -->
        <div class="row mb-2" id="estadisticas">

            <!-- Primer cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3 class="card-title">
                            6
                            <!-- <?= htmlspecialchars($estadisticas['total_deudores'] ?? 0) ?> -->
                        </h3>
                        <p class="card-text mb-0">
                            Cliente Deudores
                        </p>
                    </div>

                </div>
            </div>

            <!-- Segundo cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3 class="card-title">
                            4
                            <!-- <?= htmlspecialchars($estadisticas['por_vencer_3dias'] ?? 0) ?> -->
                        </h3>
                        <p class="card-text mb-0">Por vencer (3 Dias)</p>
                    </div>
                </div>
            </div>

            <!-- Tercer cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                        <h3 class="card-title"><?= htmlspecialchars($estadisticas['vencidos']) ?></h3>
                        <p class="card-text mb-0">Vencidos</p>
                    </div>
                </div>
            </div>

            <!-- Cuarto cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                        <h3 class="card-title">S/. <?= number_format(($estadisticas['total_por_cobrar'] ?? 0), 2) ?>
                            <!-- 1,130.00 -->
                        </h3>
                        <P class="card-text mb-0">Total por Cobrar</P>
                    </div>
                </div>
            </div>

        </div>

        <!-- Opciones -->
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i> Opciones </h6>

                    <!-- Botones -->
                    <div class="btn-group" role="group">

                        <!-- BOTON POR DEFECTO QUE MOSTRARA UN RESUMEN -->
                        <input type="radio" id="btnTodos" name="filtro" class="d-none">
                        <label for="btnTodos" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-list me-1"></i>Todos
                            <span class="badge bg-primary text-dark ms-1">
                                <?= htmlspecialchars($estadisticas['total_deudores'] ?? 0) ?>
                            </span>
                        </label>

                        <!-- BOTON DE 3 DIAS ANTES DE VENCER -->
                        <a href="/Recordatorios" class="btn btn-sm btn-outline-warning me-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> Vence: 3 días
                            <span class="badge bg-warning text-dark ms-1">
                                4
                                <!-- <?= htmlspecialchars($estadisticas['por_vencer_3dias'] ?? 0) ?> -->
                            </span>
                        </a>

                        <!-- BOTON DE VENCIDOS -->
                        <a href="/Vencidos" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times-circle me-1"></i>Vencidos
                            <span class="badge bg-danger ms-1">
                                <?= htmlspecialchars($estadisticas['vencidos'] ?? 0) ?>
                            </span>
                        </a>

                    </div>

                </div>
            </div>
        </div>

        <div class="row">

            <?php if (!empty($resumen)): ?>
                <?php foreach ($resumen as $fila): ?>
                    <div class="col-lg-4 mb-5 cobranza-item" id="contrato-card-<?= (int) $fila['idcontrato'] ?>"
                        data-categoria="<?= (strpos($fila['estado_vencimiento'], 'Vencido') !== false) ? 'vencidos' : 'por-vencer' ?>"
                        data-deuda="<?= htmlspecialchars((float) $fila['deuda_total'], ENT_QUOTES) ?>"
                        data-monto="<?= htmlspecialchars((float) $fila['monto_cuota'], ENT_QUOTES) ?>">
                        <div
                            class="card card-deuda border-start <?= (strpos($fila['estado_vencimiento'], 'Vencido') !== false) ? 'status-vencido' : 'status-por-vencer' ?> h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title mb-1"><i
                                                class="fas fa-user me-2"></i><?= htmlspecialchars($fila['cliente']) ?></h6>
                                        <p class="text-muted small mb-1"><strong>Teléfono:</strong>
                                            <?= htmlspecialchars($fila['telefono']) ?></p>
                                        <p class="text-muted small mb-0"><strong>Local:</strong>
                                            <?= htmlspecialchars($fila['tienda']) ?></p>
                                    </div>
                                    <span
                                        class="badge <?= (strpos($fila['estado_vencimiento'], 'Vencido') !== false) ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                        <?= htmlspecialchars($fila['estado_vencimiento']) ?>
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <p class="monto-destacado mb-1">Monto a Cobrar: S/. <span
                                            class="monto-cuota"><?= number_format((float) $fila['monto_cuota'], 2) ?></span></p>
                                    <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong>
                                        <?= !empty($fila['prox_fechapago']) ? date('d/m/Y', strtotime($fila['prox_fechapago'])) : '-' ?>
                                    </p>
                                    <p class="text-muted small mb-0"><strong>Deuda total:</strong> S/. <span
                                            class="deuda-total"><?= number_format((float) $fila['deuda_total'], 2) ?></span></p>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-success btn-sm flex-grow-1"
                                        onclick="event.stopPropagation(); mostrarFormularioPago(<?= (int) $fila['idcontrato'] ?>, '<?= addslashes($fila['cliente']) ?>', <?= number_format((float) $fila['monto_cuota'], 2, '.', '') ?>, <?= number_format((float) $fila['deuda_total'], 2, '.', '') ?>)">
                                        <i class="fas fa-money-bill-wave me-1"></i> Pagar
                                    </button>
                                    <!-- onclick="event.stopPropagation(); contactarCliente('<?= htmlspecialchars($fila['telefono']) ?>')" -->
                                    <button class="btn btn-outline-info btn-sm" title="Contactar">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay contratos con deuda.</p>
            <?php endif; ?>

        </div>

    </div>

    <!-- Modal para registrar pago -->
    <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalPagoLabel">
                        <i class="fas fa-money-bill-wave me-2"></i>Registrar Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Información del Cliente -->
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary bg-opacity-10">
                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Cliente:</strong> <span id="detalle-cliente"></span></p>
                                    <p class="mb-0"><strong>Contrato N°:</strong> <span id="detalle-contrato"></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Monto de Cuota:</strong> <span class="text-danger fs-5"
                                            id="detalle-monto"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Pago -->
                    <form id="formRegistrarPago">
                        <input type="hidden" id="idcontrato-pago" name="idcontrato">

                        <div class="row">
                            <!-- Monto a Pagar -->
                            <div class="col-md-6 mb-3">
                                <label for="monto-pagar" class="form-label">
                                    <i class="fas fa-dollar-sign me-1"></i>Monto a Pagar <span
                                        class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">S/.</span>
                                    <input type="number" class="form-control" id="monto-pagar" name="monto" step="0.01"
                                        min="0" required placeholder="0.00">
                                </div>
                                <small class="text-muted">Ingrese el monto que el cliente está pagando</small>
                            </div>

                            <!-- Fecha de Pago -->
                            <div class="col-md-6 mb-3">
                                <label for="fecha-pago" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Fecha de Pago <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="fecha-pago" name="fecha_pago" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Método de Pago -->
                            <div class="col-md-6 mb-3">
                                <label for="metodo-pago" class="form-label">
                                    <i class="fas fa-credit-card me-1"></i>Método de Pago <span
                                        class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="metodo-pago" name="metodo_pago" required>
                                    <option value="">Seleccione un metodó</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="yape">Yape</option>
                                    <option value="plin">Plin</option>
                                    <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                                </select>
                            </div>

                            <!-- Comprobante -->
                            <div class="col-md-6 mb-3">
                                <label for="comprobante" class="form-label">
                                    <i class="fas fa-file-invoice me-1"></i>N° de Comprobante/Operación
                                </label>
                                <input type="text" class="form-control" id="comprobante" name="comprobante"
                                    placeholder="Opcional">
                                <small class="text-muted">Número de recibo, operación o transacción</small>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">
                                <i class="fas fa-comment me-1"></i>Observaciones
                            </label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="2"
                                placeholder="Notas adicionales (opcional)"></textarea>
                        </div>

                        <!-- Resumen del Pago -->
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="fas fa-calculator me-2"></i>Resumen del Pago</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Monto de Cuota:</strong></p>
                                        <p class="mb-1"><strong>Monto a Pagar:</strong></p>
                                        <hr class="my-2">
                                        <p class="mb-0"><strong>Estado:</strong></p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <p class="mb-1">S/. <span id="resumen-cuota">0.00</span></p>
                                        <p class="mb-1 text-primary fw-bold">S/. <span id="resumen-pago">0.00</span></p>
                                        <hr class="my-2">
                                        <p class="mb-0"><span class="badge" id="resumen-estado">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-success" onclick="guardarPago()">
                        <!-- <i class="fas fa-check me-1"></i> -->Registrar Pago
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                        <!-- <i class="fas fa-times me-1"></i> -->Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</head>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<script>
    // Función para mostrar el modal con los datos del cliente
    function mostrarFormularioPago(idcontrato, nombreCliente, montoCuota) {
        // Cargar datos en el modal
        document.getElementById('detalle-cliente').textContent = nombreCliente;
        document.getElementById('detalle-contrato').textContent = idcontrato;
        document.getElementById('detalle-monto').textContent = 'S/. ' + parseFloat(montoCuota).toFixed(2);

        // Establecer valores en el formulario
        document.getElementById('idcontrato-pago').value = idcontrato;
        document.getElementById('monto-pagar').value = parseFloat(montoCuota).toFixed(2);

        // Establecer fecha actual
        const hoy = new Date().toISOString().split('T')[0];
        document.getElementById('fecha-pago').value = hoy;

        // Actualizar resumen
        document.getElementById('resumen-cuota').textContent = parseFloat(montoCuota).toFixed(2);
        document.getElementById('resumen-pago').textContent = parseFloat(montoCuota).toFixed(2);
        actualizarEstadoPago(montoCuota, montoCuota);

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalPago'));
        modal.show();
    }

    // Actualizar el resumen cuando cambie el monto
    document.addEventListener('DOMContentLoaded', function () {
        const inputMonto = document.getElementById('monto-pagar');
        if (inputMonto) {
            inputMonto.addEventListener('input', function () {
                const montoPagado = parseFloat(this.value) || 0;
                const montoCuota = parseFloat(document.getElementById('resumen-cuota').textContent) || 0;

                document.getElementById('resumen-pago').textContent = montoPagado.toFixed(2);
                actualizarEstadoPago(montoCuota, montoPagado);
            });
        }
    });

    // Función para actualizar el estado del pago
    function actualizarEstadoPago(montoCuota, montoPagado) {
        const estadoBadge = document.getElementById('resumen-estado');

        if (montoPagado >= montoCuota) {
            estadoBadge.className = 'badge bg-success';
            estadoBadge.textContent = 'Pago Completo';
        } else if (montoPagado > 0) {
            estadoBadge.className = 'badge bg-warning text-dark';
            estadoBadge.textContent = 'Pago Parcial';
        } else {
            estadoBadge.className = 'badge bg-secondary';
            estadoBadge.textContent = 'Sin Pago';
        }
    }

    // Función para guardar el pago
    function guardarPago() {
        const form = document.getElementById('formRegistrarPago');

        // Validar formulario
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Obtener datos del formulario
        const formData = new FormData(form);
        const datos = Object.fromEntries(formData.entries());

        // Aquí iría tu lógica para enviar los datos al servidor
        console.log('Datos a enviar:', datos);

        // Por ahora, solo mostramos una alerta
        alert('Pago registrado: S/. ' + datos.monto + '\nMétodo: ' + datos.metodo_pago);
        bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
    }
</script>