<?php include __DIR__ . '/../layout/header.php'; ?>

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
                <a href="/cotizaciones/listar" class="">[ Mostrar Lista ]</a>
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
                                    title="Incrementa el año del modelo y lo guarda en la base de datos"><i
                                        class="bi bi-search"></i></button>
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
                                    name="inicial" step="1" required>
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
                                <input type="number" class="form-control" id="tasaAnual" step="0.01" name="tasa"
                                    min="0">
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
                                <button class="btn btn-outline-primary w-100 h-100" data-bs-toggle="modal"
                                    data-bs-target="#modalCronograma" type="button">
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
            <div class="modal-header bg-yonda text-white">
                <h5 class="modal-title" id="modalVehiculosLabel">Seleccionar Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-hover table-bordered" id="tablaVehiculosModal">
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
                                <td><?= htmlspecialchars($v['color']) ?></td>
                                <td><?= htmlspecialchars($v['disponibilidad']) ?></td>
                                <td><?= htmlspecialchars($v['placa']) ?></td>
                                <td><?= htmlspecialchars($v['placarotativa']) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary seleccionar-vehiculo-btn"
                                        data-idvehiculo="<?= htmlspecialchars($v['idvehiculo']) ?>"
                                        data-precioventa="<?= htmlspecialchars($v['precioventa']) ?>"
                                        data-moneda="<?= htmlspecialchars($v['moneda']) ?>"
                                        data-descripcion="<?= htmlspecialchars($v['marca'] . ' ' . $v['tipovehiculo'] . ' ' . $v['modelo'] . ' ' . $v['version'] . ' ' . $v['color'] . ' - ' . $v['combustible']) ?>"
                                        data-placa="<?= htmlspecialchars($v['placa']) ?>"
                                        data-placarotativa="<?= htmlspecialchars($v['placarotativa']) ?>">
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

