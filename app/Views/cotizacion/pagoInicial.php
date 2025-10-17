<?php include __DIR__ . '/../layout/header.php'; ?>
<link rel="stylesheet" href="/assets/css/pago-inicial.css">
<!-- <?php var_dump($cotizacion)?> -->


<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pago de inicial</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizacion/P" class="border border-1 border-primary p-2" style="border-radius: 5px;">
                    <i class="bi bi-list-task me-1"></i> Listar
                </a>
            </div>
        </div>
    </div>

    <!-- Encabezado Principal -->
    <div class="encabezado-principal">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="tarjeta-icono">
                <i class="bi bi-car-front-fill" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h1 class="h2 mb-1">Gestión de Pagos - Cotización #<span id="numeroCotizacion"><?= htmlspecialchars($cotizacion['idcotizacion']) ?></span></h1>
                <p class="mb-0 opacity-75">Sistema de Registro y Control de Pagos Iniciales</p>
            </div>
        </div>

        <!-- Resumen de Pagos -->
        <div class="row g-3 mt-3">
            <div class="col-6 col-lg-3">
                <div class="tarjeta-resumen">
                    <small class="d-block opacity-75">Inicial Requerido</small>
                    <strong class="fs-5" id="inicialRequerido"><?= $cotizacion['moneda'] == 'PEN' ? 'S/' : '$/ ' ?> <?= number_format(htmlspecialchars($cotizacion['inicial']), 2, '.') ?></strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="tarjeta-resumen">
                    <small class="d-block opacity-75">Total Pagado</small>
                    <strong class="fs-5" id="totalPagado">S/ <?= number_format(htmlspecialchars($montosInfo['totalpagado']), 2, '.') ?></strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="tarjeta-resumen">
                    <small class="d-block opacity-75">Saldo Pendiente</small>
                    <strong class="fs-5" id="saldoPendiente">S/ <?= number_format(htmlspecialchars($montosInfo['saldorestante']), 2, '.') ?></strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="tarjeta-resumen">
                    <small class="d-block opacity-75">Estado</small>
                    <span class="badge bg-white mt-1 text-success fw-bold">
                        <span class="badge rounded-circle bg-warning p-1"></span>
                        <?php 
                            switch ($cotizacion['estadocotizacion']) {
                                case 'P':
                                    echo 'Pendiente';
                                    break;
                                case 'S':
                                    echo 'Serparada';
                                    break;
                                case 'A':
                                    echo 'Aprobada';
                                    break;
                                
                            }
                        
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sistema de Pestañas -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm" id="pestanasPrincipales" role="tablist">
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link active w-100" id="tab-detalles" data-bs-toggle="pill" data-bs-target="#contenido-detalles" type="button">
                <i class="bi bi-file-text me-2"></i>Detalles
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link w-100" id="tab-pago" data-bs-toggle="pill" data-bs-target="#contenido-pago" type="button">
                <i class="bi bi-credit-card me-2"></i>Registrar Pago
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link w-100" id="tab-historial" data-bs-toggle="pill" data-bs-target="#contenido-historial" type="button">
                <i class="bi bi-clock-history me-2"></i>Historial
            </button>
        </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content fade-in" id="contenidoPestanas">
        <!-- Pestaña Detalles -->
        <div class="tab-pane fade show active" id="contenido-detalles">
            <div class="row g-4">
                <!-- Información del Cliente -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header" style="background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);">
                            <i class="bi bi-person-fill text-primary me-2"></i>
                            <span class="text-primary">Información del Cliente</span>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <div class="info-icono azul">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <small class="d-block">Nombre Completo</small>
                                    <strong id="nombreCliente"><?= htmlspecialchars($cotizacion['nombrecliente']) ?></strong>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icono azul">
                                    <i class="bi bi-card-text"></i>
                                </div>
                                <div>
                                    <small class="d-block">Documento</small>
                                    <strong id="documentoCliente"><?= htmlspecialchars($cotizacion['documento']) ?></strong>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icono azul">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <small class="d-block">Teléfono</small>
                                    <strong id="telefonoCliente"><?= htmlspecialchars($cotizacion['telefono']) ?></strong>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icono azul">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <small class="d-block">Dirección</small>
                                    <strong id="direccionCliente"><?= htmlspecialchars($cotizacion['direccion'] == null ? 'No especificado' : $cotizacion['direccion']) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Vehículo -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header" style="background: linear-gradient(135deg, #ede9fe 0%, #f3e8ff 100%);">
                            <i class="bi bi-car-front text-secondary me-2"></i>
                            <span style="color: var(--color-secondary);">Información del Vehículo</span>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <div class="info-icono morado">
                                    <i class="bi bi-car-front"></i>
                                </div>
                                <div>
                                    <small class="d-block">Vehículo</small>
                                    <strong id="nombreVehiculo"><?= htmlspecialchars($cotizacion['vehiculo']) ?></strong>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icono morado">
                                    <i class="bi bi-hash"></i>
                                </div>
                                <div>
                                    <small class=" d-block">ID Vehículo</small>
                                    <strong id="idVehiculo"><?= htmlspecialchars($cotizacion['idvehiculo']) ?></strong>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icono morado">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div>
                                    <small class=" d-block">Precio de Venta</small>
                                    <strong id="precioVenta">PEN <?= number_format(htmlspecialchars($cotizacion['precioventa']), 2, '.') ?></strong>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icono morado">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                <div>
                                    <small class=" d-block">Tipo de Cotización</small>
                                    <strong id="tipoCotizacion"><?= htmlspecialchars($cotizacion['tipocotizacion']) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan de Financiamiento -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                            <i class="bi bi-credit-card text-success me-2"></i>
                            <span class="text-success">Plan de Financiamiento</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Cantidad</th>
                                            <th class="text-end">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Precio de Venta</td>
                                            <td>-</td>
                                            <td class="text-end" id="tablaPrecionVenta"><?= $cotizacion['moneda'] == 'PEN' ? 'S/' : '$/ ' ?> <?= number_format(htmlspecialchars($cotizacion['precioventa']), 2, '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td>Inicial</td>
                                            <td>-</td>
                                            <td class="text-end" id="tablaInicial"><?= $cotizacion['moneda'] == 'PEN' ? 'S/' : '$/ ' ?> <?= number_format(htmlspecialchars($cotizacion['inicial']), 2, '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td>Monto a Financiar</td>
                                            <td>-</td>
                                            <td class="text-end" id="tablaFinanciar">S/ <?= number_format(htmlspecialchars($cotizacion['precioventa'] - $cotizacion['inicial']), 2, '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td>Número de Cuotas</td>
                                            <td id="tablaCuotas"><?= htmlspecialchars($cotizacion['numcuotas']) ?></td>
                                            <td class="text-end">-</td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong>Valor de Cuota Mensual</strong></td>
                                            <td>-</td>
                                            <td class="text-end"><strong id="tablaValorCuota">S/ <?= number_format(htmlspecialchars($cotizacion['valorcuota']), 2, '.') ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Pestaña Registrar Pago -->
        <div class="tab-pane fade" id="contenido-pago">
            <div class="card">
                <div class="card-header text-white" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="tarjeta-icono">
                            <i class="bi bi-credit-card" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Registrar Nuevo Pago</h5>
                            <small class="opacity-75">Complete todos los campos obligatorios para registrar el pago del inicial</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Alertas -->
                    <div id="alertaExito" class="alerta-exito d-none">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            <div>
                                <strong class="text-success">¡Pago registrado exitosamente!</strong>
                                <p class="mb-0 text-success-emphasis">El pago se ha agregado al historial correctamente.</p>
                            </div>
                        </div>
                    </div>

                    <div id="alertaError" class="alerta-error d-none">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill text-danger fs-4"></i>
                            <div>
                                <strong class="text-danger">Error en el formulario</strong>
                                <p class="mb-0 text-danger-emphasis" id="mensajeError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Saldo -->
                    <div class="alerta-saldo">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class=" fw-bold d-block">Saldo Pendiente Actual</small>
                                <strong class="fs-5"
                                    id="saldoActualForm"
                                    data-valor="<?= htmlspecialchars($montosInfo['saldorestante']) ?>">
                                    S/ <?= number_format(htmlspecialchars($montosInfo['saldorestante']), 2, '.', ',') ?>
                                </strong>
                            </div>
                            <div class="col-md-4" id="montoAPagarContainer" style="display: none;">
                                <small class="d-block">Monto a Pagar</small>
                                <strong class="fs-5" id="montoAPagar">PEN 0.00</strong>
                            </div>
                            <div class="col-md-4" id="saldoRestanteContainer" style="display: none;">
                                <small class="d-block">Saldo Restante</small>
                                <strong class="fs-5" id="saldoRestanteForm">PEN 0.00</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form id="formularioPago" enctype="multipart/form-data" method="POST">

                        <input type="hidden" value="<?= htmlspecialchars($cotizacion['idvehiculo']) ?>" id="idvehiculo" name="idvehiculo">
                        <input type="hidden" value="<?= htmlspecialchars($cotizacion['idcotizacion']) ?>" id="idcotizacion" name="idcotizacion">

                        <input type="hidden" id="amortizacionFinalPEN" name="amortizacion">
                        <input type="hidden" id="tipoCambio" name="tipocambioaplicado">

                        <div class="row g-4">

                            <div class="col-md-4">
                                <label for="fechapago" class="form-label">Fecha de Pago <span class="text-danger fw-bold">*</span></label>
                                <input type="date" class="form-control" id="fechapago" name="fechapago" required>
                            </div>

                            <div class="col-md-4">
                                <label for="mediopago" class="form-label">Medio de Pago <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select" id="mediopago" name="mediopago" required>
                                    <option value="">Seleccione un medio</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Yape">Yape</option>
                                    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                    <option value="Plin">Plin</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                 <label for="montoOriginal" class="form-label mb-0">Monto a Pagar <span class="text-danger fw-bold">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="simboloMoneda">S/</span>
                                    <input type="number" class="form-control" id="montoOriginal" step="0.01" min="0.01" placeholder="0.00" name="montomonedaoriginal" required>
                                </div>

                                <div class="d-flex mt-2 gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" id="monedaPEN" value="PEN" checked>
                                            <label class="form-check-label" for="monedaPEN">Soles (S/)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" id="monedaUSD" value="USD">
                                            <label class="form-check-label" for="monedaUSD">Dólares ($)</label>
                                        </div>
                                    </div>
                            </div>

                             <!-- <div class="col-md-2">
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" id="monedaPEN" value="PEN" checked>
                                            <label class="form-check-label" for="monedaPEN">Soles (S/)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" id="monedaUSD" value="USD">
                                            <label class="form-check-label" for="monedaUSD">Dólares ($)</label>
                                        </div>
                                    </div>
                                </div> -->

                            <div class="col-md-4">
                                <label for="idcuentapago" class="form-label">Cuenta de Destino</label>
                                <select class="form-select" id="idcuentapago" name="idcuentapago">
                                    <option value="">Seleccione la cuenta</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="numerotransaccion" class="form-label">Número de Transacción</label>
                                <input type="text" class="form-control" id="numerotransaccion" name="numerotransaccion" placeholder="Opcional, ej: TRF-12345">
                            </div>

                            <div class="col-md-4">
                                <label for="comprobante" class="form-label">Comprobante de Pago</label>
                                <input type="file" class="form-control" id="comprobante" name="comprobante">
                            </div>

                            <div class="col-12">
                                <label for="observacion" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observacion" rows="3" placeholder="Añadir notas adicionales sobre el pago..." name="observacion"></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3 col-12">
                                <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Cancelar
                                </button>
                                <?php if ($completoInicial === 0) : ?>
                                    <button type="submit" class="btn btn-gradient text-white">
                                        <i class="bi bi-check-circle me-2"></i>Registrar Pago
                                    </button>
                                <?php else : ?>
                                    <span class="btn btn-success pe-none">
                                        <i class="bi bi-check2-circle me-2"></i>Inicial Completo
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>



        <!-- Pestaña Historial -->
        <div class="tab-pane fade" id="contenido-historial">
            <div class="card">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #9333ea 0%, #ec4899 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="tarjeta-icono">
                                <i class="bi bi-clock-history" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Historial de Pagos</h5>
                                <small class="opacity-75">Registro completo de todos los pagos realizados (<span id="cantidadPagos"><?= count($historialPagos) ?> pago<?= count($historialPagos) !== 1 ? 's' : '' ?></span>)</small>
                            </div>
                        </div>
                        <button class="btn btn-light btn-sm">
                            <i class="bi bi-download me-2"></i>Exportar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="contenedorHistorial">
                        <?php if (empty($historialPagos)) : ?>
                            <div class="sin-datos">
                                <i class="bi bi-clock-history"></i>
                                <p class="mb-2">No hay pagos registrados</p>
                                <small class="text-muted">Los pagos que registre aparecerán aquí</small>
                            </div>
                        <?php else: ?>
                            <?php
                            $totalPagado = 0;
                            foreach ($historialPagos as $pago) {
                                $totalPagado += (float)$pago['amortizacion'];
                            }
                            ?>
                            <div class="resumen-historial mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-success-emphasis d-block">Total Pagado</small>
                                        <strong class="text-success fs-5">

                                            S/ <?= number_format($totalPagado, 2, '.', ',') ?>
                                        </strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-success-emphasis d-block">Cantidad de Pagos</small>
                                        <strong class="text-success fs-5"><?= count($historialPagos) ?> pago<?= count($historialPagos) !== 1 ? 's' : '' ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Entidad Bancaria</th>
                                            <th>Cuenta</th>
                                            <th>Medio Pago</th>
                                            <th>N° Transacción</th>
                                            <th>Fecha Pago</th>
                                            <th class="text-end">Amortización</th>
                                            <th class="text-end">Saldo Restante</th>
                                            <th>Comprobante</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($historialPagos as $index => $pago): ?>
                                            <tr>
                                                <td><span class="badge badge-numero"><?= $index + 1 ?></span></td>
                                                <td><?= htmlspecialchars($pago['entidadbancaria'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($pago['numcuenta'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($pago['mediopago']) ?></td>
                                                <td><code class="codigo-transaccion"><?= htmlspecialchars($pago['numerotransaccion'] ?? '-') ?></code></td>
                                                <td><?= htmlspecialchars($pago['fechapago']) ?></td>
                                                <td class="text-end text-success">

                                                    <?= number_format((float)$pago['amortizacion'], 2, '.', ',') ?>
                                                </td>
                                                <td class="text-end text-warning">

                                                    <?= number_format((float)$pago['saldorestante'], 2, '.', ',') ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($pago['comprobante'])): ?>
                                                        <button data-bs-toggle="modal"
                                                            data-bs-target="#modalComprobante"
                                                            data-src="/archivos/<?= htmlspecialchars($pago['comprobante']) ?>" data-bs-toggle="modal" data-bs-target="#modalComprobante" class="badge bg-light text-dark ver-comprobante">Ver</button>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><small class="text-muted"><?= htmlspecialchars($pago['observacion'] ?? '-') ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<div class="modal fade" id="modalComprobante" tabindex="-1" aria-labelledby="modalComprobanteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);">
                <h5 class="modal-title" id="modalComprobanteLabel">Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <img id="imagenComprobante" src="" class="img-fluid" alt="Comprobante de Pago">
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
<script>
    const formularioPago = document.getElementById('formularioPago');
    const fechaPago = document.getElementById('fechapago');
    const modalComprobante = document.getElementById('modalComprobante');
    const selectCuentaPago = document.getElementById('idcuentapago');
    const selectMedioPago = document.getElementById('mediopago');
    const inputNumeroTransaccion = document.getElementById('numerotransaccion');
    const inputComprobante = document.getElementById('comprobante');

    // Elementos para la lógica de moneda
    const radioButtonsMoneda = document.querySelectorAll('input[name="moneda"]');
    const inputMontoOriginal = document.getElementById('montoOriginal');
    const inputTipoCambio = document.getElementById('tipoCambio');
    const simboloMoneda = document.getElementById('simboloMoneda');
    const inputAmortizacionFinalPEN = document.getElementById('amortizacionFinalPEN');
    const saldoPendienteActual = parseFloat(document.getElementById('saldoActualForm').dataset.valor);


    async function fetchTipoCambio() {
        try {

            const response = await fetch('/cotizacion/tipo-cambio');
            if (!response.ok) throw new Error('No se pudo obtener el tipo de cambio.');

            const data = await response.json();
            inputTipoCambio.value = data.tipo_cambio;
            calcularYActualizar();
        } catch (error) {
            console.error('Error al obtener tipo de cambio:', error);
            mostrarAlerta('error', 'No se pudo obtener el tipo de cambio. Intente de nuevo.');
            inputTipoCambio.value = '0.00';
        }
    }

    /**
     * Calcula la amortización en SOLES y actualiza la UI.
     */
    function calcularYActualizar() {
        const montoOriginal = parseFloat(inputMontoOriginal.value) || 0;
        const monedaSeleccionada = document.querySelector('input[name="moneda"]:checked').value;

        let amortizacionCalculadaPEN = 0;

        if (monedaSeleccionada === 'USD') {
            const tipoCambio = parseFloat(inputTipoCambio.value) || 0;
            // console.log(tipoCambio)
            amortizacionCalculadaPEN = montoOriginal * tipoCambio;
        } else {
            amortizacionCalculadaPEN = montoOriginal;
        }

        inputAmortizacionFinalPEN.value = amortizacionCalculadaPEN.toFixed(2);

        const nuevoSaldoRestante = saldoPendienteActual - amortizacionCalculadaPEN;

        const formatoNumero = {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        };

        if (montoOriginal > 0) {
            document.getElementById('montoAPagarContainer').style.display = 'block';
            document.getElementById('saldoRestanteContainer').style.display = 'block';
            document.getElementById('montoAPagar').textContent = 'S/ ' + amortizacionCalculadaPEN.toLocaleString('es-PE', formatoNumero);
            document.getElementById('saldoRestanteForm').textContent = 'S/ ' + nuevoSaldoRestante.toLocaleString('es-PE', formatoNumero);
        } else {
            document.getElementById('montoAPagarContainer').style.display = 'none';
            document.getElementById('saldoRestanteContainer').style.display = 'none';
        }
    }




    // --- EVENT LISTENERS ---

    document.addEventListener('DOMContentLoaded', () => {
        fechaPago.value = new Date().toISOString().slice(0, 10);
        cargarCuentasBancarias();
    });

    radioButtonsMoneda.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'USD') {
                simboloMoneda.textContent = '$';
                fetchTipoCambio();
            } else {
                simboloMoneda.textContent = 'S/';
                inputTipoCambio.value = '';
                calcularYActualizar();
            }
        });
    });

    inputMontoOriginal.addEventListener('input', calcularYActualizar);

    selectMedioPago.addEventListener('change', (e) => {
        inputNumeroTransaccion.disabled = false;
        selectCuentaPago.disabled = false;
        inputComprobante.disabled = false;

        switch (e.target.value) {
            case 'Efectivo':
                inputNumeroTransaccion.disabled = true;
                selectCuentaPago.disabled = true;
                inputComprobante.disabled = true;
                break;
            case 'Yape':
            case 'Plin':
                selectCuentaPago.disabled = true;
                break;
            case 'Transferencia Bancaria':
                // Todos habilitados
                break;
        }
    });

    modalComprobante.addEventListener('show.bs.modal', function(event) {
        const boton = event.relatedTarget;
        const rutaImagen = boton.getAttribute('data-src');
        document.getElementById('imagenComprobante').src = rutaImagen;
    });

    formularioPago.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (await ask('¿Confirmar pago de inicial?', 'Confirmar')) {
            validarYRegistrarPago();
        }
    });



    async function cargarCuentasBancarias() {
        try {
            const res = await fetch('/api/numcuentaspagos');
            const data = await res.json();

            selectCuentaPago.innerHTML = '<option value="">Seleccione la cuenta</option>';
            data.forEach(cuenta => {
                selectCuentaPago.innerHTML += `<option value="${cuenta.idcuentapago}">${cuenta.nombrecuenta}</option>`;
            });
        } catch (error) {
            console.error('Error al cargar cuentas:', error);
            mostrarAlerta('error', 'No se pudieron cargar las cuentas bancarias.');
        }
    }

    function validarFormulario() {
        const datos = new FormData(formularioPago);
        const medioPago = datos.get('mediopago');
        const moneda = datos.get('moneda');
        const tipoCambio = datos.get('tipocambioaplicado');
        const montoOriginal = parseFloat(datos.get('montomonedaoriginal'));
        const amortizacionFinal = parseFloat(inputAmortizacionFinalPEN.value);

        if (!datos.get('fechapago')) return 'Debe seleccionar la fecha de pago.';
        if (!medioPago) return 'Debe seleccionar un medio de pago.';
        if (!montoOriginal || montoOriginal <= 0) return 'El monto a pagar debe ser mayor a cero.';

        if (moneda === 'USD' && (!tipoCambio || parseFloat(tipoCambio) <= 0)) {
            return 'No se pudo obtener un tipo de cambio válido.';
        }

        if (amortizacionFinal > saldoPendienteActual + 0.01) {
            return 'El monto a pagar (convertido a S/) no puede ser mayor al saldo pendiente.';
        }

        if (medioPago === 'Transferencia Bancaria') {
            if (!datos.get('idcuentapago')) return 'Debe seleccionar una cuenta para la transferencia.';
            const comprobanteFile = datos.get('comprobante');
            if (!comprobanteFile || comprobanteFile.name === '') {
                return 'Debe subir el comprobante para Transferencia';
            }
            if (!datos.get('numerotransaccion').trim()) return 'Debe ingresar el número de transacción.';
        }
        if (medioPago === 'Yape' || medioPago === 'Plin') {
            if (!datos.get('numerotransaccion').trim()) return 'Debe ingresar el número de operación.';
            const comprobanteFile = datos.get('comprobante');
            if (!comprobanteFile || comprobanteFile.name === '') {
                return 'Debe subir el comprobante para pagos con Yape o Plin.';
            }
        }

        return null; // Si todo está bien
    }

    async function validarYRegistrarPago() {
        const error = validarFormulario();
        if (error) {
            mostrarAlerta('error', error);
            return;
        }

        const formData = new FormData(formularioPago);
        const monto = parseFloat(formData.get('amortizacion'));
        const nuevoSaldoRestante = saldoPendienteActual - monto;
        formData.append('saldorestante', nuevoSaldoRestante.toFixed(2));

        try {
            const request = await fetch('/cotizaciones/storePagoInicial', {
                method: 'POST',
                body: formData
            });
            const response = await request.json();

            if (response.success) {
                mostrarAlerta('exito', response.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                mostrarAlerta('error', response.message || 'Ocurrió un error inesperado.');
            }
        } catch (fetchError) {
            console.error('Error en el fetch:', fetchError);
            mostrarAlerta('error', 'No se pudo conectar con el servidor.');
        }
    }

    function limpiarFormulario() {
        formularioPago.reset();
        document.getElementById('monedaPEN').checked = true; // Reinicia a PEN
        simboloMoneda.textContent = 'S/';
        document.getElementById('montoAPagarContainer').style.display = 'none';
        document.getElementById('saldoRestanteContainer').style.display = 'none';
        ocultarAlertas();
    }

    function mostrarAlerta(tipo, mensaje) {
        ocultarAlertas();
        const alertaExito = document.getElementById('alertaExito');
        const alertaError = document.getElementById('alertaError');

        if (tipo === 'exito') {
            alertaExito.querySelector('.text-success-emphasis').textContent = mensaje;
            alertaExito.classList.remove('d-none');
        } else if (tipo === 'error') {
            alertaError.querySelector('#mensajeError').textContent = mensaje;
            alertaError.classList.remove('d-none');
        }
    }

    function ocultarAlertas() {
        document.getElementById('alertaExito').classList.add('d-none');
        document.getElementById('alertaError').classList.add('d-none');
    }
</script>