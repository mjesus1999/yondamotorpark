<?php include __DIR__ . '/../layout/header.php'; ?>

<?php

// var_dump($montosInfo); 


var_dump($historialPagos) ?>


<style>
    :root {
        --color-primary: #2563eb;
        --color-primary-dark: #1e40af;
        --color-secondary: #7c3aed;
        --color-success: #10b981;
        --color-warning: #f59e0b;
        --color-danger: #ef4444;
    }

    body {
        background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .encabezado-principal {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .tarjeta-icono {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 0.75rem;
        padding: 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .tarjeta-resumen {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 0.75rem;
        padding: 1rem;
        transition: transform 0.2s;
    }

    .tarjeta-resumen:hover {
        transform: translateY(-2px);
    }

    .nav-pills .nav-link {
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        color: #64748b;
        transition: all 0.3s;
    }

    .nav-pills .nav-link:hover {
        background-color: #f1f5f9;
        color: var(--color-primary);
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        border-bottom: 2px solid #f1f5f9;
        font-weight: 600;
        padding: 1.25rem 1.5rem;
    }

    .info-item {
        background: #f1f5f9;
        border-radius: 0.75rem;
        padding: 1rem;
        color: #475569;
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        transition: background 0.2s;
    }

    .info-item:hover {
        background: #f1f5f9;
    }

    .info-icono {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .info-icono.azul {
        background: #dbeafe;
        color: var(--color-primary);
    }

    .info-icono.morado {
        background: #ede9fe;
        color: var(--color-secondary);
    }

    .info-icono.verde {
        background: #d1fae5;
        color: var(--color-success);
    }

    .form-control,
    .form-select {
        border-radius: 0.5rem;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn {
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-gradient {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
    }

    .alerta-saldo {
        background: linear-gradient(135deg, #cca404ff 0%, #fed7aa 100%);
        border: 2px solid #f1d58cff;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .alerta-exito {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border: 2px solid #10b981;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .alerta-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid #ef4444;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }

    .table tbody tr {
        transition: background 0.2s;
    }

    .table tbody tr:hover {
        background-color: #dbeafe;
    }

    .badge {
        padding: 0.4rem 0.75rem;
        border-radius: 0.5rem;
        font-weight: 500;
    }

    .badge-numero {
        background: #dbeafe;
        color: var(--color-primary);
    }

    .resumen-historial {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border: 2px solid #10b981;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }



    .sin-datos {
        text-align: center;
        padding: 3rem 1rem;
        color: #64748b;
    }

    .sin-datos i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .encabezado-principal {
            padding: 1.5rem;
        }

        .tarjeta-resumen {
            margin-bottom: 0.75rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

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

    .codigo-transaccion {
        background: #f1f5f9;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        color: #475569;
    }
</style>

<div class="container-fluid py-4 px-3 px-md-5">
    <!-- Encabezado Principal -->
    <div class="encabezado-principal">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="tarjeta-icono">
                <i class="bi bi-car-front-fill" style="font-size: 2rem;"></i>
            </div>
            <div>
                <h1 class="h2 mb-1">Gestión de Pagos - Cotización #<span id="numeroCotizacion">34</span></h1>
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
                    <strong class="fs-5" id="totalPagado">PEN <?= number_format(htmlspecialchars($montosInfo['totalpagado']), 2, '.') ?></strong>
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
                        Aprobada
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
                                    <strong id="direccionCliente"><?= htmlspecialchars($cotizacion['direccion' ?? 'No especificado']) ?></strong>
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
                                <small class="text-warning-emphasis  fw-bold d-block">Saldo Pendiente Actual</small>
                                <strong class="text-warning-emphasis fs-5" id="saldoActualForm">PEN 45,000.00</strong>
                            </div>
                            <div class="col-md-4" id="montoAPagarContainer" style="display: none;">
                                <small class="text-warning-emphasis d-block">Monto a Pagar</small>
                                <strong class="text-warning-emphasis fs-5" id="montoAPagar">PEN 0.00</strong>
                            </div>
                            <div class="col-md-4" id="saldoRestanteContainer" style="display: none;">
                                <small class="text-warning-emphasis d-block">Saldo Restante</small>
                                <strong class="text-warning-emphasis fs-5" id="saldoRestanteForm">PEN 0.00</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form id="formularioPago">
                        <div class="row g-4">

                            <div class="col-md-4">
                                <label for="fechapago" class="form-label">Fecha de Pago <span class="text-danger fgw-bold">*</span></label>
                                <input type="date" class="form-control" id="fechapago" required>
                            </div>

                            <!-- Concepto -->
                            <div class="col-md-4">
                                <label for="idconcepto" class="form-label">Concepto de Pago <span class="text-danger fgw-bold">*</span></label>
                                <select class="form-select" id="idconcepto" required>
                                    <option value="">Seleccione el concepto</option>
                                    <option value="Inicial">Inicial</option>
                                    <option value="Cuota Mensual">Cuota Mensual</option>
                                    <option value="Pago Adelantado">Pago Adelantado</option>
                                    <option value="Pago Extraordinario">Pago Extraordinario</option>
                                </select>
                            </div>


                            <div class="col-md-4">
                                <label for="mediopago" class="form-label">Medio de Pago <span class="text-danger fgw-bold">*</span></label>
                                <select class="form-select" id="mediopago" name="mediopago">
                                    <option value="">Seleccione un medio de pago</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Yape">Yape</option>
                                    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                    <option value="Plin">Plin</option>
                                </select>
                            </div>

                            <!-- Cuenta de Pago -->
                            <div class="col-md-6">
                                <label for="idcuentapago" class="form-label">Cuenta de Pago </label>
                                <select class="form-select" id="idcuentapago" required>
                                    <option value="">Seleccione la cuenta</option>
                                    <option value="BCP - Cuenta Corriente - 191-123456789-0-50">BCP - Cuenta Corriente - 191-123456789-0-50</option>
                                    <option value="BBVA - Cuenta de Ahorros - 0011-0222-0333344444">BBVA - Cuenta de Ahorros - 0011-0222-0333344444</option>
                                    <option value="Interbank - Cuenta Corriente - 200-3001234567">Interbank - Cuenta Corriente - 200-3001234567</option>
                                    <option value="Scotiabank - Cuenta de Ahorros - 039-123456">Scotiabank - Cuenta de Ahorros - 039-123456</option>
                                </select>
                            </div>



                            <!-- Número de Transacción -->
                            <div class="col-md-6">
                                <label for="numerotransaccion" class="form-label">Número de Transacción </label>
                                <input type="text" class="form-control" id="numerotransaccion" placeholder="Ej: TRF-2025-001234" required>
                            </div>



                            <!-- Amortización -->
                            <div class="col-md-6">
                                <label for="amortizacion" class="form-label">Monto a Amortizar (PEN) <span class="text-danger fgw-bold">*</span></label>
                                <input type="number" class="form-control" id="amortizacion" step="0.01" min="0.01" placeholder="0.00" required>
                            </div>

                            <!-- Comprobante -->
                            <div class="col-md-6">
                                <label for="comprobante" class="form-label">Comprobante</label>
                                <input type="file" class="form-control" id="comprobante" placeholder="Nombre del archivo o código">
                            </div>

                            <!-- Observación -->
                            <div class="col-12">
                                <label for="observacion" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observacion" rows="4" placeholder="Ingrese observaciones adicionales sobre el pago..."></textarea>
                            </div>


                            <!-- Botones -->
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Cancelar
                                </button>

                                <button type="submit" class="btn btn-gradient text-white">
                                    <i class="bi bi-check-circle me-2"></i>Registrar Pago
                                </button>
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
                                                        <a href="<?= htmlspecialchars($pago['comprobante']) ?>" target="_blank" class="badge bg-light text-dark">Ver</a>
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



<script>
    // DATOS DE LA COTIZACIÓN
    const datosCotizacion = {
        idcotizacion: 34,
        idvehiculo: 167,
        tipocotizacion: "Independiente formal",
        vehiculo: "KIA / Carens / 2025 / MORADO",
        precioventa: 58044.80,
        moneda: "PEN",
        inicial: 45000.00,
        nombrecliente: "PADILLA CHILET, CAROLINA ALEXANDRA",
        documento: "70255454",
        telefono: "965545454",
        direccion: "Vía Expresa Elmer Faucett, 25 de Febrero",
        numcuotas: 24,
        valorcuota: 878.62,
        estadocotizacion: "A"
    };

    // ARRAY DE PAGOS
    let listaPagos = [];

    // FUNCIONES AUXILIARES
    function formatearMoneda(cantidad) {
        return cantidad.toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatearFecha(fechaString) {
        const fecha = new Date(fechaString);
        const opciones = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        return fecha.toLocaleDateString('es-PE', opciones);
    }

    function formatearFechaHora(fechaString) {
        const fecha = new Date(fechaString);
        const opciones = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        };
        return fecha.toLocaleString('es-PE', opciones);
    }

    function calcularTotalPagado() {
        return listaPagos.reduce((total, pago) => total + parseFloat(pago.amortizacion), 0);
    }

    function calcularSaldoPendiente() {
        return datosCotizacion.inicial - calcularTotalPagado();
    }

    // // INICIALIZAR DATOS
    // function inicializarDatos() {
    //     Encabezado
    //     document.getElementById('numeroCotizacion').textContent = datosCotizacion.idcotizacion;


    //     document.getElementById('nombreCliente').textContent = datosCotizacion.nombrecliente;
    //     document.getElementById('documentoCliente').textContent = datosCotizacion.documento;
    //     document.getElementById('telefonoCliente').textContent = datosCotizacion.telefono;
    //     document.getElementById('direccionCliente').textContent = datosCotizacion.direccion;

    //     Detalles Vehículo
    //     document.getElementById('nombreVehiculo').textContent = datosCotizacion.vehiculo;
    //     document.getElementById('idVehiculo').textContent = '#' + datosCotizacion.idvehiculo;
    //     document.getElementById('precioVenta').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(datosCotizacion.precioventa);
    //     document.getElementById('tipoCotizacion').textContent = datosCotizacion.tipocotizacion;

    //     Tabla Financiamiento
    //     document.getElementById('tablaPrecionVenta').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(datosCotizacion.precioventa);
    //     document.getElementById('tablaInicial').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(datosCotizacion.inicial);
    //     document.getElementById('tablaFinanciar').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(datosCotizacion.precioventa - datosCotizacion.inicial);
    //     document.getElementById('tablaCuotas').textContent = datosCotizacion.numcuotas + ' cuotas';
    //     document.getElementById('tablaValorCuota').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(datosCotizacion.valorcuota);

    //     Fecha máxima para el formulario
    //     const hoy = new Date().toISOString().split('T')[0];
    //     document.getElementById('fechapago').setAttribute('max', hoy);

    //     actualizarResumen();
    // }

    // ACTUALIZAR RESUMEN
    function actualizarResumen() {
        const totalPagado = calcularTotalPagado();
        const saldoPendiente = calcularSaldoPendiente();

        document.getElementById('inicialRequerido').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(datosCotizacion.inicial);
        document.getElementById('totalPagado').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(totalPagado);
        document.getElementById('saldoPendiente').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(saldoPendiente);
        document.getElementById('saldoActualForm').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(saldoPendiente);

        // Actualizar estado
        const elementoEstado = document.getElementById('estadoPago');
        if (saldoPendiente <= 0) {
            elementoEstado.className = 'estado-badge estado-completado';
            elementoEstado.innerHTML = '<span class="badge rounded-circle bg-success p-1"></span> Completado';
        } else {
            elementoEstado.className = 'estado-badge estado-pendiente';
            elementoEstado.innerHTML = '<span class="badge rounded-circle bg-warning p-1"></span> Pendiente';
        }
    }

    // CALCULAR SALDO RESTANTE EN TIEMPO REAL
    document.getElementById('amortizacion').addEventListener('input', function() {
        const monto = parseFloat(this.value) || 0;
        const saldoPendiente = calcularSaldoPendiente();
        const saldoRestante = saldoPendiente - monto;

        if (monto > 0) {
            document.getElementById('montoAPagarContainer').style.display = 'block';
            document.getElementById('saldoRestanteContainer').style.display = 'block';
            document.getElementById('montoAPagar').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(monto);
            document.getElementById('saldoRestanteForm').textContent = datosCotizacion.moneda + ' ' + formatearMoneda(saldoRestante);
        } else {
            document.getElementById('montoAPagarContainer').style.display = 'none';
            document.getElementById('saldoRestanteContainer').style.display = 'none';
        }
    });

    // VALIDAR FORMULARIO
    function validarFormulario(datos) {
        if (!datos.idconcepto) return 'Debe seleccionar un concepto';
        if (!datos.idcuentapago) return 'Debe seleccionar una cuenta de pago';
        if (!datos.mediopago) return 'Debe seleccionar un medio de pago';
        if (!datos.numerotransaccion.trim()) return 'Debe ingresar el número de transacción';
        if (!datos.fechapago) return 'Debe seleccionar la fecha de pago';
        if (!datos.amortizacion || parseFloat(datos.amortizacion) <= 0) return 'Debe ingresar un monto válido';
        if (parseFloat(datos.amortizacion) > calcularSaldoPendiente()) return 'El monto no puede ser mayor al saldo pendiente';
        return null;
    }

    // LIMPIAR FORMULARIO
    function limpiarFormulario() {
        document.getElementById('formularioPago').reset();
        document.getElementById('montoAPagarContainer').style.display = 'none';
        document.getElementById('saldoRestanteContainer').style.display = 'none';
        ocultarAlertas();
    }

    // MOSTRAR ALERTA
    function mostrarAlerta(tipo, mensaje) {
        ocultarAlertas();

        if (tipo === 'exito') {
            document.getElementById('alertaExito').classList.remove('d-none');
        } else if (tipo === 'error') {
            document.getElementById('mensajeError').textContent = mensaje;
            document.getElementById('alertaError').classList.remove('d-none');
        }

        // Scroll al inicio del formulario
        document.getElementById('contenido-pago').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // OCULTAR ALERTAS
    function ocultarAlertas() {
        document.getElementById('alertaExito').classList.add('d-none');
        document.getElementById('alertaError').classList.add('d-none');
    }

    // REGISTRAR PAGO
    document.getElementById('formularioPago').addEventListener('submit', function(e) {
        e.preventDefault();

        const datosPago = {
            idconcepto: document.getElementById('idconcepto').value,
            idcuentapago: document.getElementById('idcuentapago').value,
            mediopago: document.getElementById('mediopago').value,
            numerotransaccion: document.getElementById('numerotransaccion').value,
            fechapago: document.getElementById('fechapago').value,
            amortizacion: document.getElementById('amortizacion').value,
            comprobante: document.getElementById('comprobante').value || 'Sin comprobante',
            observacion: document.getElementById('observacion').value || 'Sin observaciones'
        };

        const error = validarFormulario(datosPago);
        if (error) {
            mostrarAlerta('error', error);
            return;
        }

        const nuevoPago = {
            id: listaPagos.length + 1,
            idconcepto: datosPago.idconcepto,
            idcuentapago: datosPago.idcuentapago,
            mediopago: datosPago.mediopago,
            numerotransaccion: datosPago.numerotransaccion,
            fechapago: datosPago.fechapago,
            amortizacion: parseFloat(datosPago.amortizacion).toFixed(2),
            saldorestante: (calcularSaldoPendiente() - parseFloat(datosPago.amortizacion)).toFixed(2),
            comprobante: datosPago.comprobante,
            observacion: datosPago.observacion,
            fecharegistro: new Date().toISOString()
        };

        listaPagos.push(nuevoPago);
        mostrarAlerta('exito', '');
        actualizarResumen();
        renderizarHistorial();

        setTimeout(() => {
            limpiarFormulario();
        }, 2000);
    });
</script>



<?php include __DIR__ . '/../layout/footer.php'; ?>