<!-- MODAL DEL CRONOGRAMA DE PAGOS -->
<div class="modal fade" id="modalCronograma" tabindex="-1" aria-labelledby="modalCronogramaLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-yonda">
                <h5 class="modal-title" id="modalCronogramaLabel">Cronograma de Pagos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <ul id="listaPagos" class="list-group">
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

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script>

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

    // Inicializa DataTable
    function initDataTable() {
        $('#tablaVehiculosModal').DataTable({
            order: [[0, 'desc']],
            pagingType: 'full_numbers',
            pageLength: 10,
            lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "Todos"]],
            responsive: true,
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

        // Emisión hoy
        document.getElementById('fechaEmision').value = formatDate(hoy, pad);
        // Caducidad +7 días
        const fin = new Date();
        fin.setDate(hoy.getDate() + 7);
        document.getElementById('fechaCaducidad').value = formatDate(fin, pad);
        // Grabo vigencia
        document.getElementById('inputVigenciaDias').value = 7;
    }


    // Si el usuario pudiera cambiar fechas manualmente:
    document.getElementById('fechaCaducidad').addEventListener('change', () => {
        const em = new Date(document.getElementById('fechaEmision').value);
        const ca = new Date(document.getElementById('fechaCaducidad').value);
        const diff = Math.round((ca - em) / (1000 * 60 * 60 * 24));
        document.getElementById('inputVigenciaDias').value = diff;
    });

    // Búsqueda de cliente por DNI/RUC
    async function initEventosCliente() {
        const btn = document.getElementById('btnBuscarCliente');
        const tipo = document.getElementById('tipoDocumento');
        const docIn = document.getElementById('documento');
        const hid = document.getElementById('idcliente');

        btn.addEventListener('click', async () => {
            const tipoValue = tipo.value;           // 'dni' o 'ruc'
            const docValue = docIn.value.trim();
            if (!docValue) {
                return alert('Ingresa un número de documento válido.');
            }

            const res = await fetch(`/cotizacion/buscarCliente?tipo=${tipoValue}&doc=${encodeURIComponent(docValue)}`);
            const data = await res.json();

            if (data.notFound) {
                alert('Cliente no encontrado. Debes registrarlo primero en Clientes.');
                hid.value = '';
                document.getElementById('nombres').value = '';
                document.getElementById('telprimario').value = '';
                document.getElementById('telalternativo').value = '';
            }
            else if (data.error) {
                alert(data.error);
            }
            else {
                // Hay cliente: guardamos su id y rellenamos datos
                hid.value = data.idcliente;
                document.getElementById('nombres').value = `${data.apellidos} ${data.nombres}`.trim();
                document.getElementById('telprimario').value = data.telprimario || '';
                document.getElementById('telalternativo').value = data.telalternativo || '';
            }
        });
    }

    // Selección de vehículo y cálculo de montos
    function initEventosVehiculo() {
        $('#tablaVehiculosModal').on('click', '.seleccionar-vehiculo-btn', async function () {
            const d = $(this).data();
            fillPaso2(d);
            clearConversion();
            if (typeof actualizarMontos === 'function') await actualizarMontos();
            bootstrap.Modal.getInstance($('#modalVehiculos')[0]).hide();
        });
        if (typeof actualizarMontos === 'function') {
            actualizarMontos();
        }
    }

    function fillPaso2({ idvehiculo, descripcion, placa, placarotativa, precioventa, moneda }) {
        $('#idvehiculo').val(idvehiculo);
        $('#descripcion').val(descripcion);
        $('#placa').val(placa);
        $('#placarotativa').val(placarotativa);
        $('#valor').val(Number(precioventa).toFixed(2));
        $('#monedaprecio').val(moneda === 'USD' ? '$' : 'S/.');
        $('#vehiculoMoneda').val(moneda);

        // Cambiado: referencia al select correcto
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

    async function actualizarMontos() {
        const precioOriginal = parseFloat($('#valor').val()) || 0;
        const vehMoneda = $('#vehiculoMoneda').val();    // 'USD' o 'PEN'
        const cotMoneda = $('#monedaSelect').val();      // divisa elegida por usuario
        let tipoCam = parseFloat($('#tipoCambio').val()) || 1;
        let precioFinal = precioOriginal;

        // si la divisa del vehículo y la de cotización difieren, convierto:
        if (vehMoneda !== cotMoneda) {
            if (vehMoneda === 'USD' && cotMoneda === 'PEN') {
                precioFinal = precioOriginal * tipoCam;
            } else if (vehMoneda === 'PEN' && cotMoneda === 'USD') {
                precioFinal = precioOriginal / tipoCam;
            }
        }

        // redondeo a 2 decimales
        precioFinal = Number(precioFinal.toFixed(2));

        // relleno solo-lectura
        $('#valormoneda').val(precioFinal);
        // relleno hidden para enviar al servidor
        $('#inputPrecioventa').val(precioFinal);
        $('#inputMoneda').val(cotMoneda);
        $('#inputTipoCambio').val(tipoCam);
        $('#inputValorConvertido').val(precioFinal);
    }

    // Disparadores: cuando cambie la moneda elegida o el tipo de cambio:
    $('#monedaSelect, #tipoCambio').on('change', actualizarMontos);

    function actualizarFinanciamiento() {
        const inicial = parseFloat($('#inicial').val()) || 0;
        const precioFinal = parseFloat($('#inputPrecioventa').val()) || 0;
        const valorF = Math.max(0, precioFinal - inicial);
        $('#valorFinanciar').val(valorF.toFixed(2));
        $('#inputValorFinanciar').val(valorF.toFixed(2));

        // Ahora calculo cuota mensual
        const n = parseInt($('#numcuotas').val(), 10) || 0;
        const tasaA = parseFloat($('#tasaAnual').val()) || 0;
        let cuota = 0;

        if (n > 0) {
            if (tasaA > 0) {
                const r = (tasaA / 100) / 12;
                cuota = valorF * (r / (1 - Math.pow(1 + r, -n)));
            } else {
                cuota = valorF / n;
            }
        }

        cuota = cuota || 0;
        $('#cuotaMensual').val(cuota.toFixed(2));
        $('#inputCuotaMensual').val(cuota.toFixed(2));
    }

    // Disparadores Paso 3:
    $('#inicial, #numcuotas, #tasaAnual').on('input change', actualizarFinanciamiento);

    //cuando se termine de recalcular montos del Paso 2, vuelve a invocar:
    if (typeof actualizarFinanciamiento === 'function') actualizarFinanciamiento();

    // Cargar los requisitos segun la modalidad
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


</script>