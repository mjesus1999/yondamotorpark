<?php include __DIR__ . '/../layout/header.php'; ?>

<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">

<style>
    :root {
        --font-size: 16px;
        --background: #ffffff;
        --foreground: oklch(0.145 0 0);
        --card: #ffffff;
        --card-foreground: oklch(0.145 0 0);
        --popover: oklch(1 0 0);
        --popover-foreground: oklch(0.145 0 0);
        --primary: #030213;
        --primary-foreground: oklch(1 0 0);
        --secondary: oklch(0.95 0.0058 264.53);
        --secondary-foreground: #030213;
        --muted: #ececf0;
        --muted-foreground: #717182;
        --accent: #e9ebef;
        --accent-foreground: #030213;
        --destructive: #d4183d;
        --destructive-foreground: #ffffff;
        --border: rgba(0, 0, 0, 0.1);
        --input: transparent;
        --input-background: #f3f3f5;
        --switch-background: #cbced4;
        --font-weight-medium: 500;
        --font-weight-normal: 400;
        --ring: oklch(0.708 0 0);
        --chart-1: oklch(0.646 0.222 41.116);
        --chart-2: oklch(0.6 0.118 184.704);
        --chart-3: oklch(0.398 0.07 227.392);
        --chart-4: oklch(0.828 0.189 84.429);
        --chart-5: oklch(0.769 0.188 70.08);
        --radius: 0.625rem;
        --sidebar: oklch(0.985 0 0);
        --sidebar-foreground: oklch(0.145 0 0);
        --sidebar-primary: #030213;
        --sidebar-primary-foreground: oklch(0.985 0 0);
        --sidebar-accent: oklch(0.97 0 0);
        --sidebar-accent-foreground: oklch(0.205 0 0);
        --sidebar-border: oklch(0.922 0 0);
        --sidebar-ring: oklch(0.708 0 0);
    }


    :root {
        --contrato-gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --contrato-gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --contrato-gradient-success: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --contrato-gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --contrato-info-card-bg: linear-gradient(135deg, #f8f9ff 0%, #e8eeff 100%);
        --contrato-info-card-border: #e0e7ff;
        --contrato-section-bg: #ffffff;
        --contrato-section-border: #e2e8f0;
        --contrato-section-hover-border: #667eea;
        --contrato-text-primary: #2d3748;
        --contrato-text-muted: #718096;
        --contrato-icon-accent: #667eea;
    }


    /* Modal Content */
    #contratoModal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        background: var(--background);
    }

    /* Modal Header */
    #contratoModal .modal-header {
        background: var(--contrato-gradient-primary);
        color: white;
        border: none;
        padding: 1.5rem 2rem;
        position: relative;
    }

    #contratoModal .modal-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    #contratoModal .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.9;
        transition: all 0.3s ease;
    }

    #contratoModal .modal-header .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    #contratoModal .modal-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        position: relative;
        z-index: 1;
    }

    /* Info Card - Cotización seleccionada */
    #contratoModal .info-card {
        border: 2px solid var(--contrato-info-card-border);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background: var(--contrato-info-card-bg);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    #contratoModal .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--contrato-gradient-primary);
    }

    #contratoModal .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
    }


    #contratoModal .info-card .bi-file-text {
        color: var(--contrato-icon-accent);
        font-size: 1.5rem;
    }

    #contratoModal .info-card p {
        margin: 0;
    }

    #contratoModal .info-card .text-muted {
        color: var(--contrato-text-muted);
        font-size: 0.875rem;
    }

    #contratoModal .info-card p:not(.text-muted) {
        font-weight: 600;
        color: var(--contrato-text-primary);
    }

    /* Card Sections */
    #contratoModal .card-section {
        border: 2px solid var(--contrato-section-border);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        background: var(--contrato-section-bg);
        position: relative;
        overflow: hidden;
    }

    #contratoModal .card-section::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--contrato-gradient-primary);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    #contratoModal .card-section:hover {
        border-color: var(--contrato-section-hover-border);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.12);
        transform: translateY(-2px);
    }


    #contratoModal .card-section:hover::after {
        transform: scaleX(1);
    }

    #contratoModal .card-section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        font-weight: 600;
        color: var(--contrato-text-primary);
    }

    /* Iconos en las secciones */
    #contratoModal .card-section-title i {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.25rem;
        transition: all 0.3s ease;
    }

    #contratoModal .card-section:hover .card-section-title i {
        transform: scale(1.1) rotate(5deg);
    }

    #contratoModal .icon-primary {
        background: var(--contrato-gradient-primary);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }



    #contratoModal .icon-info {
        background: var(--contrato-gradient-info);
        color: white;
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
    }



    #contratoModal .icon-success {
        background: var(--contrato-gradient-success);
        color: white;
        box-shadow: 0 4px 15px rgba(67, 233, 123, 0.3);
    }



    #contratoModal .icon-warning {
        background: var(--contrato-gradient-warning);
        color: white;
        box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
    }


    /* Form Labels */
    #contratoModal .form-label {
        font-weight: 600;
        color: var(--contrato-text-primary);
        margin-bottom: 0.5rem;
        display: block;
    }

    #contratoModal .form-label .required {
        color: #f43f5e;
        margin-left: 2px;
    }

    /* Form Controls */
    #contratoModal .form-control,
    #contratoModal .form-select {
        border: 2px solid var(--contrato-section-border);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        background: var(--background);
        color: var(--foreground);
    }

    #contratoModal .form-control:focus,
    #contratoModal .form-select:focus {
        border-color: var(--contrato-section-hover-border);
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
        outline: none;
    }


    #contratoModal .form-control::placeholder {
        color: var(--contrato-text-muted);
    }

    /* Form Text */
    #contratoModal .form-text {
        color: var(--contrato-text-muted);
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    /* Modal Footer */
    #contratoModal .modal-footer {
        border: none;
        padding: 1.5rem 2rem;
        background: var(--background);
    }

    /* Botones */
    #contratoModal .btn-gradient-primary {
        background: var(--contrato-gradient-primary);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }



    #contratoModal .btn-gradient-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }



    #contratoModal .btn-outline-custom {
        border: 2px solid var(--contrato-section-hover-border);
        color: var(--contrato-section-hover-border);
        background: transparent;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    #contratoModal .btn-outline-custom:hover {
        background: var(--contrato-section-hover-border);
        color: white;
        transform: translateY(-2px);
    }

    /* Animación Sparkle */
    #contratoModal .sparkle {
        display: inline-block;
        animation: sparkle 2s ease-in-out infinite;
    }

    @keyframes sparkle {

        0%,
        100% {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }

        50% {
            transform: scale(1.3) rotate(180deg);
            opacity: 0.7;
        }
    }

    /* Animación Fade In Up */
    #contratoModal .fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }



    /* Responsive */
    @media (max-width: 768px) {
        #contratoModal .modal-header {
            padding: 1.25rem 1.5rem;
        }

        #contratoModal .modal-body {
            padding: 1.25rem !important;
        }

        #contratoModal .card-section,
        #contratoModal .info-card {
            padding: 1.25rem;
        }

        #contratoModal .card-section-title i {
            width: 36px;
            height: 36px;
            font-size: 1.1rem;
        }
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
<script src="/assets/js/cotizacionPDF.js"></script>

