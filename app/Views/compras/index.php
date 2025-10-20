<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">
<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Compras</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-sm btn-outline-primary" href="/compras/create">Registrar</a>
            </div>
        </div>
    </div>

    <!-- Lista principal -->
    <div id="lista-oc">
        <div class="card">
            <div class="card-body">
                <div class="input-group mb-4">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input

                        type="text"
                        id="busqueda-global"
                        class="form-control"
                        placeholder="Buscar ....">
                </div>


                <!-- Aquí Tabulator dibuja la tabla -->
                <div id="tabla-compras"></div>
            </div>
        </div>
    </div>

</div>


<div id="detalle-oc" style="display: none;">
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 id="detalle-concesionario-razon"></h5>
                <h6 id="detalle-oc-summary" class="text-muted"></h6>
            </div>
            <button id="btn-volver-detalle" class="btn btn-outline-secondary btn-sm">Volver</button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm" id="tabla-detalle-oc">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Versión</th>
                        <th>Combustible</th>
                        <th>Año</th>
                        <th>Chasis</th>
                        <th>Motor</th>
                        <th>Placa</th>
                        <th>Rotativa</th>
                        <th>Color</th>
                        <th>Moneda</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFactura" tabindex="-1" aria-labelledby="modalFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-light">
                <h5 class="modal-title fw-bold" id="modalFacturaLabel">Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" style="height: 80vh;">
                <iframe id="visorFactura" src="" width="100%" height="100%" style="border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const compras = <?php echo json_encode($compras ?? []); ?>;
        const tablaCompras = new Tabulator("#tabla-compras", {
            data: compras,
            layout: "fitColumns",
            responsiveLayout: "collapse",
            responsiveLayoutCollapseStartOpen: false, // Inicia colapsado

            pagination: "local",
            paginationSize: 10,
            paginationSizeSelector: [10, 25, 50, 100],
            columns: [{
                    formatter: "responsiveCollapse",
                    width: 40,
                    minWidth: 30,
                    hozAlign: "center",
                    resizable: false,
                    headerSort: false,
                    responsive: 0 // Siempre visible
                },
                {
                    title: "#",
                    formatter: 'rownum',
                    width: 60,
                    responsive: 0, // Siempre visible
                    headerSort: false,
                    hozAlign: "center"
                },
                {
                    title: "Concesionario",
                    field: "razon_concesionario",
                    minWidth: 150,
                    responsive: 0, // Siempre visible
                    // headerFilter: "input",
                    // headerFilterPlaceholder: "Buscar..."
                },
                {
                    title: "Fecha entrega",
                    field: "fechacompra",
                    width: 150,
                    responsive: 3, // Se oculta tercero
                    hozAlign: "center",
                    sorter: "date",
                    sorterParams: {
                        format: "YYYY-MM-DD"
                    }
                },
                {
                    title: "Fecha recepción",
                    field: "fecharecepcion",
                    width: 150,
                    responsive: 4, // Se oculta cuarto
                    hozAlign: "center",
                    formatter: (cell) => cell.getValue() || "N/A"
                },
                {
                    title: "Tipo doc",
                    field: "tipodoc",
                    width: 110,
                    responsive: 5, // Se oculta quinto
                    hozAlign: "center",
                    formatter: (cell) => {
                        const val = cell.getValue();
                        if (val === "B") {
                            return '<span class="badge bg-info">Boleta</span>';
                        } else if (val === "F") {
                            return '<span class="badge bg-success">Factura</span>';
                        }
                        return val;
                    }
                },
                {
                    title: "Serie",
                    field: "serie",
                    width: 120,
                    responsive: 2, // Se oculta segundo
                    hozAlign: "center"
                },
                {
                    title: "Número",
                    field: "numdocumento",
                    width: 150,
                    responsive: 1, // Se oculta primero
                    hozAlign: "center"
                },
                {
                    title: "Acciones",
                    field: "acciones",
                    responsive: 0, // Siempre visible
                    hozAlign: "center",
                    headerSort: false,
                    formatter: (cell) => {
                        const row = cell.getRow().getData();
                        let html = `<a href="#" class="show-details" data-idoc="${row.idorden}" title="Ver detalle">
                                <i class="bi bi-info-circle text-primary fs-5"></i>
                            </a>`;
                        if (row.rutadoc) {
                            html += `<a href="#" class="ver-documento ms-2" data-url="/archivos/${row.rutadoc}" title="Ver Documento">
                                <i class="fa-solid fa-file-invoice fs-5" style="color:#ff0000;"></i>
                             </a>`;
                        } else {
                            html += `<span class="badge bg-light text-muted ms-2">N/A</span>`;
                        }
                        return html;
                    },
                    width: 150
                }
            ],
            langs: {
                "es-es": {
                    "pagination": {
                        "page_size": "Registros por página",
                        "first": "<<",
                        "first_title": "Primera página",
                        "last": ">>",
                        "last_title": "Última página",
                        "prev": "<",
                        "prev_title": "Página anterior",
                        "next": ">",
                        "next_title": "Página siguiente",
                        "all": "Todos",
                        "counter": {
                            "showing": "Mostrando",
                            "of": "de",
                            "rows": "registros",
                            "pages": "páginas"
                        }
                    },
                    "data": {
                        "loading": "Cargando...",
                        "error": "Error"
                    },
                    "headerFilters": {
                        "default": "filtrar columna...",
                        "columns": {
                            "name": "filtrar nombre..."
                        }
                    }
                }
            },
            locale: "es-es",
            // Estilos adicionales
            rowFormatter: function(row) {
                row.getElement().style.cursor = "pointer";
            }
        });

        
        const searchInput = document.getElementById("busqueda-global");
        if (searchInput) {
            searchInput.addEventListener("keyup", function(e) {
                const value = e.target.value;
                if (value === "") {
                    tablaCompras.clearFilter();
                } else {
                    tablaCompras.setFilter([
                        [{
                                field: "razon_concesionario",
                                type: "like",
                                value: value
                            }
                        

                        ]
                    ]);
                }
            });
        }


        document.addEventListener("click", (e) => {
            if (e.target.closest(".ver-documento")) {
                e.preventDefault();
                const link = e.target.closest(".ver-documento");
                const pdfUrl = link.dataset.url;
                if (pdfUrl) {
                    document.getElementById("visorFactura").src = pdfUrl;
                    new bootstrap.Modal(document.getElementById("modalFactura")).show();
                }
            }
        });


        document.addEventListener("click", async (e) => {
            if (e.target.closest(".show-details")) {
                e.preventDefault();
                const ocId = e.target.closest(".show-details").dataset.idoc;

                if (!ocId) return;

                const tablaDetallesBody = document.querySelector('#tabla-detalle-oc tbody');
                tablaDetallesBody.innerHTML = "";

                try {
                    const response = await fetch(`/api/oc/${ocId}`);
                    const data = await response.json();
                    if (!data || !data.orden || !Array.isArray(data.vehiculos)) return;

                    $("#lista-oc").slideUp(850);
                    $("#detalle-oc").slideDown(850);

                    document.getElementById('detalle-concesionario-razon').textContent = data.orden.concesionario.razon_social || "N/A";
                    document.getElementById('detalle-oc-summary').textContent =
                        `${data.orden.numero_oc_formateado} | ${data.orden.fecha_emision_oc} | ${data.orden.moneda_oc} ${parseFloat(data.orden.totales.total || 0).toFixed(2)}`;

                    data.vehiculos.forEach((vehiculo, index) => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${index+1}</td>
                            <td>${vehiculo.marca || "N/A"}</td>
                            <td>${vehiculo.modelo || "N/A"}</td>
                            <td>${vehiculo.version || "N/A"}</td>
                            <td>${vehiculo.combustible || "N/A"}</td>
                            <td>${vehiculo.anio_modelo || "N/A"}</td>
                            <td>${vehiculo.chasis || "N/A"}</td>
                            <td>${vehiculo.serie_motor || "N/A"}</td>
                            <td>${vehiculo.placa || "N/A"}</td>
                            <td>${vehiculo.placa_rotativa || "N/A"}</td>
                            <td>${vehiculo.color || "N/A"}</td>
                            <td>${data.orden.moneda_oc || "N/A"}</td>
                            <td>${vehiculo.precio_unitario ? parseFloat(vehiculo.precio_unitario).toFixed(2) : "0.00"}</td>`;
                        tablaDetallesBody.appendChild(row);
                    });
                } catch (err) {
                    console.error(err);
                }
            }
        });

        // Botón volver
        document.getElementById('btn-volver-detalle').addEventListener('click', () => {
            $("#detalle-oc").slideUp(850);
            $("#lista-oc").slideDown(850);
        });
    });
</script>