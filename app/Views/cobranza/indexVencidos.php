<?php include __DIR__ . '/../layout/header.php'; ?>

<head>

<body>
    <!-- <h2>HOLA</h2> -->

    <div class="container-fluid">

        <!-- CABECERA -->
        <div class="alert alert-info mt-2" role="alert">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center justify-content-start">
                    <nav arial-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#" class="text-decoration-none">
                                    Área de Cobranza
                                </a>
                            <li class="breadcrumb-item active" aria-current="page">
                                Vencidos
                            </li>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/cobranza" class="btn btn-sm btn-outline-primary">Volver</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <!-- CABECERA DE LA TABLA -->
                    <!-- <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-times-circle me-2"></i>Clientes Vencidos</h6>
                        </div>
                    </div> -->

                    <!-- TABLA CON DATOS -->
                    <div class="card-body">
                        <?php if (!empty($vencidos)): ?>
                            <?php 
                            // Calcular el total de deuda
                            $totalDeuda = 0;
                            foreach ($vencidos as $row) {
                                $totalDeuda += (float) $row['deuda_total'];
                            }
                            ?>
                            
                            <table class="table table-sm table-hover table-hover-yonda">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Telefono</th>
                                        <th>Vehiculo</th>
                                        <th>Tienda</th>
                                        <th>Cuotas T.</th>
                                        <th>Monto Cuota</th>
                                        <!-- <th>F.Vencimiento</th> -->
                                        <th>Deuda</th>
                                        <th>Estado de pagos</th>
                                        <th>Reporte</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php $i = 1;
                                    foreach ($vencidos as $row): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($row['cliente']) ?></td>
                                            <td><?= htmlspecialchars($row['telefono']) ?></td>
                                            <td><?= htmlspecialchars($row['vehiculo']) ?></td>
                                            <td><?= htmlspecialchars($row['tienda']) ?></td>
                                            <td><?= htmlspecialchars($row['cuotas_totales']) ?></td>
                                            <td>S/. <?= number_format((float) $row['monto_cuota'], 2) ?></td>
                                            <td><span
                                                    class="<?= ((float) $row['deuda_total'] > 0) ? 'text-danger fw-bold' : '' ?>">S/.
                                                    <?= number_format((float) $row['deuda_total'], 2) ?></span></td>
                                            <td><?= htmlspecialchars($row['estado_pagos']) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-danger mt-2" title="Notificar PDF"
                                                    onclick="window.open('/reportesAtrasado?contrato=<?= (int) $row['idcontrato'] ?>', '_blank')">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger mt-2" title="Recojo PDF"
                                                    onclick="window.open('/reportesRecojo', '_blank')">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="text-end fw-bold">
                                            <i class="fas fa-calculator me-2"></i>Total deuda:
                                        </td>
                                        <td class="fw-bold text-danger fs-6">
                                            S/. <?= number_format($totalDeuda, 2) ?>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                No hay contratos vencidos.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

    </div>

</body>
</head>

<?php include __DIR__ . '/../layout/footer.php'; ?>