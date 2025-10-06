<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<style>
    [data-bs-theme="dark"] .tabulator .tabulator-header,
    [data-bs-theme="dark"] .tabulator .tabulator-col {
        /* Fondo del header y las columnas */
        background-color: #343a40 !important;
        color: #f8f9fa !important;
        border-color: #495057 !important;
    }

    /* El Contenido de la Columna (Título del Header) */
    [data-bs-theme="dark"] .tabulator .tabulator-header .tabulator-col-content {
        color: #f8f9fa !important;
    }

    /* Filas y Paginación */

    [data-bs-theme="dark"] .tabulator-row {
        background-color: #212529 !important;
        border-color: #495057 !important;
        color: #f8f9fa !important;
    }

    [data-bs-theme="dark"] .tabulator-row:nth-child(even) {
        background-color: #2a2f33 !important;
        /* Fondo rayado alterno  */
    }

    [data-bs-theme="dark"] .tabulator-footer {
        background-color: #343a40 !important;
        color: #f8f9fa !important;
    }
</style>
<div class="container-fluid">

    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cotizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizacion/historial" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="bi bi-clock-history"></i> Historial
                </a>
                <a href="/cotizacion/create" class="btn btn-outline-primary btn-sm">
                    Registrar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">
                    <?php
                    $estadoActual = $estadoActual ?? 'P';
                    // Asume que $cotizaciones y $puede_ver_todas ya están definidos
                    ?>
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="btn-group m-1 mb-2" id="botones-filtro">
                            <a href="/cotizacion/P"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'P' ? 'btn-warning' : 'btn-outline-warning' ?>">
                                <i class="bi bi-clock"></i> Pendientes
                            </a>
                             <a href="/cotizacion/O"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'O' ? 'btn-info' : 'btn-outline-info' ?>">
                               <i class="bi bi-eye"></i> Observadas
                            </a>
                            <a href="/cotizacion/A"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'A' ? 'btn-success' : 'btn-outline-success' ?>">
                                <i class="bi bi-check-circle"></i> Aprobadas
                            </a>
                            <a href="/cotizacion/R"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'R' ? 'btn-danger' : 'btn-outline-danger' ?>">
                                <i class="bi bi-x-octagon"></i></i> Rechazadas
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($cotizaciones) > 0): ?>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input

                                    type="text"
                                    id="busqueda-global"
                                    class="form-control"
                                    placeholder="Buscar ....">
                            </div>
                        </div>

                        <div id="tabla-cotizacion" class="table-responsive"></div>

                        <!-- <script>
                            window.APP_DATA_TABLE = <?php echo json_encode($cotizaciones); ?>;

                            window.APP_CONFIG = {
                                puedeVerTodas: <?php echo $puede_ver_todas ? 'true' : 'false'; ?>,
                                estadoActual: '<?php echo $estadoActual; ?>'
                            };
                        </script> -->

                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">
                                No hay cotizaciones
                                <?php echo (strtoupper($estadoActual) === 'A') ? 'Aprobadas' : 'Pendientes/Activas'; ?>
                            </h4>
                            <p class="text-muted">No se encontraron cotizaciones con el estado seleccionado. Las cotizaciones vencidas se
                                pueden ver en el historial.</p>
                            <div class="mt-3">
                                <a href="/cotizacion/create" class="btn btn-primary me-2">
                                    <i class="bi bi-plus"></i> Nueva Cotización
                                </a>
                                <a href="/cotizacion/historial" class="btn btn-outline-secondary">
                                    <i class="bi bi-clock-history"></i> Ver Historial
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadModalLabel">Ver PDF de la Cotización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea ver la cotización de <strong id="cliente-name"></strong>?</p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="openInNewTab" checked>
                    <label class="form-check-label" for="openInNewTab">
                        Abrir en nueva pestaña
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" id="confirmDownload">Ver PDF</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="aprobarModal" tabindex="-1" aria-labelledby="aprobarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="aprobarModalLabel"><i class="bi bi-check-circle-fill me-2"></i> Confirmar Aprobación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea <strong>"Aprobar"</strong> la cotización de <strong id="cliente-aprobar-nombre"></strong>?</p>
                <p class="text-danger fw-bold small">Esta acción no se puede deshacer fácilmente.</p>
                <input type="hidden" id="cotizacion-aprobar-id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="confirmarAprobarBtn">
                    Aprobar
                </button>
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="contratoModal" tabindex="-1" aria-labelledby="contratoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formCrearContrato">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="contratoModalLabel"><i class="bi bi-file-earmark-text me-2"></i> Nuevo Contrato</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idcotizacion" id="contrato-idcotizacion" value="">

                    <p>Creando contrato para cotización ID: <strong id="contrato-cotizacion-id-display"></strong></p>

                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label for="idlocal" class="form-label">Local</label>
                            <select class="form-control" id="idlocal" name="idlocal" required>
                                <option value="">Seleccione Local</option>
                            </select>

                        </div>

                        <div class="col-md-6">
                            <label for="fechainicio" class="form-label">Fecha Inicio Contrato</label>
                            <input type="date" class="form-control" id="fechainicio" name="fechainicio" required value="<?= date('Y-m-d') ?>">
                        </div>

                    </div>

                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label for="diapago" class="form-label">Día de Pago (Mensual)</label>
                            <input type="number" class="form-control" id="diapago" name="diapago" min="1" max="31" required value="<?= date('d') ?>">
                            <small class="form-text text-muted">Día del mes en el que se efectuará cada pago.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="fechainicio" class="form-label">Fecha revisión</label>
                            <input type="date" class="form-control" id="fecharevision" name="fechainicio" required value="<?= date('Y-m-d') ?>">
                        </div>

                    </div>




                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarContrato">
                        <i class="bi bi-save"></i> Guardar y Generar Cronograma
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="/assets/js/cotizacionPDF.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', async () => {


        const aprobarModalElement = document.getElementById('aprobarModal');
        const cotizacionAprobarIdInput = document.getElementById('cotizacion-aprobar-id');
        const clienteAprobarNombreStrong = document.getElementById('cliente-aprobar-nombre');
        const confirmarAprobarBtn = document.getElementById('confirmarAprobarBtn');

        const selectLocal = document.getElementById('idlocal');

        window.APP_DATA_TABLE = <?php echo json_encode($cotizaciones); ?>;

        window.APP_CONFIG = {
            puedeVerTodas: <?php echo $puede_ver_todas ? 'true' : 'false'; ?>,
            estadoActual: '<?php echo $estadoActual; ?>'
        };

        // const tabla = new Tabulator("#tabla-cotizacion", {

        //     // Carga la data de la tabla HTML (tbody)
        //     data: true,
        //     // Carga la configuración de columnas desde el thead
        //     htmlColumns: true,

        //     theme: "simple",
        //     pagination: "local",
        //     responsiveLayout: "collapse", // hace que colapse columnas en móvil
        //     paginationSize: 4,
        //     paginationSizeSelector: [10, 25, 50, 100, true],
        //     layout: "fitColumns",
        //     movableRows: true,
        //     columns: [
        //         // 1. #
        //         {
        //             title: "#",
        //             field: "rownum",
        //             formatter: "rownum",
        //                responsive: 1

        //         },
        //         // 2. Cliente
        //         {
        //             title: "Cliente",
        //             field: "nombrecliente",
        //             widthGrow:2,
        //             responsive: 2


        //         },
        //         // 3. Vehículo
        //         {
        //             title: "Vehículo",
        //             field: "vehiculo",
        //               widthGrow:2,
        //         },
        //         // 4. Modalidad
        //         {
        //             title: "Modalidad",
        //             field: "tipocotizacion",
        //               widthGrow:2,
        //         },
        //         {
        //             title: "Inicial",
        //             field: "inicial"
        //         },
        //         // 6. N° de Doc
        //         {
        //             title: "N° de Doc",
        //             field: "documento"
        //         },
        //         // 7. Teléfono
        //         {
        //             title: "Teléfono",
        //             field: "telefono"
        //         },
        //         {
        //             title: "Registrado por",
        //             field: "asesor_info",
        //               widthGrow:2,
        //             formatter: "html",
        //             visible: <?php echo isset($puede_ver_todas) && $puede_ver_todas ? 'true' : 'false'; ?>
        //         },


        //         {
        //             title: "Opciones",
        //             field: "acciones",
        //             formatter: "html",
        //             headerSort: false,
        //             hozAlign: "left"
        //         },
        //     ],
        //     langs: {
        //         "es-es": {
        //             "pagination": {
        //                 "page_size": "Registros por página",
        //                 "first": "Primero",
        //                 "last": "Último",
        //                 "prev": "Anterior",
        //                 "next": "Siguiente",
        //                 "counter": {
        //                     "showing": "Mostrando",
        //                     "of": "de",
        //                     "rows": "registros"
        //                 }
        //             }
        //         }
        //     },


        //     locale: "es-es"
        // });

        // window.tablaCotizaciones = tabla;



        const container = document.getElementById("tabla-cotizacion");
        if (!container) return;

        const cotizaciones = window.APP_DATA_TABLE || [];
        const config = window.APP_CONFIG || {};

        if (cotizaciones.length > 0) {
            const tabla = new Tabulator("#tabla-cotizacion", {
                data: cotizaciones,
                theme: "simple",
                layout: "fitDataStrech",
                layout: "fitColumns",
                responsiveLayout: "collapse", //hace que colapse columnas en móvil
                pagination: true,
                paginationSize: 5,
                paginationCounter: "rows",

                columns: [{
                        title: "#",
                        formatter: "rownum",
                        headerSort: false,
                        hozAlign: "center",
                        width: 30,
                        responsive: 1
                    },
                    {
                        title: "Cliente",
                        field: "nombrecliente",
                        hozAlign: "left",
                        widthGrow: 2,
                        responsive: 2 // OCULTAR DESPUES _> '0' ES NO MOSTRARA
                    },
                    {
                        title: "Vehículo",
                        field: "vehiculo",
                        hozAlign: "left",


                        responsive: 1
                    },
                    {
                        title: "Inicial",
                        field: "inicial",
                        hozAlign: "right",
                        formatter: "money",
                        formatterParams: {
                            decimal: ".",
                            thousand: ",",
                            symbol: "S/ ",
                            precision: 2
                        },
                        minWidth: 50,
                        responsive: 1
                    },
                    {
                        title: "Documento",
                        field: "documento",
                        hozAlign: "center",
                        minWidth: 120,
                        responsive: 0
                    },
                    {
                        title: "Asesor",
                        field: "asesor_nombre",
                        hozAlign: "left",
                        minWidth: 150,
                        responsive: 0
                    },
                    {
                        title: "Acciones",
                        field: "idcotizacion",
                        headerSort: false,
                        minWidth: 120,
                        responsive: 0,
                        formatter: function(cell) {
                            const row = cell.getRow().getData();
                            const id = row.idcotizacion;
                            const nombrecliente = row.nombrecliente;
                            const numcuotas = row.numcuotas;

                            let html = `
                        <button type="button" class="btn btn-sm btn-download-pdf" data-id="${id}" 
                            data-cliente="${nombrecliente}" title="PDF Cotización">
                            <i class="bi bi-filetype-pdf text-danger fs-5"></i>
                        </button>`;

                            if (config.estadoActual === "P") {
                                html += `<a href="/fichasolicitud/${id}" class="" title="Adjuntar Ficha">
                                    <i class="bi bi-file-earmark-plus fs-5 text-warning fw-bold"></i>
                                 </a>`;
                            }
                            if (config.estadoActual === "A") {
                                html += `<a class="text-info fw-bold btnCrearContrato" title="Crear Contrato"
                                    data-id="${id}" 
                                    data-cliente="${nombrecliente}" 
                                    data-numcuotas="${numcuotas}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#contratoModal">
                                    <i class="bi bi-file-earmark-text fs-5"></i>
                                 </a>`;
                            }
                            return html;
                        }
                    }
                ],

                langs: {
                    "es-es": {
                        "pagination": {
                            "page_size": "Registros por página",
                            "first": "Primero",
                            "last": "Último",
                            "prev": "Anterior",
                            "next": "Siguiente",
                            "counter": {
                                "showing": "Mostrando",
                                "of": "de",
                                "rows": "registros"
                            }
                        }
                    }
                },
                locale: "es-es"
            });

            const searchInput = document.getElementById("busqueda-global");
            if (searchInput) {
                searchInput.addEventListener("keyup", function(e) {
                    const value = e.target.value;
                    if (value === "") {
                        tabla.clearFilter();
                    } else {
                        tabla.setFilter([
                            [{
                                    field: "nombrecliente",
                                    type: "like",
                                    value: value
                                },
                                {
                                    field: "vehiculo",
                                    type: "like",
                                    value: value
                                },
                                {
                                    field: "documento",
                                    type: "like",
                                    value: value
                                },
                                {
                                    field: "asesor_nombre",
                                    type: "like",
                                    value: value
                                }
                            ]
                        ]);
                    }
                });
            }


        }
































        async function getLocalesActivos() {
            try {
                const response = await fetch('/api/localesActivos', {
                    method: 'GET'
                });
                const locales = await response.json();
                // console.log(locales);

                locales.forEach(el => {
                    selectLocal.innerHTML += `<option value="${el.idlocal}">${el.local}</option>`;
                });

            } catch (error) {
                console.log('Error al obtener locales activos:', error);
            }
        }
        getLocalesActivos();




        const inputIdCotizacion = document.getElementById('contrato-idcotizacion');
        const displayIdCotizacion = document.getElementById('contrato-cotizacion-id-display');

        document.querySelectorAll('.btnCrearContrato').forEach(btn => {
            btn.addEventListener('click', function() {
                const idCotizacion = this.dataset.id;
                const cliente = this.dataset.cliente;
                const numCuotas = this.dataset.numcuotas;

                inputIdCotizacion.value = idCotizacion;
                displayIdCotizacion.textContent = `${idCotizacion} - ${cliente} (${numCuotas} cuotas)`;
            });
        });

        document.getElementById('formCrearContrato').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch("/contrato/store", {
                    method: "POST",
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert("Contrato creado con ID " + data.idcontrato);
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(err => console.error("Error:", err));
        });
















        const aprobarModal = new bootstrap.Modal(aprobarModalElement);

        aprobarModalElement.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const clienteNombre = button.getAttribute('data-nombre-cliente');

            cotizacionAprobarIdInput.value = id;
            clienteAprobarNombreStrong.textContent = clienteNombre;
        });


        confirmarAprobarBtn.addEventListener('click', function() {
            const idcotizacion = cotizacionAprobarIdInput.value;
            aprobarModal.hide();
            fetch(`/cotizacion/aprobar/${idcotizacion}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al aprobar la cotización. Código: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {

                        showToast(data.message, 'SUCCESS', 1200);
                        setTimeout(() => {
                            window.location.reload();

                        }, 1260);
                    } else {
                        showToast('Error: ' + data.message, 'ERROR', 1200);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error de conexión al aprobar la cotización.');
                });
        });




    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>