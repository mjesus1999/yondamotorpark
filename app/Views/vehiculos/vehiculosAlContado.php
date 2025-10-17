<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">
<link rel="stylesheet" href="/assets/css/vehiculosAlContado.css">

<div class="container-fluid ">


    <div class="alert alert-info mt-2 mb-5" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Vehículos al contado</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>

            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#ventaModal">
                            <i class="bi bi-plus-circle me-2"></i> Registrar
                        </button>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div id="tabla-vehiculos"></div>

    <div class="modal fade" id="ventaModal" tabindex="-1" aria-labelledby="modalVentaTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header-gradient">
                    <div class="w-100">
                        <h4 class="text-white text-center mb-0" id="modalVentaTitle">
                            Registrar Venta al Contado (Paso 1 de 3)
                        </h4>
                        <div class="progress-steps">
                            <div class="step-indicator active" id="stepV1"></div>
                            <div class="step-indicator" id="stepV2"></div>
                            <div class="step-indicator" id="stepV3"></div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div id="pasoVenta1" class="paso-content">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Búsqueda de Cliente por DNI</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-primary"><i class="bi bi-person-fill text-white"></i></span>
                                <input type="text" class="form-control" id="dniBusqueda" placeholder="Ingrese DNI (8 dígitos)" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <button class="btn btn-gradient" onclick="buscarCliente()" id="btnBuscarCliente">
                                    <i class="bi bi-search me-2"></i>Buscar
                                </button>
                            </div>
                            <div id="clienteResultado"></div>
                        </div>
                        <div class="mb-4">
                            <label for="vehiculoSelect" class="form-label fw-bold">Búsqueda de Vehículo Disponible</label>
                            <div class="input-group">
                                <span class="input-group-text bg-warning"><i class="bi bi-car-front-fill text-white"></i></span>
                                <select id="vehiculoSelect" class="form-select">
                                    <option value="">Buscar por marca, modelo...</option>
                                </select>
                            </div>
                            <div id="vehiculoResultado"></div>
                        </div>
                    </div>

                    <div id="pasoVenta2" class="paso-content d-none">
                        <div class="card mb-4">
                            <div class="card-body " style="border-radius:10px; background:linear-gradient(135deg, #5098f7ff 0%, #3b74eeff 50%, #69c7ecff 100%) ;">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <small class="text-white d-block">Cliente</small>
                                        <strong id="resumenCliente" class="text-white"></strong>
                                        <div class="text-white" id="resumenDNI"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-white d-block">Vehículo</small>
                                        <strong id="resumenVehiculo" class="text-white"></strong>
                                        <div class="text-white" id="resumenPlaca"></div>
                                        <div id="resumenPrecioVehiculo"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Moneda de Pago</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="radio-card" id="radioPEN" onclick="seleccionarMoneda('PEN')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0">Soles</h5>
                                                <small class="text-muted">PEN (S/)</small>
                                            </div>
                                            <i class="bi bi-currency-exchange fs-2 text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="radio-card selected" id="radioUSD" onclick="seleccionarMoneda('USD')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0">Dólares</h5>
                                                <small class="text-muted">USD ($)</small>
                                            </div>
                                            <i class="bi bi-currency-dollar fs-2 text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="tipoCambio" class="form-label fw-bold">Tipo de Cambio del Día (Venta)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success"><i class="bi bi-cash-coin text-white"></i></span>
                                <input type="number" class="form-control" id="tipoCambio" value="" step="0.01" oninput="calcularPrecioFinal()">
                            </div>
                        </div>
                        <div class="precio-final-display">
                            <p class="fw-bold mb-2">Precio de Venta Final</p>
                            <p class="precio-final-amount" id="precioFinal">$ 0.00</p>
                        </div>
                    </div>

                    <div id="pasoVenta3" class="paso-content d-none">
                        <div class="card mb-4 modal-final">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <small class="d-block">Cliente</small>
                                        <strong id="resumenFinalCliente"></strong>
                                        <div id="resumenFinalDNI"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="d-block">Vehículo</small>
                                        <strong id="resumenFinalVehiculo"></strong>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <small class="d-block mb-1">Monto a Pagar</small>
                                    <h3 class="mb-0 fw-bold" id="resumenFinalMonto">$ 0.00</h3>
                                </div>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="fechaPago" class="form-label fw-bold">Fecha de Pago <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text " style="background:linear-gradient(135deg, #3ebceeff 0%, #3ac7ebff 50%, #52ace0ff 100%);"><i class="bi bi-calendar-event text-white"></i></span>
                                        <input type="date" class="form-control" id="fechaPago">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="montoPagado" class="form-label fw-bold">Amortización <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:linear-gradient(135deg, #118002ff 0%, #599e31ff 50%, #98e698ff 100%);"><i class="bi bi-cash-stack text-white"></i></span>
                                        <input type="number" class="form-control" id="montoPagado" step="0.01" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="medioPago" class="form-label fw-bold">Medio de Pago <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:linear-gradient(135deg, #f1b13aff 0%, #d6af30ff 50%, #eeb958ff 100%);"><i class="bi bi-credit-card-2-front text-white"></i></span>
                                        <select class="form-select" id="medioPago">
                                            <option value="">Seleccione método de pago</option>
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                            <option value="Yape">Yape</option>
                                            <option value="Plin">Plin</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <label for="cuentaDestino" class="form-label fw-bold">Cuenta de Destino <span class="badge text-muted fw-bold">Solo Tranferencias</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style=" background:linear-gradient(135deg, #5098f7ff 0%, #3b74eeff 50%, #69c7ecff 100%);"><i class="bi bi-piggy-bank text-white"></i></span>
                                        <select class="form-select" id="cuentaDestino">
                                            <option value="">Seleccione cuenta bancaria</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-6">

                                    <label for="numeroTransaccion" class="form-label fw-bold">N° de Transacción <span class="badge text-muted fw-bold">Opcional</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:linear-gradient(135deg, #aad33bff 0%, #807d03ff 50%, #bdba21ff 100%);"> <i class="bi bi-123 text-white"></i></span>
                                        <input type="text" class="form-control" id="numeroTransaccion" placeholder="Medio de pago Yape/Plin/Transferencia">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="" class="form-label fw-bold">Comprobante</label>
                                    <div class="input-group">
                                        <input type="file" id="comprobante" class="d-none">
                                        <span id="nombre-archivo" class="form-control">Ningún archivo seleccionado...</span>
                                        <label for="comprobante" class="btn btn-outline-primary mb-0">
                                            <i class="bi bi-file-earmark-arrow-up mt-4"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="observaciones" class="form-label fw-bold">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" rows="3" placeholder="Opcional..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-body">
                    <button type="button" class="btn btn-outline-secondary" onclick="pasoVentaAnterior()" id="btnVentaAtras">
                        <i class="bi bi-chevron-left me-2"></i>Atrás
                    </button>
                    <button type="button" class="btn btn-gradient" onclick="pasoVentaSiguiente()" id="btnVentaSiguiente">
                        Siguiente<i class="bi bi-chevron-right ms-2"></i>
                    </button>
                    <button type="button" class="btn btn-success-gradient d-none" id="btnVentaRegistrar">
                        <i class="bi bi-save me-2"></i>Registrar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header-success d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                        <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-fill-add text-white fs-4"></i>
                        </div>
                        <div>
                            <h4 class="text-white mb-1" id="clienteModalLabel">Registrar Nuevo Cliente</h4>
                            <p class="text-white-50 mb-0 small">Complete los datos del cliente</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-4">
                        <div class="section-header green">
                            <i class="bi bi-person-fill"></i>
                            <h5 class="mb-0">Datos Personales</h5>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6"><label for="clienteApellidos" class="form-label">Apellidos <span class="text-danger">*</span></label><input type="text" class="form-control" id="clienteApellidos" required></div>
                            <div class="col-md-6"><label for="clienteNombres" class="form-label">Nombres <span class="text-danger">*</span></label><input type="text" class="form-control" id="clienteNombres" required></div>
                            <div class="col-md-4"><label for="clienteTipoDoc" class="form-label">Tipo Doc. <span class="text-danger">*</span></label><select class="form-select" id="clienteTipoDoc">
                                    <option value="DNI" selected>DNI</option>
                                    <option value="CEX">Carnet Extranjeria.</option>
                                    <option value="PAS">Pasaporte</option>
                                </select></div>
                            <div class="col-md-4"><label for="clienteNroDoc" class="form-label">N° Doc. <span class="text-danger">*</span></label><input type="text" class="form-control" id="clienteNroDoc" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                            <div class="col-md-4"><label class="form-label">Género <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3 mt-2">
                                    <div class="form-check"><input class="form-check-input" type="radio" name="clienteGenero" id="generoM" value="M" checked><label class="form-check-label" for="generoM">Masculino</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="clienteGenero" id="generoF" value="F"><label class="form-check-label" for="generoF">Femenino</label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="section-header blue">
                            <i class="bi bi-geo-alt-fill"></i>
                            <h5 class="mb-0">Ubicación</h5>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4"><label for="departamento" class="form-label">Departamento <span class="text-danger">*</span></label><select class="form-select" id="departamento">
                                    <option value="">Seleccione</option>
                                </select></div>
                            <div class="col-md-4"><label for="provincia" class="form-label">Provincia <span class="text-danger">*</span></label><select class="form-select" id="provincia" ">
                                <option value="">Seleccione</option>
                            </select></div>
                        <div class=" col-md-4"><label for="distrito" class="form-label">Distrito <span class="text-danger">*</span></label><select class="form-select" id="distrito">
                                        <option value="">Seleccione</option>
                                    </select></div>
                            <div class="col-12"><label for="clienteDireccion" class="form-label">Dirección</label><input type="text" class="form-control" id="clienteDireccion" placeholder="Opcional"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="section-header purple">
                            <i class="bi bi-telephone-fill"></i>
                            <h5 class="mb-0">Contacto</h5>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6"><label for="clienteTelPrimario" class="form-label">Teléfono Primario <span class="text-danger">*</span></label><input type="text" class="form-control" id="clienteTelPrimario" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required></div>
                            <div class="col-md-6"><label for="clienteTelAlternativo" class="form-label">Teléfono Alternativo</label><input type="text" class="form-control" id="clienteTelAlternativo" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Opcional"></div>
                        </div>
                    </div>
                    <div class="alert alert-warning"><small><span class="text-danger fw-bold">*</span> Campos obligatorios</small></div>
                </div>

                <div class="modal-footer bg-body">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success-gradient" id="btnGuardarCliente">
                        <i class="bi bi-save me-2"></i>Registrar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>
<script src="/assets/js/ubigeo.js" defer></script>
<script src="/assets/js/logoBase64.js"></script>
<script src=" https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


<script>
    let currentStepVenta = 1;
    let clienteSeleccionado = null;
    let vehiculoSeleccionado = null;
    let monedaSeleccionada = "USD";
    let fueClienteGuardado = false;

    let tomSelectVehiculo;
    let tipoCambio = document.getElementById('tipoCambio');
    let table;

    document.addEventListener('DOMContentLoaded', () => {

        table = new Tabulator('#tabla-vehiculos', {
            ajaxURL: 'vehiculosVendidosAlContado',
            ajaxConfig: 'GET',
            ajaxContentType: "json",
            progressiveLoadScrollMargin: 300,
            layout: "fitColumns",
            responsiveLayout: "collapse",
            pagination: "local",
            paginationSize: 15,
            index: "idvehiculo",
            placeholder: "No hay vehículos vendidos al contado.",
            movableColumns: true,
            locale: 'es-es',
            langs: {
                "es-es": {
                    "ajax": {
                        "loading": "Cargando...",
                        "error": "Error al Cargar",
                    },
                    "pagination": {
                        "first": "<<",
                        "first_title": "Primera Página",
                        "last": ">>",
                        "last_title": "Última Página",
                        "prev": "<",
                        "prev_title": "Página Anterior",
                        "next": ">",
                        "next_title": "Página Siguiente",
                    },
                }
            },


            columns: [{
                    formatter: "responsiveCollapse",
                    width: 40,
                    minWidth: 30,
                    hozAlign: "center",
                    resizable: false,
                    headerSort: false
                }, // Columna en movil para manejar el accordion
                {
                    title: "#",
                    formatter: "rownum",
                    hozAlign: "center",
                    width: 50
                },
                {
                    title: 'Amortización',
                    field: 'amortizacion',
                    hozAlign: 'right',
                    headerHozAlign: 'left',
                    width: 130,
                    tooltip: true,
                    formatter: function(cell) {
                        const rowData = cell.getRow().getData();
                        const monto = parseFloat(cell.getValue());
                        const simbolo = rowData.moneda === 'USD' ? '$' : 'S/';
                        return simbolo + ' ' + monto.toLocaleString('es-PE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                },
                {
                    title: 'Vehículo',
                    field: 'vehiculo',
                    headerHozAlign: "left",
                    minWidth: 400,
                    responsive: 4,
                    tooltip: true
                },
                {
                    title: 'Cliente',
                    field: 'cliente',
                    headerHozAlign: 'left',
                    minWidth: 280,
                    responsive: 3,
                    tooltip: true
                },
                {
                    title: 'Teléfono',
                    field: 'telprimario',
                    width: 80,
                    tooltip: true
                },
                {
                    title: 'Dirección',
                    field: 'direccion',
                    headerHozAlign: 'left',
                    minWidth: 300,
                    responsive: 1,
                    tooltip: true
                },
                {
                    title: 'Ubicación',
                    field: 'ubicacion',
                    headerHozAlign: 'left',
                    minWidth: 250,
                    responsive: 2,
                    tooltip: true
                },
                {
                    title: 'Acciones',
                    headerHozAlign: 'left',
                    formatter: (cell) => {
                        const id = cell.getRow().getData().idvehiculo;
                        return `
                             <button class="btn btn-sm btn-VerPDF" data-id="${id}" data-action="verPDF">
                               <i class="bi bi-filetype-pdf text-danger fs-5" data-id="${id}" data-action="verPDF"></i>
                             </button>
                    
                    `;
                    },
                    responsive: 0
                }
            ],

            ajaxResponse: (url, params, response) => {
                if (response.success) {
                    return response.vehiculos;
                }
            },
            ajaxError: (error) => {
                console.error("Error al cargar los datos:", error);
            }
        });

        getAllDepartamentos();

        document.getElementById('fechaPago').valueAsDate = new Date();
        const ventaModal = document.getElementById('ventaModal');
        ventaModal.addEventListener('show.bs.modal', resetearFormularioVenta);
        ventaModal.addEventListener('show.bs.modal', inicializarSelectorVehiculos);

        clienteModal.addEventListener('hidden.bs.modal', function() {
            // Siempre se ejecuta para arreglar el fondo del modal principal
            if (document.getElementById('ventaModal').classList.contains('show')) {
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
            }

            // Si el cliente se guardó, mostramos la alerta de éxito
            if (fueClienteGuardado) {
                showToast('¡Cliente Registrado!', 'SUCCESS', 1300);
                fueClienteGuardado = false; // Reseteamos la variable de control
            }
        });

        // Logica para el comprobante
        const inputComprobante = document.getElementById('comprobante');
        const nombreArchivoSpan = document.getElementById('nombre-archivo');
        inputComprobante.addEventListener('change', function() {
            // Si se seleccionó al menos un archivo
            if (this.files && this.files.length > 0) {
                // Muestra el nombre del primer archivo en el span
                nombreArchivoSpan.textContent = this.files[0].name;
            } else {

                nombreArchivoSpan.textContent = 'Ningún archivo seleccionado...';
            }
        });
    });

    async function getTipoCambio() {
        try {
            const req = await fetch('/cotizacion/tipo-cambio');
            const res = await req.json();
            if (res) {
                tipoCambio.value = res.tipo_cambio;
            } else {
                tipoCambio.value = '';
            }

        } catch (error) {
            console.log(error);
        }
    }



    function mostrarPasoVenta(paso) {
        currentStepVenta = paso;
        ['pasoVenta1', 'pasoVenta2', 'pasoVenta3'].forEach(p => document.getElementById(p).classList.add('d-none'));
        document.getElementById('pasoVenta' + paso).classList.remove('d-none');
        document.getElementById('modalVentaTitle').innerHTML = ` <i class="bi bi-bag "></i> Registrar Venta (Paso ${paso} de 3)`;

        ['stepV1', 'stepV2', 'stepV3'].forEach((id, index) => {
            const step = document.getElementById(id);
            step.classList.remove('active', 'completed');
            if (index + 1 < paso) step.classList.add('completed');
            if (index + 1 === paso) step.classList.add('active');
        });

        document.getElementById('btnVentaAtras').style.display = paso === 1 ? 'none' : 'inline-block';
        document.getElementById('btnVentaSiguiente').classList.toggle('d-none', paso === 3);
        document.getElementById('btnVentaRegistrar').classList.toggle('d-none', paso !== 3);

        if (paso === 1) validarPaso1Venta();
        if (paso === 2) cargarResumenPaso2Venta();
        if (paso === 3) cargarResumenPaso3Venta();
    }


    function pasoVentaSiguiente() {
        if (currentStepVenta < 3) {
            mostrarPasoVenta(currentStepVenta + 1);
        }
    }

    function pasoVentaAnterior() {
        if (currentStepVenta > 1) {
            mostrarPasoVenta(currentStepVenta - 1);
        }
    }

    // BÚSQUEDA 
    function inicializarSelectorVehiculos() {
        const vehiculoSelect = document.getElementById('vehiculoSelect');

        if (tomSelectVehiculo) {
            tomSelectVehiculo.destroy();
        }
        vehiculoSelect.innerHTML = '';

        tomSelectVehiculo = new TomSelect(vehiculoSelect, {
            valueField: 'idvehiculo',
            labelField: 'nombre',
            searchField: ['nombre', 'marca', 'modelo', 'color'],
            placeholder: 'Buscar por marca, modelo o color...',
            dropdownParent: 'body', // Sobresalga el select

            load: function(query, callback) {
                this.clearOptions();
                if (query.length < 2) { // Buscar al a partir de 2 caracteres
                    return callback();
                }
                fetch(`/api/vehiculo/searchVehiculo?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(json => {
                        callback(json.data || []);
                    }).catch(() => {
                        callback();
                    });
            },
            // Modificar como se vera el select:
            render: {
                loading: function() {
                    return `<div class="p-2 text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Buscando...</div>`;
                },
                no_results: function() {
                    return '<div class="p-2 text-muted">No se encontraron vehículos.</div>';
                },
                option: function(data, escape) {
                    const precio = parseFloat(data.precioventa).toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    });
                    return `<div class="d-flex justify-content-between p-2">
                            <div>
                                <div class="fw-bold">${escape(data.marca)} ${escape(data.tipovehiculo)} ${escape(data.modelo)}</div>
                                <div class="text-muted small">Versión: ${escape(data.version)} / ${escape(data.combustible)} / Año: ${escape(data.anio)} / Color: ${escape(data.color)} / Condición: ${escape(data.condicion.charAt(0).toUpperCase() + data.condicion.slice(1))}</div>
                            </div>
                            <div class="fw-bold text-success">${escape(precio)}</div>
                        </div>`;
                },
                item: function(item, escape) {
                    return `<div>${escape(item.nombre)}</div>`;
                }
            },
            // Manejar evento, cuando seleccione un vehiculo:
            onChange: function(value) {
                const vehiculoData = value ? this.options[value] : null;
                mostrarResumenVehiculo(vehiculoData);
                if (!value) {
                    this.clearOptions();
                }
            }
        });
    }

    // Poder buscar DNI apretando la tecla ENTER
    document.getElementById('dniBusqueda').addEventListener("keyup", (e) => {
        if (e.keyCode === 13) {
            e.preventDefault();
            buscarCliente();
        }
    });


    async function buscarCliente() {
        const dni = document.getElementById('dniBusqueda').value;
        const resultado = document.getElementById('clienteResultado');
        const btnBuscar = document.getElementById('btnBuscarCliente');

        if (dni.length !== 8) {
            resultado.innerHTML = '<div class="alert alert-warning mt-3">Ingrese un DNI de 8 dígitos.</div>';
            return;
        }

        btnBuscar.disabled = true;
        btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';

        try {
            const req = await fetch(`/api/clientes/${dni}`);
            const res = await req.json();

            if (res && res.idcliente) {
                //  Si se encontró en la DB:
                clienteSeleccionado = res;
                resultado.innerHTML = `
                <div class="card card-custom card-success mt-3 fade-in">
                    <div class="card-body"><div class="d-flex align-items-start gap-3">
                        <div class="icon-circle icon-success flex-shrink-0"><i class="bi bi-person-check-fill"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">${clienteSeleccionado.cliente}</h6>
                            <small class="text-muted">DNI: ${clienteSeleccionado.nrodoc}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCliente()">Cambiar</button>
                    </div></div>
                </div>`;
                document.getElementById('dniBusqueda').disabled = true;
                btnBuscar.innerHTML = '<i class="bi bi-search me-2"></i>Buscar';

            } else {
                // Si no se encontró en la DB, buscar por API:
                resultado.innerHTML = `<div class="alert alert-info mt-3">Cliente no registrado. Buscando en API externa...</div>`;

                const reqExterna = await fetch(`/persona/searchByDNIApi?dni=${dni}`);
                const resExterna = await reqExterna.json();

                if (resExterna.success) {
                    abrirModalCliente(resExterna);
                    resultado.innerHTML = `<div class="alert alert-success mt-3">¡Datos encontrados! Por favor, complete el registro.</div>`;
                } else {
                    resultado.innerHTML = `<div class="alert alert-danger mt-3 fade-in"><i class="bi bi-exclamation-triangle-fill me-2"></i>DNI no encontrado.</div><button class="btn btn-success w-100 mt-2" onclick="abrirModalCliente()"><i class="bi bi-person-plus-fill me-2"></i>Registrar Manualmente</button>`;
                }
            }
        } catch (error) {
            console.error("Error en la búsqueda:", error);
            resultado.innerHTML = `<div class="alert alert-danger mt-3">Error al conectar con el servidor.</div>`;
        } finally {
            if (!document.getElementById('dniBusqueda').disabled) {
                btnBuscar.disabled = false;
                btnBuscar.innerHTML = '<i class="bi bi-search me-2"></i>Buscar';
            }
        }
        validarPaso1Venta();
    }

    function cambiarCliente() {
        clienteSeleccionado = null;
        document.getElementById('dniBusqueda').value = '';
        document.getElementById('dniBusqueda').disabled = false;
        document.getElementById('btnBuscarCliente').disabled = false;
        document.getElementById('clienteResultado').innerHTML = '';
        validarPaso1Venta();
    }



    function mostrarResumenVehiculo(vehiculo) {
        const resultado = document.getElementById('vehiculoResultado');
        // Si no hay vehículo (porque se limpió la selección), borramos la tarjeta
        if (!vehiculo) {
            vehiculoSeleccionado = null;
            resultado.innerHTML = '';
            validarPaso1Venta();
            return;
        }

        const precioVenta = parseFloat(vehiculo.precioventa);
        // Guardamos el vehículo seleccionado en la variable global
        vehiculoSeleccionado = {
            id: vehiculo.idvehiculo,
            marca: vehiculo.marca,
            modelo: vehiculo.modelo,
            anio: vehiculo.anio,
            color: vehiculo.color,
            placa: vehiculo.placa || 'N/A',
            precioUSD: precioVenta,
            tipovehiculo: vehiculo.tipovehiculo,
            combustible: vehiculo.combustible,
            version: vehiculo.version,
            condicion: vehiculo.condicion

        };
        // console.log(vehiculoSeleccionado)
        // Creamos y mostramos la tarjeta de resumen del vehículo
        resultado.innerHTML = `
        <div class="card vehicle-summary-card fade-in-up mt-3">
            <div class="card-body">
                <div class="row g-4">
                
                    <div class="col-6 col-md-3 detail-item"><i class="bi bi-bookmark-star-fill detail-icon"></i><small>Marca</small><strong>${vehiculo.marca}</strong></div>
                    <div class="col-6 col-md-3 detail-item"><i class="bi bi-bookmark-star-fill detail-icon"></i><small>Marca</small><strong>${vehiculo.tipovehiculo}</strong></div>
                    <div class="col-6 col-md-3 detail-item"><i class="bi bi-car-front-fill detail-icon"></i><small>Modelo</small><strong>${vehiculo.modelo}</strong></div>
                    <div class="col-6 col-md-3 detail-item"><i class="bi bi-ev-front detail-icon"></i><small>Versión</small><strong>${vehiculo.version}</strong></div>
                    <div class="col-6 col-md-3 detail-item"><i class="bi bi-fuel-pump detail-icon"></i></i><small>Combustible</small><strong>${vehiculo.combustible}</strong></div>
                    <div class="col-6 col-md-3 detail-item"><i class="bi bi-palette-fill detail-icon"></i><small>Color</small><strong>${vehiculo.color}</strong></div>
                    <div class="col-6 col-md-3 detail-item"><i class="bi bi-check-circle-fill detail-icon"></i></i><small>Condición</small><strong>${vehiculo.condicion.charAt(0).toUpperCase() + vehiculo.condicion.slice(1)}</strong></div>
                    <div class="col-6 col-md-3 detail-item"><i class="bi bi-calendar3 detail-icon"></i><small>Año</small><strong>${vehiculo.anio}</strong></div>
                </div>
                <div class="price-section">
                    <div class="price-tag">
                        <i class="bi bi-tag-fill"></i>
                        <span> Precio: <strong>$${precioVenta.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} USD</strong></span>
                    </div>
                </div>
            </div>
        </div>`;

        // Validamos si ya se puede avanzar al siguiente paso
        validarPaso1Venta();
    }




    function validarPaso1Venta() {
        document.getElementById('btnVentaSiguiente').disabled = !(clienteSeleccionado && vehiculoSeleccionado);
    }


    // ==========  MONEDA ==========
    function cargarResumenPaso2Venta() {
        if (clienteSeleccionado) {
            document.getElementById('resumenCliente').textContent = clienteSeleccionado.cliente;
            document.getElementById('resumenDNI').textContent = `DNI: ${clienteSeleccionado.nrodoc}`;
        }
        if (vehiculoSeleccionado) {
            document.getElementById('resumenVehiculo').textContent = `${vehiculoSeleccionado.marca} ${vehiculoSeleccionado.modelo}`;
            // document.getElementById('resumenPlaca').textContent = `Placa: ${vehiculoSeleccionado.placa}`;
            document.getElementById('resumenPrecioVehiculo').innerHTML = `<div class="vehicle-price-tag mt-2"><i class="bi bi-tag-fill me-1"></i>Precio Base: $${vehiculoSeleccionado.precioUSD.toLocaleString()} USD</div>`;
        }
        calcularPrecioFinal();
    }

    function seleccionarMoneda(moneda) {
        monedaSeleccionada = moneda;
        document.getElementById('radioPEN').classList.toggle('selected', moneda === 'PEN');
        document.getElementById('radioUSD').classList.toggle('selected', moneda === 'USD');
        document.getElementById('tipoCambio').disabled = (moneda === 'USD' || moneda === 'PEN');
        calcularPrecioFinal();
    }

    function calcularPrecioFinal() {
        if (!vehiculoSeleccionado) return 0;
        const tipoCambio = parseFloat(document.getElementById('tipoCambio').value);
        const precioUSD = vehiculoSeleccionado.precioUSD;
        let precioFinal, simbolo;
        if (monedaSeleccionada === 'PEN') {
            precioFinal = precioUSD * tipoCambio;
            simbolo = 'S/';
        } else {
            precioFinal = precioUSD;
            simbolo = '$';
        }
        document.getElementById('precioFinal').textContent = `${simbolo} ${precioFinal.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        return precioFinal;
    }

    async function cargarCuentasBancarias() {
        const selectCuentaPago = document.getElementById('cuentaDestino');

        selectCuentaPago.innerHTML = '<option value="">Seleccione cuenta bancaria</option>';

        try {
            const res = await fetch('/api/numcuentaspagos');
            const data = await res.json();
            if (data.length > 0) {
                data.forEach(cuenta => {
                    selectCuentaPago.innerHTML += `<option value="${cuenta.idcuentapago}">${cuenta.nombrecuenta}</option>`;
                });
            }
        } catch (error) {
            console.error('Error al cargar cuentas:', error);
        }
    }



    function cargarResumenPaso3Venta() {
        if (clienteSeleccionado) {

            document.getElementById('resumenFinalCliente').textContent = clienteSeleccionado.cliente;
            document.getElementById('resumenFinalDNI').textContent = `DNI: ${clienteSeleccionado.nrodoc}`;
        }
        if (vehiculoSeleccionado) {
            document.getElementById('resumenFinalVehiculo').textContent = `${vehiculoSeleccionado.marca} ${vehiculoSeleccionado.tipovehiculo} ${vehiculoSeleccionado.modelo} ${vehiculoSeleccionado.version} ${vehiculoSeleccionado.combustible} ${vehiculoSeleccionado.anio} ${vehiculoSeleccionado.color} ${vehiculoSeleccionado.condicion}`;

        }
        const precioFinal = calcularPrecioFinal();
        const simbolo = monedaSeleccionada === 'PEN' ? 'S/' : '$';
        document.getElementById('resumenFinalMonto').textContent = `${simbolo} ${precioFinal.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('montoPagado').value = precioFinal.toFixed(2);
    }

    // Verificar medio de pagos:
    const numeroTransaccion = document.getElementById('numeroTransaccion');
    const cuentaDestino = document.getElementById('cuentaDestino');
    const comprobante = document.getElementById('comprobante');
    const labelComprobante = document.querySelector('label[for="comprobante"]');
    const nombreArchivo = document.getElementById('nombre-archivo');

    document.getElementById('medioPago').addEventListener('change', (e) => {
        const metodo = e.target.value;


        numeroTransaccion.disabled = false;
        cuentaDestino.disabled = false;
        comprobante.disabled = false;
        labelComprobante.classList.remove('disabled-label');
        nombreArchivo.classList.remove('disabled-label');

        switch (metodo) {
            case 'Efectivo':
                numeroTransaccion.disabled = true;
                cuentaDestino.disabled = true;
                comprobante.disabled = true;
                labelComprobante.classList.add('disabled-label');
                nombreArchivo.classList.add('disabled-label');
                break;

            case 'Yape':
            case 'Plin':
                cuentaDestino.disabled = true;
                break;

            case 'Transferencia Bancaria':
                // Todo habilitado, no se necesita hacer nada
                break;
        }
    });




    document.getElementById('btnVentaRegistrar').addEventListener('click', async () => {
        if (await ask('¿Registrar Venta?', 'Confirmar')) {
            registrarVenta();
        }
    });




    async function registrarVenta() {
        //  Deshabilitar botón para evitar múltiples envíos
        const registrarBtn = document.getElementById('btnVentaRegistrar');
        registrarBtn.disabled = true;
        registrarBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registrando...';


        if (!clienteSeleccionado || !vehiculoSeleccionado) {
            Swal.fire('Error', 'Falta información del cliente o del vehículo.', 'error');
            registrarBtn.disabled = false;
            registrarBtn.innerHTML = '<i class="bi bi-save me-2"></i>Registrar Venta';
            return;
        }
        const medioPago = document.getElementById('medioPago').value;
        if (!medioPago) {
            Swal.fire('Atención', 'Por favor, seleccione un medio de pago.', 'warning');
            registrarBtn.disabled = false;
            registrarBtn.innerHTML = '<i class="bi bi-save me-2"></i>Registrar Venta';
            return;
        }
        const formData = new FormData();
        formData.append('idcliente', clienteSeleccionado.idcliente);
        formData.append('idvehiculo', vehiculoSeleccionado.id);
        // Datos del formulario de pago
        formData.append('fechapago', document.getElementById('fechaPago').value);
        formData.append('mediopago', medioPago);
        formData.append('idcuentapago', document.getElementById('cuentaDestino').value);
        formData.append('numerotransaccion', document.getElementById('numeroTransaccion').value);
        formData.append('observacion', document.getElementById('observaciones').value);
        const tipoCambioActual = parseFloat(document.getElementById('tipoCambio').value);
        const precioBaseUSD = vehiculoSeleccionado.precioUSD;

        // Obtenemos el monto final que el usuario está viendo y va a pagar.
        const montoFinalPagado = parseFloat(document.getElementById('montoPagado').value);

        formData.append('moneda', monedaSeleccionada);
        formData.append('tipocambioaplicado', tipoCambioActual);
        formData.append('montomonedaoriginal', precioBaseUSD.toFixed(2));
        // Enviamos el monto exacto que el usuario pagó en la moneda seleccionada.
        formData.append('amortizacion', montoFinalPagado.toFixed(2));
        // Archivo de comprobante (si existe)
        const comprobanteFile = document.getElementById('comprobante').files[0];
        if (comprobanteFile) {
            formData.append('comprobante', comprobanteFile);
        }

        // console.log('FORMDATA: ', formData);
        try {
            const response = await fetch('/vehiculo/store/PagoAlContado', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Ocurrió un error en el servidor.');
            }

            // Cerrar el modal principal después del éxito
            const ventaModalInstance = bootstrap.Modal.getInstance(document.getElementById('ventaModal'));
            ventaModalInstance.hide();
            table.setData('vehiculosVendidosAlContado'); // Decirle a tabulator que llame la api
            showToast(data.message, 'SUCCESS', 1300);

            // Otra forma seria que cuando se agrega, el backend me devuelva los nuevos datos:
            // El 'true' al final indica que se agregue al inicio de la tabla.
            // table.addData([data.nuevaFila], true); 

        } catch (error) {

            showToast(error.message, 'ERROR', 1400);

        } finally {
            registrarBtn.disabled = false;
            registrarBtn.innerHTML = '<i class="bi bi-save me-2"></i>Registrar Venta';
        }
    }

    function abrirModalCliente(datosApi = null) {
        // SI NO vienen datos de la API, primero resetea el formulario.
        if (!datosApi) {
            resetearFormularioCliente();
        }

        //  Obtenemos el DNI del campo de búsqueda y lo ponemos en el formulario del modal
        const dniBuscado = document.getElementById('dniBusqueda').value;
        document.getElementById('clienteNroDoc').value = dniBuscado;

        //  SI SÍ vienen datos válidos de la API, llenamos los campos correspondientes
        if (datosApi && datosApi.success) {
            document.getElementById('clienteApellidos').value = datosApi.apellidos;
            document.getElementById('clienteNombres').value = datosApi.nombres;
        }

        // mostramos el modal
        const clienteModal = new bootstrap.Modal(document.getElementById('clienteModal'));
        clienteModal.show();
    }

    function resetearFormularioVenta() {
        currentStepVenta = 1;
        clienteSeleccionado = null;
        vehiculoSeleccionado = null;

        // --- PASO 1 ---
        document.getElementById('dniBusqueda').value = '';
        document.getElementById('dniBusqueda').disabled = false;
        document.getElementById('btnBuscarCliente').disabled = false;
        document.getElementById('clienteResultado').innerHTML = '';
        document.getElementById('vehiculoResultado').innerHTML = '';

        // Limpiar el select de TomSelect si ya fue inicializado
        if (tomSelectVehiculo) {
            tomSelectVehiculo.clear();
        }

        // --- PASO 3 (DATOS DE PAGO) ---
        document.getElementById('fechaPago').valueAsDate = new Date(); // AÑADIDO: Resetea la fecha al día actual
        document.getElementById('montoPagado').value = ''; // AÑADIDO: Limpia el monto
        document.getElementById('medioPago').value = ''; // AÑADIDO: Resetea el medio de pago
        document.getElementById('cuentaDestino').value = ''; // AÑADIDO: Resetea la cuenta de destino
        document.getElementById('numeroTransaccion').value = '';
        document.getElementById('observaciones').value = ''; // AÑADIDO: Limpia el campo de observaciones

        // AÑADIDO: Lógica correcta para resetear el input de archivo
        const inputComprobante = document.getElementById('comprobante');
        const nombreArchivoSpan = document.getElementById('nombre-archivo');
        inputComprobante.value = ''; // Esto es crucial para limpiar el archivo
        nombreArchivoSpan.textContent = 'Ningún archivo seleccionado...';

        // Habilita todos los campos que pudieron ser deshabilitados por la lógica del medio de pago
        document.getElementById('numeroTransaccion').disabled = false;
        document.getElementById('cuentaDestino').disabled = false;
        document.getElementById('comprobante').disabled = false;
        document.querySelector('label[for="comprobante"]').classList.remove('disabled-label');
        document.getElementById('nombre-archivo').classList.remove('disabled-label');


        // --- LÓGICA GENERAL DEL MODAL ---
        cargarCuentasBancarias();
        getTipoCambio();
        seleccionarMoneda('USD'); // Vuelve a seleccionar Dólares por defecto
        mostrarPasoVenta(1); // Muestra el primer paso
    }


    function resetearFormularioCliente() {

        document.getElementById('clienteApellidos').value = '';
        document.getElementById('clienteNombres').value = '';
        document.getElementById('clienteTipoDoc').value = 'DNI';
        document.getElementById('clienteNroDoc').value = '';
        document.getElementById('generoM').checked = true;
        document.getElementById('departamento').value = '';

        const provinciaSelect = document.getElementById('provincia');
        provinciaSelect.innerHTML = '<option value="">Seleccione</option>';

        const distritoSelect = document.getElementById('distrito');
        distritoSelect.innerHTML = '<option value="">Seleccione</option>';
        // Limpiar campos de contacto y dirección
        document.getElementById('clienteDireccion').value = '';
        document.getElementById('clienteTelPrimario').value = '';
        document.getElementById('clienteTelAlternativo').value = '';
    }

    document.getElementById('btnGuardarCliente').addEventListener('click', async () => {
        if (await ask('¿Registar cliente?', 'Confirmar')) {
            guardarCliente();
        }
    });

    async function guardarCliente() {

        const guardarBtn = document.querySelector('#btnGuardarCliente');
        guardarBtn.disabled = true;
        guardarBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

        const formData = new FormData();
        formData.append('apellidos', document.getElementById('clienteApellidos').value.trim());
        formData.append('nombres', document.getElementById('clienteNombres').value.trim());
        formData.append('tipodocumento', document.getElementById('clienteTipoDoc').value);
        formData.append('nrodoc', document.getElementById('clienteNroDoc').value.trim());
        formData.append('genero', document.querySelector('input[name="clienteGenero"]:checked').value);
        formData.append('distrito', document.getElementById('distrito').value);
        formData.append('direccion', document.getElementById('clienteDireccion').value.trim());
        formData.append('telprimario', document.getElementById('clienteTelPrimario').value.trim());
        formData.append('telalternativo', document.getElementById('clienteTelAlternativo').value.trim());

        try {

            const response = await fetch('/storepersonclient/store', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Ocurrió un error en el servidor.');
            }

            clienteSeleccionado = data.cliente; // Guardar el objeto del nuevo cliente devuelto por el backend

            // Actualizar la UI del modal de Venta para mostrar al cliente recién creado
            document.getElementById('dniBusqueda').value = clienteSeleccionado.nrodoc;
            document.getElementById('dniBusqueda').disabled = true;
            document.getElementById('btnBuscarCliente').disabled = true;

            const resultadoVenta = document.getElementById('clienteResultado');
            resultadoVenta.innerHTML = `
            <div class="card card-custom card-success mt-3 fade-in">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-circle icon-success flex-shrink-0"><i class="bi bi-person-check-fill"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">${clienteSeleccionado.cliente}</h6>
                            <small class="text-muted">DNI: ${clienteSeleccionado.nrodoc}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCliente()">Cambiar</button>
                    </div>
                </div>
            </div>`;

            validarPaso1Venta();
            // Cerrar el modal de cliente y preparar la alerta de éxito
            fueClienteGuardado = true;
            const clienteModalInstance = bootstrap.Modal.getInstance(document.getElementById('clienteModal'));
            clienteModalInstance.hide();
            resetearFormularioCliente();

        } catch (error) {
            showToast(error.message, 'ERROR', 1400);


        } finally {
            guardarBtn.disabled = false;
            guardarBtn.innerHTML = '<i class="bi bi-save me-2"></i>Registrar Cliente';
        }
    }

    async function getDataActaEntrega(idvehiculo) {
        try {
            const req = await fetch(`/api/getDataVehiculo/${idvehiculo}`);
            const res = await req.json();
            if (res.data && res.success) {
                generarActaEntrega(res.data);
                return res;
            } else {
                return null;
            }

        } catch (error) {
            console.log(error);
            return null;
        }
    }

    function generarActaEntrega(data) {
        if (!data) {
            console.error("No se recibieron datos para generar el acta.");
            showToast('Error: No se encontraron datos del vehículo.', 'ERROR', 1500);
            return;
        }

        const nombreEmpresa = window.nombreEmpresa;
        const rucEmpresa = window.rucEmpresa;
        const representanteEmpresa = window.representanteEmpresa;
        const direccionEmpresa = 'Carretera Panamericana km 201 – Chincha';
        const telefonoEmpresa = '927676338 / 971027612';
        const imgCabecera = window.cabeceraYonda;
        const footer = window.footerYonda;

        const fechaObj = new Date();
        const dia = fechaObj.getDate();
        const anio = fechaObj.getFullYear();
        const mes = fechaObj.toLocaleString('es-ES', {
            month: 'long'
        });
        const fechaFormateada = `Chincha, ${dia} de ${mes} del ${anio}`;


        const docDefinition = {
            pageSize: 'A4',
            pageOrientation: 'portrait',
            pageMargins: [70, 25, 70, 25],
            defaultStyle: {
            fontSize: 8.3,
            lineHeight: 1.15,
            color: '#333333'
            
        },
        
            header: {
                image: imgCabecera,
                width: 610,
                alignment: 'center',
                margin: [0, 25, 0, 0]
            },

            styles: {

                subheader: {
                    bold: true,
                    margin: [0, 10, 0, 3] // izqquierda, arriba, derecha, abajo
                },
                bodyText: {

                    alignment: 'justify',
                    lineHeight: 1.3
                },
                firma: {

                    alignment: 'center',
                    margin: [0, 2, 0, 0]
                },
                datosEmpresa: {

                    bold: true,
                    margin: [0, 0, 0, 2]
                },
                infoContacto: {
                    margin: [0, 0, 0, 2]
                },
            },

            // Contenido del documento
            content: [

                // {alignment: 'left',
                //     image: imgCabecera, 
                //     width: 610,
                //     margin: [-85, 0, 0, 3] 
                // },
                {
                    stack: [{
                            text: fechaFormateada,
                            alignment: 'right',
                            margin: [0, 70, 0, 0]
                        },

                        {
                            text: 'ACTA DE ENTREGA DE VEHÍCULO',
                            alignment: 'center',
                            fontSize: 11,
                            bold: true,
                            margin: [0, 10, 0, 0],
                            decoration: 'underline'
                        }
                    ],

                },

                // DATOS DE LA EMPRESA - stack -> Crea texto pero uno debajo del otro en el mismo bloque
                {
                    stack: [{
                            text: nombreEmpresa,
                            style: 'datosEmpresa'
                        },
                        {
                            text: `RUC: ${rucEmpresa}`,
                            style: 'infoContacto'
                        },
                        {
                            text: `Dirección: ${direccionEmpresa}`,
                            style: 'infoContacto'
                        },
                        {
                            text: `Teléfonos: ${telefonoEmpresa}`,
                            style: 'infoContacto'
                        },
                        {
                            text: `Representante Legal: ${representanteEmpresa}`,
                            style: 'infoContacto'
                        }
                    ],
                    margin: [0, 15, 0, 15] // Margen arriba y abajo
                },

                //  PÁRRAFO DE ENTREGA 
                {
                    text: [
                        'Conste por la presente que a la fecha, la empresa antes mencionada hace entrega formal del vehículo que se detalla a continuación a la Sra. ',
                        {
                            text: data.cliente || 'CLIENTE NO ESPECIFICADO',
                            bold: true
                        },
                        ', identificada con DNI N.º ',
                        {
                            text: data.nrodoc || 'XXXXXXXX',
                            bold: true
                        },
                        ', domiciliada en ',
                        {
                            text: data.ubicacion || 'dirección no especificada',
                            bold: true
                        },
                        ', con número de celular ',
                        {
                            text: data.telprimario || '999999999',
                            bold: true
                        },
                        ', a quien en adelante se le denominará "la compradora".'
                    ],
                    style: 'bodyText'
                },


                {
                    margin: [50, 10, 0, 10],
                    table: {

                        widths: ['auto', 250],
                        body: [
                            [{
                                text: 'DETALLE DEL VEHÍCULO ENTREGADO',
                                alignment: 'left',
                                colSpan: 2,
                                bold: true
                            }, {}],
                            [{
                                text: 'Marca',
                                bold: true
                            }, data.marca || ''],
                            [{
                                text: 'Modelo',
                                bold: true
                            }, data.modelo || ''],
                            [{
                                text: 'Número de Chasis',
                                bold: true
                            }, data.chasis || ''],
                            [{
                                text: 'Número de Motor',
                                bold: true
                            }, data.seriemotor || ''],
                            [{
                                text: 'Año de fabricación',
                                bold: true
                            }, data.anio || ''],
                            [{
                                text: 'Color',
                                bold: true
                            }, data.color || ''],
                            [{
                                text: 'Año',
                                bold: true
                            }, anio.toString()]
                        ]
                    }
                },

                // CONDICIÓN DE ENTREGA 
                {
                    text: 'CONDICIÓN DE ENTREGA:',
                    style: 'subheader'
                },
                {
                    text: 'El vehículo es entregado en perfectas condiciones mecánicas, estéticas y operativas, con todos sus accesorios completos y funcionando, no teniendo la compradora nada que reclamar a posterioridad por concepto de estado físico o funcionamiento del mismo.',
                    style: 'bodyText'
                },

                // ACCESORIOS ENTREGADOS 
                {
                    text: 'ACCESORIOS ENTREGADOS:',
                    style: 'subheader'
                },
                {
                    ul: [
                        '1 llave de contacto',
                        'Juego de pisos',
                        'Manual de garantía y manual del usuario',
                        'Llanta de repuesto y llave de rueda',
                        'Pisos delanteros y posteriores',
                        {
                            text: 'Placa vehicular: EN TRÁMITE',
                            bold: true
                        }
                    ],
                    style: 'bodyText',
                    margin: [10, 0, 0, 0],

                },

                //  NOTA 
                {
                    text: 'NOTA:',
                    style: 'subheader'
                },
                {
                    text: 'La entrega de la tarjeta de propiedad y la placa vehicular definitiva se realizará en un plazo estimado de 25 a 30 días hábiles, contados a partir de la presente fecha.',
                    style: 'bodyText'
                },

                //  PÁRRAFO DE CONFORMIDAD
                {
                    text: 'Ambas partes manifiestan su total conformidad con los términos descritos en el presente documento, firmando en señal de aceptación y recepción de lo indicado.',
                    style: 'bodyText',
                    margin: [0, 20, 0, 0]
                },

                // FIRMAS 
                {
                    columns: [{
                            stack: [{
                                    text: '________________________________________',
                                    style: 'firma',
                                    margin: [0, 20, 0, 0]
                                },
                                {
                                    text: nombreEmpresa,
                                    style: 'firma',
                                    bold: true
                                },
                                {
                                    text: `RUC: ${rucEmpresa}`,
                                    style: 'firma'
                                }
                            ],
                            width: '*'
                        },
                        {
                            stack: [{
                                    text: '________________________________________',
                                    style: 'firma',
                                    margin: [0, 20, 0, 0]
                                },
                                {
                                    text: data.cliente || 'COMPRADOR',
                                    style: 'firma',
                                    bold: true
                                },
                                {
                                    text: `DNI: ${data.nrodoc || 'XXXXXXXX'}`,
                                    style: 'firma'
                                }
                            ],
                            width: '*'
                        }
                    ],
                    columnGap: 20
                },
                {
                    stack: [{
                            text: '_______________________________________',
                            style: 'firma',
                            margin: [0, 30, 0, 0]
                        },
                        {
                            text: 'LIZ MARTINEZ',
                            style: 'firma',
                            bold: true
                        },
                        {
                            text: 'Ejecutivo de ventas',
                            style: 'firma',
                            bold: true
                        }
                    ]
                },
            ],
            footer: function() {
                return {
                    columns: [{
                        image: footer,
                        width: 600,
                        alignment: 'center',
                    }]
                };
            }

        };
        pdfMake.createPdf(docDefinition).open();
    }


    document.getElementById('tabla-vehiculos').addEventListener('click', (e) => {
        const button = e.target.closest("button[data-action]");
        if (!button) return;

        const action = button.dataset.action;
        const id = button.dataset.id;

        switch (action) {
            case 'verPDF':
                getDataActaEntrega(id);
                break;
        }




    });

</script>