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
</style>

<div class="container-fluid">

    <!-- CABECERA -->
    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <nav class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Área de cobranza</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Gestión de clientes con Deudas pendientes
                        </li>
                    </ol>
                </nav>
            </nav>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-2" id="estadisticas">

        <!-- Primer cuadro estadístico -->
        <div class="col-md-3 mb-3">
            <div class="card estadistica-card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
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
                    <i class="fas fa-clock fa-2x mb-2"></i>
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
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
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
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
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

                <div class="btn-group" role="group">
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
                <!-- Loader mientras carga la información -->
                <div id="modal-loader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando información del contrato...</p>
                </div>

                <!-- Contenido del Modal (oculto inicialmente) -->
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


                        <!-- TAB 2: INFORMACIÓN DEL CLIENTE -->
                        <div class="tab-pane fade show active" id="contenido-cliente" role="tabpanel">
                            <div class="card">
                                <div class="card-body" id="info-cliente-contenido">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: INFORMACIÓN DEL CONTRATO -->
                        <div class="tab-pane fade" id="contenido-contrato" role="tabpanel">
                            <div class="card">
                                <div class="card-body" id="info-contrato-contenido">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: CRONOGRAMA DE PAGOS -->
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

                        <!-- TAB 5: HISTORIAL DE PAGOS -->
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

                    </div> <!-- Fin tab-content -->
                </div> <!-- Fin modal-contenido -->
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
                console.log('Datos cargados correctamente');
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

            console.log('Modal cargado correctamente');

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

        if (data.tipocliente === 'P') {
            // Cliente Persona Natural
            html = `
            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>Información Personal</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre Completo:</strong> ${data.nombre_completo || 'N/A'}</p>
                    <p><strong>Tipo Documento:</strong> ${data.tipo_documento || 'N/A'}</p>
                    <p><strong>Número Documento:</strong> ${data.numero_documento || 'N/A'}</p>
                    <p><strong>Email:</strong> ${data.email_persona || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Teléfono Principal:</strong> ${data.telefono_primario_persona || 'N/A'}</p>
                    <p><strong>Teléfono Alternativo:</strong> ${data.telefono_alternativo_persona || 'N/A'}</p>
                    <p><strong>Distrito:</strong> ${data.distrito || 'N/A'}</p>
                    <p><strong>Provincia:</strong> ${data.provincia || 'N/A'}</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <p><strong>Dirección:</strong> ${data.direccion_persona || 'N/A'}</p>
                </div>
            </div>
        `;
        } else {
            // Cliente Empresa
            html = `
            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-building me-2"></i>Información Empresarial</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Razón Social:</strong> ${data.razon_social || 'N/A'}</p>
                    <p><strong>RUC:</strong> ${data.ruc || 'N/A'}</p>
                    <p><strong>Representante Legal:</strong> ${data.representante_legal || 'N/A'}</p>
                    <p><strong>Email:</strong> ${data.email_empresa || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Teléfono Principal:</strong> ${data.telefono_primario_empresa || 'N/A'}</p>
                    <p><strong>Teléfono Secundario:</strong> ${data.telefono_secundario_empresa || 'N/A'}</p>
                    <p><strong>Distrito:</strong> ${data.distrito || 'N/A'}</p>
                    <p><strong>Provincia:</strong> ${data.provincia || 'N/A'}</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <p><strong>Dirección:</strong> ${data.direccion_empresa || 'N/A'}</p>
                </div>
            </div>
        `;
        }

        document.getElementById('info-cliente-contenido').innerHTML = html;
    }

    function renderizarDetalleContrato(data) {
        const nombreMoneda = data.moneda === 'PEN' ? 'Soles' : 'Dolares';
        const simboloMoneda = data.moneda === 'PEN' ? 'S/.' : '$';
        const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-file-contract me-2"></i>Datos del Contrato</h6>
                <p><strong>N° de Contrato:</strong> ${data.idcontrato || 'N/A'}</p>
                <p><strong>Fecha Inicio:</strong> ${data.fechainicio || 'N/A'}</p>
                <p><strong>Es Crédito:</strong> ${data.escredito === 'S' ? 'Sí' : 'No'}</p>
            </div>
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-car me-2"></i>Datos del Vehículo</h6>
                <p><strong>Vehículo:</strong> ${data.vehiculo_descripcion || 'N/A'}</p>
                <p><strong>Tipo:</strong> ${data.tipovehiculo || 'N/A'}</p>
                <p><strong>Versión:</strong> ${data.version || 'N/A'}</p>
                <p><strong>Condición:</strong> ${data.condicion || 'N/A'}</p>
                <p><strong>Color:</strong> ${data.color || 'N/A'}</p>
                <p><strong>Placa:</strong> ${data.placa || 'N/A'}</p>
                <p><strong>Combustible:</strong> ${data.combustible || 'N/A'}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-dollar-sign me-2"></i>Información Financiera</h6>
                <p><strong>Moneda:</strong> ${nombreMoneda}</p>
                <p><strong>Monto:</strong> ${simboloMoneda} ${parseFloat(data.precioventa || 0).toFixed(2)}</p>
                <p><strong>Inicial:</strong> ${simboloMoneda} ${parseFloat(data.inicial || 0).toFixed(2)}</p>
                <p><strong>Total de Cuotas:</strong> ${data.numcuotas || 'N/A'}</p>
                <p><strong>Valor Cuota:</strong> ${simboloMoneda} ${parseFloat(data.valorcuota || 0).toFixed(2)}</p>
                <p><strong>Gastos Administrativos:</strong> ${simboloMoneda} ${parseFloat(data.gastosadministrativos || 0).toFixed(2)}</p>
            </div>
            <div class="col-md-6">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-comment me-2"></i>Observaciones</h6>
                    <p>${data.observaciones}</p>
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
            lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
            pageLength: 20,
            order: [[0, 'asc']],
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