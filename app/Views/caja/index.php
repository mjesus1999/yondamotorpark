<?php

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
                    <div class="table-responsive">
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
                                        <td colspan="8" class="text-center">No hay datos para mostrar.</td>
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
                                                <a href="/caja/cronograma/<?= htmlspecialchars($contrato['idcontrato']) ?>" title="Ver Cronograma"><i class="bi-receipt fs-5 text-info "></i></a>
                                                <a href="/caja/historial/pagos/<?= htmlspecialchars($contrato['idcontrato']) ?>" title="Ver historial de pagos"><i class="bi bi-clock-history fs-5"></i></a>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<script>
    $('#tabla-contratos').DataTable({
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