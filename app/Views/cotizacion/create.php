<?php include __DIR__ . '/../layout/header.php'; ?>
<style>
    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }

    .modal-header {
        background-color: #fd9628ff;
        color: #fff;
        border-bottom: 2px solid #db8534ff;
        padding: 1rem 2rem;
        position: relative;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }

    .modal-title {
        font-weight: 600;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-header .btn-close {
        filter: invert(1);
        opacity: 0.7;
        transition: opacity 0.2s ease-in-out;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 1.5rem;
        color: #333;
    }

    .table {
        font-size: 0.85rem;
        --bs-table-hover-bg: #eef2f5;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table tbody tr td {
        vertical-align: middle;
        padding: 0.6rem 0.8rem;
        border-top: 1px solid #e0e0e0;
        line-height: 1.2;
    }

    .table tbody tr:first-child td {
        border-top: none;
    }

    /* Alineación de columnas en la tabla de vehículos */
    #tablaVehiculosModal thead th:nth-child(1),
    #tablaVehiculosModal tbody td:nth-child(1),
    #tablaVehiculosModal thead th:last-child,
    #tablaVehiculosModal tbody td:last-child {
        text-align: center;
    }

    .seleccionar-vehiculo-btn {
        border-radius: 0.25rem;
        font-size: 0.75rem;
        padding: 0.4rem 0.7rem;
        font-weight: 500;
        transition: all 0.3s ease;
        background-color: #007bff;
        border: none;
        color: #fff;
    }

    .seleccionar-vehiculo-btn:hover {
        background-color: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #modalCronograma .modal-header {
        background-color: #27ae60;
        border-bottom: 2px solid #229954;
    }

    #tablaCronograma tfoot tr td {
        background-color: #e8eaf6;
        color: #2c3e50;
        font-weight: bold;
        font-size: 1rem;
        padding: 0.8rem;
        border-top: 2px solid #4a698c;
    }

    .bg-info {
        background-color: #81dfe6ff !important;
        color: #0a5461ff !important;
    }

    .seleccionar-vehiculo-radio {
        -webkit-appearance: radio;
        appearance: radio;
        width: 1rem;
        height: 1rem;
        transform: none;
        margin-top: 0;
        vertical-align: middle;
        accent-color: var(--bs-primary);
    }

    /* Ajustes del contenedor para que el texto quede alineado al lado */
    .form-check.d-inline-flex {
        gap: 0.45rem;
        align-items: center;
    }

    .form-check-label {
        cursor: pointer;
        user-select: none;
        margin-bottom: 0;
    }

    /* Opción cuando algún padre reduce mucho el font-size: forzar tamaño normal */
    .seleccionar-vehiculo-radio.force-normal {
        width: 1rem !important;
        height: 1rem !important;
    }

    .seleccionar-vehiculo-radio:checked {
        border-color: var(--bs-primary);
        background-color: var(--bs-primary) !important;
    }

    #modalVehiculos .form-check-input,
    #modalVehiculos .seleccionar-vehiculo-radio {
        filter: none !important;
        opacity: 1 !important;
    }

    /* Compactar filas SOLO en el modal Vehículos */
    #modalVehiculos .modal-body {
        padding: 0.6rem;
        /* menos padding alrededor del contenido */
    }

    /* quitar padding extra del wrapper responsive dentro del modal (si aplica) */
    #modalVehiculos .table-responsive {
        padding: 0;
        margin: 0;
    }

    /* Forzar tamaño de fuente algo menor en la tabla de vehículos */
    #tablaVehiculosModal {
        font-size: 0.90rem;
        /* ajustar según prefieras */
    }

    /* Reducir paddings de celdas para hacer filas más delgadas */
    #tablaVehiculosModal thead th,
    #tablaVehiculosModal tbody td {
        padding: 0.30rem 0.45rem;
        /* vertical horizontal */
        line-height: 1;
        /* evita alturas extra por line-height */
        white-space: nowrap;
        /* evita wraps que aumenten la altura */
        vertical-align: middle;
    }

    /* Si DataTables usa scrollBody, forzar a que no añada padding extra */
    .dataTables_scrollBody table#tablaVehiculosModal tbody td {
        padding: 0.28rem 0.45rem !important;
    }

    /* Ajustes para los radios / labels en la columna de acción */
    #modalVehiculos .form-check.d-inline-flex {
        gap: 0.35rem;
        align-items: center;
    }

    #modalVehiculos .form-check-input,
    #modalVehiculos .seleccionar-vehiculo-radio {
        width: 0.95rem;
        height: 0.95rem;
        margin-top: 0;
    }

    /* Etiqueta junto al radio un poco más pequeña */
    #modalVehiculos .form-check-label {
        font-size: 0.82rem;
        margin-bottom: 0;
        line-height: 1;
    }
