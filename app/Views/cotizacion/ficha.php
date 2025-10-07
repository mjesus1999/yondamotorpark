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

    [data-bs-theme="dark"] .alert-info {
        background-color: #1e1e1e !important;
        color: #e4e4e7 !important;
        border-left: 4px solid #3b82f6 !important;
    }



    /* Gradientes para cards */
    .card-gradient-blue {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        border: none;
    }

    .card-gradient-green {
        background: linear-gradient(135deg, #cfdac4ff 0%, #defff7ff 100%);
        border: 1px solid #bbf7d0;
    }

    .card-gradient-orange {
        background: linear-gradient(135deg, #dbc3a5ff 0%, #ffa135ff 100%);
        border: 1px solid #fdba74;
    }

    .card-gradient-gray {
        background: #EDF0F4;
        border: 1px solid #cbd5e1;
    }

    /* Botones con gradiente */
    .btn-gradient-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-gradient-green:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        transform: translateY(-1px);
    }

    .btn-gradient-blue {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-gradient-blue:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #6d28d9 100%);
        color: white;
        transform: translateY(-1px);
    }

    /* Efectos de sombra mejorados */
    .shadow-lg {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }

    /* Animaciones suaves */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    /* Colores personalizados para success */
    .text-success-emphasis {
        color: #0d5d2e !important;
    }

    .text-warning-emphasis {
        color: #cc6a00 !important;
    }

    /* Efectos de backdrop para cards con gradiente azul */
    .card-gradient-blue .bg-white {
        backdrop-filter: blur(10px);
    }

    /* Estilos para modales */
    .modal-content {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        border-radius: 1rem 1rem 0 0;
    }

    /* Animaciones de entrada */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeInUp 0.6s ease-out;
    }

    .card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .card:nth-child(4) {
        animation-delay: 0.4s;
    }


    @media (max-width: 768px) {
        .display-4 {
            font-size: 2.5rem;
        }

        .card-body h2 {
            font-size: 1.75rem;
        }

        .card-body h4 {
            font-size: 1.25rem;
        }
    }


    .form-control:focus,
    .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }


    .alert {
        border-radius: 0.75rem;
        border: none;
    }

    .alert-success {
        background-color: #ecfdf5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    .alert-info {
        background-color: #fff;
        color: #19191bff;
        border-left: 4px solid #3b82f6;
        border-top: 2px solid #3b82f6;
    }



    /* Loader personalizado */
    .spinner-custom {
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #2563eb;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Badge personalizado */
    .badge {
        padding: 0.5em 1em;
        font-size: 0.875rem;
        border-radius: 0.5rem;
    }

    /* Mejoras de accesibilidad */
    .btn:focus {
        outline: 2px solid #2563eb;
        outline-offset: 2px;
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

                <a href="/cotizacion/" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-task me-1"></i> Listar
                </a>
            </div>
        </div>
        <hr class="border border-primary border-2 opacity-75">





        <!-- Botón para registrar ficha -->
        <div class="row mt-4 mb-4">
            <div class="col-12 text-end">
                <button class="btn btn-gradient-green shadow-lg" data-bs-toggle="modal" data-bs-target="#fichaSolicitudModal">
                    <i class="bi bi-plus-circle me-2"></i>
                    Registrar Ficha 
                </button>
            </div>
        </div>


        <!-- Cards Grid -->
        <div class="row g-4 mb-4">
            <!-- Card Principal - Vehículo -->
            <div class="col-12">
                <div class="card shadow-lg border-0 card-gradient-blue">
                    <div class="card-header border-0 bg-transparent text-white pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 d-flex align-items-center">
                                <i class="bi bi-car-front me-2"></i>
                                Información del Vehículo
                            </h5>
                            <span class="badge bg-white text-primary bg-opacity-75" id="tipoCotizacion"><?= htmlspecialchars($infoFicha['tipocotizacion']) ?></span>
                        </div>
                    </div>
                    <div class="card-body text-white">
                        <h3 class="fw-bold mb-4" id="vehiculoInfo"><?= htmlspecialchars($infoFicha['vehiculo']) ?></h3>
                        <div class="row bg-white bg-opacity-10 rounded-3 p-4">
                            <div class="col-md-8">
                                <p class="small opacity-75 mb-1">Precio de Venta</p>
                                <h2 class="fw-bold mb-0" id="precioVenta">S/ <?= number_format($infoFicha['precioventa'], 2, '.') ?></h2>
                            </div>
                            <div class="col-md-4 text-end">
                                <p class="small opacity-75 mb-1">Inicial</p>
                                <h4 class="fw-semibold text-white mb-0" id="inicial">S/ <?= number_format($infoFicha['inicial'], 2, '.') ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Cliente -->
            <div class="col-md-6">
                <div class="card shadow-lg border-0 card-gradient-green text-success">
                    <div class="card-header border-0 bg-transparent pb-2">
                        <h5 class="card-title fw-bold  mb-0 d-flex align-items-center text-success">
                            <i class="bi bi-person me-2"></i>
                            Información del Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-hash  me-1"></i>
                            <span class="small  fw-medium mb-1">Nombre</span>
                            <span class="fw-semibold -emphasis mb-0 fw-bold" id="nombreCliente"><?= htmlspecialchars($infoFicha['nombrecliente']) ?></span>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center">

                                <i class="bi bi-person-vcard me-2"></i>
                                <span class="small  me-2">DNI:</span>
                                <span class="fw-semibold -emphasis fw-bold" id="documentoCliente"><?= htmlspecialchars($infoFicha['documento']) ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-telephone  me-2"></i>
                                <span class="small  me-2">Teléfono:</span>
                                <span class="fw-semibold -emphasis fw-bold" id="telefonoCliente"><?= htmlspecialchars($infoFicha['telefono']) ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center opacity-75">
                            <i class="bi bi-geo-alt  me-2"></i>
                            <span class="small " id="direccionCliente"><?= htmlspecialchars($infoFicha['direccion'] ?? 'Dirección no especificada') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Financiamiento -->
            <div class="col-md-6">
                <div class="card shadow-lg border-0 card-gradient-orange">
                    <div class="card-header border-0 bg-transparent pb-2">
                        <h5 class="card-title fw-bold mb-0 d-flex align-items-center text-white">
                            <i class="bi bi-credit-card me-2"></i>
                            Detalles Financieros
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="text-center bg-white bg-opacity-60 rounded-3 p-3">
                                    <p class="small text-warning mb-1">Inicial</p>
                                    <p class="fw-bold text-warning-emphasis mb-0" id="inicialFinanciero">S/ <?= htmlspecialchars(number_format($infoFicha['inicial'], 2, '.')) ?></p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center bg-white bg-opacity-60 rounded-3 p-3">
                                    <p class="small text-warning mb-1">Financiado</p>
                                    <p class="fw-bold text-warning-emphasis mb-0" id="montoFinanciado">S/ <?= htmlspecialchars(number_format($infoFicha['precioventa'] - $infoFicha['inicial'], 2, '.')) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center align-items-center bg-white bg-opacity-60 rounded-3 p-3 mb-3">
                            <i class="bi bi-calendar text-warning me-2"></i>
                            <span class="small text-warning me-2">Cuotas:</span>
                            <span class="fw-bold text-warning-emphasis" id="numCuotas"><?= htmlspecialchars($infoFicha['numcuotas']) ?> meses</span>
                        </div>
                        <div class="text-center">
                            <p class="small text-white mb-1">Cuota mensual aproximada</p>
                            <p class="fw-bold text-warning-emphasis mb-0" id="cuotaMensual">S/ <?= htmlspecialchars(number_format($infoFicha['valorcuota'], 2, '.')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Card Estado - Full Width -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-0 card-gradient-gray">
                    <div class="card-header fw-bold border-0 bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 d-flex align-items-center text-dark">
                                <i class="bi bi-file-text me-2"></i>
                                Estado de la Cotización
                            </h5>
                            <span class="badge bg-warning text-dark" id="estadoBadge">Pendiente</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <p class="small  text-dark mb-1">ID de Cotización</p>
                                <p class="fw-bold text-dark mb-0">#<span id="idCotizacionEstado"><?= htmlspecialchars($infoFicha['idcotizacion']) ?></span></p>
                            </div>
                            <div class="col-md-4">
                                <p class="small text-dark mb-1">Tipo de Cliente</p>
                                <p class="fw-bold text-dark mb-0" id="tipoClienteEstado"><?= htmlspecialchars($infoFicha['tipocotizacion']) ?></p>
                            </div>
                            <div class="col-md-4">
                                <p class="small text-dark mb-1">Estado</p>
                                <p class="fw-bold text-dark mb-0" id="estadoTexto">Pendiente</p>
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
                    },1280);

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