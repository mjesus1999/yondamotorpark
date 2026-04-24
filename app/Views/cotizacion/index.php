<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">
<link rel="stylesheet" href="/assets/css/contrato-modal.css">

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
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'P' ? 'btn-warning' : 'btn-outline-warning' ?>" title="Cotizaciones creadas que aún no cuentan con un pago de separación (Inicial)">
                                <i class="bi bi-clock"></i> Pendientes
                            </a>
                            <a href="/cotizacion/S"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'S' ? 'btn-secondary' : 'btn-outline-secondary' ?>" title="Cotizaciones que cuentan con pago de separación (Incial)">
                                <i class="bi bi-x-octagon"></i></i> Separadas
                            </a>
                            <a href="/cotizacion/A"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'A' ? 'btn-success' : 'btn-outline-success' ?>" title="Cotizaciones que han sido aprobadas por el análista de crédito">
                                <i class="bi bi-check-circle"></i> Aprobadas
                            </a>


                            <!-- <a href="/cotizacion/O"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'O' ? 'btn-info' : 'btn-outline-info' ?>" title="Cotizaciones que han tenido obervaciones por parte del análista de crédito">
                                <i class="bi bi-eye"></i> Observadas
                            </a>

                            <a href="/cotizacion/R"
                                class="btn btn-sm <?= strtoupper($estadoActual) === 'R' ? 'btn-danger' : 'btn-outline-danger' ?>" title="Cotizaciones rechazadas por análista de crédito">
                                <i class="bi bi-x-octagon"></i></i> Rechazadas
                            </a> -->


                        </div>
                        <button title="Exportar Reporte General por Hojas" class="btn btn-sm btn-outline-success" id="btn-exportar-general">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Reporte General
                        </button>
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


<div class="modal fade" id="contratoModal" tabindex="-1" aria-labelledby="contratoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formCrearContrato" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="contratoModalLabel">
                        <i class="bi bi-file-earmark-text"></i>
                        Nuevo Contrato
                        <i class="bi bi-stars sparkle" style="font-size: 1rem; margin-left: 0.5rem;"></i>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" name="idcotizacion" id="contrato-idcotizacion" value="">

                    <!-- Card de Información de Cotización -->
                    <div class="info-card fade-in-up">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-file-text" style="font-size: 1.5rem;"></i>
                            <div>
                                <p class="mb-1 text-secondary" style="font-size: 0.875rem;">Cotización seleccionada:</p>
                                <p class="mb-0" style="font-weight: 600;">
                                    <span id="contrato-cotizacion-id-display"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Información del Local -->
                    <div class="card-section fade-in-up" style="animation-delay: 0.1s;">
                        <div class="card-section-title">
                            <i class="bi bi-building icon-primary"></i>
                            <span>Información del Local</span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="idlocal" class="form-label">
                                    Local <span class="required">*</span>
                                </label>
                                <select class="form-select" id="idlocal" name="idlocal" required>
                                    <option value="">Seleccione Local</option>

                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fechainicio" class="form-label">
                                    Fecha Inicio Contrato <span class="required">*</span>
                                </label>
                                <input type="date" class="form-control" id="fechainicio" name="fechainicio" required value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Información de Pagos -->
                    <div class="card-section fade-in-up" style="animation-delay: 0.2s;">
                        <div class="card-section-title">
                            <i class="bi bi-credit-card icon-info"></i>
                            <span>Información de Pagos</span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="diapago" class="form-label">
                                    Día de Pago (Mensual) <span class="required">*</span>
                                </label>
                                <input type="number" class="form-control" id="diapago" name="diapago"
                                    min="1" max="31" required value="<?= date('d') ?>">
                                <small class="form-text">Día del mes en el que se efectuará cada pago.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecharevision" class="form-label">
                                    Fecha de Revisión <span class="required">*</span>
                                </label>
                                <input type="date" class="form-control" id="fecharevision" name="fecharevision" required>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Observaciones -->
                    <div class="card-section fade-in-up" style="animation-delay: 0.3s;">
                        <div class="card-section-title">
                            <i class="bi bi-pencil-square icon-success"></i>
                            <span>Observaciones</span>
                        </div>

                        <div class="mb-0">
                            <label for="observaciones" class="form-label">
                                Notas adicionales (opcional)
                            </label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="4"
                                placeholder="Ingrese cualquier observación o nota importante sobre este contrato..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-gradient-primary" id="btnGuardarContrato">
                        <i class="bi bi-save"></i>
                        Guardar
                    </button>


                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="reservaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold">Vehículo reservado</h6>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                <p id="reservaMensaje" class="mb-2"></p>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="/assets/js/logoBase64.js"></script>