</style>
<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizaciones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Registrar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizacion" class="btn-sm btn btn-primary">Mostrar Lista</a>
            </div>
        </div>
    </div>

    <div class="mb-2">
        <form id="formCotizacion" action="/cotizaciones" method="POST">

            <input type="hidden" id="idcliente" name="idcliente" value="">
            <input type="hidden" id="idvehiculo" name="idvehiculo" value="">

            <!-- Información del Cliente -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Paso 1:</strong> <span class="fst-italic">Información del cliente</span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <!-- Selector + Input + Botón interno -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select class="form-select" id="tipoDocumento">
                                    <option value="dni">DNI</option>
                                    <option value="ruc">RUC</option>
                                </select>
                                <label for="tipoDocumento">Tipo Documento <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="input-group">
                                <div class="form-floating">
                                    <input type="text" autocomplete="off" class="form-control" id="documento"
                                        name="documento" placeholder="DNI / RUC">
                                    <label for="documento">DNI / RUC <span class="text-danger">*</span></label>
                                </div>
                                <button type="button" id="btnBuscarCliente" class="btn btn-outline-success"
                                    title="Buscar cliente en la DB"><i class="bi bi-search"></i></button>
                            </div>
                        </div>


                        <!-- Apellidos y Nombres -->
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" autocomplete="off" class="form-control"
                                    placeholder="Apellidos y Nombres / Razón Social" id="nombres" name="nombres">
                                <label for="nombres">Apellidos y Nombres / Razón Social <span
                                        class="text-danger">*</span></label>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="direccion" name="direccion"
                                        placeholder="Dirección">
                                    <label for="documento">Dirección</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-1 g-2">
                        <!-- Teléfono -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" placeholder="Teléfono" id="telprimario"
                                    name="telprimario" autocomplete="off" maxlength="9" required>
                                <label for="telprimario">Teléfono <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <!-- Telefono alternativo -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" placeholder="Teléfono Alternativo"
                                    id="telalternativo" name="telalternativo" maxlength="9">
                                <label for="telalternativo">Teléfono Alternativo</label>
                            </div>
                        </div>
                        <!-- modalidad -->
                        <div class="col-md-6 mb-2">
                            <div class="form-floating">
                                <select name="modalidad" id="modalidad" class="form-select" required>
                                    <option value="">Seleccione modalidad</option>
                                    <?php foreach ($formatos as $f): ?>
                                        <option value="<?= htmlspecialchars($f['idformato']) ?>">
                                            <?= htmlspecialchars($f['tipocotizacion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="modalidad">Modalidad <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <!-- botón tamaño igual que modalidad -->
                        <div class="col-md-2 mb-2">
                            <div class="form-floating h-100 btn-ver-requisitos">
                                <button type="button" class="btn btn-outline-primary w-100 h-100" data-bs-toggle="modal"
                                    data-bs-target="#modalRequisitos" id="btnVerRequisitos">
                                    Lista Requisitos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selección de Vehículo -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Paso 2:</strong> <span class="fst-italic">
                        Selección de vehículo
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2">

                        <!-- VEHICULOS -->
                        <div class="col-md-8">
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalVehiculos">
                                    Lista Vehículos
                                </button>
                                <div class="form-floating">
                                    <input type="text" autocomplete="off" class="form-control"
                                        placeholder="Descripcion del vehiculo" id="descripcion" name="descripcion">
                                    <label for="descripcion">Descripcion del vehiculo <span
                                            class="text-danger">*</span></label>
                                </div>

                            </div>
                        </div>
                        <!-- PLACA -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" autocomplete="off" placeholder="Placa" class="form-control"
                                    id="placa" name="placa">
                                <label for="placa">Placa</label>
                            </div>
                        </div>
                        <!-- PLACA ROTATIVA -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" autocomplete="off" placeholder="Placa Rotativa" class="form-control"
                                    id="placarotativa" name="placarotativa">
                                <label for="placarotativa">Placa Rotativa</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mt-1">

                        <!-- Tipo de moneda -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="monedaprecio" readonly>
                                <label for="monedaprecio">Moneda Vta. <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <input type="hidden" name="moneda" id="inputMoneda">

                        <!-- Valor -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" placeholder="Valor" class="form-control" id="valor" name="valor"
                                    readonly>
                                <label for="valor">Valor <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <input type="hidden" name="precioventa" id="inputPrecioventa">

                        <!-- Moneda -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <select id="monedaSelect" class="form-select" required>
                                    <option value="PEN" selected>Soles</option>
                                    <option value="USD">Dólares</option>
                                </select>
                                <label for="monedaSelect">Moneda <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <!-- Tipo de cambio -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="number" step="0.0001" class="form-control" id="tipoCambio"
                                    name="tipoCambio" placeholder="Ej: 3.80" readonly>
                                <label for="tipoCambio">Tipo de Cambio <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <input type="hidden" name="tipoCambio" id="inputTipoCambio">

                        <!-- Valor en soles -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" placeholder="Valor de Moneda" class="form-control" id="valormoneda"
                                    name="valormoneda" readonly>
                                <label for="valormoneda">Valor de Moneda <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <input type="hidden" name="valorconvertido" id="inputValorConvertido">

                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" placeholder="Gastos Administrativos" class="form-control" id="gastosAdministrativos" 
                                    name="gastos_administrativos" readonly>
                                <label for="gastosAdministrativos">Gastos Administrativos <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <input type="hidden" name="gastos_administrativos" id="inputGastosAdministrativos">

                    </div>

                </div>
                <input type="hidden" id="vehiculoMoneda" value="">
            </div>

            <!-- Paso 3: Financiamiento -->
            <div class="card mb-4" id="card-paso3-financiamiento">
                <div class="card-header bg-info d-flex justify-content-between align-items-center">
                    <div><strong>Paso 3:</strong> <span class="fst-italic">Financiamiento</span></div>
                    <div>
                        <button type="button" id="btnAgregarFin" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-plus-lg"></i> Agregar
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div id="cardsFinanciamiento" class="mb-3"></div>
                    <input type="hidden" id="opciones_financiamiento" name="opciones_financiamiento" value="[]">
                    <p class="text-muted small mt-2">Cada tarjeta representa una opción de financiamiento distinta (24,
                        36, 48, 60 meses, etc.). </p>
                </div>
            </div>

            <!-- Template para cada tarjeta de financiamiento -->
            <template id="templateCardFin">
                <div class="card card-fin mb-3 border-primary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="fw-semibold fin-title">Meses</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary fin-remove-btn"
                                title="Eliminar opción">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 align-items-center">

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="number" class="form-control fin-inicial" placeholder="Inicial"
                                        step="0.01" min="0">
                                    <label>Inicial <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="text" class="form-control fin-valorFinanciar"
                                        placeholder="Valor a Financiar" readonly>
                                    <label>Valor a Financiar <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="number" class="form-control fin-numcuotas" placeholder="Meses"
                                        step="3">
                                    <label>Meses <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="number" class="form-control fin-tasaAnual" placeholder="Tasa anual"
                                        step="0.01" min="0" value="65">
                                    <label>Tasa anual (%)</label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="text" class="form-control fin-cuotaMensual" placeholder="Valor Mensual"
                                        readonly>
                                    <label>Valor Mensual <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <!-- Mantener tamaño original del botón Cronograma -->

                            <div class="col-md-2">
                                <div class="form-floating h-100">
                                    <button class="btn btn-outline-primary w-100 h-100 fin-btn-cronograma"
                                        type="button">Cronograma</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Botones de Acción -->
            <div class="card">
                <div class="card-footer">
                    <div class="row g-2 align-items-end">
                        <!-- Fecha de Emisión -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="fechaEmision" name="fechaEmision" required>
                                <label for="fechaEmision">Fecha de Emisión</label>
                            </div>
                        </div>

                        <!-- Fecha de Caducidad -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="fechaCaducidad" name="fechaCaducidad"
                                    required>
                                <label for="fechaCaducidad">Fecha de Caducidad</label>
                            </div>
                        </div>
                        <input type="hidden" name="vigenciadias" id="inputVigenciaDias" value="7">

                        <!-- Botones -->
                        <div class="col-md-8 text-end">
                            <button type="reset" id="btn-cancelar-registro"
                                class="btn btn-sm btn-outline-secondary">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm btnGuardarCotizacion">Registrar</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- MODAL DE LISTADO DE REQUISITOS -->
<div class="modal fade" id="modalRequisitos" tabindex="-1" aria-labelledby="modalRequisitosLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-yonda">
                <h5 class="modal-title" id="modalRequisitosLabel">Requisitos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <ul id="listaRequisitos" class="list-group">
                    <!-- se insertan datos dinámicamente -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL SELECCIONAR VEHÍCULO -->
<div class="modal fade" id="modalVehiculos" tabindex="-1" aria-labelledby="modalVehiculosLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="modalVehiculosLabel">Seleccionar Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered mt-2 display nowrap"
                        id="tablaVehiculosModal" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Marca</th>
                                <th>Tipo</th>
                                <th>Modelo</th>
                                <th>Versión</th>
                                <th>Condición</th>
                                <th class="text-center">Año</th>
                                <th>Color</th>
                                <th>Estado</th>
                                <th>Placa</th>
                                <th>Placa.R</th>
                                <th style="width: 80px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehiculos as $v): ?>
                                <tr>
                                    <td><?= htmlspecialchars($v['idvehiculo']) ?></td>
                                    <td><?= htmlspecialchars($v['marca']) ?></td>
                                    <td><?= htmlspecialchars($v['tipovehiculo']) ?></td>
                                    <td><?= htmlspecialchars($v['modelo']) ?></td>
                                    <td><?= htmlspecialchars($v['version']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($v['condicion'])) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($v['anio']) ?></td>
                                    <td><?= !empty($v['color']) ? htmlspecialchars($v['color']) : 'N/A' ?></td>
                                    <td><?= htmlspecialchars(ucfirst($v['disponibilidad'])) ?></td>
                                    <td><?= !empty($v['placa']) ? htmlspecialchars($v['placa']) : 'N/A' ?></td>
                                    <td><?= !empty($v['placarotativa']) ? htmlspecialchars($v['placarotativa']) : 'N/A' ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="form-check d-inline-flex align-items-center">
                                            <input class="form-check-input seleccionar-vehiculo-radio" type="radio"
                                                name="vehiculoSeleccionado"
                                                id="vehiculoRadio<?= htmlspecialchars($v['idvehiculo']) ?>"
                                                data-idvehiculo="<?= htmlspecialchars($v['idvehiculo']) ?>"
                                                data-precioventa="<?= htmlspecialchars($v['precioventa']) ?>"
                                                data-moneda="<?= htmlspecialchars($v['moneda']) ?>"
                                                data-tipovehiculo="<?= htmlspecialchars($v['tipovehiculo']) ?>"
                                                data-descripcion="<?= htmlspecialchars($v['marca'] . ' / ' . $v['tipovehiculo'] . ' / ' . $v['modelo'] . ' / ' . $v['version'] . ' / ' . ($v['color'] ?? 'N/A') . ' / ' . ($v['combustible'] ?? 'N/A') . ' / ' . $v['anio']) ?>"
                                                data-placa="<?= !empty($v['placa']) ? htmlspecialchars($v['placa']) : 'N/A' ?>"
                                                data-placarotativa="<?= !empty($v['placarotativa']) ? htmlspecialchars(strip_tags($v['placarotativa'])) : 'N/A' ?>"
                                                title="Seleccionar vehículo">
                                            <label class="form-check-label ms-2"
                                                for="vehiculoRadio<?= htmlspecialchars($v['idvehiculo']) ?>">
                                                Seleccionar
                                            </label>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE CRONOGRAMA -->
<div class="modal fade" id="modalCronograma" tabindex="-1" aria-labelledby="modalCronogramaLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCronogramaLabel">Cronograma de Pagos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end mb-2">
                    <button class="btn btn-sm btn-outline-danger me-2" id="btn-pdf" title="Generar cronograma en PDF">
                        <i class="bi bi-filetype-pdf"></i>
                        PDF
                    </button>
                    <button class="btn btn-sm btn-outline-success" id="btn-excel" title="Generar cronograma en EXCEL">
                        <i class="bi bi-file-earmark-excel"></i>
                        Excel
                    </button>
                </div>
                <div class="table-responsive p-2">
                    <table class="table table-sm table-bordered table-striped mt-2" id="tablaCronograma">
                        <thead>
                            <tr>
                                <th>ITEM</th>
                                <th>FECHA DE PAGO</th>
                                <th>INTERÉS DEL PERIODO</th>
                                <th>ABONO A CAPITAL</th>
                                <th>VALOR CUOTA</th>
                                <th>SALDO CAPITAL</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTablaCronograma">
                        </tbody>
                        <tfoot>
                            <tr id="filaTotales">
                                <td colspan="2" class="text-end fw-bold">TOTALES</td>
                                <td class="fw-bold" id="totalInteres"></td>
                                <td class="fw-bold" id="totalAbono"></td>
                                <td class="fw-bold" id="totalCuota"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade" id="modalCronograma" tabindex="-1" aria-labelledby="modalCronogramaLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCronogramaLabel">Cronograma de Pagos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end mb-2">
                    <button class="btn btn-sm btn-outline-danger me-2" id="btn-pdf" title="Generar cronograma en PDF">
                        <i class="bi bi-filetype-pdf"></i>
                        PDF
                    </button>
                    <button class="btn btn-sm btn-outline-success" id="btn-excel" title="Generar cronograma en EXCEL">
                        <i class="bi bi-file-earmark-excel"></i>
                        Excel
                    </button>
                </div>
                <div class="table-responsive p-2">
                    <table class="table table-sm table-bordered table-striped mt-2" id="tablaCronograma">
                        <thead>
                            <tr>
                                <th>ITEM</th>
                                <th>FECHA DE PAGO</th>
                                <th>INTERÉS DEL PERIODO</th>
                                <th>ABONO A CAPITAL</th>
                                <th>VALOR CUOTA</th>
                                <th>SALDO CAPITAL</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTablaCronograma">
                        </tbody>
                        <tfoot>
                            <tr id="filaTotales">
                                <td colspan="2" class="text-end fw-bold">TOTALES</td>
                                <td class="fw-bold" id="totalInteres"></td>
                                <td class="fw-bold" id="totalAbono"></td>
                                <td class="fw-bold" id="totalCuota"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div> -->

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js" defer></script>
<!-- PDFMAKE MAS RECOMENDADO PARA PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" defer></script>
<script src="/assets/js/cotizacion-cronograma-pdf/pdf.js" defer></script>

<!----------------------------------------------->
<script>
    let tipoCambioCache = null;
    const tablaCronograma = document.querySelector('#tablaCronograma');
    const btnExcel = document.querySelector('#btn-excel');
    const btnPDF = document.querySelector('#btn-pdf');

    function debounce(func, delay = 300) {
        let timeout;
        return function (...args) {
            const ctx = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(ctx, args), delay);
        };
    }

    async function calcularCuotaAPI(importeTotal, inicial, meses, tasaPercent) {
        try {
            const res = await fetch(
                `/api/cotizacion/calcularpagomensual/${encodeURIComponent(importeTotal)}/${encodeURIComponent(inicial)}/${encodeURIComponent(meses)}?tasa=${encodeURIComponent(tasaPercent)}`
            );
            if (!res.ok) throw new Error('Error calculando cuota');
            const j = await res.json();
            return parseFloat(j.pago_mensual || 0);
        } catch (err) {
            console.error('calcularCuotaAPI error', err);
            return 0;
        }
    }
    async function generarYMostrarCronograma(importeTotal, inicial, meses, tasaPercent) {
        try {
            const res = await fetch(`/api/cotizacion/generar-cronograma/${encodeURIComponent(importeTotal)}/${encodeURIComponent(inicial)}/${encodeURIComponent(meses)}?tasa=${encodeURIComponent(tasaPercent)}`);
            if (!res.ok) throw new Error('Error generando cronograma');
            const cronograma = await res.json();

            const tablaCronograma = document.getElementById('tablaCronograma');
            if (!tablaCronograma) {
                throw new Error('Tabla de cronograma no encontrada');
            }

            if ($.fn.DataTable.isDataTable('#tablaCronograma')) {
                $('#tablaCronograma').DataTable().clear().destroy();
            }

            const tbody = document.getElementById('cuerpoTablaCronograma');
            if (tbody) {
                tbody.innerHTML = '';

                let totalInteres = 0, totalAbono = 0, totalCuota = 0;

                cronograma.forEach(pago => {
                    const row = tbody.insertRow();
                    row.insertCell(0).innerText = pago.item;
                    row.insertCell(1).innerText = pago.fecha_pago;
                    row.insertCell(2).innerText = `S/ ${Number(pago.interes).toFixed(2)}`;
                    row.insertCell(3).innerText = `S/ ${Number(pago.abono_capital).toFixed(2)}`;
                    row.insertCell(4).innerText = `S/ ${Number(pago.valor_cuota).toFixed(2)}`;
                    row.insertCell(5).innerText = `S/ ${Number(pago.saldo_capital).toFixed(2)}`;

                    totalInteres += Number(pago.interes);
                    totalAbono += Number(pago.abono_capital);
                    totalCuota += Number(pago.valor_cuota);
                });

                const totalInteresEl = document.getElementById('totalInteres');
                const totalAbonoEl = document.getElementById('totalAbono');
                const totalCuotaEl = document.getElementById('totalCuota');

                if (totalInteresEl) totalInteresEl.innerText = `S/ ${totalInteres.toFixed(2)}`;
                if (totalAbonoEl) totalAbonoEl.innerText = `S/ ${totalAbono.toFixed(2)}`;
                if (totalCuotaEl) totalCuotaEl.innerText = `S/ ${totalCuota.toFixed(2)}`;
            }

            const modalCronograma = new bootstrap.Modal(document.getElementById('modalCronograma'));
            modalCronograma.show();

            setTimeout(() => {
                initTableModalVehiculo();
            }, 300);

        } catch (err) {
            console.error('Error cronograma:', err);
            if (typeof showToast === 'function') {
                showToast('Error al generar cronograma', 'ERROR', 1500);
            } else {
                alert('Error al generar cronograma');
            }
        }
    }

    let finCounter = 0;
    const cardsContainer = document.getElementById('cardsFinanciamiento');
    const templateCard = document.getElementById('templateCardFin');


    // Función para limpiar el valor "0" cuando el usuario empieza a escribir
    function setupZeroValueClearing(inputElement) {
        if (!inputElement) return;

        // Manejador para cuando el usuario hace foco en el campo
        inputElement.addEventListener('focus', function () {
            if (this.value === '0') {
                this.value = '';
            }
        });

        // Manejador para cuando el usuario empieza a escribir
        inputElement.addEventListener('input', function () {
            // Si el valor actual es "0" y el usuario escribió algo más, limpiarlo
            if (this.value.startsWith('0') && this.value.length > 1 && this.value !== '0.') {
                // Mantener solo lo que escribió después del 0
                this.value = this.value.substring(1);
            }
        });

        // Manejador para cuando pierde el foco - opcional: restaurar 0 si está vacío
        inputElement.addEventListener('blur', function () {
            if (this.value === '' || this.value === null) {
                this.value = '0';
            }
        });
    }

    function crearTarjetaFin(data = {}) {
        if (!cardsContainer || !templateCard) {
            console.error('Missing required elements for financing cards');
            return null;
        }

        finCounter++;
        const clone = templateCard.content.cloneNode(true);
        const card = clone.querySelector('.card-fin');
        card.dataset.finId = 'fin_' + finCounter;

        const inicialEl = card.querySelector('.fin-inicial');
        const valorFinEl = card.querySelector('.fin-valorFinanciar');
        const numEl = card.querySelector('.fin-numcuotas');
        const tasaEl = card.querySelector('.fin-tasaAnual');
        const cuotaEl = card.querySelector('.fin-cuotaMensual');
        const btnCrono = card.querySelector('.fin-btn-cronograma');
        const btnRemove = card.querySelector('.fin-remove-btn');
        const titleEl = card.querySelector('.fin-title');

        function obtenerInicialReferencia() {
            const primeraCard = cardsContainer.querySelector('.card-fin .fin-inicial');
            if (primeraCard && primeraCard.value && primeraCard.value !== '0') {
                return primeraCard.value;
            }
            return '0';
        }

        const inicialReferencia = obtenerInicialReferencia();
        if (inicialEl) {
            inicialEl.value = (typeof data.inicial !== 'undefined') ? data.inicial : inicialReferencia;
            // Aplicar la funcionalidad de limpiar el 0
            setupZeroValueClearing(inicialEl);
        }

        if (numEl) {
            numEl.value = (typeof data.numcuotas !== 'undefined') ? data.numcuotas : '0';
            // Aplicar la funcionalidad de limpiar el 0
            setupZeroValueClearing(numEl);
        }

        if (tasaEl) tasaEl.value = (typeof data.tasa !== 'undefined') ? data.tasa : 65;
        if (cuotaEl) cuotaEl.value = data.valorcuota ? Number(data.valorcuota).toFixed(2) : '';

        function actualizarValorFinanciar() {
            const precioFinal = parseFloat(document.getElementById('inputValorConvertido')?.value || document.getElementById('valor')?.value || 0) || 0;
            const inicial = parseFloat(inicialEl?.value || 0);
            const vf = Math.max(0, precioFinal - inicial);
            if (valorFinEl) valorFinEl.value = vf.toFixed(2);
        }

        const calcularYSetCuota = debounce(async function () {
            const precioFinal = parseFloat(document.getElementById('inputValorConvertido')?.value || document.getElementById('valor')?.value || 0) || 0;
            const inicial = parseFloat(inicialEl?.value || 0);
            const meses = parseInt(numEl?.value || 0, 10) || 0;
            const tasaPercent = parseFloat(tasaEl?.value || 65);

            actualizarValorFinanciar();

            if (!meses || precioFinal <= 0) {
                if (cuotaEl) cuotaEl.value = '';
                actualizarHiddenOpciones();
                return;
            }
            //const cuota = await calcularCuotaAPI(precioFinal, inicial, meses);
            const cuota = await calcularCuotaAPI(precioFinal, inicial, meses, tasaPercent);

            if (cuotaEl) cuotaEl.value = cuota ? Number(cuota).toFixed(2) : '';
            actualizarHiddenOpciones();
        }, 250);

        if (inicialEl) {
            inicialEl.addEventListener('input', function () {
                const nuevaInicial = this.value;

                cardsContainer.querySelectorAll('.card-fin .fin-inicial').forEach(otroInput => {
                    if (otroInput !== this) {
                        otroInput.value = nuevaInicial;
                        const otherCard = otroInput.closest('.card-fin');
                        const otroNumEl = otherCard?.querySelector('.fin-numcuotas');
                        if (otroNumEl && otroNumEl.value && otroNumEl.value !== '0') {
                            const ev = new Event('input');
                            otroNumEl.dispatchEvent(ev);
                        }
                    }
                });

                calcularYSetCuota();
            });
        }

        if (numEl) numEl.addEventListener('input', calcularYSetCuota);
        if (tasaEl) tasaEl.addEventListener('input', calcularYSetCuota);

        if (btnCrono) {
            btnCrono.addEventListener('click', (e) => {
                e.preventDefault();
                const precioFinal = parseFloat(document.getElementById('inputValorConvertido')?.value || document.getElementById('valor')?.value || 0) || 0;
                const inicial = parseFloat(inicialEl?.value || 0);
                const meses = parseInt(numEl?.value || 0, 10) || 0;
                const tasaPercent = parseFloat(tasaEl?.value || 65);

                if (meses <= 0 || (precioFinal - inicial) <= 0) {
                    if (typeof showToast === 'function') {
                        showToast('Ingresa valores válidos para cronograma', 'ERROR', 1200);
                    } else {
                        alert('Ingresa valores válidos para cronograma');
                    }
                    return;
                }
                generarYMostrarCronograma(precioFinal, inicial, meses, tasaPercent);
            });
        }

        if (btnRemove) {
            btnRemove.addEventListener('click', () => {
                card.remove();
                actualizarHiddenOpciones();
            });
        }

        const updateTitle = () => {
            const meses = numEl?.value && numEl.value !== '0' ? `${numEl.value} meses` : 'Meses';
            if (titleEl) titleEl.textContent = meses;
        };
        if (numEl) numEl.addEventListener('input', updateTitle);

        cardsContainer.appendChild(card);
        calcularYSetCuota();
        updateTitle();
        return card;
    }

    function actualizarHiddenOpciones() {
        const cards = cardsContainer?.querySelectorAll('.card-fin') || [];
        const opciones = [];
        cards.forEach(card => {
            const inicial = parseFloat(card.querySelector('.fin-inicial')?.value || 0);
            const numcuotas = parseInt(card.querySelector('.fin-numcuotas')?.value || 0, 10) || 0;
            const valorcuota = parseFloat((card.querySelector('.fin-cuotaMensual')?.value || '').replace(/,/g, '')) || 0;
            const precioventa = parseFloat(document.getElementById('inputValorConvertido')?.value || document.getElementById('valor')?.value || 0) || 0;
            if (numcuotas > 0 && (valorcuota > 0 || inicial >= 0)) {
                opciones.push({
                    numcuotas,
                    inicial: Number(inicial.toFixed(2)),
                    valorcuota: Number(valorcuota.toFixed(2)),
                    precioventa: Number(precioventa.toFixed(2))
                });
            }
        });
        const hiddenInput = document.getElementById('opciones_financiamiento');
        if (hiddenInput) hiddenInput.value = JSON.stringify(opciones);
    }

    function initFinancingEvents() {
        const btnAgregar = document.getElementById('btnAgregarFin');
        if (btnAgregar) {
            btnAgregar.addEventListener('click', (e) => {
                e.preventDefault();
                crearTarjetaFin();
            });
        }

        crearTarjetaFin({
            inicial: 0,
            numcuotas: 0,
            tasa: 65,
            valorcuota: ''
        });

        const globalTriggers = ['inputValorConvertido', 'valor'];
        globalTriggers.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', debounce(() => {
                if (cardsContainer) {
                    cardsContainer.querySelectorAll('.card-fin').forEach(card => {
                        const ev = new Event('input');
                        const numInput = card.querySelector('.fin-numcuotas');
                        if (numInput) numInput.dispatchEvent(ev);
                    });
                }
            }, 300));
        });
    }

    function initFormSubmitHandler() {
        const formCot = document.getElementById('formCotizacion');
        if (formCot) {
            formCot.addEventListener('submit', function (e) {
                actualizarHiddenOpciones();
            });
        }
    }

    function generarReporteExcel() {
        const tableElement = document.getElementById('tablaCronograma');
        if (!tableElement) {
            console.warn('Table tablaCronograma not found for Excel export');
            return;
        }

        try {
            if ($.fn.DataTable.isDataTable('#tablaCronograma')) {
                const dataTable = $('#tablaCronograma').DataTable();
                dataTable.page.len(-1).draw();

                setTimeout(() => {
                    const ws = XLSX.utils.table_to_sheet(tableElement);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Cronograma");

                    const nombresEl = document.getElementById('nombres');
                    const fileName = nombresEl ? `Cronograma-${nombresEl.value}.xlsx` : 'Cronograma.xlsx';
                    XLSX.writeFile(wb, fileName);

                    dataTable.page.len(10).draw();
                }, 100);
            } else {
                const ws = XLSX.utils.table_to_sheet(tableElement);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Cronograma");

                const nombresEl = document.getElementById('nombres');
                const fileName = nombresEl ? `Cronograma-${nombresEl.value}.xlsx` : 'Cronograma.xlsx';
                XLSX.writeFile(wb, fileName);
            }
        } catch (error) {
            console.error('Error generating Excel report:', error);
            if (typeof showToast === 'function') {
                showToast('Error al generar reporte Excel', 'ERROR', 2000);
            } else {
                alert('Error al generar reporte Excel');
            }
        }
    }

    function bindExcelButton() {
        if (btnExcel) {
            btnExcel.addEventListener('click', generarReporteExcel);
        }
    }

    function formatDate(date, pad) {
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    }

    async function verificarUltimoClienteRegistrado() {
        try {
            const response = await fetch('/api/ultimo-cliente-registrado');
            const data = await response.json();

            if (data.success && data.cliente) {
                const cliente = data.cliente;
                const tiempoTranscurrido = Math.floor((Date.now() / 1000) - cliente.timestamp);
                if (tiempoTranscurrido < 1800) {
                    mostrarSugerenciaUltimoCliente(cliente);
                }
            }
        } catch (error) {
            console.log('No hay cliente reciente para sugerir');
        }
    }

    function mostrarSugerenciaUltimoCliente(cliente) {
        const modalHtml = `
            <div class="modal fade" id="modalSugerenciaCliente" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-primary">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-person-plus-fill me-2"></i>
                                Cliente Recién Registrado
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>Detectamos que acabas de registrar un cliente</div>
                            </div>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="bi bi-person-badge me-2"></i>
                                        Datos del Cliente
                                    </h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Documento:</strong><br>
                                            <span class="text-muted">${cliente.tipodoc} ${cliente.nrodoc}</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Teléfono:</strong><br>
                                            <span class="text-muted">${cliente.telprimario}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <strong>Nombre completo:</strong><br>
                                        <span class="text-primary fw-semibold">${cliente.apellidos} ${cliente.nombres}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <p class="mb-3">¿Deseas usar este cliente para la cotización?</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-primary" onclick="usarClienteSugerido(${JSON.stringify(cliente).replace(/"/g, '&quot;')})">
                                        <i class="bi bi-check-circle me-1"></i>Sí
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i>No
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('modalSugerenciaCliente'));
            modal.show();
            document.getElementById('modalSugerenciaCliente').addEventListener('hidden.bs.modal', function () {
                this.remove();
            });
        }, 500);
    }

    function usarClienteSugerido(cliente) {
        const tipoDoc = document.getElementById('tipoDocumento');
        const doc = document.getElementById('documento');
        const idCliente = document.getElementById('idcliente');
        const nombres = document.getElementById('nombres');
        const telPrimario = document.getElementById('telprimario');
        const telAlternativo = document.getElementById('telalternativo');

        if (tipoDoc) tipoDoc.value = cliente.tipodoc.toLowerCase();
        if (doc) doc.value = cliente.nrodoc;
        if (idCliente) idCliente.value = cliente.idcliente;
        if (nombres) nombres.value = `${cliente.apellidos} ${cliente.nombres}`;
        if (telPrimario) telPrimario.value = cliente.telprimario;
        if (telAlternativo) telAlternativo.value = cliente.telalternativo || '';

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalSugerenciaCliente'));
        if (modal) modal.hide();

        if (typeof showToast === 'function') {
            showToast('Cliente cargado correctamente', 'SUCCESS', 2000);
        }

        fetch('/api/limpiar-ultimo-cliente', { method: 'POST' });
    }

    function initDataTable() {
        if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
            console.warn('jQuery or DataTables not loaded yet');
            return;
        }

        const table = document.getElementById('tablaVehiculosModal');
        if (!table) {
            console.warn('Table tablaVehiculosModal not found');
            return;
        }

        try {
            if ($.fn.DataTable.isDataTable('#tablaVehiculosModal')) {
                $('#tablaVehiculosModal').DataTable().clear().destroy();
            }

            setTimeout(() => {
                const tableElement = document.getElementById('tablaVehiculosModal');
                if (tableElement && tableElement.parentNode) {
                    $('#tablaVehiculosModal').DataTable({
                        order: [[0, 'desc']],
                        pagingType: 'full_numbers',
                        pageLength: 20,
                        lengthMenu: [[15, 20, 35, -1], [15, 20, 35, "Todos"]],
                        scrollX: true,
                        destroy: true,
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/2.0.7/i18n/es-ES.json",
                            paginate: {
                                first: '«',
                                previous: '‹',
                                next: '›',
                                last: '»'
                            }
                        }
                    });
                }
            }, 100);
        } catch (error) {
            console.error('Error initializing vehicle table:', error);
        }
    }

    function initTableModalVehiculo() {
        if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
            console.warn('jQuery or DataTables not loaded yet');
            return;
        }

        const table = document.getElementById('tablaCronograma');
        if (!table) {
            console.warn('Table tablaCronograma not found');
            return;
        }

        try {
            if ($.fn.DataTable.isDataTable('#tablaCronograma')) {
                $('#tablaCronograma').DataTable().clear().destroy();
            }

            setTimeout(() => {
                const tableElement = document.getElementById('tablaCronograma');
                if (tableElement && tableElement.parentNode) {
                    $('#tablaCronograma').DataTable({
                        order: [[0, 'asc']],
                        pagingType: 'full_numbers',
                        pageLength: 15,
                        lengthMenu: [[10, 15, 25, -1], [10, 15, 25, "Todos"]],
                        scrollX: true,
                        destroy: true,
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/2.0.7/i18n/es-ES.json",
                            paginate: {
                                first: '«',
                                previous: '‹',
                                next: '›',
                                last: '»'
                            }
                        }
                    });
                }
            }, 100);
        } catch (error) {
            console.error('Error initializing cronograma table:', error);
        }
    }

    function initFechas() {
        const fechaEmision = document.getElementById('fechaEmision');
        const fechaCaducidad = document.getElementById('fechaCaducidad');
        const inputVigenciaDias = document.getElementById('inputVigenciaDias');

        if (!fechaEmision || !fechaCaducidad) return;

        const hoy = new Date();
        const pad = n => String(n).padStart(2, '0');
        fechaEmision.value = formatDate(hoy, pad);
        const fin = new Date();
        fin.setDate(hoy.getDate() + 7);
        fechaCaducidad.value = formatDate(fin, pad);
        if (inputVigenciaDias) inputVigenciaDias.value = 7;

        fechaCaducidad.addEventListener('change', () => {
            const em = new Date(fechaEmision.value);
            const ca = new Date(fechaCaducidad.value);
            const diff = Math.round((ca - em) / (1000 * 60 * 60 * 24));
            if (inputVigenciaDias) inputVigenciaDias.value = diff;
        });
    }

    async function initEventosCliente() {
        const btn = document.getElementById('btnBuscarCliente');
        const tipo = document.getElementById('tipoDocumento');
        const docIn = document.getElementById('documento');
        const hid = document.getElementById('idcliente');

        if (!btn || !tipo || !docIn || !hid) return;

        btn.addEventListener('click', async () => {
            const tipoValue = tipo.value;
            const docValue = docIn.value.trim();
            if (!docValue) return alert('Ingresa un número de documento válido.');
            const res = await fetch(`/cotizacion/buscarCliente?tipo=${tipoValue}&doc=${encodeURIComponent(docValue)}`);
            const data = await res.json();
            console.log('DATA DE DNI: ', data);
            if (data.notFound) {
                const confirmRedirect = confirm('Cliente no encontrado. ¿Desea ir a registrarlo ahora?');
                if (confirmRedirect) {
                    const returnUrl = `/clientes/createpersonclient?return_to=cotizacion&dni=${encodeURIComponent(docValue)}&tipo=${encodeURIComponent(tipoValue)}`;
                    window.location.href = returnUrl;
                } else {
                    hid.value = '';
                    const nombres = document.getElementById('nombres');
                    const telPrimario = document.getElementById('telprimario');
                    const telAlternativo = document.getElementById('telalternativo');
                    const direccion = document.getElementById('direccion');
                    if (nombres) nombres.value = '';
                    if (telPrimario) telPrimario.value = '';
                    if (telAlternativo) telAlternativo.value = '';
                    if (direccion) direccion.value = '';
                }
            } else if (data.error) {
                alert(data.error);
            } else {
                hid.value = data.idcliente;
                const nombres = document.getElementById('nombres');
                const telPrimario = document.getElementById('telprimario');
                const telAlternativo = document.getElementById('telalternativo');
                const direccion = document.getElementById('direccion');
                if (nombres) nombres.value = `${data.apellidos} ${data.nombres}`.trim();
                if (telPrimario) telPrimario.value = data.telprimario || '';
                if (telAlternativo) telAlternativo.value = data.telalternativo || '';
                if (direccion) direccion.value = `${data.direccion}` ?? '';
            }
        });
    }

    function copiarAlPortapapeles(texto) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(texto).then(() => {
                if (typeof showToast === 'function') {
                    showToast('Texto copiado al portapapeles', 'SUCCESS', 1500);
                }
            }).catch(err => {
                console.error('Error al copiar:', err);
                copiarConMetodoFallback(texto);
            });
        } else {
            copiarConMetodoFallback(texto);
        }
    }

    function copiarConMetodoFallback(texto) {
        const textArea = document.createElement('textarea');
        textArea.value = texto;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            const exitoso = document.execCommand('copy');
            if (exitoso && typeof showToast === 'function') {
                showToast('Texto copiado al portapapeles', 'SUCCESS', 1500);
            } else if (!exitoso && typeof showToast === 'function') {
                showToast('No se pudo copiar el texto', 'ERROR', 2000);
            }
        } catch (err) {
            console.error('Error al copiar:', err);
            if (typeof showToast === 'function') {
                showToast('Error al copiar texto', 'ERROR', 2000);
            }
        } finally {
            document.body.removeChild(textArea);
        }
    }

    function aplicarPoliticaFinanciamientoPorTipo(tipovehiculo) {
        const esMoto = ['Mototaxi', 'Motolineal'].includes(String(tipovehiculo || '').trim());

        const btnAgregar = document.getElementById('btnAgregarFin');
        if (btnAgregar) btnAgregar.disabled = esMoto;

        // tasa por defecto segun tipo
        const tasaPorDefecto = esMoto ? 80 : 65;

        // Si es moto, restringe a una sola tarjeta
        if (esMoto) {
            // Elimina todas menos la primera
            const cards = cardsContainer?.querySelectorAll('.card-fin') || [];
            cards.forEach((card, idx) => {
                if (idx > 0) card.remove();
            });
            // Si no hay tarjetas, crea una
            if (!cardsContainer.querySelector('.card-fin')) {
                crearTarjetaFin();
            }
        } else {
            // En vehículos normales permitimos múltiples y dejamos el botón activo
            if (btnAgregar) btnAgregar.disabled = false;
        }

        // Setea la tasa en las tarjetas existentes y recalcula
        (cardsContainer?.querySelectorAll('.card-fin') || []).forEach(card => {
            const tasaEl = card.querySelector('.fin-tasaAnual');
            if (tasaEl) {
                tasaEl.value = tasaPorDefecto;
                const ev = new Event('input');
                tasaEl.dispatchEvent(ev);
            }
        });
    }

    function initEventosVehiculo() {
        const tabla = document.getElementById('tablaVehiculosModal');
        if (!tabla) return;

        $('#tablaVehiculosModal').on('change', '.seleccionar-vehiculo-radio', async function () {
            const $input = $(this);
            const d = $input.data();

            // Rellenar paso 2 con los datos
            fillPaso2(d);

            // Guarda el tipo en hidden si lo agregaste
            const tipoHidden = document.getElementById('tipoVehiculoSeleccionado');
            if (tipoHidden) tipoHidden.value = d.tipovehiculo || '';

            // Limpia y actualiza montos / financiamiento
            clearConversion();
            await actualizarMontos();

            aplicarPoliticaFinanciamientoPorTipo(d.tipovehiculo);
            await actualizarFinanciamiento();

            // Cerrar modal (Bootstrap 5)
            const modalEl = document.getElementById('modalVehiculos');
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.hide();
            }
        });

        actualizarMontos();
    }

    function fillPaso2({
        idvehiculo,
        descripcion,
        placa,
        placarotativa,
        precioventa,
        moneda
    }) {
        const valorPlaca = (placa || '').trim() || 'N/A';
        const valorPlacaRotativa = (placarotativa || '').replace(/<[^>]+>/g, '').trim() || 'N/A';
        const precio = parseFloat(precioventa);
        const valorPrecio = isNaN(precio) ? '0.00' : precio.toFixed(2);

        const idVehiculoEl = document.getElementById('idvehiculo');
        const descripcionEl = document.getElementById('descripcion');
        const placaEl = document.getElementById('placa');
        const placaRotativaEl = document.getElementById('placarotativa');
        const valorEl = document.getElementById('valor');
        const monedaPrecioEl = document.getElementById('monedaprecio');
        const vehiculoMonedaEl = document.getElementById('vehiculoMoneda');
        const monedaSelectEl = document.getElementById('monedaSelect');

        if (idVehiculoEl) idVehiculoEl.value = idvehiculo;
        if (descripcionEl) descripcionEl.value = descripcion;
        if (placaEl) placaEl.value = valorPlaca.toUpperCase();
        if (placaRotativaEl) placaRotativaEl.value = valorPlacaRotativa.toUpperCase();
        if (valorEl) valorEl.value = valorPrecio;
        if (monedaPrecioEl) monedaPrecioEl.value = moneda === 'USD' ? 'Dólares' : 'Soles';
        if (vehiculoMonedaEl) vehiculoMonedaEl.value = moneda;

        if (monedaSelectEl) {
            if (moneda === 'PEN') {
                monedaSelectEl.value = 'PEN';
                monedaSelectEl.disabled = true;
            } else {
                monedaSelectEl.disabled = false;
            }
        }
    }

    function clearConversion() {
        const tipoCambioEl = document.getElementById('tipoCambio');
        const valorMonedaEl = document.getElementById('valormoneda');
        if (tipoCambioEl) tipoCambioEl.value = '';
        if (valorMonedaEl) valorMonedaEl.value = '';
    }

    async function fetchTipoCambio(force = false) {
        if (!force && tipoCambioCache !== null) {
            return tipoCambioCache;
        }

        try {
            const res = await fetch('/cotizacion/tipo-cambio');
            if (!res.ok) throw new Error(res.statusText);
            const { tipo_cambio } = await res.json();
            tipoCambioCache = parseFloat(tipo_cambio) || 1;
            return tipoCambioCache;
        } catch (err) {
            console.error('Error al obtener tipo de cambio:', err);
            return 1;
        }
    }

    async function actualizarMontos() {
        const valorEl = document.getElementById('valor');
        const vehiculoMonedaEl = document.getElementById('vehiculoMoneda');
        const monedaSelectEl = document.getElementById('monedaSelect');
        const idVehiculoEl = document.getElementById('idvehiculo');
        const tipoCambioEl = document.getElementById('tipoCambio');
        const valorMonedaEl = document.getElementById('valormoneda');
        const inputPrecioventaEl = document.getElementById('inputPrecioventa');
        const inputMonedaEl = document.getElementById('inputMoneda');
        const inputTipoCambioEl = document.getElementById('inputTipoCambio');
        const inputValorConvertidoEl = document.getElementById('inputValorConvertido');

        if (!valorEl || !vehiculoMonedaEl || !monedaSelectEl || !idVehiculoEl) return;

        const precioOriginal = parseFloat(valorEl.value) || 0;
        const vehMoneda = vehiculoMonedaEl.value;
        const cotMoneda = monedaSelectEl.value;
        let tipoCam = 1;

        const idvehiculo = idVehiculoEl.value;
        if (!idvehiculo) {
            if (tipoCambioEl) tipoCambioEl.value = '';
            if (valorMonedaEl) valorMonedaEl.value = '';
            if (inputPrecioventaEl) inputPrecioventaEl.value = '';
            if (inputMonedaEl) inputMonedaEl.value = '';
            if (inputTipoCambioEl) inputTipoCambioEl.value = '';
            if (inputValorConvertidoEl) inputValorConvertidoEl.value = '';
            return;
        }

        if (vehMoneda !== cotMoneda) {
            tipoCam = await fetchTipoCambio();
            if (tipoCambioEl) tipoCambioEl.value = tipoCam.toFixed(4);
        } else {
            if (tipoCambioEl) tipoCambioEl.value = '';
        }

        let precioFinal = precioOriginal;
        if (vehMoneda !== cotMoneda) {
            if (vehMoneda === 'USD' && cotMoneda === 'PEN') {
                precioFinal = precioOriginal * tipoCam;
            } else if (vehMoneda === 'PEN' && cotMoneda === 'USD') {
                precioFinal = precioOriginal / tipoCam;
            }
        }

        precioFinal = Number(precioFinal.toFixed(2));
        if (valorMonedaEl) valorMonedaEl.value = precioFinal;
        if (inputPrecioventaEl) inputPrecioventaEl.value = precioFinal;
        if (inputMonedaEl) inputMonedaEl.value = cotMoneda;
        if (inputTipoCambioEl) inputTipoCambioEl.value = tipoCam;
        if (inputValorConvertidoEl) inputValorConvertidoEl.value = precioFinal;
    }

    /* async function actualizarFinanciamiento() {
        // This function is intentionally simplified since the new card-based system handles this
        // Individual cards will recalculate themselves when needed
    } */

    async function actualizarFinanciamiento() {
        const inicial = parseFloat($('#inicial').val()) || 0;
        const precioFinal = parseFloat($('#inputValorConvertido').val()) || 0;
        const valorF = Math.max(0, precioFinal - inicial);
        $('#valorFinanciar').val(valorF.toFixed(2));
        $('#inputValorFinanciar').val(valorF.toFixed(2));

        const n = parseInt($('#numcuotas').val(), 10) || 0;

        if (n > 0) {
            try {
                const res = await fetch(`/api/cotizacion/calcularpagomensual/${precioFinal}/${inicial}/${n}`);
                if (!res.ok) throw new Error(res.statusText);
                const {
                    pago_mensual
                } = await res.json();

                $('#cuotaMensual').val(pago_mensual.toFixed(2));
                $('#inputCuotaMensual').val(pago_mensual.toFixed(2));
            } catch (err) {
                console.error('Error calculando financiamiento:', err);
                $('#cuotaMensual').val('');
                $('#inputCuotaMensual').val('');
            }
        } else {
            $('#cuotaMensual').val('');
            $('#inputCuotaMensual').val('');
        }
    }

    const debouncedActualizarMontosFinanciamiento = debounce(async () => {
        await actualizarMontos();
        await actualizarFinanciamiento();
    }, 280);

    function bindMonedaEvents() {
        const elementos = ['tipoCambio', 'monedaSelect'];
        elementos.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', debouncedActualizarMontosFinanciamiento);
                el.addEventListener('change', debouncedActualizarMontosFinanciamiento);
            }
        });
    }

    function initModalRequisitos() {
        const modalRequisitos = document.getElementById('modalRequisitos');
        if (!modalRequisitos) return;

        modalRequisitos.addEventListener('show.bs.modal', async () => {
            const modalidadEl = document.getElementById('modalidad');
            const listaRequisitosEl = document.getElementById('listaRequisitos');

            if (!modalidadEl || !listaRequisitosEl) return;

            const id = modalidadEl.value;
            listaRequisitosEl.innerHTML = '';

            if (!id) {
                listaRequisitosEl.innerHTML = '<li class="list-group-item text-muted">Selecciona primero una modalidad.</li>';
                return;
            }

            try {
                const resp = await fetch(`/cotizaciones/requisitos/${id}`);
                const arr = await resp.json();
                if (!arr.length) {
                    listaRequisitosEl.innerHTML = '<li class="list-group-item text-muted">No hay requisitos definidos.</li>';
                } else {
                    arr.forEach(i => {
                        listaRequisitosEl.innerHTML += `<li class="list-group-item">${i.requisito}</li>`;
                    });
                }
            } catch {
                listaRequisitosEl.innerHTML = '<li class="list-group-item text-danger">Error cargando requisitos.</li>';
            }
        });
    }

    function attachCotizacionConfirm() {
        const formCot = document.getElementById('formCotizacion');
        if (!formCot) return;

        formCot.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitButton = formCot.querySelector('button[type="submit"]');

            let confirmado;
            if (typeof ask === 'function') {
                confirmado = await ask('¿Desea confirmar el registro de esta cotización?', '¿Registrar cotización?');
            } else {
                confirmado = confirm('¿Desea confirmar el registro de esta cotización?');
            }

            if (!confirmado) return;

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Registrando...';
            }
            formCot.submit();
        });
    }

    function initDocumentValidation() {
        const tipo = document.getElementById("tipoDocumento");
        const doc = document.getElementById("documento");

        if (!tipo || !doc) return;

        doc.setAttribute("maxlength", "8");
        doc.placeholder = "DNI (8 dígitos)";

        tipo.addEventListener("change", () => {
            if (tipo.value === "dni") {
                doc.setAttribute("maxlength", "8");
                doc.placeholder = "DNI (8 dígitos)";
                doc.value = "";
            } else if (tipo.value === "ruc") {
                doc.setAttribute("maxlength", "11");
                doc.placeholder = "RUC (11 dígitos)";
                doc.value = "";
            }
        });

        doc.addEventListener("input", () => {
            doc.value = doc.value.replace(/\D/g, "");
        });
    }

    // Initialize everything when DOM is ready
    document.addEventListener("DOMContentLoaded", () => {
        console.log('DOM Content Loaded - Initializing...');

        // Initialize basic functions first
        initFechas();
        initEventosCliente();
        initModalRequisitos();
        initFinancingEvents();
        initFormSubmitHandler();
        bindExcelButton();
        bindMonedaEvents();
        attachCotizacionConfirm();
        initDocumentValidation();
        verificarUltimoClienteRegistrado();

        // Initialize DataTables after a delay to ensure all scripts are loaded
        setTimeout(() => {
            initDataTable();
            initEventosVehiculo();
        }, 500);

        // Initialize monetary calculations
        setTimeout(async () => {
            await actualizarMontos();
            await actualizarFinanciamiento();
        }, 600);
    });

    //para subir el modal-body mas arriba
    (function () {
        const modalEl = document.getElementById('modalVehiculos');
        if (!modalEl) return;

        modalEl.addEventListener('shown.bs.modal', function () {
            const mb = modalEl.querySelector('.modal-body');
            if (!mb) return;

            // guarda valor original para poder restaurarlo luego
            if (typeof mb.dataset.originalPaddingTop === 'undefined') {
                mb.dataset.originalPaddingTop = mb.style.paddingTop || '';
            }
            mb.style.paddingTop = '4px';

            const tr = mb.querySelector('.table-responsive');
            if (tr) {
                if (typeof tr.dataset.originalMarginTop === 'undefined') {
                    tr.dataset.originalMarginTop = tr.style.marginTop || '';
                }
                tr.style.marginTop = '0';
            }
            mb.scrollTop = 0;
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            const mb = modalEl.querySelector('.modal-body');
            if (!mb) return;

            // restaura valores originales
            if (typeof mb.dataset.originalPaddingTop !== 'undefined') {
                mb.style.paddingTop = mb.dataset.originalPaddingTop;
                delete mb.dataset.originalPaddingTop;
            }
            const tr = mb.querySelector('.table-responsive');
            if (tr && typeof tr.dataset.originalMarginTop !== 'undefined') {
                tr.style.marginTop = tr.dataset.originalMarginTop;
                delete tr.dataset.originalMarginTop;
            }
        });
    })();

</script>