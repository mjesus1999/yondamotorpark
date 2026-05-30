<?php include __DIR__ . '/../layout/header.php'; ?>

<link href="https://unpkg.com/tabulator-tables@6.3.1/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">
<style>
    #tabla-vehiculos-recepcion {
        padding: 0 !important;
        margin: 0 !important;
    }

    #tabla-vehiculos-recepcion .tabulator {
        border: none !important;
        width: 100% !important;
    }
</style>
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

    <div class="row d-none d-md-block mt-3">
        <div class="d-flex justify-content-end align-items-end">
            <button class="btn btn-sm btn-outline-success mb-2" id="btnExportExcel">Exportar Excel</button>
        </div>
        <div class="card">
            <div class="card-body">

                <div id="tabla-vehiculos-recepcion"></div>
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
<script src="https://unpkg.com/tabulator-tables@6.3.1/dist/js/tabulator.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    const data = <?= json_encode($data) ?>;
    
    data.forEach((row, index) => {
        row.num_fila = index + 1;
    });

    const tablaVehiculos = new Tabulator("#tabla-vehiculos-recepcion", {
        data,
        layout: "fitColumns",
        pagination: "local",
        paginationSize: 20,
        paginationSizeSelector: [5, 10, 15, 25],
        responsiveLayout: "collapse",
        placeholder: "<i class='fas fa-info-circle'></i> No hay vehículos por recepcionar",
        columns: [{
                title: "#",
                field: "num_fila",
                width: 50,
                hozAlign: "center",
                download: true, 
            },
            {
                title: "Concesionario",
                field: "nombrecomercial",
                download: true
            },
            {
                title: "Dirección Conces.",
                field: "direccion_completa_concesionario",
                minWidth: 300,
                download: true
            },
            {
                title: "Emisión OC",
                field: "fecha_emision_oc",
                download: true
            },
            {
                title: "Serie OC",
                field: "serie_oc",
                download: true
            },
            {
                title: "Fecha compra",
                field: "fechacompra",
                download: true
            },
            {
                title: "Cant. Pendientes",
                field: "vehiculos_pendientes",
                formatter: "html",
                download: true
            },
            {
                title: "Por / Liberar",
                field: "listos_para_liberar",
                formatter: "html",
                download: true
            },
            {
                title: "Acciones",
                field: "acciones",
                download: false,
                formatter: (cell) => {
                    const data = cell.getRow().getData();
                    return `<a href="/recepcionVehiculos/edit/${data.idcompra}" title="Llevará a la vista de recepción de vehículos">
                    <i class="fa-solid fa-book fs-5" style="color: #40cbf5ff;"></i>
                    
                  </a>`;


                }
            },
        ],
        locale: "es-es",
        langs: {
            "es-es": {
                pagination: {
                    page_size: "Registros por página",
                    first: "<<",
                    last: ">>",
                    prev: "<",
                    next: ">",
                },
            },
        },
    });

    document.getElementById('btnExportExcel').addEventListener('click', () => {
        tablaVehiculos.download("xlsx", "VehículosPorRecepcionar.xlsx", {
            sheetName: "VehiculosXRecepcionar"
        });
    });
</script>