<script>

    
    function mostrarModalReserva(cliente, motivo) {
        const mensajeEl = document.getElementById('reservaMensaje');
        const modalEl = document.getElementById('reservaModal');
        if (!mensajeEl || !modalEl) return;

        const textoMotivo = motivo === 'contrato' ?
            'Este vehículo ya ha sido vendido (tiene un contrato asociado) por el cliente:' :
            'Este vehículo ya está separado por el cliente:';

        mensajeEl.innerHTML = `<p class="mb-0">${textoMotivo}<br><strong class="text-primary">${cliente || 'No disponible'}</strong></p>`;

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };

    document.addEventListener('DOMContentLoaded', async () => {


        const aprobarModalElement = document.getElementById('aprobarModal');
        const cotizacionAprobarIdInput = document.getElementById('cotizacion-aprobar-id');
        const clienteAprobarNombreStrong = document.getElementById('cliente-aprobar-nombre');
        const confirmarAprobarBtn = document.getElementById('confirmarAprobarBtn');
        const diaPago = document.getElementById('diapago');
        const fechaInicio = document.getElementById('fechainicio');

        const selectLocal = document.getElementById('idlocal');











        window.APP_DATA_TABLE = <?php echo json_encode($cotizaciones); ?>;

        window.APP_CONFIG = {
            puedeVerTodas: <?php echo $puede_ver_todas ? 'true' : 'false'; ?>,
            estadoActual: '<?php echo $estadoActual; ?>'
        };


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
                paginationSize: 15,
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
                        responsive: 2 ,// OCULTAR DESPUES _> '0' ES NO MOSTRARA,
                        tooltip:true
                    },
                    {
                        title: "Vehículo",
                        field: "vehiculo",
                        hozAlign: "left",
                        tooltip:true,
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
                        responsive: 1,
                        tooltip:true
                    },
                    {
                        title: "Documento",
                        field: "documento",
                        hozAlign: "center",
                        minWidth: 120,
                        responsive: 0,
                        tooltip:true
                    },
                    {
                        title: "Asesor",
                        field: "asesor_nombre",
                        hozAlign: "left",
                        minWidth: 150,
                        responsive: 0,
                        tooltip:true
                    },
                    {
                        title: "Acciones",
                        field: "idcotizacion",
                        headerSort: false,
                        minWidth: 120,
                        responsive: 0,
                        formatter: function(cell) {
                            // 1. Extraer todos los datos necesarios de la fila
                            const {
                                idcotizacion: id,
                                estadocotizacion: estado,
                                nombrecliente,
                                numcuotas,
                                habilitar_contrato,
                                existe_reserva,
                                idcotizacion_reserva,
                                reserva_cliente_nombre,
                                contrato_cliente_nombre,
                                vehiculo_en_contrato
                            } = cell.getRow().getData();

                            // 2. Definir estados clave para una lógica más clara
                            const inicialCompleta = Number(habilitar_contrato) === 1;
                            const esMiReserva = Number(idcotizacion_reserva) === Number(id);
                            const vehiculoYaVendido = Number(vehiculo_en_contrato) === 1 && !esMiReserva;
                            const vehiculoReservadoPorOtro = Number(existe_reserva) === 1 && !esMiReserva;

                            const acciones = [];

                            // Acción de PDF (siempre presente)
                            acciones.push(`
                                    <a class="px-1" href="#" onclick="event.preventDefault(); /* tu función para ver PDF */" title="PDF Cotización">
                                        <i class="bi bi-filetype-pdf text-danger fs-5"></i>
                                    </a>
                            `);

                            // 3. Determinar acciones según el estado de la fila actual
                            switch (estado) {

                                case 'P': // PENDIENTE
                                    if (vehiculoYaVendido) {
                                        acciones.push(`
                                        <span class="px-1" style="cursor: pointer;" onclick="mostrarModalReserva('${contrato_cliente_nombre}', 'contrato')" title="Vehículo ya vendido">
                                            <i class="bi bi-currency-dollar fs-5 text-muted"></i>
                                        </span>`);
                                                        } else if (vehiculoReservadoPorOtro) {
                                                            acciones.push(`
                                        <span class="px-1" style="cursor: pointer;" onclick="mostrarModalReserva('${reserva_cliente_nombre}', 'reservado')" title="Vehículo separado">
                                            <i class="bi bi-currency-dollar fs-5 text-muted"></i>
                                        </span>`);
                                                        } else {
                                        acciones.push(`
                                    <a class="px-1" href="/cotizacion/pagoInicial/${id}" title="Registrar Primer Pago">
                                        <i class="bi bi-currency-dollar fs-5 text-success"></i>
                                    </a>`);
                                    }
                                    break;

                                case 'S': // SEPARADO
                                    acciones.push(`
                                <a class="px-1" href="/fichasolicitud/${id}" title="Adjuntar Ficha de Solicitud">
                                    <i class="bi bi-file-earmark-plus fs-5 text-warning fw-bold"></i>
                                </a>`);

                                    if (!inicialCompleta) {
                                        acciones.push(`
                                    <a class="px-1" href="/cotizacion/pagoInicial/${id}" title="Continuar Pagando Inicial">
                                        <i class="bi bi-currency-dollar fs-5 text-success"></i>
                                    </a>`);
                                    } else {
                                        acciones.push(`<span class="px-1" title="Inicial completa"><i class="bi bi-currency-dollar fs-5 text-muted"></i></span>`);
                                    }
                                    break;

                                case 'A': // APROBADO
                                    if (inicialCompleta) {
                                        if (vehiculoYaVendido) {
                                            acciones.push(`
                                                <span class="px-1" style="cursor: pointer;" onclick="mostrarModalReserva('${reserva_cliente_nombre}', 'contrato')" title="Vehículo ya tiene contrato">
                                                    <i class="bi bi-file-earmark-text fs-5 text-muted"></i>
                                                </span>`);
                                        } else {
                                            acciones.push(`
                                                <a class="px-1 text-info fw-bold btnCrearContrato" title="Crear Contrato"
                                                data-id="${id}" data-cliente="${nombrecliente}" data-numcuotas="${numcuotas}"
                                                data-bs-toggle="modal" data-bs-target="#contratoModal">
                                                <i class="bi bi-file-earmark-text fs-5"></i>
                                                </a>`);
                                        }
                                        acciones.push(`<span class="px-1" title="Inicial Pagada"><i class="bi bi-currency-dollar fs-5 text-muted"></i></span>`);
                                    } else {
                                        acciones.push(`
                                                <a class="px-1" href="/cotizacion/pagoInicial/${id}" title="Completar Pago de Inicial">
                                                    <i class="bi bi-currency-dollar fs-5 text-success"></i>
                                                </a>`);
                                    }
                                    break;
                            }

                            // 4. Unir y devolver todas las acciones
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

        document.addEventListener('click', (event) => {
            const btn = event.target.closest('.btnCrearContrato');
            if (!btn) return;

            const idCotizacion = btn.dataset.id;
            const cliente = btn.dataset.cliente;
            const numCuotas = btn.dataset.numcuotas;

            inputIdCotizacion.value = idCotizacion;
            displayIdCotizacion.textContent = `${idCotizacion} - ${cliente} (${numCuotas} cuotas)`;
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





    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>