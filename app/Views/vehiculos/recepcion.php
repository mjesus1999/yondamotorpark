<?php include __DIR__ . '/../layout/header.php'; ?>

<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">

<div class="container-fluid">
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-12 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Recepción vehículos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row d-none d-md-block">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="tabla-vehiculos-recepcion">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Concesionario</th>
                                    <th>Dirección Conces.</th>
                                    <th>Emisión OC</th>
                                    <th>Serie OC</th>
                                    <th>Fecha compra</th>
                                    <th>Cant. Pendientes</th>
                                    <th>Por / Liberar</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay datos registrados.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1; ?>
                                    <?php foreach ($data as $compras): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($numeroFila++) ?></td>
                                            <td><?= htmlspecialchars($compras['nombrecomercial']) ?></td>
                                            <td><?= htmlspecialchars($compras['direccion_completa_concesionario']) ?></td>
                                            <td><?= htmlspecialchars($compras['fecha_emision_oc']) ?></td>
                                            <td><?= htmlspecialchars($compras['serie_oc']) ?></td>
                                            <td><?= htmlspecialchars($compras['fechacompra']) ?></td>
                                            <td><span class="badge bg-danger text-white"><?= htmlspecialchars($compras['vehiculos_pendientes']) ?></span></td>
                                            <td><span class="badge bg-info text-white"><?= htmlspecialchars($compras['listos_para_liberar']) ?></span></td>
                                            <td>
                                                <a href="/recepcionVehiculos/edit/<?= htmlspecialchars($compras['idcompra']) ?>" title="Llevará a la vista de recepción de vehículos">
                                                    <i class="fa-solid fa-book fs-5" style="color: #40cbf5ff;"></i>
                                                </a>
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

    <div class="row d-md-none">
        <div class="col-12">
            <div class="accordion" id="accordionListado">
                <?php if (empty($data)): ?>
                    <div class="text-center p-3">No hay datos registrados.</div>
                <?php else: ?>
                    <?php $numeroFila = 1; ?>
                    <?php foreach ($data as $compras): ?>
                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header" id="heading-<?= $numeroFila ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $numeroFila ?>" aria-expanded="false" aria-controls="collapse-<?= $numeroFila ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center me-3">
                                        <div class="fw-semibold"><i class="fa-solid fa-car me-2 text-primary"></i>#<?= htmlspecialchars($numeroFila) ?> - <?= htmlspecialchars($compras['nombrecomercial']) ?></div>
                                        <div class="d-flex flex-column text-end">
                                            <span class="badge bg-danger text-white mb-1">Pendientes: <?= htmlspecialchars($compras['vehiculos_pendientes']) ?></span>
                                            <span class="badge bg-info text-white">Liberar: <?= htmlspecialchars($compras['listos_para_liberar']) ?></span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse-<?= $numeroFila ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $numeroFila ?>" data-bs-parent="#accordionListado">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Concesionario:</strong> <?= htmlspecialchars($compras['nombrecomercial']) ?></li>
                                        <li class="list-group-item"><strong>Dirección:</strong> <?= htmlspecialchars($compras['direccion_completa_concesionario']) ?></li>
                                        <li class="list-group-item"><strong>Emisión OC:</strong> <?= htmlspecialchars($compras['fecha_emision_oc']) ?></li>
                                        <li class="list-group-item"><strong>Serie OC:</strong> <?= htmlspecialchars($compras['serie_oc']) ?></li>
                                        <li class="list-group-item"><strong>Fecha compra:</strong> <?= htmlspecialchars($compras['fechacompra']) ?></li>
                                        <li class="list-group-item d-flex justify-content-center">
                                            <a href="/recepcionVehiculos/edit/<?= htmlspecialchars($compras['idcompra']) ?>" title="Llevará a la vista de recepción de vehículos">
                                                <i class="fa-solid fa-book fs-4" style="color: #40cbf5ff;"></i>
                                                <span class="ms-2">Ver detalles</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php $numeroFila++; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>


<script>
    const tablaVehiculos = new Tabulator("#tabla-vehiculos-recepcion", {
        layout: "fitColumns",
        pagination: "local",
        paginationSize: 10,
        responsiveLayout: "collapse",
        paginationSizeSelector: [5, 10, 20],
        columns: [{
                title: "#",
                field: "#",
                width:20
            },
            {
                title: "Concesionario",
                field: "Concesionario",
             minWidth:180,
             tooltip:true,
            },
            {
                title: "Dirección Conces.",
                field: "direccion_completa_concesionario",
                      minWidth:350,
                      tooltip:true,
            },
            {
                title: "Emisión OC",
                field: "Emisión OC",
                width:150,
                tooltip:true,
            },
            {
                title: "Serie OC",
                field: "Serie OC",
                  width:150,
                  tooltip:true,
            },
            {
                title: "Fecha compra",
                field: "Fecha compra",
                  width:150,
                  tooltip:true,
            },
            {
                title: "Cant. Pendientes",
                field: "vehiculos_pendientes'",
                formatter: "html",
                   width:150,
                   tooltip:true,
            },
            {
                title: "Por / Liberar",
                field: "listos_por_liberar",
                formatter: "html",
                   width:150,
                   tooltip:true,
            },
            {
                title: "Acciones",
                field: "Acciones",
                formatter: "html",
                minWidth:150,
                  
            }
        ],
        locale: "es-es",
        langs: {
            "es-es": {
                "pagination": {
                    "page_size": "Registros por página",
                    "first": "Primero",
                    "last": "Último",
                    "prev": "Anterior",
                    "next": "Siguiente",
                }
            }
        }
    });
</script>