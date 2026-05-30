<!-- app/views/cobranza/index.php -->
<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    .status-vencido {
        border-left: 4px solid #dc3545 !important;
    }

    .status-vence-hoy {
        border-left: 4px solid #ffc107 !important;
    }

    .status-por-vencer {
        border-left: 4px solid #0dcaf0 !important;
    }

    .card-deuda {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card-deuda:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    input[type="radio"]:checked+label {
        background-color: #f97316 !important;
        color: white !important;
        border-color: #ea580c !important;
    }

    input[type="radio"]:checked+label .badge {
        background-color: white !important;
        color: #ea580c !important;
    }
</style>

<div class="container-fluid cobranza-ui">

    <!-- CABECERA -->
    <?php
    $pageHeaderBreadcrumbs = [
        ['label' => 'Inicio', 'url' => '/'],
        ['label' => 'Cobranza', 'url' => '/Cobranza'],
        ['label' => 'Deudas pendientes', 'url' => null],
    ];
    $pageHeaderActionsHtml = '
        <a href="/Vencidos" class="btn btn-outline-light btn-sm"><i class="bi bi-exclamation-triangle me-1"></i>Vencidos</a>
        <a href="/Recordatorios" class="btn btn-primary btn-sm"><i class="bi bi-bell me-1"></i>Recordatorios</a>';
    $pageHeaderClass = 'cobranza-ui';
    include __DIR__ . '/../components/page-header.php';
    ?>

    <!-- Estadísticas principales -->
    <div class="row mb-2" id="estadisticas">

        <!-- Primer cuadro estadístico -->
        <div class="col-md-3 mb-3">
            <div class="card estadistica-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users text-primary fa-2x mb-2"></i>
                    <h3 class="card-title" id="stat-deudores">
                        <span class="spinner-border spinner-border-sm"></span>
                    </h3>
                    <p class="card-text mb-0">Clientes Deudores</p>
                </div>
            </div>
        </div>

        <!-- Segundo cuadro estadístico -->
        <div class="col-md-3 mb-3">
            <div class="card estadistica-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-info mb-2"></i>
                    <h3 class="card-title" id="stat-por-vencer">
                        <span class="spinner-border spinner-border-sm"></span>
                    </h3>
                    <p class="card-text mb-0">Por vencer (3 Días)</p>
                </div>
            </div>
        </div>

        <!-- Tercer cuadro estadístico -->
        <div class="col-md-3 mb-3">
            <div class="card estadistica-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle text-danger fa-2x mb-2"></i>
                    <h3 class="card-title" id="stat-vencidos">
                        <span class="spinner-border spinner-border-sm"></span>
                    </h3>
                    <p class="card-text mb-0">Vencidos</p>
                </div>
            </div>
        </div>

        <!-- Cuarto cuadro estadístico -->
        <div class="col-md-3 mb-3">
            <div class="card estadistica-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign text-success fa-2x mb-2"></i>
                    <h3 class="card-title" id="stat-total-cobrar">
                        <span class="spinner-border spinner-border-sm"></span>
                    </h3>
                    <p class="card-text mb-0">Total por Cobrar</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Opciones -->
    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-filter me-2"></i> Opciones </h6>

                <div class="btn-group cobranza-actions" role="group">

                    <!-- VER TODOS -->
                    <input type="radio" id="btnTodos" name="filtro" class="d-none" checked>
                    <label for="btnTodos" class="btn btn-sm btn-outline-primary me-2">
                        <i class="fas fa-list me-1"></i>Todos
                        <span class="badge bg-primary text-dark ms-1" id="badge-todos"></span>
                    </label>

                    <!-- POR VENCER -->
                    <a href="/Recordatorios" class="btn btn-sm btn-outline-warning me-2">
                        <i class="fas fa-exclamation-triangle me-1"></i> Vence: 3 días
                        <span class="badge bg-warning text-dark ms-1" id="badge-por-vencer"></span>
                    </a>

                    <!-- VENCIDOS -->
                    <a href="/Vencidos" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-times-circle me-1"></i>Vencidos
                        <span class="badge bg-danger ms-1" id="badge-vencidos"></span>
                    </a>

                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor de tarjetas -->
    <div class="row" id="contenedor-tarjetas">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando tarjetas de cobranza...</p>
        </div>
    </div>

</div>

<!-- Modal para mostrar detalles del contrato -->
<div class="modal fade" id="modalDetalleContrato" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">

            <!-- Header del Modal -->
            <div class="modal-header bg-yonda">
                <h5 class="modal-title" id="modalDetalleLabel">
                    <i class="fas fa-file-contract me-2"></i>
                    Detalle del Contrato - <span id="modal-nombre-cliente">Cargando...</span>
                </h5>
                <div class="ms-auto me-3 d-flex align-items-center text-white">
                    <div>
                        <small class="d-block opacity-75">Deuda Total</small>
                        <h5 class="mb-0 fw-bold">
                            <span id="modal-moneda">S/</span>
                            <span id="modal-deuda-total">0.00</span>
                        </h5>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body del Modal -->
            <div class="modal-body">
                <div id="modal-loader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando información del contrato...</p>
                </div>

                <!-- Contenido del Modal -->
                <div id="modal-contenido" style="display: none;">

                    <!-- Tabs de navegación -->
                    <ul class="nav nav-tabs mb-3" id="tabsDetalleContrato" role="tablist">
                        <!--  <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-resumen" data-bs-toggle="tab"
                                data-bs-target="#contenido-resumen" type="button" role="tab">
                                <i class="fas fa-chart-pie me-1"></i> Resumen
                            </button>
                        </li> -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-cliente" data-bs-toggle="tab"
                                data-bs-target="#contenido-cliente" type="button" role="tab">
                                <i class="fas fa-user me-1"></i> Cliente
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-contrato" data-bs-toggle="tab"
                                data-bs-target="#contenido-contrato" type="button" role="tab">
                                <i class="fas fa-file-contract me-1"></i> Contrato
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-cronograma" data-bs-toggle="tab"
                                data-bs-target="#contenido-cronograma" type="button" role="tab">
                                <i class="fas fa-calendar-alt me-1"></i> Cronograma
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-historial" data-bs-toggle="tab"
                                data-bs-target="#contenido-historial" type="button" role="tab">
                                <i class="fas fa-history me-1"></i> Historial
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de los Tabs -->
                    <div class="tab-content" id="tabsDetalleContratoContent">


                        <!-- 2: INFORMACIÓN DEL CLIENTE -->
                        <div class="tab-pane fade show active" id="contenido-cliente" role="tabpanel">
                            <div class="card">
                                <div class="card-body" id="info-cliente-contenido">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <!-- 3: INFORMACIÓN DEL CONTRATO -->
                        <div class="tab-pane fade" id="contenido-contrato" role="tabpanel">
                            <div class="card">
                                <div class="card-body" id="info-contrato-contenido">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <!-- 4: CRONOGRAMA DE PAGOS -->
                        <div class="tab-pane fade" id="contenido-cronograma" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="tabla-cronograma">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Fecha Pago</th>
                                            <th class="text-end">Capital</th>
                                            <th class="text-end">Interés</th>
                                            <th class="text-end">Penalidad</th>
                                            <th class="text-end">Total Cuota</th>
                                            <th class="text-end">Saldo Capital</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Días</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-cronograma">
                                        <!-- Se llenará dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 5: HISTORIAL DE PAGOS -->
                        <div class="tab-pane fade" id="contenido-historial" role="tabpanel">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <strong>Últimos Pagos Realizados</strong>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="tabla-historial">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>N°</th>
                                                    <th>Fecha Programada</th>
                                                    <th class="text-end">Capital</th>
                                                    <th class="text-end">Interés</th>
                                                    <th class="text-end">Penalidad</th>
                                                    <th class="text-end">Total Pagado</th>
                                                    <th class="text-end">Saldo Restante</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-historial">
                                                <!-- Se llenará dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <!-- <button type="button" class="btn btn-primary" onclick="imprimirDetalle()">
                    <i class="fas fa-print me-1"></i> Imprimir
                </button> -->
            </div>
        </div>
    </div>
</div>

<script>
    // Función para cargar estadísticas
    function cargarEstadisticas() {
        return new Promise((resolve, reject) => {
            fetch('/Cobranza/getEstadisticas')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        resolve(data.data);
                    } else {
                        reject(new Error(data.message || 'Error al cargar estadísticas'));
                    }
                })
                .catch(error => {
                    reject(error);
                });
        });
    }

    // Función para cargar tarjetas
    function cargarTarjetas() {
        return new Promise((resolve, reject) => {
            fetch('/Cobranza/getTarjetas')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        resolve(data.data);
                    } else {
                        reject(new Error(data.message || 'Error al cargar tarjetas'));
                    }
                })
                .catch(error => {
                    reject(error);
                });
        });
    }

    // Actualizar las estadísticas en el DOM
    function actualizarEstadisticas(estadisticas) {
        document.getElementById('stat-deudores').textContent = estadisticas.total_deudores || 0;
        document.getElementById('stat-por-vencer').textContent = estadisticas.por_vencer_3dias || 0;
        document.getElementById('stat-vencidos').textContent = estadisticas.contratos_con_vencidos || 0;
        document.getElementById('stat-total-cobrar').textContent =
            'S/. ' + parseFloat(estadisticas.total_por_cobrar || 0).toFixed(2);

        // Actualizar badges
        document.getElementById('badge-todos').textContent = estadisticas.total_deudores || 0;
        document.getElementById('badge-por-vencer').textContent = estadisticas.por_vencer_3dias || 0;
        document.getElementById('badge-vencidos').textContent = estadisticas.contratos_con_vencidos || 0;
    }

    // Función para obtener la clase de estado del badge
    function obtenerClaseBadge(estadoVencimiento) {
        if (estadoVencimiento === 'VENCIDO') {
            return 'bg-danger';
        } else if (estadoVencimiento === 'VENCE HOY') {
            return 'bg-warning text-dark';
        } else {
            return 'bg-warning text-dark';
        }
    }

    // Función para obtener la clase del borde de la tarjeta
    function obtenerClaseBorde(estadoVencimiento) {
        if (estadoVencimiento === 'VENCIDO') {
            return 'status-vencido';
        } else if (estadoVencimiento === 'VENCE HOY') {
            return 'status-vence-hoy';
        } else {
            return 'status-por-vencer';
        }
    }

    // Renderizar las tarjetas en el DOM
    function renderizarTarjetas(tarjetas) {
        const contenedor = document.getElementById('contenedor-tarjetas');

        if (tarjetas.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay clientes con cuotas pendientes o próximas a vencer</p>
                </div>
            `;
            return;
        }

        let html = '';

        tarjetas.forEach((tarjeta, index) => {
            const claseBadge = obtenerClaseBadge(tarjeta.estado_vencimiento);
            const claseBorde = obtenerClaseBorde(tarjeta.estado_vencimiento);

            html += `
                <div class="col-lg-4 mb-2 cobranza-item" id="contrato-card-${tarjeta.idcontrato}">
                    <div class="card card-deuda border-start ${claseBorde}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="card-title mb-2">
                                        <i class="fas fa-user me-2"></i>${tarjeta.nombre_cliente}
                                    </h6>
                                    <p class="text-muted small mb-1"><strong>Teléfono:</strong> ${tarjeta.telefono || 'N/A'}</p>
                                    <p class="text-muted small mb-1"><strong>Local:</strong> ${tarjeta.local}</p>
                                    <p class="text-muted small mb-0"><strong>Tipo de vehículo:</strong> ${tarjeta.tipo_vehiculo}</p>
                                </div>
                                <span class="badge ${claseBadge}">${tarjeta.estado_vencimiento}</span>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong> ${tarjeta.fecha_vencimiento}</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-success btn-sm flex-grow-1"
                                    onclick="mostrarFormularioPago(${tarjeta.idcontrato}, '${tarjeta.nombre_cliente}')">
                                    Ver detalle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        contenedor.innerHTML = html;
    }

    // Función principal que carga todo usando Promise.all
    function inicializarVista() {
        Promise.all([cargarEstadisticas(), cargarTarjetas()])
            .then(([estadisticas, tarjetas]) => {
                actualizarEstadisticas(estadisticas);
                renderizarTarjetas(tarjetas);

            })
            .catch(error => {
                console.error('Error al cargar datos:', error);
                document.getElementById('contenedor-tarjetas').innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar los datos. Por favor, recargue la página.
                    </div>
                </div>
            `;
            });
    }

    // Ejecutar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', inicializarVista);

    async function mostrarFormularioPago(idContrato, nombreCliente) {

        //RESETEAR MODAL A LA PESTAÑA
        const allTabs = document.querySelectorAll('#tabsDetalleContrato .nav-link');
        allTabs.forEach(tab => tab.classList.remove('active'));

        const allTabContents = document.querySelectorAll('#tabsDetalleContratoContent .tab-pane');
        allTabContents.forEach(content => {
            content.classList.remove('show', 'active');
        });
        document.getElementById('tab-cliente').classList.add('active');
        document.getElementById('contenido-cliente').classList.add('show', 'active');
        //FIN DEL RESET

        const modal = new bootstrap.Modal(document.getElementById('modalDetalleContrato'));
        modal.show();
        // Actualizar nombre del cliente en el header
        document.getElementById('modal-nombre-cliente').textContent = nombreCliente;
        document.getElementById('modal-loader').style.display = 'block';
        document.getElementById('modal-contenido').style.display = 'none';

        try {
            // Cargar todos los datos en paralelo usando Promise.all
            const [infoCliente, detalleContrato, cronograma, historialPagos] =
            await Promise.all([
                fetch(`/Cobranza/getInfoCliente/${idContrato}`).then(r => r.json()),
                fetch(`/Cobranza/getDetalleContrato/${idContrato}`).then(r => r.json()),
                fetch(`/Cobranza/getCronogramaPagos/${idContrato}`).then(r => r.json()),
                fetch(`/Cobranza/getHistorialPagos/${idContrato}/5`).then(r => r.json())
            ]);

            // Verificar que todas las peticiones fueron exitosas
            if (!infoCliente.success || !detalleContrato.success || !cronograma.success || !historialPagos.success) {
                throw new Error('Error al cargar algunos datos del contrato');
            }

            // Actualizar deuda total en el header
            const contrato = detalleContrato.data;
            const simboloMoneda = contrato.moneda === 'PEN' ? 'S/' : '$';
            document.getElementById('modal-moneda').textContent = simboloMoneda;
            document.getElementById('modal-deuda-total').textContent =
                parseFloat(contrato.deuda_total || 0).toFixed(2);

            // Renderizar cada sección
            renderizarInfoCliente(infoCliente.data);
            renderizarDetalleContrato(detalleContrato.data);
            renderizarCronograma(cronograma.data);
            renderizarHistorialPagos(historialPagos.data);

            // Ocultar loader y mostrar contenido
            document.getElementById('modal-loader').style.display = 'none';
            document.getElementById('modal-contenido').style.display = 'block';



        } catch (error) {
            console.error('Error al cargar el modal:', error);
            document.getElementById('modal-loader').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar la información del contrato. Por favor, intente nuevamente.
                </div>
            `;
        }
    }

    function renderizarInfoCliente(data) {
        let html = '';

       
        const item = (label, value) => `
        <div class="mb-3">
            <label class="d-block text-body-secondary text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                ${label}
            </label>
            <div class="text-body-emphasis fw-medium fs-6 text-break">
                ${value || '<span class="text-body-secondary opacity-50 fst-italic small">No registrado</span>'}
            </div>
        </div>
    `;

        // Icono con fondo adaptable
        const headerIcon = (iconClass, title) => `
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-secondary border-opacity-10">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="${iconClass} fa-lg"></i>
            </div>
            <h6 class="text-body-emphasis fw-bold mb-0 text-uppercase ls-1">${title}</h6>
        </div>
    `;

        if (data.tipocliente === 'P') {
            // --- Cliente Persona Natural ---
            html = `
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="p-4 bg-body-tertiary rounded-4 h-100 border border-translucent">
                    ${headerIcon('fas fa-user', 'Información Personal')}
                    
                    <div class="row g-2">
                        <div class="col-12">
                            ${item('Nombre Completo', data.nombre_completo)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Tipo Documento', data.tipo_documento)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Número Documento', data.numero_documento)}
                        </div>
                        <div class="col-12">
                            ${item('Email', data.email_persona)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Teléfono Principal', data.telefono_primario_persona)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Teléfono Alternativo', data.telefono_alternativo_persona)}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-4 bg-body-tertiary rounded-4 h-100 border border-translucent">
                    ${headerIcon('fas fa-map-marker-alt', 'Ubicación')}
                    
                    <div class="row g-2">
                        <div class="col-sm-6">
                            ${item('Distrito', data.distrito)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Provincia', data.provincia)}
                        </div>
                        <div class="col-12">
                            ${item('Dirección Domiciliaria', data.direccion_persona)}
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        } else {
            // --- Cliente Empresa ---
            html = `
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="p-4 bg-body-tertiary rounded-4 h-100 border border-translucent">
                    ${headerIcon('fas fa-building', 'Información Empresarial')}
                    
                    <div class="row g-2">
                        <div class="col-12">
                            ${item('Razón Social', data.razon_social)}
                        </div>
                        <div class="col-sm-6">
                            ${item('RUC', data.ruc)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Representante Legal', data.representante_legal)}
                        </div>
                        <div class="col-12">
                            ${item('Email Corporativo', data.email_empresa)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Teléfono Principal', data.telefono_primario_empresa)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Teléfono Secundario', data.telefono_secundario_empresa)}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-4 bg-body-tertiary rounded-4 h-100 border border-translucent">
                    ${headerIcon('fas fa-map-marker-alt', 'Ubicación Fiscal')}
                    
                    <div class="row g-2">
                        <div class="col-sm-6">
                            ${item('Distrito', data.distrito)}
                        </div>
                        <div class="col-sm-6">
                            ${item('Provincia', data.provincia)}
                        </div>
                        <div class="col-12">
                            ${item('Dirección Fiscal', data.direccion_empresa)}
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        }

        document.getElementById('info-cliente-contenido').innerHTML = html;
    }

    function renderizarDetalleContrato(data) {
        const nombreMoneda = data.moneda === 'PEN' ? 'Soles (PEN)' : 'Dólares (USD)';
        const simboloMoneda = data.moneda === 'PEN' ? 'S/.' : '$';

        // Badges modernos y sutiles
        const esCreditoBadge = data.escredito === 'S' ?
            '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 rounded-pill">Crédito</span>' :
            '<span class="badge bg-info-subtle text-info border border-info-subtle px-3 rounded-pill">Contado</span>';

        const item = (label, value, isHighlight = false) => `
        <div class="mb-3">
            <label class="d-block text-body-secondary text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                ${label}
            </label>
            <div class="${isHighlight ? 'text-primary fw-bold fs-5' : 'text-body-emphasis fw-medium fs-6'}">
                ${value || '<span class="text-body-secondary opacity-50">-</span>'}
            </div>
        </div>
    `;

        const headerIcon = (iconClass, title, colorClass = 'text-primary', bgClass = 'bg-primary') => `
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-secondary border-opacity-10">
            <div class="${bgClass} bg-opacity-10 ${colorClass} rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="${iconClass} fa-lg"></i>
            </div>
            <h6 class="text-body-emphasis fw-bold mb-0 text-uppercase ls-1">${title}</h6>
        </div>
    `;

        const html = `
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="p-4 bg-body-tertiary rounded-4 h-100 border border-translucent">
                ${headerIcon('fas fa-file-invoice-dollar', 'Detalles Financieros')}
                
                <div class="row g-2">
                     <div class="col-6">
                        ${item('N° Contrato', `<span class="badge bg-body-secondary text-body-emphasis border">${data.idcontrato || 'N/A'}</span>`)}
                    </div>
                    <div class="col-6">
                        ${item('Modalidad', esCreditoBadge)}
                    </div>
                    <div class="col-6">
                        ${item('Fecha Inicio', data.fechainicio)}
                    </div>
                    <div class="col-6">
                        ${item('Moneda', nombreMoneda)}
                    </div>
                    
                    <div class="col-12 my-2"><hr class="text-secondary opacity-25"></div>

                    <div class="col-6">
                        ${item('Precio Venta', `${simboloMoneda} ${parseFloat(data.precioventa || 0).toFixed(2)}`, true)}
                    </div>
                    <div class="col-6">
                        ${item('Cuota Inicial', `${simboloMoneda} ${parseFloat(data.inicial || 0).toFixed(2)}`)}
                    </div>
                    <div class="col-6">
                         ${item('Valor Cuota', `${simboloMoneda} ${parseFloat(data.valorcuota || 0).toFixed(2)}`)}
                    </div>
                    <div class="col-6">
                         ${item('Gastos Admin.', `${simboloMoneda} ${parseFloat(data.gastosadministrativos || 0).toFixed(2)}`)}
                    </div>
                     <div class="col-12">
                         ${item('Plazo / Cuotas', `${data.numcuotas || '0'} <small class="text-body-secondary fw-normal">mensuales</small>`)}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="d-flex flex-column gap-3 h-100">
                
                <div class="p-4 bg-body-tertiary rounded-4 border border-translucent flex-grow-1">
                    ${headerIcon('fas fa-car', 'Datos del Vehículo', 'text-success', 'bg-success')}
                    
                    <div class="row g-2">
                        <div class="col-12">
                            ${item('Vehículo', data.vehiculo_descripcion)}
                        </div>
                        <div class="col-6">
                            ${item('Placa', `<span class="fw-bold mt-5 p-1 bg-warning bg-opacity-10 text-warning border border-warning  rounded small">${data.placa || 'EN TRÁMITE'}</span>`)}
                        </div>
                        <div class="col-6">
                            ${item('Color', data.color)}
                        </div>
                        <div class="col-6">
                            ${item('Tipo', data.tipovehiculo)}
                        </div>
                         <div class="col-6">
                            ${item('Combustible', data.combustible)}
                        </div>
                        <div class="col-6">
                            ${item('Versión', data.version)}
                        </div>
                        <div class="col-6">
                            ${item('Condición', data.condicion)}
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-warning bg-opacity-10 rounded-4 border border-warning border-opacity-25">
                    <h6 class="text-body-emphasis fw-bold mb-2 small text-uppercase d-flex align-items-center">
                        <i class="fas fa-comment-dots me-2 text-warning"></i>Observaciones
                    </h6>
                    <p class="mb-0 text-body-emphasis fst-italic small">
                        ${data.observaciones || 'Sin observaciones registradas.'}
                    </p>
                </div>
            </div>
        </div>
    </div>
    `;

        document.getElementById('info-contrato-contenido').innerHTML = html;
    }

    function renderizarCronograma(cronograma) {
        const tbody = document.getElementById('tbody-cronograma');

        // Destruir DataTable existente si existe
        if ($.fn.DataTable.isDataTable('#tabla-cronograma')) {
            $('#tabla-cronograma').DataTable().destroy();
        }

        if (cronograma.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    No hay cuotas registradas para este contrato
                </td>
            </tr>
        `;
            return;
        }

        let html = '';

        cronograma.forEach(cuota => {
            // Determinar clase de badge según estado
            let badgeClass = '';
            let estadoTexto = '';

            switch (cuota.estado_vencimiento) {
                case 'PAGADO':
                    badgeClass = 'bg-success';
                    estadoTexto = 'Pagado';
                    break;
                case 'VENCIDO':
                    badgeClass = 'bg-danger';
                    estadoTexto = 'Vencido';
                    break;
                case 'VENCE HOY':
                    badgeClass = 'bg-warning text-dark';
                    estadoTexto = 'Vence Hoy';
                    break;
                case 'POR VENCER':
                    badgeClass = 'bg-info text-dark';
                    estadoTexto = 'Por Vencer';
                    break;
                default:
                    badgeClass = 'bg-warning text-dark';
                    estadoTexto = 'Pendiente';
            }

            // Determinar texto de días
            let diasTexto = '';
            if (cuota.estado_vencimiento === 'PAGADO') {
                diasTexto = '-';
            } else if (cuota.estado_vencimiento === 'VENCIDO') {
                diasTexto = `<span class="text-danger">${cuota.dias_diferencia} días</span>`;
            } else {
                diasTexto = `<span class="text-muted">${cuota.dias_diferencia} días</span>`;
            }

            html += `
            <tr class="${cuota.estado_vencimiento === 'VENCIDO' ? 'table-danger' : ''}">
                <td class="text-center">${cuota.numcuota}</td>
                <td>${cuota.fecha_pago_formateada}</td>
                <td class="text-end">S/. ${parseFloat(cuota.abonocapital).toFixed(2)}</td>
                <td class="text-end">S/. ${parseFloat(cuota.interes).toFixed(2)}</td>
                <td class="text-end ${cuota.penalidad > 0 ? 'text-danger fw-bold' : ''}">
                    S/. ${parseFloat(cuota.penalidad).toFixed(2)}
                </td>
                <td class="text-end fw-bold">S/. ${parseFloat(cuota.monto_total_cuota).toFixed(2)}</td>
                <td class="text-end">S/. ${parseFloat(cuota.saldocapital).toFixed(2)}</td>
                <td class="text-center">
                    <span class="badge ${badgeClass}">${estadoTexto}</span>
                </td>
                <td class="text-center">${diasTexto}</td>
            </tr>
        `;
        });

        tbody.innerHTML = html;

        // Inicializar DataTable
        /* $('#tabla-cronograma').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            pageLength: 10,
            order: [[0, 'asc']],
            responsive: true
        }); */
        $('#tabla-cronograma').DataTable({
            language: {
                decimal: "",
                emptyTable: "No hay datos disponibles en la tabla",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrar _MENU_ registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No se encontraron registros coincidentes",
                paginate: {
                    first: '«',
                    last: '»',
                    next: '›',
                    previous: '‹'
                },
                aria: {
                    sortAscending: ": activar para ordenar la columna ascendente",
                    sortDescending: ": activar para ordenar la columna descendente"
                }
            },
            lengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            pageLength: 20,
            order: [
                [0, 'asc']
            ],
            responsive: true
        });
    }

    function renderizarHistorialPagos(historial) {
        const tbody = document.getElementById('tbody-historial');

        if (historial.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No hay pagos registrados</p>
                </td>
            </tr>
        `;
            return;
        }

        let html = '';

        historial.forEach(pago => {
            html += `
            <tr>
                <td class="fw-bold">${pago.numcuota}</td>
                <td>${pago.fecha_pago_programada}</td>
                <td class="text-end">S/. ${parseFloat(pago.abonocapital).toFixed(2)}</td>
                <td class="text-end">S/. ${parseFloat(pago.interes).toFixed(2)}</td>
                <td class="text-end ${pago.penalidad > 0 ? 'text-danger' : ''}">
                    S/. ${parseFloat(pago.penalidad).toFixed(2)}
                </td>
                <td class="text-end fw-bold text-success">
                    S/. ${parseFloat(pago.monto_pagado).toFixed(2)}
                </td>
                <td class="text-end text-muted">
                    S/. ${parseFloat(pago.saldo_capital_restante).toFixed(2)}
                </td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    function imprimirDetalle() {
        window.print();
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>