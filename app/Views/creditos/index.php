<!-- app/views/credito/index.php -->
<?php include __DIR__ . '/../layout/header.php'; ?>
<style>
    .card-gradient {
        background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
    }

    .border-4 {
        border-width: 4px !important;
    }

    .moroso-item {
        transition: all 0.3s ease;
    }

    .upload-area {
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        background-color: #020f1dff;
    }

    .badge.custom-warning {
        background: #ffc107;
        color: #212529;
    }

    .direccion-info {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Estilos para el skeleton loader */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-text {
        height: 20px;
        margin-bottom: 10px;
    }

    .skeleton-title {
        height: 30px;
        width: 60%;
        margin-bottom: 15px;
    }

    .spinner-border-sm {
        width: 1.5rem;
        height: 1.5rem;
    }
</style>

<div class="container-fluid">
    <!-- Encabezado -->
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Área de Crédito</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Gestión de morosos y seguimiento de pagos
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas con loader -->
    <div class="row mb-2" id="estadisticasContainer">
        <!-- Skeleton loaders iniciales -->
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <div class="skeleton skeleton-text mx-auto" style="width: 80px;"></div>
                    <p class="card-text mb-0">Total Morosos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="spinner-border text-success mb-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <div class="skeleton skeleton-text mx-auto" style="width: 120px;"></div>
                    <p class="card-text mb-0">Deuda Total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="spinner-border text-info mb-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <div class="skeleton skeleton-text mx-auto" style="width: 60px;"></div>
                    <p class="card-text mb-0">Seguimientos Hoy</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="spinner-border text-warning mb-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <div class="skeleton skeleton-text mx-auto" style="width: 80px;"></div>
                    <p class="card-text mb-0">Días Promedio</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de clasificación con loader -->
    <div class="card mb-3" id="clasificacionPanel" style="display: none;">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Clasificación por Morosidad</h6>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="filtroMorosidad" id="filtro5dias" autocomplete="off"
                        checked>
                    <label class="btn btn-sm btn-outline-warning" for="filtro5dias" onclick="filtrarPor('5-dias')">
                        <i class="fas fa-clock me-1"></i>5 Días
                        <span class="badge bg-warning text-dark ms-1" id="count5dias">0</span>
                    </label>

                    <input type="radio" class="btn-check" name="filtroMorosidad" id="filtro2semanas" autocomplete="off">
                    <label class="btn btn-sm btn-outline-warning" for="filtro2semanas"
                        onclick="filtrarPor('2-semanas')">
                        <i class="fas fa-calendar-week me-1"></i>2 Semanas
                        <span class="badge bg-warning text-dark ms-1" id="count2semanas">0</span>
                    </label>

                    <input type="radio" class="btn-check" name="filtroMorosidad" id="filtro1mes" autocomplete="off">
                    <label class="btn btn-sm btn-outline-danger" for="filtro1mes" onclick="filtrarPor('1-mes')">
                        <i class="fas fa-calendar-times me-1"></i>+1 Mes
                        <span class="badge bg-danger ms-1" id="count1mes">0</span>
                    </label>

                    <input type="radio" class="btn-check" name="filtroMorosidad" id="filtroTodos" autocomplete="off">
                    <label class="btn btn-sm btn-outline-primary" for="filtroTodos" onclick="filtrarPor('todos')">
                        <i class="fas fa-list me-1"></i>Ver Todos
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Loader para la lista de morosos -->
    <div class="row" id="morososLoader">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Cargando morosos...</span>
            </div>
            <p class="mt-3 text-muted">Cargando lista de morosos...</p>
        </div>
    </div>

    <!-- Lista de morosos-->
    <div class="row" id="listaMoresos" style="display: none;"></div>
</div>

<!-- Modal para seguimiento -->
<div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-labelledby="modalSeguimientoLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-yonda text-white">
                <h5 class="modal-title" id="modalSeguimientoLabel">
                    <i class="fas fa-user-check me-2"></i>Registrar Seguimiento de Cobranza
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimiento" method="POST" action="/creditos/seguimiento" enctype="multipart/form-data">
                    <input type="hidden" name="idcontrato" id="hiddenIdContrato" value="">

                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><strong><i
                                            class="fas fa-user me-1"></i>Cliente:</strong></label>
                                <p id="clienteNombre" class="mb-0 text-primary fw-bold"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><strong><i
                                            class="fas fa-dollar-sign me-1"></i>Deuda:</strong></label>
                                <p id="clienteDeuda" class="mb-0 text-danger fw-bold"></p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><strong><i class="fas fa-clock me-1"></i>Fecha y
                                        Hora:</strong></label>
                                <p id="fechaHora" class="mb-0"></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-1 mt-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-clipboard-check me-2"></i>Tipo de Seguimiento:
                        </label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipoSeguimiento"
                                                id="documentoFirmado" value="documento" checked>
                                            <label class="form-check-label w-100" for="documentoFirmado">
                                                <i class="fas fa-file-signature text-success fa-2x mb-2 d-block"></i>
                                                <strong>Documento Firmado</strong><br>
                                                <small class="text-muted">El cliente firmó el documento de
                                                    notificación</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tipoSeguimiento"
                                                id="evidenciaVisita" value="evidencia">
                                            <label class="form-check-label w-100" for="evidenciaVisita">
                                                <i class="fas fa-camera text-warning fa-2x mb-2 d-block"></i>
                                                <strong>Evidencia de Visita</strong><br>
                                                <small class="text-muted">Foto de que se realizó la visita</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label fw-bold">
                            <i class="fas fa-sticky-note me-2"></i>Observaciones del Seguimiento
                        </label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"
                            placeholder="Describe detalladamente lo ocurrido durante la visita"></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">
                            <i class="fas fa-cloud-upload-alt me-2"></i>Subir Evidencia
                        </label>
                        <div class="card border-2 border-dashed upload-area" id="uploadArea" style="cursor: pointer;"
                            onclick="document.getElementById('evidenciaFile').click()">
                            <div class="card-body text-center py-4">
                                <div id="uploadDefault">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h6 class="mb-2">Haz clic aquí para subir la evidencia</h6>
                                    <p class="text-muted small mb-0">
                                        <strong>Formatos permitidos:</strong> JPG, PNG, PDF<br>
                                        <strong>Tamaño máximo:</strong> 5MB
                                    </p>
                                </div>
                                <div id="uploadSuccess" style="display: none;">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h6 class="text-success mb-2">Archivo cargado exitosamente</h6>
                                    <p class="text-muted small mb-2" id="nombreArchivo"></p>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="cambiarArchivo()">
                                        <i class="fas fa-exchange-alt me-1"></i>Cambiar archivo
                                    </button>
                                </div>
                                <input type="file" id="evidenciaFile" name="evidenciaFile" accept="image/*,.pdf"
                                    style="display: none;" onchange="mostrarArchivo(this)">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-save me-2"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast para notificaciones -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">
                <i class="fas fa-check-circle text-success me-2"></i>Sistema de Crédito
            </strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">Seguimiento registrado correctamente</div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>

    // VARIABLES GLOBALES
    let morososData = {
        '5-dias': [],
        '2-semanas': [],
        '1-mes': []
    };

    // FUNCIONES DE FORMATO
    function fmtMoney(val) {
        return 'S/. ' + parseFloat(val).toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // CARGAR ESTADÍSTICAS CON PROMESAS
    async function cargarEstadisticas() {
        try {
            const response = await fetch('/api/creditos/estadisticas', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });


            if (!response.ok) {
                throw new Error('Error al cargar estadísticas');
            }
            
            const data = await response.json();

            renderizarEstadisticas(data);
        } catch (error) {
            console.error('Error:', error);
            renderizarEstadisticasError();
        }
    }

    function renderizarEstadisticas(stats) {
        const container = document.getElementById('estadisticasContainer');
        container.innerHTML = `
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2 text-warning"></i>
                        <h3 class="card-title">${stats.total_morosos || 0}</h3>
                        <p class="card-text mb-0">Total Morosos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2 text-success"></i>
                        <h3 class="card-title">${fmtMoney(stats.deuda_total || 0)}</h3>
                        <p class="card-text mb-0">Deuda Total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2 text-info"></i>
                        <h3 class="card-title">${stats.seguimientos_hoy || 0}</h3>
                        <p class="card-text mb-0">Seguimientos Hoy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar fa-2x mb-2 text-primary"></i>
                        <h3 class="card-title">${Math.round(stats.dias_promedio || 0)}</h3>
                        <p class="card-text mb-0">Días Promedio</p>
                    </div>
                </div>
            </div>
        `;
    }

    function renderizarEstadisticasError() {
        const container = document.getElementById('estadisticasContainer');
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar estadísticas. Por favor, recarga la página.
                </div>
            </div>
        `;
    }

    // CARGAR MOROSOS CON PROMESAS
    async function cargarMorosos() {
        try {
            const response = await fetch('/api/creditos/morosos', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Error al cargar morosos');
            }

            const data = await response.json();
            morososData = data;

            // Actualizar contadores
            document.getElementById('count5dias').textContent = data['5-dias'].length;
            document.getElementById('count2semanas').textContent = data['2-semanas'].length;
            document.getElementById('count1mes').textContent = data['1-mes'].length;

            // Mostrar panel de clasificación
            document.getElementById('clasificacionPanel').style.display = 'block';

            // Renderizar morosos CON FILTRO INICIAL APLICADO
            renderizarMorosos('5-dias');

            // Ocultar loader y mostrar lista
            document.getElementById('morososLoader').style.display = 'none';
            document.getElementById('listaMoresos').style.display = 'flex';

        } catch (error) {
            console.error('Error:', error);
            renderizarMorososError();
        }
    }

    function renderizarMorosos(filtroInicial = null) {
        const container = document.getElementById('listaMoresos');
        const map = {
            '5-dias': { badge: 'bg-warning text-dark', border: 'border-warning' },
            '2-semanas': { badge: 'bg-warning text-dark', border: 'border-warning border-2' },
            '1-mes': { badge: 'bg-danger', border: 'border-danger' }
        };

        let html = '';

        // Generar alertas de "no morosos" por categoría
        ['5-dias', '2-semanas', '1-mes'].forEach(categoria => {
            const morososCategoria = morososData[categoria] || [];
            const display = morososCategoria.length === 0 ? 'block' : 'none';

            // Si hay filtro inicial, solo mostrar la alerta correspondiente
            const visibilidad = filtroInicial ? (filtroInicial === categoria ? display : 'none') : display;

            html += `
                <div class="col-12 no-morosos" id="noMorosos-${categoria}" data-categoria="${categoria}" style="display: ${visibilidad};">
                    <div class="alert alert-secondary text-center mb-0">
                        <strong>No hay morosos en la categoría ${categoria.replace('-', ' ')}.</strong>
                    </div>
                </div>
            `;

            // Renderizar cada moroso
            morososCategoria.forEach(m => {
                const idcontrato = m.idcontrato || 0;
                const cliente = escapeHtml(m.cliente || 'Sin nombre');
                const ndoc = escapeHtml(m.ndocumento || m.nrodoc || '');
                const telefono = escapeHtml(m.telefono || m.telprimario || '');
                const dias = parseInt(m.dias_atraso || m.dias_max_vencido || 0);
                const deuda = m.saldo_pendiente || m.deuda_total || 0;
                const fecha_venc = m.fecha_vencimiento || m.fechapago || '';
                const direccion_persona = escapeHtml(m.direccion_persona || 'Sin dirección');
                const direccion_local = escapeHtml(m.direccion_local || 'Sin dirección del local');
                const deudaFmt = fmtMoney(deuda);
                const deudaNum = parseFloat(deuda).toFixed(2);

                // Si hay filtro inicial, aplicar display desde el renderizado
                const displayItem = filtroInicial ? (filtroInicial === categoria ? 'block' : 'none') : 'block';

                html += `
                    <div class="col-lg-4 mb-2 moroso-item" data-categoria="${categoria}" id="cliente-${idcontrato}" style="display: ${displayItem};">
                        <div class="card border-start ${map[categoria].border}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="card-title mb-1"><i class="fas fa-user me-2"></i>${cliente}</h6>
                                        <p class="text-muted small mb-1"><strong>DNI:</strong> ${ndoc}</p>
                                        ${telefono ? `<p class="text-muted small mb-0"><strong>Teléfono:</strong> ${telefono}</p>` : ''}
                                        <p class="direccion-info mb-0"><strong>Dirección:</strong> ${direccion_persona}</p>
                                    </div>
                                    <span class="badge ${map[categoria].badge}">${dias} días</span>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1"><strong class="text-danger">Deuda: ${deudaFmt}</strong></p>
                                    ${fecha_venc ? `<p class="text-muted small mb-1"><strong>Vencimiento:</strong> ${fecha_venc}</p>` : ''}
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-store me-1"></i><strong>Local:</strong> ${direccion_local}
                                    </p>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-success btn-sm flex-grow-1"
                                        onclick="iniciarSeguimiento(${idcontrato}, '${cliente.replace(/'/g, "\\'")}', '${deudaNum}')">
                                        <i class="fas fa-camera me-1"></i>Hacer Seguimiento
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="verHistorial(${idcontrato})" title="Ver historial">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        });

        // Mensaje si no hay morosos en ninguna categoría
        const totalMorosos = morososData['5-dias'].length + morososData['2-semanas'].length + morososData['1-mes'].length;
        const displayGlobal = totalMorosos === 0 ? 'block' : 'none';

        html += `
            <div class="col-12" id="noMorososTodosGlobal" style="display:${displayGlobal};">
                <div class="alert alert-secondary text-center">No hay morosos para mostrar.</div>
            </div>
        `;

        container.innerHTML = html;
    }

    function renderizarMorososError() {
        document.getElementById('morososLoader').innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar morosos. Por favor, recarga la página.
                </div>
            </div>
        `;
    }

    // FILTRAR MOROSOS POR CATEGORÍA
    function filtrarPor(categoria) {
        const items = document.querySelectorAll('.moroso-item');
        const noMorososAlerts = document.querySelectorAll('.no-morosos');
        const globalNo = document.getElementById('noMorososTodosGlobal');

        if (categoria === 'todos') {
            items.forEach(item => item.style.display = 'block');
            noMorososAlerts.forEach(alert => alert.style.display = 'none');
            globalNo.style.display = (items.length === 0) ? 'block' : 'none';
            return;
        }

        items.forEach(item => {
            item.style.display = (item.dataset.categoria === categoria) ? 'block' : 'none';
        });

        noMorososAlerts.forEach(alert => {
            if (alert.dataset.categoria === categoria) {
                const count = document.querySelectorAll('.moroso-item[data-categoria="' + categoria + '"]').length;
                alert.style.display = (count === 0) ? 'block' : 'none';
            } else {
                alert.style.display = 'none';
            }
        });

        if (globalNo) globalNo.style.display = 'none';
    }

    // FUNCIONES DEL MODAL
    function iniciarSeguimiento(id, nombre, deuda) {
        document.getElementById('clienteNombre').textContent = nombre;
        document.getElementById('clienteDeuda').textContent = 'S/. ' + deuda;
        document.getElementById('fechaHora').textContent = new Date().toLocaleString('es-PE');
        document.getElementById('hiddenIdContrato').value = id;
        document.getElementById('uploadDefault').style.display = 'block';
        document.getElementById('uploadSuccess').style.display = 'none';
        document.getElementById('evidenciaFile').value = '';
        document.getElementById('observaciones').value = '';

        const modal = new bootstrap.Modal(document.getElementById('modalSeguimiento'));
        modal.show();
    }

    function mostrarArchivo(input) {
        if (input.files && input.files[0]) {
            const archivo = input.files[0];
            const tamaño = (archivo.size / 1024 / 1024).toFixed(2);

            if (tamaño > 5) {
                alert('El archivo es demasiado grande. Máximo 5MB.');
                input.value = '';
                return;
            }

            document.getElementById('uploadDefault').style.display = 'none';
            document.getElementById('uploadSuccess').style.display = 'block';
            document.getElementById('nombreArchivo').textContent = `${archivo.name} (${tamaño} MB)`;
        }
    }

    function cambiarArchivo() {
        document.getElementById('evidenciaFile').click();
    }

    function verHistorial(id) {
        window.location.href = '/creditos/historial/' + id;
    }

    function showToast(mensaje, tipo = 'success') {
        const toastEl = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastHeader = toastEl.querySelector('.toast-header strong');

        toastMessage.textContent = mensaje;

        if (tipo === 'success') {
            toastHeader.innerHTML = '<i class="fas fa-check-circle text-success me-2"></i>Sistema de Crédito';
        } else if (tipo === 'info') {
            toastHeader.innerHTML = '<i class="fas fa-info-circle text-info me-2"></i>Sistema de Crédito';
        } else if (tipo === 'warning') {
            toastHeader.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-2"></i>Sistema de Crédito';
        }

        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    // VALIDACIÓN Y ENVÍO DEL FORMULARIO
    document.getElementById('formSeguimiento').addEventListener('submit', function (e) {
        const observaciones = document.getElementById('observaciones').value;
        const evidencia = document.getElementById('evidenciaFile').files[0];

        if (!observaciones.trim()) {
            e.preventDefault();
            showToast('Por favor, ingresa las observaciones del seguimiento.', 'warning');
            return;
        }

        if (!evidencia) {
            e.preventDefault();
            showToast('Por favor, sube la evidencia del seguimiento.', 'warning');
            return;
        }

        showToast('Guardando seguimiento...', 'info');
    });

    // INICIALIZACIÓN AL CARGAR LA PÁGINA
    document.addEventListener('DOMContentLoaded', function () {
        // Cargar todo con promesas en paralelo
        Promise.all([
            cargarEstadisticas(),
            cargarMorosos()
        ])
            .then(() => {
                
            })
            .catch(error => {
                console.error('Error al cargar datos:', error);
                showToast('Error al cargar algunos datos. Por favor, recarga la página.', 'warning');
            });
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>