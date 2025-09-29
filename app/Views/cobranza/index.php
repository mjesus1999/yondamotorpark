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
                        <h3 class="card-title">9</h3>
                        <p class="card-text mb-0">
                            Cliente Deudores
                        </p>
                    </div>

                </div>
            </div>

            <!-- Segundo cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3 class="card-title">4</h3>
                        <p class="card-text mb-0">Por vencer (3 Dias)</p>
                    </div>
                </div>
            </div>

            <!-- Tercer cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                        <h3 class="card-title">5</h3>
                        <p class="card-text mb-0">Vencidos</p>
                    </div>
                </div>
            </div>

            <!-- Cuarto cuadro estadistico -->
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                        <h3 class="card-title">S/. 1,130.00</h3>
                        <P class="card-text mb-0">Total por Cobrar</P>
                    </div>
                </div>
            </div>

        </div>

        <!-- Opciones -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i> Opciones </h6>

                    <!-- Botones -->
                    <div class="btn-group" role="group">

                        <!-- BOTON POR DEFECTO QUE MOSTRARA UN RESUMEN -->
                        <label for="btnTodos" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-list me-1"></i>Todos
                            <span class="badge bg-primary text-dark ms-1">8</span>
                        </label>

                        <!-- BOTON DE 3 DIAS ANTES DE VENCER -->
                        <a href="/Recordatorios" class="btn btn-sm btn-outline-warning me-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> Vence: 3 días
                            <span class="badge bg-warning text-dark ms-1">4</span>
                        </a>

                        <!-- BOTON DE VENCIDOS -->
                        <a href="/Vencidos" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times-circle me-1"></i>Vencidos
                            <span class="badge bg-danger ms-1">5</span>
                        </a>

                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <!-- Cliente 1 - Por vencer -->
            <div class="col-lg-3 mb-5 cobranza-item" data-categoria="por-vencer"
                onclick="irAPagar(1, 'María González', 450.00)">
                <div class="card card-deuda border-start status-por-vencer h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1"><i class="fas fa-user me-2"></i>María González</h5>
                                <p class="text-muted small mb-1"><strong>DNI:</strong> 12345678</p>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 987654321</p>
                                <p class="direccion-info mb-0"><strong>Dirección:</strong> Av. Los Pinos
                                </p>
                            </div>
                            <span class="badge bg-warning text-dark"> 2 días</span>
                        </div>
                        <div class="mb-3">
                            <p class="monto-destacado mb-1">Monto a Cobrar: S/. 450.00</p>
                            <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong> 27/09/2025</p>
                            <p class="text-muted small mb-0"><i class="fas fa-store me-1"></i><strong>Local:</strong>
                                Chincha Alta / Chincha</p>
                        </div>
                        <!-- <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="event.stopPropagation(); mostrarFormularioPago(1, 'María González', 450.00, '12345678', '987654321')">
                                <i class="fas fa-money-bill-wave me-1"></i>Cobrar Ahora
                            </button>
                            <button class="btn btn-outline-info btn-sm"
                                onclick="event.stopPropagation(); contactarCliente('987654321')" title="Contactar">
                                <i class="fas fa-phone"></i>
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- Resto de clientes vencidos -->
            <div class="col-lg-3 mb-5 cobranza-item pulse-animation" data-categoria="vencidos"
                onclick="irAPagar(2, 'Carlos Mendoza', 680.00)">
                <div class="card card-deuda border-start status-vencido h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1"><i class="fas fa-user me-2"></i>Carlos Mendoza</h5>
                                <p class="text-muted small mb-1"><strong>DNI:</strong> 87654321</p>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 912345678</p>
                                <p class="direccion-info mb-0"><strong>Dirección:</strong> Av San Martin</p>
                            </div>
                            <span class="badge bg-danger">1 día vencido</span>
                        </div>
                        <div class="mb-3">
                            <p class="monto-destacado mb-1">Monto a Cobrar: S/. 680.00</p>
                            <p class="text-danger small mb-1"><strong>Venció:</strong> 24/09/2025</p>
                            <p class="text-muted small mb-0"><i class="fas fa-store me-1"></i><strong>Local:</strong>
                                Chincha Alta / Chincha</p>
                        </div>
                        <!-- <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="event.stopPropagation(); mostrarFormularioPago(2, 'Carlos Mendoza', 680.00, '87654321', '912345678')">
                                <i class="fas fa-exclamation-triangle me-1"></i>Cobrar Urgente
                            </button>
                            <button class="btn btn-outline-info btn-sm"
                                onclick="event.stopPropagation(); contactarCliente('912345678')" title="Contactar">
                                <i class="fas fa-phone"></i>
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</head>

<?php include __DIR__ . '/../layout/footer.php'; ?>