<script src="/assets/js/cotizacionPDF.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>

<script>
    function mostrarModalReserva(cliente, motivo) {
        const mensajeEl = document.getElementById('reservaMensaje');
        const modalEl = document.getElementById('reservaModal');
        if (!mensajeEl || !modalEl) return;

        let textoMotivo = '';

        switch (motivo) {
            case 'contrato':
                textoMotivo = 'Este vehículo ya tiene un contrato asociado con el cliente:';
                break;
            case 'reservado':
                textoMotivo = 'Este vehículo ya está separado por otra cotización del cliente:';
                break;
            case 'contado':
                textoMotivo = 'Este vehículo ya fue vendido al contado al cliente:';
                break;
            default:
                textoMotivo = 'Este vehículo no está disponible. Pertenece a:';
                break;
        }


        mensajeEl.innerHTML = `<p class="mb-0">${textoMotivo}<br><strong class="text-primary">${cliente || 'No disponible'}</strong></p>`;

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };

    document.addEventListener('DOMContentLoaded', async () => {

        const cotizacionAprobarIdInput = document.getElementById('cotizacion-aprobar-id');
        const clienteAprobarNombreStrong = document.getElementById('cliente-aprobar-nombre');
        const confirmarAprobarBtn = document.getElementById('confirmarAprobarBtn');
        const diaPago = document.getElementById('diapago');
        const fechaInicio = document.getElementById('fechainicio');
        const selectLocal = document.getElementById('idlocal');
        const btnExcelReporteGeneral = document.getElementById('btn-exportar-general');
        window.APP_DATA_TABLE = <?php echo json_encode($cotizaciones); ?>;

        window.APP_CONFIG = {
            puedeVerTodas: <?php echo $puede_ver_todas ? 'true' : 'false'; ?>,
            estadoActual: '<?php echo $estadoActual; ?>'
        };


        const container = document.getElementById("tabla-cotizacion");
        if (!container) return;

        const cotizaciones = window.APP_DATA_TABLE || [];

        const config = window.APP_CONFIG || {};


        const tabla = new Tabulator("#tabla-cotizacion", {
            data: cotizaciones,
            theme: "simple",
            layout: "fitColumns",
            responsiveLayout: "collapse", //hace que colapse columnas en móvil
            pagination: true,
            paginationSize: 15,
            paginationCounter: "rows",
            responsiveLayoutCollapseStartOpen: false, // Inicia colapsado
            groupBy: 'documento',
            groupHeader: function(value, count, data, group) {
                const nombreCliente = data[0].nombrecliente || 'Cliente Desconocido';
                return `${nombreCliente} (${value}) <span class='badge bg-info ms-2'>${count} cotizaciones</span>`;
            },
            groupStartOpen: false,
            groupToggleElement: "header",
            columns: [{
                    formatter: "responsiveCollapse",
                    width: 40,
                    minWidth: 30,
                    hozAlign: "center",
                    resizable: false,
                    headerSort: false,
                    responsive: 0 // Siempre visible
                }, {
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
                    responsive: 2, // OCULTAR DESPUES _> '0' ES NO MOSTRARA,
                    tooltip: true
                },
                {
                    title: "Vehículo",
                    field: "vehiculo",
                    hozAlign: "left",
                    tooltip: true,
                    responsive: 1
                },
                {
                    title: "Inicial",
                    field: "inicial",
                    hozAlign: "right",
                    formatter: function(cell) {
                        const data = cell.getRow().getData();
                        const moneda = data.moneda || "PEN";
                        const symbol = moneda === "USD" ? "$ " : "S/ ";

                        const value = parseFloat(cell.getValue() || 0).toFixed(2);
                        return `${symbol}${value}`;
                    },
                    tooltip: true,
                    minWidth: 80,
                    responsive: 1
                },
                {
                    title: "N° Cuotas",
                    field: "numcuotas",
                    tooltip: true,
                    hozAlign: "center",
                },
                {
                    title: "Valor Cuota",
                    field: "valorcuota",
                    hozAlign: "right",
                    formatter: function(cell) {
                        const data = cell.getRow().getData();
                        const moneda = data.moneda || "PEN";
                        const symbol = moneda === "USD" ? "$ " : "S/ ";

                        const value = parseFloat(cell.getValue() || 0).toFixed(2);
                        return `${symbol}${value}`;
                    },
                    tooltip: true,
                    minWidth: 90
                },
                {
                    title: "Documento",
                    field: "documento",
                    hozAlign: "center",
                    minWidth: 120,
                    responsive: 0,
                    tooltip: true
                },
                {
                    title: "Asesor",
                    field: "asesor_nombre",
                    hozAlign: "left",
                    minWidth: 150,
                    responsive: 0,
                    tooltip: true
                },
                {
                    title: "Acciones",
                    field: "idcotizacion",
                    headerSort: false,
                    minWidth: 120,
                    responsive: 0,
                    formatter: function(cell) {
                        const {
                            idcotizacion: id,
                            estadocotizacion: estado,
                            nombrecliente,
                            valorcuota,
                            moneda,
                            numcuotas,
                            habilitar_contrato,
                            existe_reserva,
                            idcotizacion_reserva,
                            reserva_cliente_nombre,
                            contrato_cliente_nombre,
                            vehiculo_en_contrato,
                            vehiculo_vendido_contado,
                            contado_cliente_nombre
                        } = cell.getRow().getData();

                        const inicialCompleta = Number(habilitar_contrato) === 1;
                        const esMiReserva = Number(idcotizacion_reserva) === Number(id);
                        const vehiculoYaVendidoPorContrato = Number(vehiculo_en_contrato) === 1 && !esMiReserva;
                        const vehiculoReservadoPorOtro = Number(existe_reserva) === 1 && !esMiReserva;
                        const vehiculoVendidoAlContado = Number(vehiculo_vendido_contado) === 1;

                        const acciones = [];


                        if (vehiculoVendidoAlContado) {

                            acciones.push(`<span class="px-1" style="cursor: pointer;" onclick="mostrarModalReserva('${contado_cliente_nombre}', 'contado')" title="Vehículo vendido al contado"><i class="bi bi-cash-coin fs-5 text-muted"></i></span>`);
                        } else {
                            switch (estado) {
                                case 'P':
                                    acciones.push(`<a class="btn-download-pdf px-1" style="cursor: pointer;" data-id="${id}" data-cliente="${nombrecliente}" title="PDF Cotización"><i class="bi bi-filetype-pdf text-danger fs-5"></i></a>`);
                                    if (vehiculoYaVendidoPorContrato) {
                                        acciones.push(`<span class="px-1" style="cursor: pointer;" onclick="mostrarModalReserva('${contrato_cliente_nombre}', 'contrato')" title="Vehículo ya vendido"><i class="bi bi-currency-dollar fs-5 text-muted"></i></span>`);
                                    } else if (vehiculoReservadoPorOtro) {
                                        acciones.push(`<span class="px-1" style="cursor: pointer;" onclick="mostrarModalReserva('${reserva_cliente_nombre}', 'reservado')" title="Vehículo separado"><i class="bi bi-currency-dollar fs-5 text-muted"></i></span>`);
                                    } else {
                                        acciones.push(`<a class="px-1" href="/cotizacion/pagoInicial/${id}" title="Registrar Primer Pago"><i class="bi bi-currency-dollar fs-5 text-success"></i></a>`);
                                    }
                                    break;
                                case 'S':
                                    acciones.push(`<a class="px-1" style="cursor: pointer;" data-id="${id}" title="Acta de separación vehicular" data-action="verActa"><i class="bi bi-file-earmark-pdf fs-5"></i></a>`);
                                    acciones.push(`<a class="px-1" href="/fichasolicitud/${id}" title="Adjuntar Ficha de Solicitud"><i class="bi bi-file-earmark-plus fs-5 text-warning fw-bold"></i></a>`);
                                    if (!inicialCompleta) {
                                        acciones.push(`<a class="px-1" href="/cotizacion/pagoInicial/${id}" title="Continuar Pagando Inicial"><i class="bi bi-currency-dollar fs-5 text-success"></i></a>`);
                                    } else {
                                        acciones.push(`<span class="px-1" title="Inicial completa"><i class="bi bi-currency-dollar fs-5 text-muted"></i></span>`);
                                    }
                                    break;
                                case 'A':
                                    if (inicialCompleta) {
                                        if (vehiculoYaVendidoPorContrato ) {
                                            console.log('vehiculoYaVendidoPorContrato', vehiculoYaVendidoPorContrato);
                                            acciones.push(`<span class="px-1" style="cursor: pointer;" onclick="mostrarModalReserva('${reserva_cliente_nombre}', 'contrato')" title="Vehículo ya tiene contrato"><i class="bi bi-file-earmark-text fs-5 text-muted"></i></span>`);
                                        } else {
                                            acciones.push(`<a class="px-1 text-info fw-bold btnCrearContrato" title="Crear Contrato" data-id="${id}" data-cliente="${nombrecliente}" data-numcuotas="${numcuotas}" data-valorCuota="${valorcuota}"  data-moneda="${moneda}"data-bs-toggle="modal" data-bs-target="#contratoModal"><i class="bi bi-file-earmark-text fs-5"></i></a>`);
                                        }
                                        acciones.push(`<span class="px-1" title="Inicial Pagada"><i class="bi bi-currency-dollar fs-5 text-muted"></i></span>`);
                                    } else {
                                        acciones.push(`<a class="px-1" href="/cotizacion/pagoInicial/${id}" title="Completar Pago de Inicial"><i class="bi bi-currency-dollar fs-5 text-success"></i></a>`);
                                    }
                                    break;
                            }
                        }
                        return acciones.join('');
                    },

                    cellClick: function(e, cell) {
                        const row = cell.getRow().getData();
                        const id = row.idcotizacion;
                        const idReserva = row.idcotizacion_reserva ? Number(row.idcotizacion_reserva) : null;

                        if (row.existe_reserva && idReserva !== id) {
                            const motivo = row.reserva_tipo === 'PAGO' ? 'Pago inicial' :
                                (row.reserva_tipo === 'APROBADA' ? 'Cotización aprobada' : 'Reserva');

                            mostrarReserva(
                                row.reserva_cliente_nombre || "N/D",
                                row.reserva_documento || "N/D",
                                motivo
                            );
                        }
                    }
                }


            ],

            langs: {
                "es-es": {
                    "pagination": {
                        "page_size": "Registros por página",
                        "first": "<<",
                        "last": ">>",
                        "prev": "<",
                        "next": ">",
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
        searchInput.addEventListener("input", function(e) {
            const value = e.target.value.trim();

            if (value === "") {
                tabla.clearFilter(true);
                tabla.setData(cotizaciones);
                tabla.redraw(true);
            } else {
                tabla.setFilter([
                    [{
                            field: "nombrecliente",
                            type: "like",
                            value
                        },
                        {
                            field: "documento",
                            type: "like",
                            value
                        }
                    ]
                ]);
            }
        });




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

        document.addEventListener('click', (event) => {
            const btn = event.target.closest('.btnCrearContrato');
            if (!btn) return;

            const idCotizacion = btn.dataset.id;
            const cliente = btn.dataset.cliente;
            const numCuotas = btn.dataset.numcuotas;
            const valorCuota = btn.dataset.valorcuota;
            const simboloMoneda = btn.dataset.moneda === 'PEN' ? 'S/' : '$/';

            inputIdCotizacion.value = idCotizacion;
            displayIdCotizacion.textContent = `${idCotizacion} - ${cliente} (${numCuotas} cuotas) - (${simboloMoneda} ${valorCuota} valor de cuota)`;
        });

        fechaInicio.addEventListener('change', (e) => {
            let fecha = e.target.value
            let dia = fecha.split('-')[2];
            diaPago.value = dia;
        });



        document.getElementById('formCrearContrato').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const btnContrato = document.getElementById('btnGuardarContrato');
            const originalHTML = btnContrato.innerHTML;


            btnContrato.innerHTML = `<div class="spinner-border text-white" role="status"></div> Registrando...`;
            btnContrato.disabled = true;

            try {

                const result = await ask('¿Crear contrato?', 'Confirmar');
                if (!result) return;

                const req = await fetch('/contrato/store', {
                    method: 'POST',
                    body: formData
                });
                const res = await req.json();

                if (res.success) {
                    showToast('Contrato creado correctamente', 'SUCCESS', 1200);
                    setTimeout(() => location.reload(), 1270);
                } else {
                    showToast(res.message, 'ERROR', 1200);
                }

            } catch (error) {
                console.log(error);
                showToast('Error en la conexión', 'ERROR', 1200);
            } finally {
                // Restaurar contenido del botón
                btnContrato.innerHTML = originalHTML;
                btnContrato.disabled = false;
            }
        });


        async function getDataActaSeparacion(idcotizacion) {
            try {
                const req = await fetch(`/api/actaSeparacion/${idcotizacion}`, {
                    method: 'GET',
                });

                if (!req.ok) {
                    throw new Error('Error en la solicitud');
                }
                const res = await req.json();
                if (res.success && res.data) {
                    generarPDFActaSeparacion(res.data);

                } else {
                    return null;
                }


            } catch (error) {
                console.error('Error al obtener datos del acta de separación:', error);
                return null;
            }
        }
function generarPDFActaSeparacion(data) {
    if (!data) {
        showToast('No se encontraron datos para el acta de separación.', 'ERROR', 2000);
        return;
    }

    
    const clienteNombre = (data.cliente || 'N/D').toUpperCase();
    const nroDocumento = data.nrodoc || 'N/D';
    const telefono = data.telprimario || 'N/D';
    const fechaPago = data.fechapago || 'N/D';
    const montoAmortizacion = parseFloat(data.amortizacion || 0).toFixed(2);
    
    
    const dirPartes = [
        data.direccion || 'DIRECCIÓN NO ESPECIFICADA',
        data.distrito || '',
        data.provincia || '',
        data.departamento || ''
    ].filter(part => part.length > 0); 
    const direccionCompleta = dirPartes.join(', ').toUpperCase();

    
    const vMarca = (data.marca || 'N/D').toUpperCase();
    const vModelo = (data.modelo || 'N/D').toUpperCase();
    const vColor = (data.color || 'N/D').toUpperCase();
    const vAnio = data.anio || 'N/D';
    const vCombustible = (data.combustible || 'N/D').toUpperCase();

    
    const fechaObj = new Date();
    const dia = fechaObj.getDate();
    const anio = fechaObj.getFullYear();
    const mes = fechaObj.toLocaleString('es-ES', { month: 'long' });
    const fechaFormateada = `Chincha, ${dia} de ${mes} del ${anio}`;
    
    const simboloMoneda = data.moneda == 'PEN' ? 'S/' : '$';
    const tipoDoc = data.tipocliente == 'P' ? 'DNI' : 'RUC';

    let textoPago = '';
    const medio = data.mediopago || 'N/D';
    if (medio === 'Efectivo') {
        textoPago = `EFECTIVO`;
    } else if (medio === 'Yape' || medio === 'Plin') {
        textoPago = `pago por ${medio} con N° de operación ${data.numerotransaccion || 'S/N'}`;
    } else if (medio === 'Transferencia Bancaria') {
        textoPago = `pago por ${medio} a la cuenta de ${data.entidad || 'N/D'} con N° de operación ${data.numerotransaccion || 'S/N'}`;
    } else {
        textoPago = medio;
    }

    const docDefinition = {
        pageSize: 'A4',
        pageOrientation: 'portrait',
        pageMargins: [70, 100, 70, 40],
        defaultStyle: {
            fontSize: 9.3,
            lineHeight: 1.15,
            color: '#333333'
        },
        header: {
            image: window.cabeceraYonda,
            width: 595,
            alignment: 'center',
            margin: [0, 25, 0, 0]
        },
        content: [
            { text: fechaFormateada, alignment: 'right', margin: [0, 2, 0, 20], fontSize: 10 },
            { text: 'CONSTANCIA DE SEPARACIÓN DE VEHÍCULO', bold: true, alignment: 'center', margin: [0, 0, 0, 15], fontSize: 12, decoration: 'underline' },
            {
                stack: [
                    { text: window.nombreEmpresa, style: 'datosEmpresa' },
                    { text: `RUC: ${window.rucEmpresa}`, style: 'infoContacto' },
                    { text: 'Dirección: Carretera Panamericana km 201 – Chincha', style: 'infoContacto' },
                    { text: 'Teléfonos: 927 676 338 / 971 027 612', style: 'infoContacto' }
                ],
                margin: [0, 0, 0, 10]
            },
            {
                text: [
                    'Conste por el presente documento que la empresa ',
                    { text: window.nombreEmpresa, bold: true },
                    ', ha recibido del Sr. ',
                    { text: clienteNombre, bold: true },
                    `, identificado con ${tipoDoc} N.º `,
                    { text: nroDocumento, bold: true },
                    ', con domicilio en ',
                    { text: direccionCompleta },
                    ', y número de celular ',
                    { text: telefono, bold: true },
                    `, la suma de ${simboloMoneda} `,
                    { text: montoAmortizacion, bold: true },
                    ', mediante ',
                    { text: textoPago, bold: true },
                    ' con fecha ',
                    { text: fechaPago, bold: true },
                    '.'
                ],
                style: 'bodyText',
                margin: [0, 0, 0, 10]
            },
            { text: 'Este monto corresponde a la cuota inicial por concepto de separación del siguiente vehículo:', style: 'bodyText', margin: [0, 0, 0, 10] },
            {
                table: {
                    headerRows: 1,
                    widths: ['auto', 250],
                    body: [
                        [{ text: 'CONCEPTO', bold: true, fillColor: '#eeeeee' }, { text: 'DETALLE', alignment: 'center', bold: true, fillColor: '#eeeeee' }],
                        [{ text: 'Marca', bold: true }, { text: vMarca }],
                        [{ text: 'Modelo', bold: true }, { text: vModelo }],
                        [{ text: 'Color', bold: true }, { text: vColor }],
                        [{ text: 'Año Modelo', bold: true }, { text: vAnio }],
                        [{ text: 'Combustible', bold: true }, { text: vCombustible }]
                    ]
                },
                margin: [50, 0, 0, 10]
            },
            
            {
                columns: [
                    {
                        stack: [
                            { text: '____________________________________________', style: 'firma', margin: [0, 50, 0, 0] },
                            { text: window.nombreEmpresa, style: ['firma', 'datosEmpresa'] },
                            { text: `RUC: ${window.rucEmpresa}`, style: 'firma' }
                        ],
                        width: '*'
                    },
                    {
                        stack: [
                            { text: '______________________________________________', style: 'firma', margin: [0, 50, 0, 0] },
                            { text: clienteNombre, style: 'firma' },
                            { text: `${tipoDoc}: ${nroDocumento}`, style: 'firma' }
                        ],
                        width: '*'
                    }
                ],
                margin: [0, 20, 0, 0]
            }
        ],
        styles: {
            datosEmpresa: { bold: true, margin: [0, 0, 0, 2] },
            infoContacto: { margin: [0, 0, 0, 2] },
            bodyText: { alignment: 'justify', lineHeight: 1.2 },
            firma: { alignment: 'center', bold: true },
            notaImportante: { bold: true, fontSize: 8, margin: [0, 10, 0, 5] },
            textoPequeno: { alignment: 'justify', fontSize: 8.5 }
        },
        footer: function() {
            return {
                image: window.footerYonda,
                width: 600,
                alignment: 'center'
            };
        }
    };

    pdfMake.createPdf(docDefinition).open();
}


        async function generarExcel() {
            if (btnExcelReporteGeneral) {
                try {
                    const req = await fetch('/api/cotizacion/reporte-general', {
                        method: 'GET'
                    });
                    const res = await req.json();
                    if (res.success && Object.keys(res.data).length > 0) {

                        const styleHeader = (cell) => {
                          
                            cell.font = {
                                bold: true,
                                color: {
                                    argb: 'FFFFFFFF'
                                }
                            };
                            cell.fill = {
                                type: 'pattern',
                                pattern: 'solid',
                                fgColor: {
                                    argb: 'FF007BFF'
                                }
                            };
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
                        };

                        const styleCell = (cell) => {
                   
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
                        };

                        const capitalizar = (str) => str.charAt(0).toUpperCase() + str.slice(1);

                        const workbook = new ExcelJS.Workbook();

                        const dataAgrupada = res.data; 

                        const headers = [
                            '#',
                            'Cliente',
                            'Documento',
                            'Vehículo',
                            'Moneda',
                            'Inicial',
                            'Número cuotas',
                            'Valor cuota',
                            'Asesor',
                            'Estado'
                        ];

                        for (const estado in dataAgrupada) {
                            const listaCotizaciones = dataAgrupada[estado];
                            const nombreHoja = capitalizar(estado);

                            if (listaCotizaciones.length === 0) continue;

                            const worksheet = workbook.addWorksheet(nombreHoja);

                            const headerRow = worksheet.addRow(headers);
                            headerRow.eachCell(styleHeader);


                            listaCotizaciones.forEach((cot, index) => {

                                const row = worksheet.addRow([
                                    index + 1,
                                    cot.cliente,
                                    cot.documento,
                                    cot.vehiculo,
                                    cot.moneda,
                                    parseFloat(cot.inicial.replace(/[S/$,]/g, '').trim()), 
                                    cot.numcuotas,
                                    parseFloat(cot.valorcuota.replace(/[S/$,]/g, '').trim()), 

                                    cot.asesor,
                                    cot.estado
                                ]);

                                row.eachCell(styleCell);

    
                                row.getCell(6).numFmt = '#,##0.00';
                                row.getCell(8).numFmt = '#,##0.00';

                                // Añadir la moneda al formato (ej. S/ #,##0.00 o $ #,##0.00)
                                const currencySymbol = cot.moneda === 'USD' ? '$ ' : 'S/ ';
                                row.getCell(6).numFmt = `"${currencySymbol}"#,##0.00`;
                                row.getCell(8).numFmt = `"${currencySymbol}"#,##0.00`;

                            });

                         
                            worksheet.columns.forEach((column, i) => {
                                let maxLength = 0;
                                column.eachCell({
                                    includeEmpty: true
                                }, (cell) => {
                                    let columnLength = cell.value ? cell.value.toString().length : 10;
                                    if (columnLength > maxLength) {
                                        maxLength = columnLength;
                                    }
                                });
                                column.width = Math.min(60, maxLength + 2);
                            });

                            worksheet.getColumn(6).width = 18;

                        }

                        // Generar y Descargar el Archivo 
                        const buffer = await workbook.xlsx.writeBuffer();
                        const blob = new Blob([buffer], {
                            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'Reporte_General_Cotizaciones.xlsx';
                        a.click();
                        window.URL.revokeObjectURL(url);
                    } else if (res.success && Object.keys(res.data).length === 0) {
                   
                        console.log(res.message); 
                    } else {
                       
                        console.error(res.message); 
                    }

                } catch (error) {
                    console.error('Error al procesar la solicitud o generar el Excel:', error);
                }

            } else {
                console.log('No existe el botón para exportar el excel');
            }

        }

        btnExcelReporteGeneral.addEventListener('click', generarExcel);


        document.getElementById('tabla-cotizacion').addEventListener('click', function(e) {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;
            const id = btn.dataset.id;
            const action = btn.dataset.action;

            switch (action) {
                case 'verActa':
                    getDataActaSeparacion(id);
                    break;
            }

        });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>