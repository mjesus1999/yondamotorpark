<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    [data-bs-theme="dark"] .card:not(.card-gradient-blue):not(.card-gradient-green):not(.card-gradient-orange):not(.card-gradient-gray) {
        background: #1e1e1e !important;
        border-color: #2c2c2c !important;
        color: #e4e4e7 !important;
    }

    [data-bs-theme="dark"] .card:not(.card-gradient-blue):not(.card-gradient-green):not(.card-gradient-orange):not(.card-gradient-gray) .card-title,
    [data-bs-theme="dark"] .card:not(.card-gradient-blue):not(.card-gradient-green):not(.card-gradient-orange):not(.card-gradient-gray) .fw-bold,
    [data-bs-theme="dark"] .card:not(.card-gradient-blue):not(.card-gradient-green):not(.card-gradient-orange):not(.card-gradient-gray) p,
    [data-bs-theme="dark"] .card:not(.card-gradient-blue):not(.card-gradient-green):not(.card-gradient-orange):not(.card-gradient-gray) span {
        color: #e4e4e7 !important;
    }

    [data-bs-theme="dark"] .card:not(.card-gradient-blue):not(.card-gradient-green):not(.card-gradient-orange):not(.card-gradient-gray) .card-header {
        background-color: #2a2a2a !important;
        border-bottom: 1px solid #3a3a3a !important;
        color: #fff !important;
    }

    /* Gradientes para cards */
    .card-gradient-blue {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .card-gradient-blue::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
        animation: shimmer 10s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-15%, -15%); }
    }

    .card-gradient-green {
        background: linear-gradient(135deg, #cfdac4ff 0%, #defff7ff 100%);
        border: 2px solid #bbf7d0;
        position: relative;
        overflow: hidden;
    }

    .card-gradient-green::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s;
    }

    .card-gradient-green:hover::after {
        left: 100%;
    }

    [data-bs-theme="dark"] .card-gradient-green {
        background: linear-gradient(135deg, #1a3a1a 0%, #0d3030 100%);
        border: 2px solid #2d5a2d;
    }

    .card-gradient-orange {
        background: linear-gradient(135deg, #dbc3a5ff 0%, #ffa135ff 100%);
        border: 2px solid #fdba74;
        position: relative;
        overflow: hidden;
    }

    [data-bs-theme="dark"] .card-gradient-orange {
        background: linear-gradient(135deg, #3a2a1a 0%, #4a3015 100%);
        border: 2px solid #6d4a2d;
    }

    .card-gradient-gray {
        background: linear-gradient(135deg, #f8fafc 0%, #EDF0F4 100%);
        border: 2px solid #cbd5e1;
        position: relative;
    }

    [data-bs-theme="dark"] .card-gradient-gray {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 2px solid #334155;
    }

    /* Efectos hover mejorados */
    .card-hover-effect {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover-effect:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2) !important;
    }

    [data-bs-theme="dark"] .card-hover-effect:hover {
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5) !important;
    }

    /* Título del vehículo */
    .vehiculo-title {
        font-size: 1.75rem;
        font-weight: 700;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        letter-spacing: -0.5px;
    }

    /* Caja de precios con glassmorphism */
    .precio-box {
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.12) !important;
        border: 1px solid rgba(255, 255, 255, 0.18);
        transition: all 0.3s ease;
    }

    .precio-box:hover {
        background: rgba(255, 255, 255, 0.18) !important;
        transform: scale(1.02);
    }

    /* Badge personalizado */
    .badge-premium {
        background: rgba(255, 255, 255, 0.95);
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .badge-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    /* Info rows mejoradas para cliente */
    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 0.85rem 1rem;
        background: rgba(255, 255, 255, 0.35);
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .info-item:hover {
        background: rgba(255, 255, 255, 0.5);
        transform: translateX(8px);
        border-color: rgba(16, 185, 129, 0.3);
    }

    [data-bs-theme="dark"] .info-item {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.08);
    }

    [data-bs-theme="dark"] .info-item:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .info-icon-badge {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 10px;
        color: white;
        font-size: 1.1rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        transition: transform 0.3s ease;
    }

    .info-item:hover .info-icon-badge {
        transform: rotate(10deg) scale(1.1);
    }

    [data-bs-theme="dark"] .info-icon-badge {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .info-label-small {
        font-size: 0.7rem;
        color: #059669;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 0.15rem;
        display: block;
    }

    [data-bs-theme="dark"] .info-label-small {
        color: #6ee7b7;
    }

    .info-value-text {
        font-weight: 700;
        color: #065f46;
        font-size: 0.95rem;
        line-height: 1.4;
    }

    [data-bs-theme="dark"] .info-value-text {
        color: #d1fae5;
    }

    
    .finance-card {
        background: rgba(255, 255, 255, 0.8);
        border: 2px solid rgba(245, 158, 11, 0.2);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        text-align: center;
    }

    .finance-card:hover {
        transform: translateY(-5px) scale(1.03);
        border-color: #f59e0b;
        box-shadow: 0 12px 24px rgba(245, 158, 11, 0.25);
        background: rgba(255, 255, 255, 0.95);
    }

    .finance-icon {
        font-size: 2rem;
        color: #f59e0b;
        margin-bottom: 0.5rem;
        display: block;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .finance-amount {
        font-weight: 700;
        color: #d97706;
        font-size: 1.1rem;
    }

    [data-bs-theme="dark"] .finance-amount {
        color: #fbbf24;
    }

    /* Status info boxes */
    .status-box {
        padding: 1.25rem;
        border-radius: 14px;
        transition: all 0.3s ease;
        background: rgba(0, 0, 0, 0.02);
    }

    .status-box:hover {
        background: rgba(0, 0, 0, 0.04);
        transform: translateY(-5px);
    }

    [data-bs-theme="dark"] .status-box:hover {
        background: rgba(255, 255, 255, 0.06);
    }

    .status-icon-circle {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 0.75rem;
        font-size: 1.6rem;
        transition: all 0.3s ease;
    }

    .status-box:hover .status-icon-circle {
        transform: rotate(360deg) scale(1.1);
    }

    /* Badge estado */
    .estado-badge {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #78350f;
        padding: 0.65rem 1.3rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 16px rgba(251, 191, 36, 0.35);
        transition: all 0.3s ease;
    }

    .estado-badge:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(251, 191, 36, 0.45);
    }

    [data-bs-theme="dark"] .estado-badge {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: #fef3c7;
        box-shadow: 0 4px 16px rgba(217, 119, 6, 0.4);
    }

    /* Animaciones de entrada */
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-left {
        animation: slideInLeft 0.6s ease-out;
    }

    .animate-slide-up-1 {
        animation: slideInUp 0.7s ease-out;
    }

    .animate-slide-up-2 {
        animation: slideInUp 0.8s ease-out;
    }

    .animate-slide-up-3 {
        animation: slideInUp 0.9s ease-out;
    }

    /* Sombras mejoradas */
    .shadow-premium {
        box-shadow: 0 15px 35px -8px rgba(0, 0, 0, 0.15), 0 8px 15px -5px rgba(0, 0, 0, 0.1) !important;
    }

    [data-bs-theme="dark"] .shadow-premium {
        box-shadow: 0 15px 35px -8px rgba(0, 0, 0, 0.45), 0 8px 15px -5px rgba(0, 0, 0, 0.35) !important;
    }

    /* Colores de texto dark mode */
    [data-bs-theme="dark"] .text-dark {
        color: #e4e4e7 !important;
    }

    [data-bs-theme="dark"] .text-muted {
        color: #9ca3af !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .vehiculo-title {
            font-size: 1.4rem;
        }

        .card-hover-effect:hover {
            transform: translateY(-4px);
        }

        .info-item:hover {
            transform: translateX(4px);
        }

        .status-icon-circle {
            width: 54px;
            height: 54px;
            font-size: 1.3rem;
        }
    }
</style>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Subir ficha</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">

                <a href="/cotizacion/S" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-task me-1"></i> Listar
                </a>
            </div>
        </div>
    </div>





        <!-- Botón para registrar ficha -->
        <div class="row mt-4 mb-4">
            <div class="col-12 text-end">
                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#fichaSolicitudModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Registrar Ficha
                </button>
            </div>
        </div>


        <!-- Cards Grid -->
        <div class="row g-4 mb-4">
            <!-- Card Principal - Vehículo -->
            <div class="col-12">
                <div class="card shadow-premium border-0 card-gradient-blue card-hover-effect animate-slide-left">
                    <div class="card-header border-0 bg-transparent text-white pb-2">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5 class="card-title fw-bold mb-0 d-flex align-items-center">
                                <i class="bi bi-car-front-fill me-2"></i>
                                Información del Vehículo
                            </h5>
                            <span class="badge-premium text-primary" id="tipoCotizacion">
                                <?= htmlspecialchars($infoFicha['tipocotizacion']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body text-white">
                        <h3 class="vehiculo-title mb-4" id="vehiculoInfo">
                            <?= htmlspecialchars($infoFicha['vehiculo']) ?>
                        </h3>
                        <div class="row precio-box rounded-4 p-4">
                            <div class="col-md-8 mb-3 mb-md-0">
                                <p class="small opacity-75 mb-2 text-uppercase" style="letter-spacing: 1px;">Precio de Venta</p>
                                <h2 class="fw-bold mb-0" id="precioVenta" style="font-size: 2.2rem; letter-spacing: -0.5px;">
                                    S/ <?= number_format($infoFicha['precioventa'], 2, '.', ',') ?>
                                </h2>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <p class="small opacity-75 mb-2 text-uppercase" style="letter-spacing: 1px;">Inicial</p>
                                <h4 class="fw-bold text-white mb-0" id="inicial" style="font-size: 1.5rem;">
                                    S/ <?= number_format($infoFicha['inicial'], 2, '.', ',') ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Cliente -->
            <div class="col-md-6">
                <div class="card shadow-premium border-0 card-gradient-green card-hover-effect animate-slide-up-1 h-100">
                    <div class="card-header border-0 bg-transparent pb-2">
                        <h5 class="card-title fw-bold mb-0 d-flex align-items-center" style="color: #059669;">
                            <i class="bi bi-person-circle me-2"></i>
                            Información del Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Nombre -->
                        <div class="info-item mb-3">
                            <div class="info-icon-badge">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="info-label-small">Nombre Completo</span>
                                <div class="info-value-text" id="nombreCliente">
                                    <?= htmlspecialchars($infoFicha['nombrecliente']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- DNI -->
                        <div class="info-item mb-3">
                            <div class="info-icon-badge">
                                <i class="bi bi-person-vcard"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="info-label-small">DNI</span>
                                <div class="info-value-text" id="documentoCliente">
                                    <?= htmlspecialchars($infoFicha['documento']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="info-item mb-3">
                            <div class="info-icon-badge">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="info-label-small">Teléfono</span>
                                <div class="info-value-text" id="telefonoCliente">
                                    <?= htmlspecialchars($infoFicha['telefono']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="info-item">
                            <div class="info-icon-badge">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="info-label-small">Dirección</span>
                                <div class="info-value-text" id="direccionCliente">
                                    <?= htmlspecialchars($infoFicha['direccion'] ?? 'Dirección no especificada') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Financiamiento -->
            <div class="col-md-6">
                <div class="card shadow-premium border-0 card-gradient-orange card-hover-effect animate-slide-up-2 h-100">
                    <div class="card-header border-0 bg-transparent pb-2">
                        <h5 class="card-title fw-bold mb-0 d-flex align-items-center text-white">
                            <i class="bi bi-credit-card-2-front-fill me-2"></i>
                            Detalles Financieros
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Inicial y Financiado -->
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="finance-card">
                                    <i class="bi bi-cash-stack finance-icon"></i>
                                    <p class="small fw-semibold text-dark mb-1">Inicial</p>
                                    <p class="finance-amount mb-0" id="inicialFinanciero">
                                        S/ <?= number_format($infoFicha['inicial'], 2, '.', ',') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="finance-card">
                                    <i class="bi bi-piggy-bank-fill finance-icon"></i>
                                    <p class="small fw-semibold text-dark mb-1">Financiado</p>
                                    <p class="finance-amount mb-0" id="montoFinanciado">
                                        S/ <?= number_format($infoFicha['precioventa'] - $infoFicha['inicial'], 2, '.', ',') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Cuotas -->
                        <div class="d-flex justify-content-center align-items-center finance-card mb-3">
                            <i class="bi bi-calendar-range text-warning me-2 fs-5"></i>
                            <span class="small fw-semibold text-dark me-2">Plazo:</span>
                            <span class="finance-amount" id="numCuotas">
                                <?= htmlspecialchars($infoFicha['numcuotas']) ?> meses
                            </span>
                        </div>

                        <!-- Cuota Mensual -->
                        <div class="finance-card">
                            <i class="bi bi-calendar-check-fill finance-icon"></i>
                            <p class="small fw-semibold text-dark mb-1">Cuota mensual aproximada</p>
                            <p class="finance-amount mb-0 fs-5" id="cuotaMensual">
                                S/ <?= number_format($infoFicha['valorcuota'], 2, '.', ',') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Estado - Full Width -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-premium border-0 card-gradient-gray card-hover-effect animate-slide-up-3">
                    <div class="card-header fw-bold border-0 bg-transparent">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5 class="card-title mb-0 d-flex align-items-center text-dark">
                                <i class="bi bi-file-text-fill me-2"></i>
                                Estado de la Cotización
                            </h5>
                            <span class="estado-badge" id="estadoBadge">
                                <i class="bi bi-clock-history"></i>
                                Separado
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-4">
                            <!-- ID Cotización -->
                            <div class="col-md-4">
                                <div class="status-box">
                                    <div class="status-icon-circle bg-primary bg-opacity-10">
                                        <i class="bi bi-hash text-primary"></i>
                                    </div>
                                    <p class="small text-muted mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                        ID de Cotización
                                    </p>
                                    <p class="fw-bold text-dark mb-0 fs-5">
                                        #<span id="idCotizacionEstado"><?= htmlspecialchars($infoFicha['idcotizacion']) ?></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Tipo Cliente -->
                            <div class="col-md-4">
                                <div class="status-box">
                                    <div class="status-icon-circle bg-success bg-opacity-10">
                                        <i class="bi bi-person-badge-fill text-success"></i>
                                    </div>
                                    <p class="small text-muted mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                        Tipo de Cliente
                                    </p>
                                    <p class="fw-bold text-dark mb-0 fs-5" id="tipoClienteEstado">
                                        <?= htmlspecialchars($infoFicha['tipocotizacion']) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-4">
                                <div class="status-box">
                                    <div class="status-icon-circle bg-warning bg-opacity-10">
                                        <i class="bi bi-info-circle-fill text-warning"></i>
                                    </div>
                                    <p class="small text-muted mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                                        Estado
                                    </p>
                                    <p class="fw-bold text-dark mb-0 fs-5" id="estadoTexto">
                                        Separado
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ficha de Solicitud -->
        <div class="modal fade" id="fichaSolicitudModal" tabindex="-1" aria-labelledby="fichaSolicitudModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);;">
                        <h5 class="modal-title d-flex align-items-center" id="fichaSolicitudModalLabel">
                            <i class="bi bi-file-text me-2"></i>
                            Registrar Ficha de Solicitud
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">


                        <form id="fichaSolicitudForm">
                            <input type="hidden" value="<?= htmlspecialchars($infoFicha['idcotizacion']) ?>" id="idcotizacion">
                            <!-- Información básica -->
                            <div class="row mb-4">

                                <div class="col-md-12">
                                    <label for="fechaVisita" class="form-label fw-bold">Fecha de Visita <span class="text-danger fw-bold">*</span></label>
                                    <input type="date" class="form-control" id="fechaVisita" required>
                                </div>
                            </div>

                            <!-- Referencias Personales -->
                            <h6 class="mb-3 d-flex align-items-center fw-bold">
                                <i class="bi bi-people me-2"></i>
                                Referencias Personales
                            </h6>

                            <!-- Cónyuge -->
                            <div class="card  border-primary mb-3" style="background-color: #EFF6FF;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label text-primary fw-medium mb-0">Cónyuge (Opcional)</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openPersonModal('conyuge')">
                                            <i class="bi bi-person me-1"></i>
                                            <span id="conyugeButtonText">Buscar/Agregar</span>
                                        </button>
                                    </div>
                                    <div id="conyugeInfo" class="d-none">
                                        <div class="bg-body rounded p-3">
                                            <p class="fw-medium text-body mb-1" id="conyugeNombre"></p>
                                            <p class="small text-body fw-bold mb-0" id="conyugeDetalle"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aval -->
                            <div class="card border-success mb-3" style="background-color: #F0FDF4;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label text-success fw-medium mb-0">Aval (Opcional)</label>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="openPersonModal('aval')">
                                            <i class="bi bi-shield me-1"></i>
                                            <span id="avalButtonText">Buscar/Agregar</span>
                                        </button>
                                    </div>
                                    <div id="avalInfo" class="d-none">
                                        <div class="bg-body rounded p-3">
                                            <p class="fw-medium mb-1 text-body " id="avalNombre"></p>
                                            <p class="small text-body fw-bold mb-0" id="avalDetalle"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cónyuge del Aval -->
                            <div class="card  border-warning mb-4" style="background-color: #fcffc8ff;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label text-warning fw-medium mb-0">Cónyuge del Aval (Opcional)</label>
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="openPersonModal('avalConyuge')">
                                            <i class="bi bi-people me-1"></i>
                                            <span id="avalConyugeButtonText">Buscar/Agregar</span>
                                        </button>
                                    </div>
                                    <div id="avalConyugeInfo" class="d-none">
                                        <div class="bg-body rounded p-3">
                                            <p class="fw-medium mb-1 text-body" id="avalConyugeNombre"></p>
                                            <p class="small text-body fw-bold mb-0" id="avalConyugeDetalle"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Subir archivo -->
                            <div class="mb-4">
                                <label for="archivoFicha" class="form-label d-flex align-items-center gap-1 fw-bold">
                                    <i class="bi bi-upload me-2"></i>
                                    Subir Ficha <span class="text-danger fw-bold"> *</span>
                                </label>
                                <input type="file" class="form-control" id="archivoFicha" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>

                                <div id="archivoSeleccionado" class="text-success small mt-1 d-none"></div>
                            </div>

                            <div class="row">
                                <div class="d-flex gap-2">

                                    <div class="form-check text-success fw-bold">
                                        <input class="form-check-input" type="radio" name="detalle" value="Aprobado" id="aprobado" checked>
                                        <label class="form-check-label" for="aprobado">
                                            Aprobado
                                        </label>
                                    </div>
                                    <div class="form-check text-warning fw-bold">
                                        <input class="form-check-input" type="radio" name="detalle" value="Observado" id="observado">
                                        <label class="form-check-label" for="observado">
                                            Observado
                                        </label>
                                    </div>
                                    <div class="form-check text-danger fw-bold">
                                        <input class="form-check-input" type="radio" name="detalle" value="Anulado" id="anulado">
                                        <label class="form-check-label" for="anulado">
                                            Anulado
                                        </label>
                                    </div>
                                </div>


                            </div>



                            <!-- Comentarios -->
                            <div class="mb-4 mt-3">
                                <label for="comentarios" class="form-label d-flex align-items-center">
                                    <i class="bi bi-chat-text me-2"></i>
                                    Comentarios
                                </label>
                                <textarea class="form-control" id="comentarios" rows="4" placeholder="Agregar comentarios adicionales sobre la solicitud..."></textarea>
                            </div>



                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-sm text-white" id="submitBtn" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);">
                            Registrar Ficha
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Modal Búsqueda de Personas -->
        <div class="modal fade" id="personSearchModal" tabindex="-1" aria-labelledby="personSearchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white fw-bold" style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);">
                        <h5 class="modal-title d-flex align-items-center" id="personSearchModalLabel">
                            <i class="bi bi-person me-1"></i>
                            <span id="personModalTitle">Buscar/Agregar Persona</span>

                        </h5>
                        <button type="button" class="btn-close  btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Búsqueda por DNI -->
                        <div class="row mb-4">
                            <div class="col-9">
                                <label for="searchDni" class="form-label">Buscar por DNI</label>
                                <input type="text" class="form-control" id="searchDni" placeholder="Ingrese número de DNI" maxlength="8">
                            </div>
                            <div class="col-3 d-flex align-items-end">
                                <button type="button" class="btn bg-dark border border-secondary text-white  w-100" onclick="searchPerson()">
                                    <i class="bi bi-search me-1"></i>
                                    Buscar
                                </button>
                            </div>
                        </div>

                        <!-- Persona encontrada -->
                        <div id="personFound" class="alert alert-success d-none">
                            <h6 class="alert-heading">Persona encontrada</h6>
                            <div class="row" id="foundPersonDetails"></div>
                            <hr>
                            <button type="button" class="btn btn-success w-100" onclick="selectFoundPerson()">
                                Seleccionar esta persona
                            </button>
                        </div>

                        <!-- Formulario nueva persona -->
                        <div id="newPersonForm" class="alert alert-info d-none">
                            <h6 class="alert-heading d-flex align-items-center fw-bold">
                                <i class="bi bi-plus-circle me-2 text-primary fw-bold"></i>
                                Agregar nueva persona
                            </h6>
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="newTipoDoc" class="form-label">Tipo de Documento <span class="text-danger fw-bold">*</span> </label>
                                    <select class="form-select" id="newTipoDoc">
                                        <option value="DNI" selected>DNI</option>
                                        <option value="CEX">Carné de Extranjería</option>
                                        <option value="PAS">Pasaporte</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="newNroDoc" class="form-label">Número de Documento <span class="text-danger fw-bold">*</span> </label>
                                    <input type="text" class="form-control" id="newNroDoc" placeholder="Número de documento">
                                </div>
                                <div class="col-md-4">
                                    <label for="newApellidos" class="form-label">Apellidos <span class="text-danger fw-bold">*</span> </label>
                                    <input type="text" class="form-control" id="newApellidos" placeholder="Apellidos completos">
                                </div>
                                <div class="col-md-4">
                                    <label for="newNombres" class="form-label">Nombres <span class="text-danger fw-bold">*</span> </label>
                                    <input type="text" class="form-control" id="newNombres" placeholder="Nombres completos">
                                </div>

                                <div class="col-md-4">
                                    <label for="newGenero" class="form-label">Género <span class="text-danger fw-bold">*</span> </label>
                                    <select class="form-select" id="newGenero">
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="newTelefono" class="form-label">Teléfono <span class="text-danger fw-bold">*</span> </label>
                                    <input type="text" class="form-control" id="newTelefono" placeholder="999999999" maxlength="9">
                                </div>
                                <div class="col-6">
                                    <label for="newDireccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="newDireccion" placeholder="Dirección completa">
                                </div>


                                <div class="col-md-4">
                                    <label for="departamento" class="form-label">Departamento <span class="text-danger fw-bold">*</span> </label>
                                    <select class="form-select" id="departamento">
                                        <option>Seleccione</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="provincia" class="form-label">Provincias <span class="text-danger fw-bold">*</span> </label>
                                    <select class="form-select" id="provincia">
                                        <option>Seleccione</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="distrito" class="form-label">Distritos <span class="text-danger fw-bold">*</span> </label>
                                    <select class="form-select" id="distrito">
                                        <option>Seleccione</option>
                                    </select>
                                </div>

                            </div>
                            <hr>
                            <button type="button" class="btn bg-dark text-white border border-secondary w-100" onclick="addNewPerson()" id="addPersonBtn">
                                Agregar y Seleccionar
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>



        <?php include __DIR__ . '/../layout/footer.php'; ?>

        <script src="/assets/js/ubigeo.js" defer></script>

        <script>
            // Variables globales
            let selectedPersons = {
                conyuge: null,
                aval: null,
                avalConyuge: null
            };

            let currentPersonType = '';
            let foundPerson = null;




            // Configurar event listeners
            function setupEventListeners() {
                const inputs = ['newApellidos', 'newNombres', 'newNroDoc', 'newTelefono'];
                inputs.forEach(id => {
                    const input = document.getElementById(id);
                    if (input) {
                        input.addEventListener('input', validateNewPersonForm);
                    }
                });


                const distritoInput = document.getElementById('distrito');
                if (distritoInput) {
                    distritoInput.addEventListener('change', validateNewPersonForm);
                }
            }


            // Validar formulario principal
            function validateForm() {
                const fecha = document.getElementById('fechaVisita')?.value || '';
                const archivo = document.getElementById('archivoFicha')?.files.length > 0 || false;

                const isValid = fecha && archivo;
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.disabled = !isValid;
                }
            }

            // Validar formulario nueva persona
            function validateNewPersonForm() {
                const apellidos = document.getElementById('newApellidos')?.value.trim();
                const nombres = document.getElementById('newNombres')?.value.trim();
                const nroDoc = document.getElementById('newNroDoc')?.value.trim();
                const telefono = document.getElementById('newTelefono')?.value.trim();
                const distrito = parseInt(document.getElementById('distrito')?.value.trim());
                console.log(distrito, 'valor distrito')

                const btnAgregar = document.getElementById('addPersonBtn');

                let isValid = true;

                //  Validaciones básicas
                if (!apellidos || apellidos.length < 2) isValid = false;
                if (!nombres || nombres.length < 2) isValid = false;
                if (!nroDoc || nroDoc.length < 8) isValid = false;
                if (!telefono || telefono.length < 6) isValid = false;
                if (!distrito || distrito == 0) isValid = false;

                //  Habilitar/Deshabilitar botón
                if (btnAgregar) {
                    btnAgregar.disabled = !isValid;
                }
            }


            // Abrir modal de búsqueda de persona
            function openPersonModal(personType) {
                currentPersonType = personType;

                const titles = {
                    'conyuge': 'Buscar/Agregar Cónyuge',
                    'aval': 'Buscar/Agregar Aval',
                    'avalConyuge': 'Buscar/Agregar Cónyuge del Aval'
                };

                const titleElement = document.getElementById('personModalTitle');
                if (titleElement) {
                    titleElement.textContent = titles[personType];
                }

                // Limpiar formularios
                const searchDniInput = document.getElementById('searchDni');
                if (searchDniInput) {
                    searchDniInput.value = '';
                }

                const personFound = document.getElementById('personFound');
                if (personFound) {
                    personFound.classList.add('d-none');
                }

                const newPersonForm = document.getElementById('newPersonForm');
                if (newPersonForm) {
                    newPersonForm.classList.add('d-none');
                }

                clearNewPersonForm();

                // Abrir modal
                const modal = new bootstrap.Modal(document.getElementById('personSearchModal'));
                modal.show();
            }


            // Buscar persona por DNI
            async function searchPerson() {
                const searchDniInput = document.getElementById('searchDni');
                if (!searchDniInput) return;

                const dni = searchDniInput.value.trim();

                if (!dni) {
                    alert('Por favor ingrese un número de DNI');
                    return;
                }

                try {
                    const response = await fetch(`/api/fichaSolicitud/searchPersonaByDNI/${dni}`);
                    const data = await response.json();
                    // console.log('ARRAY: ', data);

                    if (data.success && data.persona) {
                        foundPerson = {
                            idpersona: data.persona.idpersona,
                            nombres: data.persona.nombres,
                            apellidos: data.persona.apellidos,
                            nrodoc: data.persona.dni,
                            telprimario: data.persona.telprimario,
                            genero: data.persona.genero,
                            estadocivil: data.persona.estadocivil
                        };

                        showToast('Persona encontrada', 'SUCCESS', 1400);
                        showFoundPerson(foundPerson);

                        const newPersonForm = document.getElementById('newPersonForm');
                        if (newPersonForm) newPersonForm.classList.add('d-none');
                    } else {

                        try {
                            const response = await fetch(`/api/fichaSolicitud/searchPersonaByReniec/${dni}`);
                            const data = await response.json();

                            if (data.success) {

                                showNewPersonForm(data, dni);
                                const personFound = document.getElementById('personFound');
                                if (personFound) personFound.classList.add('d-none');
                                showToast('No se encontró en registros locales, pero sí en RENIEC', 'INFO', 1800);
                                console.log('PERSONA EN RENIEC: ', data);
                            } else {

                                showToast('No se encontró la persona en ningún registro', 'WARNING', 1800);
                                console.log('PERSONA NO EXISTE');
                            }

                        } catch (error) {
                            console.error('Error en búsqueda RENIEC:', error);
                            showToast('Error al consultar RENIEC', 'ERROR', 1250);
                        }

                    }

                } catch (error) {
                    console.error('Error en búsqueda:', error);
                    showToast('Ocurrió un error al buscar la persona', 'ERROR', 1250);
                }
            }



            // Mostrar persona encontrada
            function showFoundPerson(person) {
                const detailsDiv = document.getElementById('foundPersonDetails');
                if (detailsDiv) {
                    detailsDiv.innerHTML = `
                <input type="hidden" value=${person.idepersona}></input>
            <div class="col-md-6"><strong>Nombres:</strong> ${person.nombres}</div>
            <div class="col-md-6"><strong>Apellidos:</strong> ${person.apellidos}</div>
            <div class="col-md-6"><strong>DNI:</strong> ${person.nrodoc}</div>
            <div class="col-md-6"><strong>Teléfono:</strong> ${person.telprimario}</div>
            <div class="col-md-6"><strong>Estado Civil:</strong> ${person.estadocivil}</div>
            <div class="col-md-6"><strong>Género:</strong> ${person.genero}</div>
        `;
                }

                const personFound = document.getElementById('personFound');
                if (personFound) {
                    personFound.classList.remove('d-none');
                }
            }




            // Mostrar formulario nueva persona
            function showNewPersonForm(person, dni) {
                const newNroDocInput = document.getElementById('newNroDoc');
                const newApellidosInput = document.getElementById('newApellidos');
                const newNombresInput = document.getElementById('newNombres');
                if (newNroDocInput && newApellidosInput && newNombresInput) {
                    newNroDocInput.value = dni;
                    newApellidosInput.value = person.apellidos;
                    newNombresInput.value = person.nombres;
                }

                const newPersonForm = document.getElementById('newPersonForm');
                if (newPersonForm) {
                    newPersonForm.classList.remove('d-none');
                }

                validateNewPersonForm();
            }

            // Limpiar formulario nueva persona
            function clearNewPersonForm() {
                const fields = [{
                        id: 'newApellidos',
                        value: ''
                    },
                    {
                        id: 'newNombres',
                        value: ''
                    },
                    {
                        id: 'newTipoDoc',
                        value: 'DNI'
                    },
                    {
                        id: 'newNroDoc',
                        value: ''
                    },
                    {
                        id: 'newGenero',
                        value: 'M'
                    },
                    {
                        id: 'newFechaNac',
                        value: ''
                    },
                    {
                        id: 'newEstadoCivil',
                        value: 'SOL'
                    },
                    {
                        id: 'newTelefono',
                        value: ''
                    },
                    {
                        id: 'newDireccion',
                        value: ''
                    }
                ];

                // Reset de inputs normales
                fields.forEach(field => {
                    const element = document.getElementById(field.id);
                    if (element) element.value = field.value;
                });

                // Reset de selects de Ubigeo
                const departamento = document.getElementById('departamento');
                const provincia = document.getElementById('provincia');
                const distrito = document.getElementById('distrito');

                if (departamento) {
                    departamento.innerHTML = '<option value="">Seleccione departamento</option>';
                    getAllDepartamentos();
                }
                if (provincia) {
                    provincia.innerHTML = '<option value="">Seleccione</option>';
                }
                if (distrito) {
                    distrito.innerHTML = '<option value="">Seleccione</option>';
                }
            }




            // Seleccionar persona encontrada
            function selectFoundPerson() {
                if (foundPerson) {
                    selectPerson(foundPerson);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('personSearchModal'));
                    if (modal) {
                        modal.hide();
                    }
                }
            }


            async function addNewPerson() {
                try {
                    const newPerson = {
                        apellidos: document.getElementById('newApellidos')?.value.trim() || '',
                        nombres: document.getElementById('newNombres')?.value.trim() || '',
                        tipodoc: document.getElementById('newTipoDoc')?.value || 'DNI',
                        nrodoc: document.getElementById('newNroDoc')?.value.trim() || '',
                        genero: document.getElementById('newGenero')?.value || 'M',
                        direccion: document.getElementById('newDireccion')?.value.trim() || '',
                        telprimario: document.getElementById('newTelefono')?.value.trim() || '',
                        iddistrito: document.getElementById('distrito')?.value || ''
                    };

                    // Validación obligatorios
                    if (!newPerson.apellidos || !newPerson.nombres || !newPerson.nrodoc || !newPerson.telprimario || !newPerson.iddistrito) {
                        alert('Por favor complete todos los campos obligatorios');
                        return;
                    }


                    const response = await fetch('/storePersonaFicha', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams(newPerson).toString()
                    });

                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {

                        showToast('Persona registrada correctamente', 'SUCCESS', 1200);
                        // Asignar el ID real de BD
                        newPerson.idpersona = data.lastId;
                        selectPerson(newPerson);


                        const modal = bootstrap.Modal.getInstance(document.getElementById('personSearchModal'));
                        if (modal) modal.hide();

                    } else {
                        showToast(data.message || 'No se pudo registrar la persona', 'ERRROR', 1350);
                    }

                } catch (error) {

                    showToast('Ocurrió un error al registrar la persona. Intente nuevamente.', 1350);
                }
            }




            // Seleccionar persona
            function selectPerson(person) {
                // Guardar la persona seleccionada
                selectedPersons[currentPersonType] = person;

                // Actualizar UI
                const infoDiv = document.getElementById(`${currentPersonType}Info`);
                const nombreSpan = document.getElementById(`${currentPersonType}Nombre`);
                const detalleSpan = document.getElementById(`${currentPersonType}Detalle`);
                const buttonTextSpan = document.getElementById(`${currentPersonType}ButtonText`);

                if (nombreSpan) {
                    nombreSpan.textContent = `${person.nombres} ${person.apellidos}`;
                }

                if (detalleSpan) {
                    detalleSpan.textContent = `DNI: ${person.nrodoc} | Tel: ${person.telprimario}`;
                }

                if (buttonTextSpan) {
                    buttonTextSpan.textContent = 'Cambiar';
                }

                if (infoDiv) {
                    infoDiv.classList.remove('d-none');
                }

                console.log('Persona seleccionada:', person);
                console.log('Tipo:', currentPersonType);
                console.log('Personas seleccionadas:', selectedPersons);
            }


            async function submitFichaSolicitud() {
                const idcotizacion = document.getElementById('idcotizacion').value;
                const fechaVisita = document.getElementById('fechaVisita')?.value || '';
                const comentarios = document.getElementById('comentarios')?.value || '';
                const archivo = document.getElementById('archivoFicha')?.files[0] || null;

                // Validar radio estado
                const estadoRadio = document.querySelector("input[name='detalle']:checked");
                if (!estadoRadio) {
                    showToast("Debes seleccionar un estado (Observado, Aprobado o Anulado).", "INFO", 1300);
                    return;
                }
                const estado = estadoRadio.value.toUpperCase();

                // Validaciones mínimas
                if (!idcotizacion || !fechaVisita || !archivo) {
                    showToast('Por favor complete todos los campos obligatorios', 'WARNING', 1300);
                    return;
                }

                // Construir FormData
                const formData = new FormData();
                formData.append("idcotizacion", idcotizacion);
                formData.append("fechavisita", fechaVisita);
                formData.append("comentarios", comentarios);
                formData.append("estado", estado);
                formData.append("rutaficha", archivo);

                // Personas seleccionadas
                formData.append("idconyuge", selectedPersons.conyuge?.idpersona || "");
                formData.append("idaval", selectedPersons.aval?.idpersona || "");
                formData.append("idavalconyuge", selectedPersons.avalConyuge?.idpersona || "");

                try {
                    const response = await fetch("/storeFicha", {
                        method: "POST",
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {

                        showToast("Ficha registrada correctamente", 'SUCCESS', 1200);

                        // Cerrar modal y resetear
                        const modal = bootstrap.Modal.getInstance(document.getElementById('fichaSolicitudModal'));
                        if (modal) modal.hide();

                        const ruta = estado ? estado[0] : 'P';

                        setTimeout(() => {
                            window.location = `/cotizacion/${ruta}`;
                        }, 1280);

                        resetForm();
                    } else {
                        showToast(result.message, 'ERROR', 1250);
                    }
                } catch (err) {
                    console.error(err);
                    showToast("Ocurrió un error en la conexión.", "ERROR", 1250);
                }
            }


            document.getElementById('submitBtn').addEventListener('click', async () => {
                if (await ask('¿Seguro de registrar la ficha?', 'Registrar')) {
                    submitFichaSolicitud();
                }
            })







            // Resetear formulario
            function resetForm() {
                const form = document.getElementById('fichaSolicitudForm');
                if (form) {
                    form.reset();
                }

                const archivoDiv = document.getElementById('archivoSeleccionado');
                if (archivoDiv) {
                    archivoDiv.classList.add('d-none');
                }

                // Limpiar personas seleccionadas
                selectedPersons = {
                    conyuge: null,
                    aval: null,
                    avalConyuge: null
                };

                // Ocultar info de personas y resetear botones
                ['conyuge', 'aval', 'avalConyuge'].forEach(type => {
                    const infoDiv = document.getElementById(`${type}Info`);
                    if (infoDiv) {
                        infoDiv.classList.add('d-none');
                    }

                    const buttonText = document.getElementById(`${type}ButtonText`);
                    if (buttonText) {
                        buttonText.textContent = 'Buscar/Agregar';
                    }
                });

                validateForm();
            }


            document.addEventListener('DOMContentLoaded', function() {
                const distritoInput = document.getElementById('distrito');
                if (distritoInput) {
                    distritoInput.addEventListener('change', validateNewPersonForm);
                }


                // Limpiar formulario cuando se cierre el modal
                const fichaSolicitudModal = document.getElementById('fichaSolicitudModal');
                if (fichaSolicitudModal) {
                    fichaSolicitudModal.addEventListener('hidden.bs.modal', function() {
                        resetForm();
                    });
                }
            });
        </script>