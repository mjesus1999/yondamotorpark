<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    .card-pagos {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-header-pagos {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
        padding: 1rem 1.5rem;
        border-bottom: none;
    }

    .table-pagos th,
    .table-pagos td {
        padding: 0.45rem 0.5rem !important;
        font-size: 0.97rem;
        vertical-align: middle !important;
    }

    .table-pagos th {
        font-size: 0.85rem;
        letter-spacing: 0.03em;
    }


    .table-pagos .no-imprimir {
        min-width: 110px;
    }


    /* Estilos para el modal */
    .modal-pago {
        border-radius: 12px;
        overflow: hidden;
        border: none;
    }

    .modal-header-pago {
        background: linear-gradient(135deg, #3a7bd5, #00d2ff);
        color: white;
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .modal-title-pago {
        font-weight: 600;
    }


    .form-floating-pago label {
        color: #6c757d;
    }

    .form-floating-pago .form-control {
        border-radius: 8px;
        padding: 1rem 0.75rem;
        border: 1px solid #e0e0e0;
    }

    .form-floating-pago .form-control:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 0.25rem rgba(58, 123, 213, 0.25);
    }

    /* Estilos para el mensaje de no hay pagos */
    .empty-state {
        padding: 2rem;
        text-align: center;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<div class="container-fluid mt-4">

    <?php if (!empty($autos)): ?>
        <div class="d-none d-md-block">
            <div class="card mb-4 shadow-sm">
                <div class="card-header card-header-pagos text-white">
                    <h5 class="mb-0"><i class="fas fa-car me-2"></i>INVENTARIO DE VEHÍCULOS</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-pagos table-hover align-middle mb-0" id="tabla-vehiculos">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">Marca / Modelo</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Versión</th>
                                    <th class="text-center">Combustible</th>
                                    <th class="text-center">Color</th>
                                    <th class="text-center">Chasis</th>
                                    <th class="text-center">Placa</th>
                                    <th class="text-center">P. Rotativa</th>
                                    <th class="text-center">S. Motor</th>
                                    <th class="text-center">Año</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($autos as $auto): ?>
                                    <tr>
                                        <td class="fw-bold text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['marca'] ?? '') ?> / <?= htmlspecialchars($auto['modelo'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['tipovehiculo'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['version'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['combustible'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['color'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['chasis'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['placa'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['placarotativa'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['seriemotor'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><?= htmlspecialchars($auto['anio'] ?? '') ?></td>
                                        <td class="text-center" style="font-size: 12px;"><span class="badge bg-<?= ($auto['condicion'] ?? '') == 'Nuevo' ? 'success' : 'warning' ?>"><?= htmlspecialchars($auto['condicion'] ?? '') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-block d-md-none">
            <div class="card mb-4 shadow-sm">
                <div class="card-header card-header-pagos text-white">
                    <h5 class="mb-0"><i class="fas fa-car me-2"></i>INVENTARIO DE VEHÍCULOS</h5>
                </div>
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="acordeonVehiculos">
                        <?php foreach ($autos as $key => $auto): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingVehiculo<?= $key ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVehiculo<?= $key ?>" aria-expanded="false" aria-controls="collapseVehiculo<?= $key ?>">
                                        <i class="fas fa-car me-2"></i><strong><?= htmlspecialchars($auto['marca'] ?? '') ?> / <?= htmlspecialchars($auto['modelo'] ?? '') ?></strong>
                                    </button>
                                </h2>
                                <div id="collapseVehiculo<?= $key ?>" class="accordion-collapse collapse" aria-labelledby="headingVehiculo<?= $key ?>" data-bs-parent="#acordeonVehiculos">
                                    <div class="accordion-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Tipo:</strong> <?= htmlspecialchars($auto['tipovehiculo'] ?? '') ?></li>
                                            <li class="list-group-item"><strong>Versión:</strong> <?= htmlspecialchars($auto['version'] ?? '') ?></li>
                                            <li class="list-group-item"><strong>Combustible:</strong> <?= htmlspecialchars($auto['combustible'] ?? '') ?></li>
                                            <li class="list-group-item"><strong>Color:</strong> <?= htmlspecialchars($auto['color'] ?? '') ?></li>
                                            <li class="list-group-item"><strong>Chasis:</strong> <?= htmlspecialchars($auto['chasis'] ?? '') ?></li>
                                            <li class="list-group-item"><strong>Placa:</strong> <?= htmlspecialchars($auto['placa'] ?? '') ?></li>
                                            <li class="list-group-item"><strong>P. Rotativa:</strong> <?= htmlspecialchars($auto['placarotativa'] ?? '') ?></li>
                                            <li class="list-group-item"><strong>S. Motor:</strong> <?= htmlspecialchars($auto['seriemotor'] ?? '') ?></li>
                                            <li class="list-group-item"><strong>Año:</strong> <?= htmlspecialchars($auto['anio'] ?? '') ?></li>
                                            <li class="list-group-item">
                                                <strong>Estado:</strong>
                                                <span class="badge bg-<?= ($auto['condicion'] ?? '') == 'Nuevo' ? 'success' : 'warning' ?>"><?= htmlspecialchars($auto['condicion'] ?? '') ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No hay vehículos disponibles en este momento.
        </div>
    <?php endif; ?>



    <div class="row">
        <div class="col-md-12">
            <div class="card card-pagos">
                <div class="card-header card-header-pagos">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">

                        <div class="mb-2 mb-md-0 d-flex flex-column align-items-start">
                            <h6 class="mb-1 fw-semibold text-white">
                                <i class="fas fa-money-check-alt me-2"></i> Pagos Realizados
                            </h6>

                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <?php if (isset($concesionario['concesionario'])): ?>
                                    <span class="badge bg-white text-primary" id="concesionario">
                                        Concesionario: <?= htmlspecialchars($concesionario['concesionario']) ?> - <?= $concesionario['ubicacion'] ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (isset($concesionario['idordencompra'])): ?>
                                    <span class="badge bg-white text-primary">OC #<?= htmlspecialchars($concesionario['idordencompra']) ?></span>
                                    <span class="badge bg-white text-primary" id="numeroIdentificadorOC"><?= htmlspecialchars($concesionario['numeroOCIdentificador']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-start justify-content-md-end">
                            <span class="badge bg-white text-primary p-2" id="total-pagado">
                                <i class="fas fa-dollar-sign me-1"></i>
                                Total Pagado: $<?= isset($totalAmortizado) ? number_format($totalAmortizado, 2) : '0.00' ?>
                            </span>
                            <button class="btn btn-danger btn-sm" id="btn-generar-pdf">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </button>
                            <button class="btn btn-success btn-sm" id="btn-excel">
                                <i class="bi bi-file-earmark-excel me-1"></i> Excel
                            </button>
                        </div>

                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-pagos table-hover align-middle mb-0" id="tabla-pagos-datos">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Fecha Real Pago</th>
                                    <th>Entidad</th>
                                    <th>N° Transacción</th>
                                    <th>Moneda</th>
                                    <th>Monto Pagado</th>
                                    <th>Saldo Restante</th>
                                    <th>T. Cambio</th>
                                    <th>Valor Dólares</th>
                                    <th>Observaciones</th>
                                    <th>Comprobante</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-pagos">
                                <?php if (!empty($pagos)): ?>
                                    <?php $numFila = 1; ?>
                                    <?php foreach ($pagos as $pago): ?>
                                        <tr>
                                            <td class="ps-4"><?= $numFila++ ?></td>
                                            <td>
                                                <?php if (!empty($pago['fecharealpago'])): ?>
                                                    <span class="badge bg-light text-dark">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        <?= date('d/m/Y H:i', strtotime($pago['fecharealpago'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($pago['entidad'] ?? 'N/A') ?></td>
                                            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($pago['numtransaccion'] ?? 'N/A') ?></span></td>
                                            <td>
                                                <?= $pago['moneda'] === 'USD'
                                                    ? '<span class="badge bg-success">USD</span>'
                                                    : ($pago['moneda'] === 'PEN'
                                                        ? '<span class="badge bg-primary">PEN</span>'
                                                        : '<span class="badge bg-secondary">' . htmlspecialchars($pago['moneda']) . '</span>'
                                                    )
                                                ?>
                                            </td>
                                            <td class="fw-semibold text-success">
                                                <?php if (($pago['moneda'] ?? '') === 'USD'): ?>
                                                    $
                                                <?php elseif (($pago['moneda'] ?? '') === 'PEN'): ?>
                                                    S/
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                                <?= number_format($pago['amortizacion'] ?? 0, 2) ?>
                                            </td>
                                            <td class="text-danger fw-bold">$ <?= number_format($pago['saldo'] ?? 0, 2) ?></td>
                                            <td><span class="badge bg-info text-white"><?= htmlspecialchars($pago['tipocambio'] ?? '') ?></span></td>
                                            <td class="fw-bold text-success">
                                                <?= $pago['valorUSD'] ? '$ ' . htmlspecialchars($pago['valorUSD']) : '' ?>
                                            </td>
                                            <td class="celda-observaciones" data-bs-toggle="tooltip" title="<?= htmlspecialchars($pago['observaciones'] ?? '') ?>">
                                                <?php if (!empty($pago['observaciones'])): ?>
                                                    <button type="button" class="btn btn-link p-0 ver-detalle-observacion" data-bs-toggle="modal" data-bs-target="#modalDetalleObservacion" data-observacion="<?= htmlspecialchars($pago['observaciones']) ?>">
                                                        <i class="fas fa-eye me-1"></i> Ver detalle
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">Sin observación</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="no-imprimir">
                                                <?php if (!empty($pago['comprobante'])): ?>
                                                    <?php $esPdf = strtolower(pathinfo($pago['comprobante'], PATHINFO_EXTENSION)) === 'pdf'; ?>
                                                    <?php $urlSegura = "/archivos/" . htmlspecialchars($pago['comprobante']); ?>
                                                    <?php if ($esPdf): ?>
                                                        <a href="<?= $urlSegura ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-file-pdf me-1"></i> Ver PDF
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-primary ver-comprobante-img" data-img="<?= $urlSegura ?>">
                                                            <i class="fas fa-image me-1"></i>Comprobante
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted">Sin archivo</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="12" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-money-bill-wave"></i>
                                                <h5 class="mt-3">No hay pagos registrados</h5>
                                                <p class="mb-0">No se han encontrado pagos para esta orden de compra.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-block d-md-none">
                        <div class="accordion accordion-flush" id="acordeonPagos">
                            <?php if (!empty($pagos)): ?>
                                <?php $numFila = 1; ?>
                                <?php foreach ($pagos as $pago): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingPago<?= $numFila ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePago<?= $numFila ?>" aria-expanded="false" aria-controls="collapsePago<?= $numFila ?>">
                                                <i class="fas fa-money-bill-alt me-2"></i> Pago #<?= $numFila ?>
                                                <span class="badge bg-<?= ($pago['moneda'] ?? '') == 'USD' ? 'success' : 'primary' ?> ms-auto">
                                                    <?= ($pago['moneda'] ?? '') == 'USD' ? '$' : 'S/' ?><?= number_format($pago['amortizacion'] ?? 0, 2) ?>
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapsePago<?= $numFila ?>" class="accordion-collapse collapse" aria-labelledby="headingPago<?= $numFila ?>" data-bs-parent="#acordeonPagos">
                                            <div class="accordion-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pago['fecharealpago'])) ?></li>
                                                    <li class="list-group-item"><strong>Entidad:</strong> <?= htmlspecialchars($pago['entidad'] ?? 'N/A') ?></li>
                                                    <li class="list-group-item"><strong>N° Transacción:</strong> <?= htmlspecialchars($pago['numtransaccion'] ?? 'N/A') ?></li>
                                                    <li class="list-group-item"><strong>Moneda:</strong> <?= htmlspecialchars($pago['moneda'] ?? '') ?></li>
                                                    <li class="list-group-item"><strong>Monto Pagado:</strong> <?= number_format($pago['amortizacion'] ?? 0, 2) ?></li>
                                                    <li class="list-group-item"><strong>Saldo Restante:</strong> $ <?= number_format($pago['saldo'] ?? 0, 2) ?></li>
                                                    <li class="list-group-item"><strong>Tipo de Cambio:</strong> <?= htmlspecialchars($pago['tipocambio'] ?? 'N/A') ?></li>
                                                    <li class="list-group-item"><strong>Valor USD:</strong> <?= $pago['valorUSD'] ? '$ ' . htmlspecialchars($pago['valorUSD']) : 'N/A' ?></li>
                                                    <li class="list-group-item">
                                                        <strong>Observaciones:</strong>
                                                        <?php if (!empty($pago['observaciones'])): ?>
                                                            <button type="button" class="btn btn-link p-0 ver-detalle-observacion" data-bs-toggle="modal" data-bs-target="#modalDetalleObservacion" data-observacion="<?= htmlspecialchars($pago['observaciones']) ?>">
                                                                <i class="fas fa-eye me-1"></i> Ver detalle
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="text-muted">Sin observación</span>
                                                        <?php endif; ?>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Comprobante:</strong>
                                                        <?php if (!empty($pago['comprobante'])): ?>
                                                            <?php $esPdf = strtolower(pathinfo($pago['comprobante'], PATHINFO_EXTENSION)) === 'pdf'; ?>
                                                            <?php $urlSegura = "/archivos/" . htmlspecialchars($pago['comprobante']); ?>
                                                            <?php if ($esPdf): ?>
                                                                <a href="<?= $urlSegura ?>" target="_blank" class="btn btn-sm btn-outline-danger w-100 mt-2">
                                                                    <i class="fas fa-file-pdf me-1"></i> Ver PDF
                                                                </a>
                                                            <?php else: ?>
                                                                <button type="button" class="btn btn-sm btn-primary ver-comprobante-img w-100 mt-2" data-img="<?= $urlSegura ?>">
                                                                    <i class="fas fa-image me-1"></i>Comprobante
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="badge bg-light text-muted">Sin archivo</span>
                                                        <?php endif; ?>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $numFila++; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info mt-3 mx-3 text-center">
                                    No hay pagos registrados para esta orden de compra.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end align-items-center border-top gap-3">
                    <a href="<?= $saldoRestante > 0 ? '/oc/listar/proceso' : '/oc/listar/pagado' ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Volver
                    </a>
                    <?php if ($saldoRestante > 0): ?>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPago">
                            <i class="fas fa-plus-circle me-1"></i> Registrar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetalleObservacion" tabindex="-1" aria-labelledby="modalDetalleObservacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetalleObservacionLabel"><i class="fas fa-info-circle me-2"></i>Detalle de Observación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="textoDetalleObservacion" class="mb-0" style="white-space: pre-line;"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content modal-pago">
                <div class="modal-header modal-header-pago">
                    <h5 class="modal-title modal-title-pago" id="modalPagoLabel">
                        <i class="fas fa-plus-circle me-2"></i>
                        Registrar Pago (OC #<?= htmlspecialchars($concesionario['idordencompra'] ?? '---') ?>)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPago" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="idorden" value="<?= htmlspecialchars($concesionario['idordencompra'] ?? 0) ?>">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0 fw-semibold"><i class="fas fa-money-bill-wave me-2"></i>Información de Montos</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="amortizacion" id="amortizacion" class="form-control" placeholder="0.00" required>
                                            <label for="amortizacion"><i class="fas fa-dollar-sign me-1"></i> Monto a pagar <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" id="saldo" name="saldo" class="form-control bg-light fw-bold text-success" value="<?= number_format($saldoRestante ?? 0, 2) ?>" disabled>
                                            <label for="saldo"><i class="fas fa-wallet me-1"></i> Saldo restante</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between border rounded p-2 bg-body">
                                            <p>Moneda <span class="text-danger fw-bold">*</span></p>
                                            <div class="form-check">
                                                <input class="form-check-input border-primary" type="radio" name="moneda" id="usd" value="USD" checked>
                                                <label class="form-check-label fw-semibold text-body" for="usd">USD</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input border-primary" type="radio" name="moneda" id="pen" value="PEN">
                                                <label class="form-check-label fw-semibold text-body" for="pen">SOLES</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" id="tipocambio" name="tipocambio" class="form-control" placeholder="Tipo de Cambio (si aplica)">
                                            <label for="tipocambio"><i class="fas fa-exchange-alt me-1"></i> Tipo de Cambio (si aplica)</label>
                                        </div>
                                    </div>
                                    <input type="hidden" id="valorUSD" name="valorUSD">
                                    <div class="row g-2 mb-1">
                                        <div id="valorCalculado" class="alert alert-info d-none mt-3 p-2">
                                            Monto aproximado en USD: <strong id="valorCalculadoTexto"></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0 fw-semibold"><i class="fas fa-exchange-alt me-2"></i>Detalles de Transacción</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input type="datetime-local" id="fecharealpago" name="fecharealpago" class="form-control" required>
                                            <label for="fecharealpago"><i class="far fa-calendar-alt me-1"></i> Fecha de pago <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="identidadpago" name="identidadpago" required>
                                                <?php foreach ($entidadesPago as $entidad): ?>
                                                    <option value="<?= htmlspecialchars($entidad['identidadpago']) ?>"><?= htmlspecialchars($entidad['entidad']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="identidadpago"><i class="fas fa-university me-1"></i> Entidad de pago <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" id="numtransaccion" name="numtransaccion" class="form-control" placeholder="Número de Transacción" maxlength="30" required>
                                            <label for="numtransaccion"><i class="fas fa-receipt me-1"></i> N° Transacción <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <label for="comprobante" class="form-label fw-semibold">
                                                <i class="fas fa-file-upload me-1"></i> Comprobante <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" name="comprobante" id="comprobante" class="form-control" accept="application/pdf,image/*" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4 border-0 shadow-sm p-3">
                            <div class="card-header bg-info text-white py-2">
                                <h6 class="mb-0 fw-semibold"><i class="fas fa-info-circle me-2"></i>Información Adicional</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-floating">
                                    <textarea class="form-control" id="observaciones" name="observaciones" placeholder="Observaciones" style="height: 80px;" maxlength="385"></textarea>
                                    <label for="observaciones"><i class="fas fa-comments me-1"></i> Observaciones (opcional)</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm px-4" id="btn-agregar-pago">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalImagenComprobante" tabindex="-1" aria-labelledby="modalImagenComprobanteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="border-0">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalImagenComprobanteLabel">
                        <i class="fas fa-image me-2"></i> Comprobante de Pago
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <img id="imagenAmpliada" src="" class="img-fluid w-100" alt="Comprobante de Pago" style="max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" defer></script>
<script src="/assets/js/logoBase64.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>

<script src="/assets/js/pagosOc-pdf/pdf.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // =========================================================================
        //  1. Selectores de Elementos y Variables Globales
        // =========================================================================
        const btnExcel = document.getElementById('btn-excel');
        const modalDetalleObservacion = new bootstrap.Modal(document.getElementById('modalDetalleObservacion'));
        const modalImagenComprobante = new bootstrap.Modal(document.getElementById('modalImagenComprobante'));

        const amortizacionInput = document.getElementById("amortizacion");
        const saldoInput = document.getElementById("saldo");
        const btnGuardarPago = document.getElementById("btn-agregar-pago");
        const formPago = document.getElementById("formPago");
        const fechaRealPago = document.getElementById('fecharealpago');

        const radioUSD = document.getElementById('usd');
        const radioPEN = document.getElementById('pen');
        const tipoCambioInput = document.getElementById('tipocambio');
        const saldoRestanteEnUSD = parseFloat(saldoInput.value.replace(/,/g, "")) || 0;


        const valorCalculadoBox = document.getElementById("valorCalculado");
        const valorUSDInput = document.getElementById("valorUSD");
        const valorCalculadoTexto = document.getElementById("valorCalculadoTexto");

        // Variable para controlar si ya se mostró el toast
        let toastMostradoParaPEN = false;

        btnExcel.addEventListener("click", async () => {
            const dataPagos = document.querySelectorAll("#tabla-pagos-datos tbody tr");
            const dataVehiculos = document.querySelectorAll("#tabla-vehiculos tbody tr");
            if (dataPagos.length === 0 || (dataPagos.length === 1 && dataPagos[0].querySelectorAll("td").length === 1)) {
                alert("No hay datos en la tabla para exportar el Excel.");
                return;
            }

            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('REPORTE_OC');

            // ----------------------------------------------------
            // Estilos reutilizables
            // ----------------------------------------------------
            const headerStyle = {
                font: {
                    bold: true,
                    size: 11
                },
                fill: {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: {
                        argb: 'FFE0E0E0'
                    }
                },
                alignment: {
                    vertical: 'middle',
                    horizontal: 'center'
                },
                border: {
                    top: {
                        style: 'thin'
                    },
                    left: {
                        style: 'thin'
                    },
                    bottom: {
                        style: 'thin'
                    },
                    right: {
                        style: 'thin'
                    }
                }
            };
            const titleStyle = {
                font: {
                    bold: true,
                    size: 16
                },
                alignment: {
                    horizontal: 'center'
                }
            };
            const subtitleStyle = {
                font: {
                    bold: true,
                    size: 14
                },
                alignment: {
                    horizontal: 'center'
                }
            };

            // ----------------------------------------------------
            // TABLA DE PAGOS
            // ----------------------------------------------------

            // Título principal
            worksheet.addRow(['REPORTE DE ORDEN DE COMPRA']).getCell(1).style = titleStyle;
            worksheet.mergeCells('A1:J1');
            worksheet.addRow([]);

            // Información del concesionario y OC
            const infoRows = [
                ['Concesionario:', `<?= htmlspecialchars($concesionario['concesionario'] ?? '---') ?>`],
                ['OC-Identificador:', `<?= htmlspecialchars($concesionario['numeroOCIdentificador'] ?? '---') ?>`],
                ['Fecha del Reporte:', `<?= date('d-m-Y') ?>`]
            ];
            infoRows.forEach((row) => {
                const newRow = worksheet.addRow(row);
                newRow.getCell(1).font = {
                    bold: true
                };
            });

            worksheet.addRow([]);
            worksheet.addRow(['PAGOS REALIZADOS']).getCell(1).style = subtitleStyle;
            worksheet.mergeCells('A' + worksheet.rowCount + ':J' + worksheet.rowCount);
            worksheet.addRow([]);

            // Encabezados de pagos
            const headersPagos = [
                "#", "Fecha Real Pago", "Entidad", "N° Transacción", "Moneda",
                "Monto Pagado", "Saldo Restante", "T. Cambio", "Valor en Dólares",
                "Observaciones"
            ];
            const headerRowPagos = worksheet.addRow(headersPagos);
            headerRowPagos.eachCell(cell => {
                cell.style = headerStyle;
            });

            // Datos de pagos
            for (const fila of dataPagos) {
                const celdas = fila.querySelectorAll("td");
                if (celdas.length < 10) continue;

                const rowData = [];
                let observacion = '';

                for (let i = 0; i < 9; i++) {
                    rowData.push(celdas[i]?.innerText?.trim() || '');
                }

                const celdaObs = celdas[9];

                const btn = celdaObs.querySelector(".ver-detalle-observacion");
                if (btn) {
                    observacion = btn.getAttribute("data-observacion") || '';
                    console.log(observacion)
                } else {
                    observacion = celdaObs?.innerText?.trim() || '';
                }

                rowData.push(''); // Se agrega un espacio en blanco para la celda de Observaciones
                const newRow = worksheet.addRow(rowData);

                if (observacion) {
                    newRow.getCell(10).note = {
                        texts: [{
                            text: observacion,
                        }],
                    };
                }

                newRow.eachCell((cell, colNumber) => {
                    cell.border = {
                        top: {
                            style: 'thin'
                        },
                        left: {
                            style: 'thin'
                        },
                        bottom: {
                            style: 'thin'
                        },
                        right: {
                            style: 'thin'
                        }
                    };
                    if (colNumber === 2) {
                        cell.numFmt = 'dd-mm-yyyy hh:mm';
                    }
                    if (colNumber === 6 || colNumber === 7 || colNumber === 9) {
                        const moneda = newRow.getCell(5).value;
                        let value = parseFloat(cell.value.replace(/[^0-9.-]+/g, ''));
                        if (!isNaN(value)) {
                            cell.value = value;
                            cell.numFmt = moneda === 'PEN' ? '_("S/"* #,##0.00_);_("S/"* (#,##0.00);_("S/"* "-"??_);_(@_)' : '_("$"* #,##0.00_);_("$"* (#,##0.00);_("$"* "-"??_);_(@_)';
                            cell.alignment = {
                                horizontal: 'right'
                            };
                        }
                    }
                });
            }

            // ----------------------------------------------------
            // TABLA DE VEHÍCULOS
            // ----------------------------------------------------
            worksheet.addRow([]);
            worksheet.addRow(['VEHÍCULOS']).getCell(1).style = subtitleStyle;
            worksheet.mergeCells('A' + worksheet.rowCount + ':K' + worksheet.rowCount);
            worksheet.addRow([]); // Espacio en blanco

            // Encabezados de vehículos
            const headersVehiculos = [
                "Marca / Modelo", "Tipo", "Versión", "Combustible", "Color",
                "Chasis", "Placa", "P. Rotativa", "S. Motor", "Año", "Estado"
            ];
            const headerRowVehiculos = worksheet.addRow(headersVehiculos);
            headerRowVehiculos.eachCell(cell => {
                cell.style = headerStyle;
            });

            // Datos de vehículos
            for (const fila of dataVehiculos) {
                const celdas = fila.querySelectorAll("td");
                if (celdas.length < 11) continue;
                const rowDataVehiculos = Array.from(celdas).map((cell, i) => {
                    if (i === 10) { // Celda de estado
                        const span = cell.querySelector("span");
                        return span ? span.innerText.trim() : cell.innerText.trim();
                    }
                    return cell.innerText.trim();
                });
                const newRow = worksheet.addRow(rowDataVehiculos);
                newRow.eachCell(cell => {
                    cell.border = {
                        top: {
                            style: 'thin'
                        },
                        left: {
                            style: 'thin'
                        },
                        bottom: {
                            style: 'thin'
                        },
                        right: {
                            style: 'thin'
                        }
                    };
                });
            }

            // Ajustar anchos de columna para ambas tablas
            const columnWidthsPagos = [5, 18, 18, 25, 10, 15, 15, 10, 15, 30];
            const columnWidthsVehiculos = [20, 15, 20, 15, 15, 25, 15, 15, 25, 10, 15];

            const finalColumnWidths = columnWidthsPagos.map((width, i) => Math.max(width, columnWidthsVehiculos[i] || 0));
            worksheet.columns = finalColumnWidths.map(width => ({
                width
            }));

            // Escribir el archivo y descargarlo
            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Reporte-OC-<?= htmlspecialchars($concesionario['idordencompra'] ?? '---') ?>_<?= date('d-m-Y') ?>.xlsx`;
            a.click();
            window.URL.revokeObjectURL(url);
        });




        // =========================================================================
        //  2. Funciones y Manejo de Eventos
        // =========================================================================

        /**
         * Muestra un modal con el detalle de una observación.
         */
        document.querySelectorAll('.ver-detalle-observacion').forEach(btn => {
            btn.addEventListener('click', () => {
                const textoObservacion = btn.getAttribute('data-observacion');
                document.getElementById('textoDetalleObservacion').textContent = textoObservacion;
                modalDetalleObservacion.show();
            });
        });

        /**
         * Muestra el modal para ver el comprobante de pago.
         */
        document.querySelectorAll('.ver-comprobante-img').forEach(button => {
            button.addEventListener('click', () => {
                const imgSrc = button.dataset.img;
                document.getElementById('imagenAmpliada').src = imgSrc;
                modalImagenComprobante.show();
            });
        });

        /**
         * Habilita/deshabilita los campos de tipo de cambio y amortización, y muestra un toast.
         */
        const actualizarEstadoTipoCambio = () => {
            const isPENChecked = radioPEN.checked;

            tipoCambioInput.disabled = !isPENChecked;
            tipoCambioInput.required = isPENChecked;

            if (isPENChecked) {
                if (!toastMostradoParaPEN) {
                    showToast("Por favor, ingrese el tipo de cambio del día para continuar.", "INFO", 3000);
                    toastMostradoParaPEN = true;
                }
                amortizacionInput.disabled = true;
                amortizacionInput.value = '';
                amortizacionInput.classList.remove('is-valid', 'is-invalid');
                tipoCambioInput.focus();
                valorCalculadoBox.classList.add('d-none'); // Oculta la caja cuando cambia a PEN
            } else {
                tipoCambioInput.value = '';
                tipoCambioInput.classList.remove("is-invalid");
                amortizacionInput.disabled = false;
                toastMostradoParaPEN = false;
                valorCalculadoBox.classList.add('d-none'); // Oculta la caja si es USD
            }
            validarMonto();
        };

        if (radioUSD && radioPEN && tipoCambioInput) {
            radioUSD.addEventListener('change', actualizarEstadoTipoCambio);
            radioPEN.addEventListener('change', actualizarEstadoTipoCambio);
            actualizarEstadoTipoCambio();
        }

        /**
         * Valida en tiempo real y actualiza el valor calculado.
         */
        function validarMonto() {
            const amortizacion = parseFloat(amortizacionInput.value);
            const tipoCambio = parseFloat(tipoCambioInput.value) || 0;
            const isPEN = radioPEN.checked;
            let montoValidacion = 0;

            // Habilita el campo de amortización si el tipo de cambio es válido
            if (isPEN) {
                amortizacionInput.disabled = !(tipoCambio > 0);
            } else {
                amortizacionInput.disabled = false;
            }

            tipoCambioInput.classList.toggle("is-invalid", isPEN && !(tipoCambio > 0));

            // Lógica de cálculo y visualización en la caja
            if (isPEN && !isNaN(amortizacion) && amortizacion > 0 && tipoCambio > 0) {
                montoValidacion = amortizacion / tipoCambio;
                // Redondear el valor para la validación
                montoValidacion = Math.round(montoValidacion * 100) / 100;

                // Asignar el valor al input oculto
                valorUSDInput.value = montoValidacion;

                // Actualiza el texto visible
                valorCalculadoTexto.textContent = `$${montoValidacion.toLocaleString("en-US", { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            })}`;

                valorCalculadoBox.classList.remove('d-none');
            } else {
                // Limpiar el valor del input y ocultar la caja cuando no aplica
                valorUSDInput.value = '';
                valorCalculadoBox.classList.add('d-none');
                montoValidacion = amortizacion; // Para la validación del monto en USD
            }

            // Lógica de validación principal para los campos
            const esMontoValido = !isNaN(amortizacion) && amortizacion > 0 && montoValidacion <= saldoRestanteEnUSD;
            const esTipoCambioValido = !isPEN || (isPEN && tipoCambio > 0);

            const haEscritoAmortizacion = amortizacionInput.value.length > 0;

            if (haEscritoAmortizacion) {
                amortizacionInput.classList.toggle("is-invalid", !esMontoValido);
                amortizacionInput.classList.toggle("is-valid", esMontoValido);
            } else {
                amortizacionInput.classList.remove("is-invalid", "is-valid");
            }

            btnGuardarPago.disabled = !(esMontoValido && esTipoCambioValido);
        };

        // Escuchar cambios para la validación y el cálculo
        amortizacionInput.addEventListener("input", validarMonto);
        tipoCambioInput.addEventListener('input', validarMonto);

        // =========================================================================
        //  3. Envío del Formulario
        // =========================================================================

        formPago.addEventListener("submit", async (event) => {
            event.preventDefault();

            // Validaciones finales antes del envío
            const amortizacion = parseFloat(amortizacionInput.value);
            const tipoCambio = parseFloat(tipoCambioInput.value) || 0;
            const isPEN = radioPEN.checked;

            if (isPEN && (isNaN(tipoCambio) || tipoCambio <= 0)) {
                alert("Ingrese un tipo de cambio válido para el pago en soles.");
                return;
            }
            if (isNaN(amortizacion) || amortizacion <= 0) {
                alert("Ingrese un monto de amortización válido y mayor a 0.");
                return;
            }

            let montoParaComparar = amortizacion;
            if (isPEN) {
                montoParaComparar = amortizacion / tipoCambio;
                // Redondear el monto final antes de la comparación
                montoParaComparar = Math.round(montoParaComparar * 100) / 100;
            }

            if (montoParaComparar > saldoRestanteEnUSD) {
                alert(`El monto a pagar (${montoParaComparar.toFixed(2)} USD) no debe ser mayor al saldo restante (${saldoRestanteEnUSD.toFixed(2)} USD).`);
                return;
            }

            if (!fechaRealPago.value) {
                alert("La fecha de pago es obligatoria.");
                return;
            }

            // Confirmación y envío del formulario
            if (await ask('¿Desea registrar el pago?', 'Confirmar Pago')) {
                try {
                    const formData = new FormData(formPago);
                    const response = await fetch("/oc/pagos/store", {
                        method: "POST",
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast(data.message, "SUCCESS", 1200);
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showToast(data.message, "WARNING", 1200);
                    }
                } catch (error) {
                    showToast("Error de conexión con el servidor. Intente nuevamente.", "ERROR", 1200);
                }
            }
        });
    });
</script>
<?php include __DIR__ . '/../layout/footer.php'; ?>