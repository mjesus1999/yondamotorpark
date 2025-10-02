<!-- app/views/cobranza/index.php -->
<?php include __DIR__ . '/../layout/header.php'; ?>

<head>

<body>

    <div class="container-fluid">

        <!-- CABECERA -->
        <div class="alert alert-info mt-2" role="alert">
            <div class="row">
                <nav class="col-md-6 d-flex align-items-center justify-content-start">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Area de cobranza</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"> Gestion de clientes con Deudas
                                pendientes</li>
                        </ol>
                    </nav>
                </nav>
                <div class="col-md-6 text-end">
                    <button class="btn btn-secondary btn-sm" id="btnVolver" onclick="volverALista()"
                        style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i>Volver a la Lista
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadisticas principales -->
        <div class="row mb-2" id="estadisticas">

            <!-- Primer cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3 class="card-title" id="stat-deudores">3</h3>
                        <p class="card-text mb-0">Cliente Deudores</p>
                    </div>
                </div>
            </div>

            <!-- Segundo cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3 class="card-title">3</h3>
                        <p class="card-text mb-0">Por vencer (3 Dias)</p>
                    </div>
                </div>
            </div>

            <!-- Tercer cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                        <h3 class="card-title">2</h3>
                        <p class="card-text mb-0">Vencidos</p>
                    </div>
                </div>
            </div>

            <!-- Cuarto cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                        <h3 class="card-title" id="stat-total-cobrar">S/. 11,725.58</h3>
                        <P class="card-text mb-0">Total por Cobrar</P>
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
                        <input type="radio" id="btnTodos" name="filtro" class="d-none">
                        <label for="btnTodos" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-list me-1"></i>Todos
                            <span class="badge bg-primary text-dark ms-1" id="badge-todos">3</span>
                        </label>

                        <!-- POR VENCER -->
                        <a href="/Recordatorios" class="btn btn-sm btn-outline-warning me-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> Vence: 3 días
                            <span class="badge bg-warning text-dark ms-1">3</span>
                        </a>

                        <!-- VENCIDOS -->
                        <a href="/Vencidos" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times-circle me-1"></i>Vencidos
                            <span class="badge bg-danger ms-1">2</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="contenedor-tarjetas">


            <!-- Tarjeta 1: Flores Mendoza Juana María -->
            <div class="col-lg-4 mb-2 cobranza-item" id="contrato-card-1" data-deuda="2196.00" data-monto="2196.00">
                <div class="card card-deuda border-start status-por-vencer">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="card-title mb-2"><i class="fas fa-user me-2"></i>Flores Mendoza Juana María
                                </h6>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 957973856</p>
                                <p class="text-muted small mb-1"><strong>Local:</strong> Chincha Alta / Chincha</p>
                                <p class="text-muted small mb-0"><strong>Tipo de vehiculo:</strong> GEELY Emgrand</p>
                            </div>
                            <span class="badge bg-warning text-dark">Vence en 3 días</span>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong> 03/10/2025</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="mostrarFormularioPago(1, 'Flores Mendoza Juana María', 2196.00, 2196.00)">
                                Ver detalle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2: Pérez Mendoza Enrique Martín -->
            <div class="col-lg-4 mb-2 cobranza-item" id="contrato-card-2" data-deuda="2415.60" data-monto="2415.60">
                <div class="card card-deuda border-start status-por-vencer">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="card-title mb-2"><i class="fas fa-user me-2"></i>Pérez Mendoza Enrique Martín
                                </h6>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 973946354</p>
                                <p class="text-muted small mb-1"><strong>Local:</strong> Chincha Alta / Chincha</p>
                                <p class="text-muted small mb-0"><strong>Tipo de vehiculo:</strong> GEELY Emgrand</p>
                            </div>
                            <span class="badge bg-warning text-dark">Vence en 3 días</span>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong> 03/10/2025</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="mostrarFormularioPago(2, 'Pérez Mendoza Enrique Martín', 2415.60, 2415.60)">
                                Ver detalle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 3: Flores Mendoza Juana María (HYUNDAI) -->
            <div class="col-lg-4 mb-2 cobranza-item" id="contrato-card-7" data-deuda="7113.98" data-monto="6894.38">
                <div class="card card-deuda border-start status-por-vencer">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="card-title mb-1"><i class="fas fa-user me-2"></i>Flores Mendoza Juana María
                                </h6>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 957973856</p>
                                <p class="text-muted small mb-1"><strong>Local:</strong> Chincha Alta / Chincha</p>
                                <p class="text-muted small mb-0"><strong>Tipo de vehiculo:</strong> HYUNDAI i20 Hatch
                                </p>
                            </div>
                            <span class="badge bg-warning text-dark">Vence HOY</span>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong> 01/10/2025</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="mostrarFormularioPago(7, 'Flores Mendoza Juana María', 6894.38, 7113.98)">
                                Ver detalle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</head>

<?php include __DIR__ . '/../layout/footer.php'; ?>