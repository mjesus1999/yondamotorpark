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
        /* background-color: #f4f7f9; */
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

    .table-responsive-style {
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid #e0e0e0;
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

    #btn-excel {
        background-color: #27ae60;
        border-color: #27ae60;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        transition: background-color 0.3s ease;
    }

    #btn-excel:hover {
        background-color: #229954;
    }

    #tablaCronograma tfoot tr td {
        background-color: #e8eaf6;
        color: #2c3e50;
        font-weight: bold;
        font-size: 1rem;
        padding: 0.8rem;
        border-top: 2px solid #4a698c;
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
                <a href="/cotizacion" class="">[ Mostrar Lista ]</a>
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
                                <label for="tipoDocumento">Tipo Documento</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="input-group">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="documento" name="documento"
                                        placeholder="DNI / RUC">
                                    <label for="documento">DNI / RUC</label>
                                </div>
                                <button type="button" id="btnBuscarCliente" class="btn btn-outline-success"
                                    title="Buscar cliente en la DB"><i class="bi bi-search"></i></button>
                            </div>
                        </div>

                        <!-- Apellidos y Nombres -->
                        <div class="col-md-7">
                            <div class="form-floating">
                                <input type="text" class="form-control" placeholder="Apellidos y Nombres / Razón Social"
                                    id="nombres" name="nombres">
                                <label for="nombres">Apellidos y Nombres / Razón Social</label>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-1 g-2">
                        <!-- Teléfono -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" placeholder="Teléfono" id="telprimario"
                                    name="telprimario" maxlength="9" required>
                                <label for="telprimario">Teléfono</label>
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
                                    <input type="text" class="form-control" placeholder="Descripcion del vehiculo"
                                        id="descripcion" name="descripcion">
                                    <label for="descripcion">Descripcion del vehiculo</label>
                                </div>

                            </div>
                        </div>
                        <!-- PLACA -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" placeholder="Placa" class="form-control" id="placa" name="placa">
                                <label for="placa">Placa</label>
                            </div>
                        </div>
                        <!-- PLACA ROTATIVA -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" placeholder="Placa Rotativa" class="form-control" id="placarotativa"
                                    name="placarotativa">
                                <label for="placarotativa">Placa Rotativa</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mt-1">

                        <!-- Tipo de moneda -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="monedaprecio" readonly>
                                <label for="monedaprecio">Moneda Vta.</label>
                            </div>
                        </div>
                        <input type="hidden" name="moneda" id="inputMoneda">

                        <!-- Valor -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" placeholder="Valor" class="form-control" id="valor" name="valor"
                                    readonly>
                                <label for="valor">Valor</label>
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
                                <label for="monedaSelect">Moneda</label>
                            </div>
                        </div>

                        <!-- Tipo de cambio -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="number" step="0.0001" class="form-control" id="tipoCambio"
                                    name="tipoCambio" placeholder="Ej: 3.80">
                                <label for="tipoCambio">Tipo de Cambio</label>
                            </div>
                        </div>
                        <input type="hidden" name="tipoCambio" id="inputTipoCambio">

                        <!-- Valor en soles -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" placeholder="Valor de Moneda" class="form-control" id="valormoneda"
                                    name="valormoneda" readonly>
                                <label for="valormoneda">Valor de Moneda</label>
                            </div>
                        </div>
                        <input type="hidden" name="valorconvertido" id="inputValorConvertido">

                    </div>

                </div>
                <input type="hidden" id="vehiculoMoneda" value="">
            </div>


            <!-- Condiciones de Cotización -->
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <strong>Paso 3:</strong> <span class="fst-italic">
                        Financiamiento
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <!-- Cuota Inicial -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="number" placeholder="Inicial" class="form-control" id="inicial"
                                    name="inicial" step="0.01" min="0" required>
                                <label for="inicial">Inicial</label>
                            </div>
                        </div>

                        <!-- Valor a Financiar -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="valorFinanciar" readonly>
                                <label for="valorFinanciar">Valor a Financiar</label>
                            </div>
                        </div>
                        <input type="hidden" name="valorfinanciar" id="inputValorFinanciar">

                        <!-- Meses -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="number" placeholder="Meses" class="form-control" id="numcuotas"
                                    name="numcuotas" step="3" min="0" required>
                                <label for="numcuotas">Meses</label>
                            </div>
                        </div>

                        <!-- Tasa -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="tasaAnual" step="0.01" name="tasa" min="0"
                                    value="65">
                                <label for="tasaAnual">Tasa anual (%)</label>
                            </div>
                        </div>

                        <!-- Valor mensual -->
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="cuotaMensual" readonly>
                                <label for="cuotaMensual">Valor Mensual</label>
                            </div>
                        </div>
                        <input type="hidden" name="valorcuota" id="inputCuotaMensual">

                        <!-- botón de Cronograma -->
                        <div class="col-md-2">
                            <div class="form-floating h-100">
                                <button class="btn btn-outline-primary w-100 h-100" data-bs-toggle="modal" type="button"
                                    id="btn-generar-cronograma">
                                    Cronograma
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

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
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="modalVehiculosLabel">Seleccionar Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive-style ">
                    <table class="table table-sm table-hover table-bordered mt-2" id="tablaVehiculosModal">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Marca</th>
                                <th>Tipo Vehículo</th>
                                <th>Modelo</th>
                                <th>Versión</th>
                                <th>Condición</th>
                                <th>Color</th>
                                <th>Disponibilidad</th>
                                <th>Placa</th>
                                <th>Placa Rotativa</th>
                                <th>Seleccionar</th>
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
                                    <td><?= htmlspecialchars($v['condicion']) ?></td>
                                    <td><?= htmlspecialchars($v['color'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($v['disponibilidad']) ?></td>
                                    <td><?= $v['placa'] === null ? 'N/A' : htmlspecialchars($v['placa']) ?></td>
                                    <td><?= $v['placarotativa'] === null ? 'N/A' : htmlspecialchars($v['placarotativa']) ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-primary seleccionar-vehiculo-btn"
                                            data-idvehiculo="<?= htmlspecialchars($v['idvehiculo']) ?>"
                                            data-precioventa="<?= htmlspecialchars($v['precioventa']) ?>"
                                            data-moneda="<?= htmlspecialchars($v['moneda']) ?>"
                                            data-descripcion="<?= htmlspecialchars($v['marca'] . ' ' . $v['tipovehiculo'] . ' ' . $v['modelo'] . ' ' . $v['version'] . ' ' . $v['color'] . ' - ' . $v['combustible']) ?>"
                                            data-placa="<?= htmlspecialchars($v['placa'] ?? 'N/A') ?>"
                                            data-placarotativa="<?= htmlspecialchars(strip_tags($v['placarotativa'] ?? 'N/A')) ?>">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <!-- data-moneda="<?= htmlspecialchars($v['moneda']) ?>" -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE CRONOGRAMA -->
<div class="modal fade" id="modalCronograma" tabindex="-1" aria-labelledby="modalCronogramaLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCronogramaLabel">Cronograma de Pagos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-sm btn-success" id="btn-excel" title="Generar cronograma en Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                        Excel
                    </button>
                </div>
                <div class="table-responsive-style p-2">
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

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js" defer></script>
<script>
    let tipoCambioCache = null; //Guardaremos el tipo de cambio

    // Función de utilidad para debouncing
    const debounce = (func, delay) => {
        let timeout;
        return (...args) => {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    };

    const btnExcel = document.querySelector('#btn-excel');
    const tablaCronograma = document.querySelector('#tablaCronograma');

    function generarReporteExcel() {
        const dataTable = $('#tablaCronograma').DataTable();
        dataTable.page.len(-1).draw();
        let ws = XLSX.utils.table_to_sheet(document.getElementById('tablaCronograma'));
        let wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Cronograma");
        XLSX.writeFile(wb, `Cronograma-${$('#nombres').val()}.xlsx`);
        dataTable.page.len(10).draw();
    }

    btnExcel.addEventListener('click', generarReporteExcel);

    function formatDate(date, pad) {
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    }

    document.addEventListener("DOMContentLoaded", () => {
        initDataTable();
        initFechas();
        initEventosCliente();
        initEventosVehiculo();
        initModalRequisitos();
    });

    // $('#modalVehiculos').on('shown.bs.modal', function() {
    //     initDataTable();
    // });

    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#tablaVehiculosModal')) {
            $('#tablaVehiculosModal').DataTable().destroy();
        }

        $('#tablaVehiculosModal').DataTable({
            order: [
                [0, 'desc']
            ],
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, -1],
                [5, 10, 25, "Todos"]
            ],
            scrollX: true,
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

    function initTableModalVehiculo() {
        $('#tablaCronograma').DataTable({
            order: [
                [0, 'asc']
            ],
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, -1],
                [5, 10, 25, "Todos"]
            ],
            scrollX: true,
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

    function initFechas() {
        const hoy = new Date();
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('fechaEmision').value = formatDate(hoy, pad);
        const fin = new Date();
        fin.setDate(hoy.getDate() + 7);
        document.getElementById('fechaCaducidad').value = formatDate(fin, pad);
        document.getElementById('inputVigenciaDias').value = 7;
    }

    document.getElementById('fechaCaducidad').addEventListener('change', () => {
        const em = new Date(document.getElementById('fechaEmision').value);
        const ca = new Date(document.getElementById('fechaCaducidad').value);
        const diff = Math.round((ca - em) / (1000 * 60 * 60 * 24));
        document.getElementById('inputVigenciaDias').value = diff;
    });

    async function initEventosCliente() {
        const btn = document.getElementById('btnBuscarCliente');
        const tipo = document.getElementById('tipoDocumento');
        const docIn = document.getElementById('documento');
        const hid = document.getElementById('idcliente');

        btn.addEventListener('click', async () => {
            const tipoValue = tipo.value;
            const docValue = docIn.value.trim();
            if (!docValue) return alert('Ingresa un número de documento válido.');

            const res = await fetch(`/cotizacion/buscarCliente?tipo=${tipoValue}&doc=${encodeURIComponent(docValue)}`);
            const data = await res.json();

            if (data.notFound) {
                const confirmRedirect = confirm('Cliente no encontrado. ¿Desea ir a registrarlo ahora?');

                if (confirmRedirect) {
                    window.location.href = `/clientes/createpersonclient?dni=${encodeURIComponent(docValue)}`;
                } else {
                    // Limpiar campos si no quiere ir a registrar
                    hid.value = '';
                    document.getElementById('nombres').value = '';
                    document.getElementById('telprimario').value = '';
                    document.getElementById('telalternativo').value = '';
                }
            } else if (data.error) {
                alert(data.error);
            } else {
                hid.value = data.idcliente;
                document.getElementById('nombres').value = `${data.apellidos} ${data.nombres}`.trim();
                document.getElementById('telprimario').value = data.telprimario || '';
                document.getElementById('telalternativo').value = data.telalternativo || '';
            }
        });
    }

    function initEventosVehiculo() {
        $('#tablaVehiculosModal').on('click', '.seleccionar-vehiculo-btn', async function () {
            const d = $(this).data();
            fillPaso2(d);
            clearConversion();
            await actualizarMontos();
            await actualizarFinanciamiento();
            bootstrap.Modal.getInstance($('#modalVehiculos')[0]).hide();
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

        $('#idvehiculo').val(idvehiculo);
        $('#descripcion').val(descripcion);
        $('#placa').val(valorPlaca.toUpperCase());
        $('#placarotativa').val(valorPlacaRotativa.toUpperCase());
        $('#valor').val(valorPrecio);
        $('#monedaprecio').val(moneda === 'USD' ? 'Dólares' : 'Soles');
        $('#vehiculoMoneda').val(moneda);

        const $monedaSelect = $('#monedaSelect');
        if (moneda === 'PEN') {
            $monedaSelect.val('PEN').prop('disabled', true);
        } else {
            $monedaSelect.prop('disabled', false);
        }
    }

    function clearConversion() {
        $('#tipoCambio, #valormoneda').val('');
    }

    async function fetchTipoCambio(force = false) {
        if (!force && tipoCambioCache !== null) {
            return tipoCambioCache; // Usa caché si ya se tiene
        }

        try {
            const res = await fetch('/cotizacion/tipo-cambio');
            if (!res.ok) throw new Error(res.statusText);
            const {
                tipo_cambio
            } = await res.json();
            tipoCambioCache = parseFloat(tipo_cambio) || 1; // Guarda en caché
            return tipoCambioCache;
        } catch (err) {
            console.error('Error al obtener tipo de cambio:', err);
            return 1;
        }
    }


    async function actualizarMontos() {
        const precioOriginal = parseFloat($('#valor').val()) || 0;
        const vehMoneda = $('#vehiculoMoneda').val();
        const cotMoneda = $('#monedaSelect').val();
        let tipoCam = 1;

        const idvehiculo = $('#idvehiculo').val();
        if (!idvehiculo) {
            $('#tipoCambio').val('');
            $('#valormoneda').val('');
            $('#inputPrecioventa').val('');
            $('#inputMoneda').val('');
            $('#inputTipoCambio').val('');
            $('#inputValorConvertido').val('');
            return;
        }

        if (vehMoneda !== cotMoneda) {
            tipoCam = await fetchTipoCambio(); // Solo si necesario
            $('#tipoCambio').val(tipoCam.toFixed(4));
        } else {
            $('#tipoCambio').val('');
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
        $('#valormoneda').val(precioFinal);
        $('#inputPrecioventa').val(precioFinal);
        $('#inputMoneda').val(cotMoneda);
        $('#inputTipoCambio').val(tipoCam);
        $('#inputValorConvertido').val(precioFinal);
    }


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

    // APLICAMOS DEBOUNCE AQUÍ
    const debouncedActualizarMontosFinanciamiento = debounce(async () => {
        await actualizarMontos();
        await actualizarFinanciamiento();
    }, 280);

    $('#inicial, #numcuotas, #tasaAnual, #tipoCambio, #monedaSelect')
        .on('input change', debouncedActualizarMontosFinanciamiento);

    $('#tasaAnual').val(65);

    $(document).ready(async () => {
        await actualizarMontos();
        await actualizarFinanciamiento();
    });

    function initModalRequisitos() {
        $('#modalRequisitos').on('show.bs.modal', async () => {
            const id = $('#modalidad').val();
            const ul = $('#listaRequisitos').empty();
            if (!id) return ul.append('<li class="list-group-item text-muted">Selecciona primero una modalidad.</li>');
            try {
                const resp = await fetch(`/cotizaciones/requisitos/${id}`);
                const arr = await resp.json();
                if (!arr.length) ul.append('<li class="list-group-item text-muted">No hay requisitos definidos.</li>');
                else arr.forEach(i => ul.append(`<li class="list-group-item">${i.requisito}</li>`));
            } catch {
                ul.append('<li class="list-group-item text-danger">Error cargando requisitos.</li>');
            }
        });
    }

    (function attachCotizacionConfirm() {
        const formCot = document.getElementById('formCotizacion');
        if (!formCot) return;

        formCot.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitButton = formCot.querySelector('button[type="submit"]');

            const confirmado = await ask('¿Desea confirmar el registro de esta cotización?', '¿Registrar cotización?');
            if (!confirmado) return;

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Registrando...';
            }
            formCot.submit();
        });
    })();


    document.getElementById('btn-generar-cronograma').addEventListener('click', async (e) => {
        e.preventDefault();

        const importeTotal = parseFloat($('#inputValorConvertido').val()) || 0;
        const inicial = parseFloat($('#inicial').val()) || 0;
        const meses = parseInt($('#numcuotas').val(), 10) || 0;

        if (meses <= 0 || (importeTotal - inicial) <= 0) {
            alert('Ingresa valores válidos para calcular el cronograma.');
            return;
        }

        try {
            const res = await fetch(`/api/cotizacion/generar-cronograma/${importeTotal}/${inicial}/${meses}`);
            if (!res.ok) throw new Error(res.statusText);
            const cronograma = await res.json();

            if ($.fn.DataTable.isDataTable('#tablaCronograma')) {
                $('#tablaCronograma').DataTable().destroy();
            }

            const tbody = document.getElementById('cuerpoTablaCronograma');
            tbody.innerHTML = '';

            let totalInteres = 0;
            let totalAbono = 0;
            let totalCuota = 0;

            cronograma.forEach(pago => {
                const row = tbody.insertRow();
                row.insertCell(0).innerText = pago.item;
                row.insertCell(1).innerText = pago.fecha_pago;
                row.insertCell(2).innerText = `S/ ${pago.interes.toFixed(2)}`;
                row.insertCell(3).innerText = `S/ ${pago.abono_capital.toFixed(2)}`;
                row.insertCell(4).innerText = `S/ ${pago.valor_cuota.toFixed(2)}`;
                row.insertCell(5).innerText = `S/ ${pago.saldo_capital.toFixed(2)}`;

                totalInteres += pago.interes;
                totalAbono += pago.abono_capital;
                totalCuota += pago.valor_cuota;
            });

            document.getElementById('totalInteres').innerText = `S/ ${totalInteres.toFixed(2)}`;
            document.getElementById('totalAbono').innerText = `S/ ${totalAbono.toFixed(2)}`;
            document.getElementById('totalCuota').innerText = `S/ ${totalCuota.toFixed(2)}`;

            initTableModalVehiculo();

            const modalCronograma = new bootstrap.Modal(document.getElementById('modalCronograma'));
            modalCronograma.show();

        } catch (err) {

            showToast('Hubo un error al generar el cronograma de pagos.', 'ERROR', 1200);
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        const tipo = document.getElementById("tipoDocumento");
        const doc = document.getElementById("documento");

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

        // Solo números
        doc.addEventListener("input", () => {
            doc.value = doc.value.replace(/\D/g, "");
        });
    });

</script>