<?php
// phpinfo();
include __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <!-- SOLO ESCRITORIO -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-contratos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>N° Documento</th>
                                    <th>Tienda</th>
                                    <th>Vehículo</th>
                                    <th>Meses</th>
                                    <th>Cuota</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($contratos)) : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay datos para mostrar.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1; ?>
                                    <?php foreach ($contratos as $contrato) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($numeroFila++) ?></td>
                                            <td><?= htmlspecialchars($contrato['cliente']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($contrato['documento']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($contrato['ndocumento']) ?></td>
                                            <td><span class="badge bg-primary fw-bold text-white"><?= htmlspecialchars($contrato['tienda']) ?></span></td>
                                            <td><span class="badge bg-primary fw-bold text-white"><?= htmlspecialchars($contrato['vehiculo']) ?></span></td>
                                            <td><?= htmlspecialchars($contrato['meses']) ?></td>
                                            <td><?= htmlspecialchars($contrato['cuota']) ?></td>
                                            <td>
                                                <a href="/caja/cronograma/<?= htmlspecialchars($contrato['idcontrato']) ?>" title="Ver Cronograma">
                                                    <i class="bi-receipt fs-5 text-info"></i>
                                                </a>
                                                <a href="/caja/historial/pagos/<?= htmlspecialchars($contrato['idcontrato']) ?>" title="Ver historial de pagos">
                                                    <i class="bi bi-clock-history fs-5"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                   
                    <!-- SOLO MÓVIL (ACORDEÓN) -->
                    <div class="d-block d-md-none">
                        <?php if (!empty($contratos)) : ?>
                            <?php $numeroFila = 1; ?>
                            <?php foreach ($contratos as $contrato): ?>
                                <div class="card mb-2 shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapse<?= $contrato['idcontrato'] ?>" style="cursor:pointer;">
                                        <span><i class="bi bi-person-circle me-2 text-primary  fw-bold"></i><?= htmlspecialchars($contrato['cliente']) ?></span>
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                    <div id="collapse<?= $contrato['idcontrato'] ?>" class="collapse">
                                        <div class="card-body">
                                            <p><strong>#:</strong> <?= $numeroFila++ ?></p>
                                            <p><strong>Documento:</strong> <?= htmlspecialchars($contrato['documento']) ?></p>
                                            <p><strong>N° Documento:</strong> <?= htmlspecialchars($contrato['ndocumento']) ?></p>
                                            <p><strong>Tienda:</strong> <span class="badge bg-primary"><?= htmlspecialchars($contrato['tienda']) ?></span></p>
                                            <p><strong>Vehículo:</strong> <span class="badge bg-primary"><?= htmlspecialchars($contrato['vehiculo']) ?></span></p>
                                            <p><strong>Meses:</strong> <?= htmlspecialchars($contrato['meses']) ?></p>
                                            <p><strong>Cuota:</strong> <?= htmlspecialchars($contrato['cuota']) ?></p>
                                            <p>
                                                <strong>Acciones:</strong><br>
                                                <a href="/caja/cronograma/<?= $contrato['idcontrato'] ?>" title="Ver Cronograma">
                                                    <i class="bi-receipt fs-5 text-info me-2"></i>
                                                </a>
                                                <a href="/caja/historial/pagos/<?= $contrato['idcontrato'] ?>" title="Ver historial de pagos">
                                                    <i class="bi bi-clock-history fs-5"></i>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center">No hay datos para mostrar.</div>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>

    <?php include __DIR__ . '/../layout/footer.php'; ?>

    <script>
        $('#tabla-contratos').DataTable({
            responsive: true,
            language: {
                emptyTable: "No hay datos disponibles en la tabla",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 entradas",
                infoFiltered: "(filtrado de _MAX_ entradas totales)",
                lengthMenu: "Mostrar _MENU_ registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No se encontraron registros coincidentes",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending: ": activar para ordenar la columna ascendente",
                    sortDescending: ": activar para ordenar la columna descendente"
                }
            }
        });
    </script>