<?php include __DIR__ . '/../layout/header.php'; ?>

<!-- <?php var_dump($ingresos_efectivo) ?>
<?php var_dump($egresos_dia) ?>
<?php var_dump($arqueos) ?> -->
<style>
    .card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #2196F3;
        color: white;
        text-align: center;
    }

    .modal-content {
        border-radius: 1rem;
    }

    .alert-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        min-width: 300px;
    }

    .btn-group .btn-active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
</style>



<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="toast-message" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toast-body-message"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="container-fluid mt-2">
    <div class="alert alert-info" role="alert" style="border-left: 4px solid #3498db; border-radius: 0 8px 8px 0;">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Arqueo Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <button type="button" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#arqueoModal">
                    <i class="fas fa-plus me-1"></i> Registrar Nuevo Arqueo
                </button>
                <?php if ($entregado !== 'S'): ?>
                    <button type="button" class="btn btn-success btn-sm" id="btn-entregar-seleccionados" disabled>
                        <i class="fas fa-handshake me-1"></i> Entregar Seleccionados
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-file-earmark-spreadsheet-fill"> Historial de Arqueos</i></h5>
            <div class="btn-group" role="group">
                <?php $estadoActual = $entregado ?? 'N'; ?>
                <a href="/arqueoCaja/listar/N" class="btn btn-outline-light <?= $estadoActual === 'N' ? 'active' : '' ?>">
                    <i class="fas fa-box-open"></i> Sin Entregar
                </a>
                <a href="/arqueo/entregados" class="btn btn-outline-light <?= $estadoActual === 'S' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle"></i> Entregados
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <?php if ($entregado === 'N'): ?>
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="selectAllArqueos"></th>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Monto Inicial <br><span class="badge bg-primary text-white">(Inicio del día)</span></th>
                                <th>Monto Final <br><span class="badge bg-warning text-white">(Último arqueo)</span></th>
                                <th>Diferencia <br><span class="badge bg-success text-white">(Final del día)</span></th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($arqueos) && is_array($arqueos) && !empty($arqueos)): ?>
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($arqueos as $arqueo): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                class="arqueo-checkbox"
                                                data-ids="<?= htmlspecialchars($arqueo['ids_arqueos_del_dia']) ?>"
                                                data-monto="<?= htmlspecialchars($arqueo['monto_fisico_dia']) ?>">
                                        </td>
                                        <td><?= $numeroFila++ ?></td>
                                        <td><i class="far fa-calendar-alt text-muted me-1"></i><?= htmlspecialchars(date('d/m/Y', strtotime($arqueo['fecha']))); ?></td>
                                        <td>S/ <?= number_format($arqueo['saldo_inicial_dia'], 2); ?></td>
                                        <td>S/ <?= number_format($arqueo['monto_fisico_dia'], 2); ?></td>
                                        <td>
                                            <?php
                                            // USAMOS EL CAMPO DE DIFERENCIA FINAL
                                            $diferencia = $arqueo['diferencia_final'];
                                            if ($diferencia > 0) {
                                                echo '<span class="text-warning fw-bold">S/ ' . number_format($diferencia, 2) . '</span>';
                                            } elseif ($diferencia < 0) {
                                                echo '<span class="text-danger fw-bold">S/ ' . number_format($diferencia, 2) . '</span>';
                                            } else {
                                                echo '<span class="text-success fw-bold">S/ ' . number_format($diferencia, 2) . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Pendiente</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-danger btn-pdf-arqueo" title="Generar PDF" data-fecha='<?= $arqueo['fecha'] ?>' data-ids=<?= htmlspecialchars($arqueo['ids_arqueos_del_dia']) ?>>
                                                <i class="fas fa-file-pdf"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success btn-entregar-singular"
                                                title="Marcar como Entregado"
                                                data-ids="<?= htmlspecialchars($arqueo['ids_arqueos_del_dia']) ?>"
                                                data-monto="<?= htmlspecialchars($arqueo['monto_fisico_dia']) ?>">
                                                <i class="fas fa-handshake"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay arqueos registrados sin entregar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="arqueoModal" tabindex="-1" aria-labelledby="arqueoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="arqueoModalLabel"><i class="fas fa-check-double me-2"></i>Arqueo de Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <div class=" p-3 rounded bg-primary">
                            <h6 class="text-white fw-bold">Saldo Inicial</h6>
                            <h4 class="fw-bold text-white" id="modalMontoInicial">S/ <?= number_format($saldo_inicial, 2); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" p-3 rounded bg-success">
                            <h6 class="text-white fw-bold">Total Ingresos</h6>
                            <h4 class="fw-bold text-white" id="modalTotalIngresos">+S/ <?= number_format($ingresos_efectivo + $ingresos_digital, 2); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" p-3 rounded bg-danger">
                            <h6 class="text-white fw-bold">Total Egresos</h6>
                            <h4 class="fw-bold text-white" id="modalTotalEgresos">-S/ <?= number_format($egresos_dia, 2); ?></h4>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mb-3 text-center">
                    <h4 class="text-info fw-bold">Saldo Teórico Esperado</h4>
                    <h2 class="fw-bold" id="modalMontoTeorico">S/ <?= number_format($monto_teorico, 2); ?></h2>
                </div>
                <form id="arqueoForm" data-es-primer-arqueo="<?= htmlspecialchars($es_primer_arqueo ? 'true' : 'false') ?>">

                    <input type="hidden" name="ingresos_efectivo" value="<?= htmlspecialchars($ingresos_efectivo) ?>">
                    <input type="hidden" name="ingresos_digital" value="<?= htmlspecialchars($ingresos_digital) ?>">
                    <input type="hidden" name="egresos_dia" value="<?= htmlspecialchars($egresos_dia) ?>">
                    <input type="hidden" name="monto_teorico" id="monto-teorico-input" value="<?= htmlspecialchars($monto_teorico) ?>">
                    <input type="hidden" name="diferencia" id="diferencia-input">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fechaInicio" class="form-label fw-bold">Hora de Inicio <span class="text-danger fw-bold">* </span> </label>
                            <input type="time" class="form-control" id="fechaInicio" name="hora_inicio" value="08:00"  required>
                        </div>
                        <div class="col-md-6">
                            <label for="fechaFin" class="form-label fw-bold">Hora de Fin <span class="text-danger fw-bold">* </span> </label>
                            <input type="time" class="form-control" id="fechaFin" name="hora_fin" step="1" required>
                        </div>
                    </div>

                    <div class="row mb-4">

                        <div class="col-md-6">
                            <label for="saldoInicial" class="form-label fw-bold">Saldo Inicial</label>
                            <input
                                type="number"
                                step="0.01"
                                class="form-control form-control-lg"
                                id="saldoInicial"
                                name="saldo_inicial"
                                value="<?= htmlspecialchars($saldo_inicial) ?>"

                                required>

                        </div>
                        <div class="col-md-6">
                            <label for="montoFisico" class="form-label fw-bold">Monto Físico Contado <span class="text-danger fw-bold">* </span> </label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white fw-bold">S/</span>
                                <input type="number" step="0.01" class="form-control form-control-lg" id="montoFisico" name="monto_fisico" placeholder="Ingresa el monto que contaste" required>
                            </div>
                            <div class="form-text">Billetes y monedas en tu caja.</div>
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="observaciones-group">
                        <label for="observaciones" class="form-label fw-bold text-danger">Observaciones (Requerido para Faltante)</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Describe el motivo del faltante."></textarea>
                    </div>
                    <div class="alert alert-info text-center mt-4" id="alert-diferencia">
                        <h5 class="mb-0">Diferencia: <span id="diferencia-span">S/ 0.00</span></h5>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-sm btn-outline-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-outline-primary bt-sm">Finalizar Arqueo</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="entregaModal" tabindex="-1" aria-labelledby="entregaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="entregaModalLabel"><i class="fas fa-handshake me-2"></i>Registrar Entrega de Dinero</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="entregaForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipoDestino" class="form-label">Destino de la Entrega <span class="text-danger fw-bold">*</span></label>
                        <select class="form-select" id="tipoDestino" name="tipodestino" required>
                            <option value="Gerente">Gerente</option>
                            <option value="Deposito">Depósito Bancario</option>
                        </select>
                    </div>
                    <div id="gerenteDestinoGroup" class="mb-3">
                        <label for="selectDestino" class="form-label">Seleccionar Gerente <span class="text-danger fw-bold">*</span></label>
                        <select class="form-select" id="selectDestino" name="iddestino" required></select>
                    </div>

                    <div id="depositoDestinoGroup" class="d-none">
                        <label class="form-label">Detalle de Depósitos <span class="text-danger fw-bold">*</span></label>
                        <div id="deposito-fields-container">
                        </div>
                        <div class="d-grid gap-2 mt-2 mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addDepositoField">
                                <i class="fas fa-plus"></i> Agregar Depósito
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="montoTotalEntrega" class="form-label">Monto Total a Entregar</label>
                        <input type="number" step="0.01" class="form-control" id="montoTotalEntrega" name="montoentregado" readonly required>
                    </div>
                    <div class="mb-3">
                        <label for="observacionesEntrega" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observacionesEntrega" name="observaciones" rows="3"></textarea>
                    </div>
                    <input type="hidden" id="idsArqueosEntrega" name="ids_arqueos">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-outline-primary" id="btn-entregar-dinero"">Confirmar Entrega</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<script src=" https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
                        <script src="/assets/js/logoBase64.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>

                        <script>
                            document.addEventListener('DOMContentLoaded', async () => {

                                const form = document.getElementById('arqueoForm');
                                const saldoInicialInput = document.getElementById('saldoInicial');
                                const montoFisicoInput = document.getElementById('montoFisico');
                                const montoTeoricoH2 = document.getElementById('modalMontoTeorico');
                                const montoTeoricoInput = document.getElementById('monto-teorico-input');
                                const diferenciaSpan = document.getElementById('diferencia-span');
                                const diferenciaInput = document.getElementById('diferencia-input');
                                const alertDiv = document.getElementById('alert-diferencia');
                                const observacionesGroup = document.getElementById('observaciones-group');
                                const observacionesInput = document.getElementById('observaciones');

                                const saldoInicial = parseFloat("<?= htmlspecialchars($saldo_inicial) ?>");
                                const ingresosEfectivo = parseFloat("<?= htmlspecialchars($ingresos_efectivo) ?>");
                                const egresosDia = parseFloat("<?= htmlspecialchars($egresos_dia) ?>");



                                const btnEntregarSeleccionados = document.getElementById('btn-entregar-seleccionados');
                                const checkboxes = document.querySelectorAll('.arqueo-checkbox');
                                const selectAllCheckbox = document.getElementById('selectAllArqueos');
                                const entregaModal = new bootstrap.Modal(document.getElementById('entregaModal'));
                                const tipoDestinoSelect = document.getElementById('tipoDestino');
                                const gerenteDestinoGroup = document.getElementById('gerenteDestinoGroup');
                                const depositoDestinoGroup = document.getElementById('depositoDestinoGroup');
                                const selectDestino = document.getElementById('selectDestino');
                                const addDepositoFieldBtn = document.getElementById('addDepositoField');
                                const depositoFieldsContainer = document.getElementById('deposito-fields-container');
                                const montoTotalEntregaInput = document.getElementById('montoTotalEntrega');
                                const idsArqueosEntregaInput = document.getElementById('idsArqueosEntrega');
                                const entregaForm = document.getElementById('entregaForm');
                                const submitButton = entregaForm.querySelector('button[type="submit"]');

                           
                                const inputHora = document.getElementById("fechaFin");
                                const ahora = new Date();
                                const horas = String(ahora.getHours()).padStart(2, '0');
                                const minutos = String(ahora.getMinutes()).padStart(2, '0');
                                const segundos = String(ahora.getSeconds()).padStart(2, '0');
                                inputHora.value = `${horas}:${minutos}:${segundos}`;
                                const pdfButtons = document.querySelectorAll('.btn-pdf-arqueo');

                                const esPrimerArqueo = form.dataset.esPrimerArqueo === 'true';

                                function calcularDiferencia() {
                                    const montoFisico = parseFloat(montoFisicoInput.value) || 0;
                                    let montoTeoricoCalculado;

                                    if (esPrimerArqueo) {
                                        const saldoInicialUser = parseFloat(saldoInicialInput.value) || 0;
                                        montoTeoricoCalculado = saldoInicialUser + ingresosEfectivo - egresosDia;
                                    } else {
                                        montoTeoricoCalculado = saldoInicial + ingresosEfectivo - egresosDia;
                                    }

                                    const diferencia = montoFisico - montoTeoricoCalculado;

                                    montoTeoricoH2.textContent = `S/ ${montoTeoricoCalculado.toFixed(2)}`;
                                    montoTeoricoInput.value = montoTeoricoCalculado.toFixed(2);
                                    diferenciaSpan.textContent = `S/ ${diferencia.toFixed(2)}`;
                                    diferenciaInput.value = diferencia.toFixed(2);

                                    alertDiv.classList.remove('alert-info', 'alert-warning', 'alert-danger', 'alert-success');
                                    if (diferencia > 0) {
                                        alertDiv.classList.add('alert-warning');
                                        observacionesGroup.classList.add('d-none');
                                        observacionesInput.removeAttribute('required');
                                    } else if (diferencia < 0) {
                                        alertDiv.classList.add('alert-danger');
                                        observacionesGroup.classList.remove('d-none');
                                        observacionesInput.setAttribute('required', 'required');
                                    } else {
                                        alertDiv.classList.add('alert-success');
                                        observacionesGroup.classList.add('d-none');
                                        observacionesInput.removeAttribute('required');
                                    }
                                }

                                // calcularDiferencia();
                                saldoInicialInput.addEventListener('input', calcularDiferencia);
                                montoFisicoInput.addEventListener('input', calcularDiferencia);

                                form.addEventListener('submit', async (event) => {
                                    event.preventDefault();
                                    if (await ask('¿Confirmar arqueo?', 'Confirmar')) {
                                        const formData = new FormData(form);
                                        const submitButton = form.querySelector('button[type="submit"]');
                                        submitButton.disabled = true;
                                        showToast('Registrando arqueo...', 'bg-primary');

                                        try {
                                            const response = await fetch('/arqueocaja/store', {
                                                method: 'POST',
                                                body: formData,
                                            });

                                            if (!response.ok) {
                                                throw new Error(`Error HTTP: ${response.status}`);
                                            }

                                            const data = await response.json();

                                            if (data.success) {
                                                showToast(data.message, 'bg-success');
                                                setTimeout(() => window.location.reload(), 1000);
                                            } else {
                                                showToast(data.message, 'bg-danger');
                                            }
                                        } catch (error) {

                                            showToast('Ocurrió un error inesperado al registrar el arqueo.', 'bg-danger');
                                            console.error('Error en el envío de arqueo:', error);
                                        } finally {

                                            submitButton.disabled = false;
                                        }
                                    }
                                });



                                function updateEntregarButton() {
                                    const checkedCount = document.querySelectorAll('.arqueo-checkbox:checked').length;
                                    btnEntregarSeleccionados.disabled = checkedCount === 0;
                                }

                                checkboxes.forEach(checkbox => {
                                    checkbox.addEventListener('change', updateEntregarButton);
                                });

                                selectAllCheckbox.addEventListener('change', function() {
                                    checkboxes.forEach(checkbox => {
                                        checkbox.checked = this.checked;
                                    });
                                    updateEntregarButton();
                                });

                                document.querySelectorAll('.btn-entregar-singular').forEach(button => {
                                    button.addEventListener('click', function() {
                                        const id = this.dataset.ids;
                                        const monto = this.dataset.monto;
                                        idsArqueosEntregaInput.value = id;
                                        montoTotalEntregaInput.value = parseFloat(monto).toFixed(2);
                                        tipoDestinoSelect.value = 'Gerente';
                                        cargarDestinos();
                                        entregaModal.show();
                                    });
                                });


                            
                                document.getElementById('btn-entregar-seleccionados').addEventListener('click', () => {
                                    const checkboxes = document.querySelectorAll('.arqueo-checkbox:checked');
                                    const ids = Array.from(checkboxes).map(cb => cb.dataset.ids).join(',');
                                    const montoTotal = Array.from(checkboxes).reduce((sum, cb) => sum + parseFloat(cb.dataset.monto), 0);

                                    document.getElementById('idsArqueosEntrega').value = ids; 
                                    document.getElementById('montoTotalEntrega').value = montoTotal.toFixed(2);

                                    
                                    const entregaModal = new bootstrap.Modal(document.getElementById('entregaModal'));
                                    entregaModal.show();
                                });


                                // Carga los destinos 
                                async function cargarDestinos() {
                                    const tipo = tipoDestinoSelect.value;
                                    selectDestino.innerHTML = '';

                                    if (tipo === 'Gerente') {
                                        gerenteDestinoGroup.classList.remove('d-none');
                                        depositoDestinoGroup.classList.add('d-none');
                                        selectDestino.setAttribute('required', 'required');
                                        depositoFieldsContainer.innerHTML = '';
                                    } else {
                                        gerenteDestinoGroup.classList.add('d-none');
                                        depositoDestinoGroup.classList.remove('d-none');
                                        selectDestino.removeAttribute('required');
                                        depositoFieldsContainer.innerHTML = '';
                                        addDepositoField();
                                    }

                                    try {

                                        const response = await fetch('/arqueocaja/destinos/' + tipo);
                                        const data = await response.json();

                                        if (data.success) {
                                            if (tipo === 'Gerente') {
                                                data.destinos.forEach(destino => {
                                                    const option = document.createElement('option');
                                                    option.value = destino.id;
                                                    option.textContent = destino.nombre;
                                                    selectDestino.appendChild(option);
                                                });
                                            }
                                        } else {
                                            showToast('Error al cargar los destinos.', 'bg-danger');
                                        }



                                    } catch (error) {
                                        console.log('ERROR:', error)
                                    }
                                }

                                // Añade un nuevo campo de depósito al formulario

                                async function addDepositoField() {
                                    try {

                                        const response = await fetch('/arqueocaja/destinos/Deposito');

                                        if (!response.ok) {

                                            throw new Error(`Error HTTP: ${response.status}`);
                                        }


                                        const data = await response.json();


                                        if (data.success) {
                                            const optionsHtml = data.destinos.map(d => `<option value="${d.id}">${d.nombre}</option>`).join('');
                                            const fieldId = `deposito-${Date.now()}`;
                                            const newField = document.createElement('div');

                                            newField.classList.add('input-group', 'mb-2', 'deposito-field');
                                            newField.innerHTML = `
                                                                <select class="form-select" name="destinos_multiples[${fieldId}][iddestino]" required width="500">${optionsHtml}</select>
                                                                <span class="input-group-text bg-success text-white">S/</span>
                                                                <input type="number" step="0.01" class="form-control deposito-monto" name="destinos_multiples[${fieldId}][monto]" placeholder="Monto" required>
                                                                <button type="button" class="btn btn-danger remove-deposito-field"><i class="fas fa-trash"></i></button>
                                                            `;


                                            depositoFieldsContainer.appendChild(newField);

                                            newField.querySelector('.deposito-monto').addEventListener('input', updateAndValidateDeposits);
                                            newField.querySelector('.remove-deposito-field').addEventListener('click', function() {
                                                newField.remove();

                                                updateAndValidateDeposits();
                                            });
                                        } else {

                                            showToast('Error al cargar opciones de depósito (API).', 'bg-danger');
                                        }

                                    } catch (error) {

                                        showToast('Error de red o de servidor al cargar opciones de depósito.', 'bg-danger');
                                        console.error('Error en addDepositoField:', error);
                                    }
                                }

                                // Nueva función que suma y valida sin modificar el monto de referencia
                                function updateAndValidateDeposits() {
                                    const totalDepositos = Array.from(document.querySelectorAll('.deposito-monto'))
                                        .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);

                                    const montoTotalEntrega = parseFloat(montoTotalEntregaInput.value);

                                    if (Math.abs(totalDepositos - montoTotalEntrega) > 0.01) {
                                        showToast('La suma de los depósitos no coincide con el monto total a entregar. Por favor, ajústelo.', 'bg-warning');
                                        submitButton.disabled = true;
                                    } else {
                                        showToast('La suma de los depósitos coincide. Puede continuar.', 'bg-success');
                                        submitButton.disabled = false;
                                    }
                                }

                                tipoDestinoSelect.addEventListener('change', cargarDestinos);
                                addDepositoFieldBtn.addEventListener('click', addDepositoField);


                                entregaForm.addEventListener('submit', async (event) => {
                                    event.preventDefault();

                                    const tipoDestino = tipoDestinoSelect.value;
                                    const submitButton = document.getElementById('btn-entregar-dinero');
                                    submitButton.disabled = true; // Deshabilita al inicio

                                    const jsonData = {};
                                    const data = new FormData(entregaForm);
                                    data.forEach((value, key) => {
                                        jsonData[key] = value;
                                    });

                                    // Validaciones para DEPOSITO
                                    if (tipoDestino === 'Deposito') {
                                        
                                        delete jsonData.iddestino;

                                        const totalDepositos = Array.from(document.querySelectorAll('.deposito-monto'))
                                            .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
                                        const montoTotalEntrega = parseFloat(montoTotalEntregaInput.value);

                                        if (Math.abs(totalDepositos - montoTotalEntrega) > 0.01) {
                                            showToast('Error: La suma de los depósitos no coincide con el monto total a entregar.', 'bg-danger');
                                            submitButton.disabled = false;
                                            return;
                                        }
                                    }

                              
                                    const depositoFields = document.querySelectorAll('.deposito-field');
                                    if (depositoFields.length > 0 && tipoDestino === 'Deposito') { 
                                        jsonData.destinos_multiples = [];
                                        depositoFields.forEach(field => {
                                            const id = field.querySelector('select').value;
                                            const monto = parseFloat(field.querySelector('input').value) || 0;
                                            if (id && monto) {
                                                jsonData.destinos_multiples.push({
                                                    iddestino: id,
                                                    monto: monto
                                                });
                                            }
                                        });
                                        if (jsonData.destinos_multiples.length === 0) {
                                            showToast('Se requiere al menos un destino de depósito válido.', 'bg-danger');
                                            submitButton.disabled = false;
                                            return;
                                        }
                                    }


                                    if (await ask('¿Confimar entrega?', 'Confirmar')) {
                                        console.log(jsonData);
                                        try {
                                            const response = await fetch('/arqueocaja/entregar', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify(jsonData),
                                            });

                                            if (!response.ok) {

                                                throw new Error(`Error de servidor (${response.status} ${response.statusText})`);
                                            }

                                            const apiResponse = await response.json();

                                            if (apiResponse.success) {

                                                showToast(apiResponse.message, 'bg-success');
                                                await new Promise(resolve => setTimeout(resolve, 2800));

                                                window.location.reload();
                                            } else {
                                                showToast(apiResponse.message, 'bg-danger');
                                            }

                                        } catch (error) {
                                            showToast('Ocurrió un error de red, de servidor o de procesamiento.', 'bg-danger');
                                            console.error('Error:', error);
                                        } finally {

                                            submitButton.disabled = false;
                                        }
                                    } else {
                                        submitButton.disabled = false;
                                    }
                                });



                                // Lógica del Toast
                                function showToast(message, type) {
                                    const toastElement = document.getElementById('toast-message');
                                    const toastBody = document.getElementById('toast-body-message');
                                    toastBody.textContent = message;
                                    toastElement.className = `toast align-items-center text-white border-0 ${type}`;
                                    const toast = new bootstrap.Toast(toastElement);
                                    toast.show();
                                }


                                pdfButtons.forEach(button => {
                                    button.addEventListener('click', async (e) => {
                                        const btn = e.currentTarget;

                                        const ids_arqueo = btn.dataset.ids;

                                
                                        const fecha = btn.dataset.fecha;

                                        if (!ids_arqueo) {
                                            alert('Error: IDs de arqueo no encontrados.');
                                            return;
                                        }

                                        btn.disabled = true;
                                        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                                        try {
                                            
                                            const response = await fetch(`/api/arqueo/reporte-ciclo/${ids_arqueo}`);

                                            if (!response.ok) {
                                                throw new Error(`Error HTTP: ${response.status}`);
                                            }

                                            const data = await response.json();

                                            if (data.success && data.data) {
                                                
                                                generateArqueoPdf(data.data);
                                            } else {
                                                alert('No se pudo obtener el reporte: ' + (data.message || 'Respuesta API inválida.'));
                                            }

                                        } catch (error) {
                                            console.error('Error al generar el reporte:', error);
                                            alert('Ocurrió un error inesperado al generar el PDF. Revise la consola para detalles.');
                                        } finally {
                                            btn.disabled = false;
                                            btn.innerHTML = '<i class="fas fa-file-pdf"></i>';
                                        }
                                    });
                                });
                               

                                function generateArqueoPdf(data) {
                                    const logo = window.logoBase64;
                                    const nombreEmpresa = 'YONDA & GRUPO HUARACA E.I.R.L';
                                    const rucEmpresa = 'RUC: 20609396866';

                                

                                    const styles = {
                                        header: {
                                            fontSize: 16,
                                            bold: true,
                                            color: '#1a4e8d',
                                            alignment: 'center',
                                            margin: [0, 10, 0, 15],
                                        },
                                        subheader: {
                                            fontSize: 12,
                                            bold: true,
                                            color: '#34495e',
                                            margin: [0, 10, 0, 5],
                                        },
                                        tableHeader: {
                                            bold: true,
                                            fillColor: '#f0f0f0',
                                            alignment: 'center'
                                        },
                                        summaryValue: {
                                            bold: true,
                                            alignment: 'right',
                                        }
                                    };

                                    const content = [{
                                            columns: [{
                                                    image: logo,
                                                    width: 80,
                                                    alignment: 'left'
                                                },
                                                {
                                                    stack: [{
                                                            text: nombreEmpresa,
                                                            bold: true,
                                                            color: '#2c3e50',
                                                            alignment: 'right',
                                                            fontSize: 10
                                                        },
                                                        {
                                                            text: rucEmpresa,
                                                            bold: true,
                                                            margin: [0, 2, 0, 0],
                                                            alignment: 'right'
                                                        }
                                                    ],
                                                    alignment: 'right'
                                                }
                                            ],
                                            margin: [0, 0, 0, 20]
                                        },

                                        {
                                            text: 'REPORTE DE ARQUEO DE CAJA',
                                            style: 'header'
                                        },

                                        {
                                            columns: [{
                                                    text: `Cajero(a): ${data.arqueo.nombre_cajero}`
                                                },
                                                {
                                                    text: `Fecha: ${data.arqueo.fecha}`,
                                                    alignment: 'right'
                                                },
                                            ],
                                            margin: [0, 0, 0, 5]
                                        },
                                        {
                                            columns: [{
                                                    text: `Hora inicio: ${data.arqueo.hora_inicio}`
                                                },
                                                {
                                                    text: `Hora cierre: ${data.arqueo.hora_fin}`,
                                                    alignment: 'right'
                                                },
                                            ],
                                            margin: [0, 0, 0, 20]
                                        },

                                        // Saldo Inicial
                                        {
                                            text: 'SALDO INICIAL',
                                            style: 'subheader'
                                        },
                                        {
                                            columns: [{
                                                    text: 'Saldo de caja al inicio del día:',
                                                    bold: true
                                                },
                                                {
                                                    text: `S/ ${parseFloat(data.arqueo.saldo_inicial).toFixed(2)}`,
                                                    alignment: 'right',
                                                    bold: true,
                                                    background: '#FFF2CC',
                                                    padding: [0, 5, 0, 5]
                                                }
                                            ],
                                            margin: [0, 5, 0, 15]
                                        },

                                        // Ingresos Detallados
                                        {
                                            text: 'INGRESOS DEL DÍA',
                                            style: 'subheader'
                                        },
                                        {
                                            table: {
                                                widths: ['*', 100],
                                                body: [
                                                    [{
                                                        text: 'Concepto de Ingreso',
                                                        style: 'tableHeader'
                                                    }, {
                                                        text: 'Monto',
                                                        style: 'tableHeader'
                                                    }],
                                                    [{
                                                        text: 'Efectivo'
                                                    
                                                    }, {
                                                        text: `S/ ${parseFloat(data.arqueo.ingresos_efectivo_total).toFixed(2)}`,
                                                        alignment: 'right'
                                                    }],
                                                    ...data.ingresos_digitales.map(item => [
                                                 
                                                        `${item.concepto} - ${item.entidad_bancaria || ''}`,
                                                        {
                                                            text: `S/ ${parseFloat(item.monto).toFixed(2)}`,
                                                            alignment: 'right'
                                                        }
                                                    ]),
                                                    [{
                                                        text: '**Total Ingresos Digitales**',
                                                        bold: true
                                                        
                                                    }, {
                                                        text: `S/ ${parseFloat(data.arqueo.ingresos_digital_total).toFixed(2)}`,
                                                        alignment: 'right',
                                                        bold: true,
                                                        fillColor: '#FFF2CC'
                                                    }]
                                                ]
                                            },
                                            layout: 'lightHorizontalLines',
                                            margin: [0, 5, 0, 20]
                                        },

                                        // Egresos Detallados
                                        {
                                            text: 'EGRESOS DEL DÍA',
                                            style: 'subheader'
                                        },
                                        {
                                            table: {
                                                widths: ['*', 100],
                                                body: [
                                                    [{
                                                        text: 'Concepto de Egreso',
                                                        style: 'tableHeader'
                                                    }, {
                                                        text: 'Monto',
                                                        style: 'tableHeader'
                                                    }],
                                                    ...data.egresos.map(egreso => [
                                                        egreso.concepto,
                                                        {
                                                            text: `S/ ${parseFloat(egreso.monto_total).toFixed(2)}`,
                                                            alignment: 'right'
                                                        }
                                                    ]),
                                                    [{
                                                        text: '**Total Egresos**',
                                                        bold: true
                                                        
                                                    }, {
                                                        text: `S/ ${parseFloat(data.arqueo.egresos_dia_total).toFixed(2)}`,
                                                        alignment: 'right',
                                                        bold: true,
                                                        fillColor: '#FFF2CC'
                                                    }]
                                                ]
                                            },
                                            layout: 'lightHorizontalLines',
                                            margin: [0, 5, 0, 20]
                                        },

                                        // Resumen Final
                                        {
                                            text: 'RESUMEN FINANCIERO',
                                            style: 'subheader'
                                        },
                                        {
                                            table: {
                                                widths: [200, '*'],
                                                body: [
                                                    ['Saldo anterior en efectivo', {
                                                        text: `S/ ${parseFloat(data.arqueo.saldo_inicial).toFixed(2)}`,
                                                        alignment: 'right'
                                                    }],
                                                    ['Ingreso diario en efectivo', {
                                                     
                                                        text: `S/ ${parseFloat(data.arqueo.ingresos_efectivo_total).toFixed(2)}`,
                                                        alignment: 'right'
                                                    }],
                                                    ['Total de egresos', {
                                                        
                                                        text: `S/ ${parseFloat(data.arqueo.egresos_dia_total).toFixed(2)}`,
                                                        alignment: 'right'
                                                    }],
                                                    [{
                                                        text: 'Total efectivo en caja (teórico)',
                                                        bold: true,
                                                        fillColor: '#FFF2CC'
                                                    }, {
                                                      
                                                        text: `S/ ${parseFloat(data.arqueo.monto_teorico_final).toFixed(2)}`,
                                                        style: 'summaryValue',
                                                        fillColor: '#FFF2CC'
                                                    }],
                                                    ['Ingresos digitales', {
                                                      
                                                        text: `S/ ${parseFloat(data.arqueo.ingresos_digital_total).toFixed(2)}`,
                                                        alignment: 'right'
                                                    }]
                                                ]
                                            },
                                            layout: 'noBorders',
                                            margin: [0, 5, 0, 20]
                                        },

                                        // Observaciones
                                        {
                                            text: 'OBSERVACIONES',
                                            style: 'subheader'
                                        },
                                        {
                                            text: data.arqueo.observaciones || 'No se han registrado observaciones.',
                                            margin: [0, 5, 0, 0],
                                            alignment: 'justify'
                                        }
                                    ];

                                    const docDefinition = {
                                        pageSize: 'A4',
                                        pageOrientation: 'portrait',
                                        pageMargins: [40, 25, 25, 25],
                                        defaultStyle: {
                                            fontSize: 9,
                                        },
                                        styles: styles,
                                        content: content
                                    };

                                    pdfMake.createPdf(docDefinition).open();
                                }


                            });
                        </script>