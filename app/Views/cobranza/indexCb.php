<!-- app/views/cobranza/index.php -->
<?php include __DIR__ . '/../layout/header.php'; ?>
<style>
    .card-gradient {
        background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
    }

    .border-4 {
        border-width: 4px !important;
    }

    .cobranza-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .status-por-vencer {
        border-left: 4px solid #ffc107;
    }

    .status-vencido {
        border-left: 4px solid #dc3545;
    }

    .direccion-info {
        font-size: 12px;
        color: #6c757d;
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    .monto-destacado {
        font-size: 15px;
        font-weight: bold;
        color: #dc3545;
    }

    .card-deuda {
        background: linear-gradient(135deg, #fff 0%, #fff8f8 100%);
    }

    .btn-cobrar {
        background-color: seagreen;
        border: none;
        color: white;
        padding: 4px 20px;
        transition: all 0.3s ease;
    }

    .btn-cobrar:hover {
        background-color: #2e6b3afb;
        transform: scale(1.00);
        color: white;
    }

    .estadistica-card {
        background: linear-gradient(135deg, #fff, #f8f9fa);
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Estilos para el formulario de pago */
    .formulario-pago {
        background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .cliente-info {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border-radius: 10px;
    }

    .metodo-pago {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .metodo-pago:hover {
        border-color: #007bff;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
    }

    .metodo-pago.selected {
        border-color: #007bff;
        background-color: #e3f2fd;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-out {
        animation: slideOut 0.3s ease-out;
    }

    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }

        to {
            opacity: 0;
            transform: translateX(-50px);
        }
    }
</style>
</head>

<body>
    <div class="container-fluid">
        <div class="alert alert-info mt-2" role="alert">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center justify-content-start">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Área de Cobranza</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Gestión de Clientes con Deudas
                                Pendientes</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-secondary btn-sm" id="btnVolver" onclick="volverALista()"
                        style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i>Volver a la Lista
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas principales -->
        <div class="row mb-4" id="estadisticas">
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-3"></i>
                        <h3 class="card-title">8</h3>
                        <p class="card-text mb-0">Clientes Deudores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-3"></i>
                        <h3 class="card-title">3</h3>
                        <p class="card-text mb-0">Por Vencer (7 días)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x mb-3"></i>
                        <h3 class="card-title">5</h3>
                        <p class="card-text mb-0">Vencidos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card estadistica-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                        <h3 class="card-title">S/. 4,310.00</h3>
                        <p class="card-text mb-0">Total por Cobrar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4" id="filtros">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtrar Deudores</h5>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="filtroDeuda" id="filtroPorVencer" autocomplete="off"
                            checked>
                        <label class="btn btn-outline-warning" for="filtroPorVencer" onclick="filtrarPor('por-vencer')">
                            <i class="fas fa-exclamation-triangle me-1"></i>Por Vencer (3D)
                            <span class="badge bg-warning text-dark ms-1">3</span>
                        </label>

                        <input type="radio" class="btn-check" name="filtroDeuda" id="filtroVencidos" autocomplete="off">
                        <label class="btn btn-outline-danger" for="filtroVencidos" onclick="filtrarPor('vencidos')">
                            <i class="fas fa-times-circle me-1"></i>Vencidos
                            <span class="badge bg-danger ms-1">5</span>
                        </label>

                        <input type="radio" class="btn-check" name="filtroDeuda" id="filtroTodos" autocomplete="off">
                        <label class="btn btn-outline-dark" for="filtroTodos" onclick="filtrarPor('todos')">
                            <i class="fas fa-list me-1"></i>Todos
                            <span class="badge bg-light text-dark ms-1">8</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de deudores -->
        <div class="row" id="listaDeudores">
            <!-- Cliente 1 - Por vencer -->
            <div class="col-lg-6 mb-4 cobranza-item" data-categoria="por-vencer"
                onclick="irAPagar(1, 'María González', 450.00)">
                <div class="card card-deuda border-start status-por-vencer h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1"><i class="fas fa-user me-2"></i>María González</h5>
                                <p class="text-muted small mb-1"><strong>DNI:</strong> 12345678</p>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 987654321</p>
                                <p class="direccion-info mb-0"><strong>Dirección:</strong> Av. Los Pinos 123, San Isidro
                                </p>
                            </div>
                            <span class="badge bg-warning text-dark">5 días</span>
                        </div>
                        <div class="mb-3">
                            <p class="monto-destacado mb-1">Monto a Cobrar: S/. 450.00</p>
                            <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong> 29/09/2025</p>
                            <p class="text-muted small mb-0"><i class="fas fa-store me-1"></i><strong>Local:</strong>
                                MotorPark / Ica / Chincha Alta / Chincha</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="event.stopPropagation(); mostrarFormularioPago(1, 'María González', 450.00, '12345678', '987654321')">
                                <i class="fas fa-money-bill-wave me-1"></i>Cobrar Ahora
                            </button>
                            <button class="btn btn-outline-info btn-sm"
                                onclick="event.stopPropagation(); contactarCliente('987654321')" title="Contactar">
                                <i class="fas fa-phone"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cliente 3 - Por vencer -->
            <div class="col-lg-6 mb-4 cobranza-item" data-categoria="por-vencer"
                onclick="irAPagar(3, 'Ana López', 320.00)">
                <div class="card card-deuda border-start status-por-vencer h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1"><i class="fas fa-user me-2"></i>Ana López</h5>
                                <p class="text-muted small mb-1"><strong>DNI:</strong> 11223344</p>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 998877665</p>
                                <p class="direccion-info mb-0"><strong>Dirección:</strong> Av. Javier Prado 789, Surco
                                </p>
                            </div>
                            <span class="badge bg-warning text-dark">3 días</span>
                        </div>
                        <div class="mb-3">
                            <p class="monto-destacado mb-1">Monto a Cobrar: S/. 320.00</p>
                            <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong> 27/09/2025</p>
                            <p class="text-muted small mb-0"><i class="fas fa-store me-1"></i><strong>Local:</strong>
                                Plaza Norte</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="event.stopPropagation(); mostrarFormularioPago(3, 'Ana López', 320.00, '11223344', '998877665')">
                                <i class="fas fa-money-bill-wave me-1"></i>Cobrar Ahora
                            </button>
                            <button class="btn btn-outline-info btn-sm"
                                onclick="event.stopPropagation(); contactarCliente('998877665')" title="Contactar">
                                <i class="fas fa-phone"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cliente 5 - Por vencer -->
            <div class="col-lg-6 mb-4 cobranza-item" data-categoria="por-vencer"
                onclick="irAPagar(5, 'Sofia Torres', 520.00)">
                <div class="card card-deuda border-start status-por-vencer h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1"><i class="fas fa-user me-2"></i>Sofia Torres</h5>
                                <p class="text-muted small mb-1"><strong>DNI:</strong> 33221100</p>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 977665544</p>
                                <p class="direccion-info mb-0"><strong>Dirección:</strong> Av. Brasil 567, Magdalena</p>
                            </div>
                            <span class="badge bg-warning text-dark">7 días</span>
                        </div>
                        <div class="mb-3">
                            <p class="monto-destacado mb-1">Monto a Cobrar: S/. 520.00</p>
                            <p class="text-muted small mb-1"><strong>Fecha de Vencimiento:</strong> 01/10/2025</p>
                            <p class="text-muted small mb-0"><i class="fas fa-store me-1"></i><strong>Local:</strong>
                                San Miguel</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="event.stopPropagation(); mostrarFormularioPago(5, 'Sofia Torres', 520.00, '33221100', '977665544')">
                                <i class="fas fa-money-bill-wave me-1"></i>Cobrar Ahora
                            </button>
                            <button class="btn btn-outline-info btn-sm"
                                onclick="event.stopPropagation(); contactarCliente('977665544')" title="Contactar">
                                <i class="fas fa-phone"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resto de clientes vencidos -->
            <div class="col-lg-6 mb-4 cobranza-item pulse-animation" data-categoria="vencidos"
                onclick="irAPagar(2, 'Carlos Mendoza', 680.00)">
                <div class="card card-deuda border-start status-vencido h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1"><i class="fas fa-user me-2"></i>Carlos Mendoza</h5>
                                <p class="text-muted small mb-1"><strong>DNI:</strong> 87654321</p>
                                <p class="text-muted small mb-1"><strong>Teléfono:</strong> 912345678</p>
                                <p class="direccion-info mb-0"><strong>Dirección:</strong> Jr. Las Flores 456,
                                    Miraflores</p>
                            </div>
                            <span class="badge bg-danger">15 días vencido</span>
                        </div>
                        <div class="mb-3">
                            <p class="monto-destacado mb-1">Monto a Cobrar: S/. 680.00</p>
                            <p class="text-danger small mb-1"><strong>Venció:</strong> 09/09/2025</p>
                            <p class="text-muted small mb-0"><i class="fas fa-store me-1"></i><strong>Local:</strong>
                                Sucursal Barranco</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-sm flex-grow-1"
                                onclick="event.stopPropagation(); mostrarFormularioPago(2, 'Carlos Mendoza', 680.00, '87654321', '912345678')">
                                <i class="fas fa-exclamation-triangle me-1"></i>Cobrar Urgente
                            </button>
                            <button class="btn btn-outline-info btn-sm"
                                onclick="event.stopPropagation(); contactarCliente('912345678')" title="Contactar">
                                <i class="fas fa-phone"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agregar más clientes según sea necesario -->
        </div>

        <!-- Formulario de Pago -->
        <div class="formulario-pago" id="formularioPago">
            <div class="card">
                <div class="card-header cliente-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1"><i class="fas fa-user me-2"></i><span id="nombreClientePago"></span></h5>

                        </div>
                        <div class="col-md-4 text-end">
                            <p class="mb-0"><strong>DNI:</strong> <span id="dniClientePago"></span> |
                                <strong>Teléfono:</strong> <span id="telefonoClientePago"></span>
                            </p>
                            <!-- <h3 class="mb-0">S/. <span id="montoClientePago"></span></h3>
                            <small>Monto a Cobrar</small> -->
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="formPago" onsubmit="procesarPago(event)">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3"><i class="fas fa-credit-card me-2"></i>Método de Pago</h6>

                                <!-- Efectivo -->
                                <div class="metodo-pago p-3 mb-3" onclick="seleccionarMetodo('efectivo')">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="metodoPago" value="efectivo" id="efectivo"
                                            class="me-3">
                                        <i class="fas fa-money-bill-wave fa-2x me-3 text-success"></i>
                                        <div>
                                            <h6 class="mb-0">Efectivo</h6>
                                            <small class="text-muted">Pago en efectivo</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Yape -->
                                <div class="metodo-pago p-3 mb-3" onclick="seleccionarMetodo('yape')">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="metodoPago" value="yape" id="yape" class="me-3">
                                        <i class="fas fa-mobile-alt fa-2x me-3 text-purple"></i>
                                        <div>
                                            <h6 class="mb-0">Yape</h6>
                                            <small class="text-muted">Pago móvil</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plin -->
                                <div class="metodo-pago p-3 mb-3" onclick="seleccionarMetodo('plin')">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="metodoPago" value="plin" id="plin" class="me-3">
                                        <i class="fas fa-mobile-alt fa-2x me-3 text-info"></i>
                                        <div>
                                            <h6 class="mb-0">Plin</h6>
                                            <small class="text-muted">Pago móvil</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transferencia -->
                                <div class="metodo-pago p-3 mb-3" onclick="seleccionarMetodo('transferencia')">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="metodoPago" value="transferencia" id="transferencia"
                                            class="me-3">
                                        <i class="fas fa-university fa-2x me-3 text-primary"></i>
                                        <div>
                                            <h6 class="mb-0">Transferencia Bancaria</h6>
                                            <small class="text-muted">Transferencia directa</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mb-3"><i class="fas fa-file-invoice me-2"></i>Detalles del Pago</h6>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="number" placeholder="S/." id="montoRecibido" class="form-control"
                                            step="0.01" required onchange="calcularCambio()"
                                            placeholder="Apellidos completos" required>
                                        <label for="form-label">Monto Recibido *<span
                                                class="text-danger">*</span></label>
                                    </div>
                                </div>

                                <!-- <div class="mb-3">
                                    <label class="form-label">Monto Recibido *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/.</span>
                                        <input type="number" class="form-control" id="montoRecibido" step="0.01"
                                            required onchange="calcularCambio()">
                                    </div>
                                </div> -->

                                <!-- <div class="mb-3" id="cambioDivision" style="display: none;">
                                    <label class="form-label">Cambio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/.</span>
                                        <input type="text" class="form-control" id="cambio" readonly>
                                    </div>
                                </div> -->

                                <!-- <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" rows="3"
                                        placeholder="Comentarios adicionales (opcional)"></textarea>
                                </div> -->

                                <div class="mb-3">
                                    <label class="form-label">Fecha y Hora de Pago</label>
                                    <input type="datetime-local" class="form-control" id="fechaPago" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-end">
                                    <button type="button" class="btn btn-secondary" onclick="volverALista()">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check me-1"></i>Confirmar Pago
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mensaje cuando no hay resultados -->
        <div class="col-12" id="noResultados" style="display: none;">
            <div class="alert alert-info text-center">
                <i class="fas fa-search me-2"></i>
                <strong>No se encontraron deudores con los filtros seleccionados.</strong>
            </div>
        </div>
    </div>

    <!-- Toast para notificaciones -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">
                    <i class="fas fa-check-circle text-success me-2"></i>Sistema de Cobranza
                </strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Acción completada
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layout/footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        let clienteActual = null;

        // Filtrar deudores por categoría
        function filtrarPor(categoria) {
            const items = document.querySelectorAll('.cobranza-item');
            const noResultados = document.getElementById('noResultados');
            let itemsVisibles = 0;

            items.forEach(item => {
                if (categoria === 'todos' || item.dataset.categoria === categoria) {
                    item.style.display = 'block';
                    itemsVisibles++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (itemsVisibles === 0) {
                noResultados.style.display = 'block';
            } else {
                noResultados.style.display = 'none';
            }
        }

        // Mostrar formulario de pago
        function mostrarFormularioPago(id, nombre, monto, dni, telefono) {
            clienteActual = { id, nombre, monto, dni, telefono };

            // Ocultar la lista y filtros con animación
            document.getElementById('listaDeudores').classList.add('slide-out');
            document.getElementById('filtros').style.display = 'none';
            document.getElementById('estadisticas').style.display = 'none';
            document.getElementById('btnVolver').style.display = 'inline-block';

            setTimeout(() => {
                document.getElementById('listaDeudores').style.display = 'none';

                // Llenar datos del cliente
                document.getElementById('nombreClientePago').textContent = nombre;
                document.getElementById('dniClientePago').textContent = dni;
                document.getElementById('telefonoClientePago').textContent = telefono;
                /* document.getElementById('montoClientePago').textContent = monto.toFixed(2); */
                document.getElementById('montoRecibido').value = monto.toFixed(2);

                // Establecer fecha actual
                const ahora = new Date();
                ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
                document.getElementById('fechaPago').value = ahora.toISOString().slice(0, 16);

                // Mostrar formulario con animación
                document.getElementById('formularioPago').style.display = 'block';
                document.getElementById('formularioPago').classList.add('fade-in');
            }, 300);
        }

        // Volver a la lista
        function volverALista() {
            document.getElementById('formularioPago').style.display = 'none';
            document.getElementById('filtros').style.display = 'block';
            document.getElementById('estadisticas').style.display = 'flex';
            document.getElementById('btnVolver').style.display = 'none';
            document.getElementById('listaDeudores').style.display = 'flex';
            document.getElementById('listaDeudores').classList.remove('slide-out');

            // Limpiar formulario
            document.getElementById('formPago').reset();
            document.querySelectorAll('.metodo-pago').forEach(metodo => {
                metodo.classList.remove('selected');
            });

            clienteActual = null;
        }

        // Seleccionar método de pago
        /* function seleccionarMetodo(metodo) {
            document.querySelectorAll('.metodo-pago').forEach(item => {
                item.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            document.getElementById(metodo).checked = true;

            // Mostrar/ocultar campo de cambio para efectivo
            if (metodo === 'efectivo') {
                document.getElementById('cambioDivision').style.display = 'block';
                calcularCambio();
            } else {
                document.getElementById('cambioDivision').style.display = 'none';
                document.getElementById('montoRecibido').value = clienteActual.monto.toFixed(2);
            }
        } */

        // Calcular cambio
        /* function calcularCambio() {
            if (!clienteActual) return;

            const montoRecibido = parseFloat(document.getElementById('montoRecibido').value) || 0;
            const montoDeuda = clienteActual.monto;
            const cambio = montoRecibido - montoDeuda;

            document.getElementById('cambio').value = cambio >= 0 ? cambio.toFixed(2) : '0.00';

            // Validar que el monto recibido sea suficiente
            if (montoRecibido < montoDeuda) {
                document.getElementById('cambio').style.color = '#dc3545';
                document.getElementById('cambio').value = 'Monto insuficiente';
            } else {
                document.getElementById('cambio').style.color = '#28a745';
            }
        } */

        // Procesar pago
        /* function procesarPago(event) {
            event.preventDefault();

            const metodoPago = document.querySelector('input[name="metodoPago"]:checked');
            const montoRecibido = parseFloat(document.getElementById('montoRecibido').value);
            const observaciones = document.getElementById('observaciones').value;
            const fechaPago = document.getElementById('fechaPago').value;

            // Validaciones
            if (!metodoPago) {
                mostrarToast('Por favor selecciona un método de pago', 'error');
                return;
            }

            if (montoRecibido < clienteActual.monto) {
                mostrarToast('El monto recibido debe ser mayor o igual al monto de la deuda', 'error');
                return;
            }

            // Simular procesamiento
            const datosTransaccion = {
                clienteId: clienteActual.id,
                cliente: clienteActual.nombre,
                dni: clienteActual.dni,
                montoDeuda: clienteActual.monto,
                montoRecibido: montoRecibido,
                metodoPago: metodoPago.value,
                cambio: montoRecibido - clienteActual.monto,
                observaciones: observaciones,
                fechaPago: fechaPago,
                usuario: 'Usuario Actual', // Aquí iría el usuario logueado
                timestamp: new Date().toISOString()
            };

            console.log('Procesando pago:', datosTransaccion);

            // Aquí irían las llamadas AJAX al backend
            // procesarPagoBackend(datosTransaccion);

            // Mostrar éxito y volver
            mostrarToast(`Pago procesado exitosamente. Cliente: ${clienteActual.nombre}, Monto: S/. ${clienteActual.monto}`, 'success');

            setTimeout(() => {
                volverALista();
            }, 2000);
        } */

        // Mostrar toast
        /* function mostrarToast(mensaje, tipo = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const toastHeader = toast.querySelector('.toast-header strong');

            toastMessage.textContent = mensaje;

            if (tipo === 'error') {
                toastHeader.innerHTML = '<i class="fas fa-exclamation-circle text-danger me-2"></i>Error';
                toast.classList.add('bg-danger', 'text-white');
            } else {
                toastHeader.innerHTML = '<i class="fas fa-check-circle text-success me-2"></i>Sistema de Cobranza';
                toast.classList.remove('bg-danger', 'text-white');
            }

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        } */

        // Contactar cliente (placeholder)
        /* function contactarCliente(telefono) {
            mostrarToast(`Contactando al cliente: ${telefono}`, 'info');
        } */

        // Ir a pagar (placeholder para cuando se hace clic en la tarjeta)
        function irAPagar(id, nombre, monto) {
            // Esta función se puede usar para otros propósitos si es necesario
            console.log(`Seleccionado cliente: ${nombre}, Monto: ${monto}`);
        }

        // Animaciones de entrada al cargar la página
        document.addEventListener('DOMContentLoaded', function () {
            // Animar tarjetas al cargar
            const cards = document.querySelectorAll('.cobranza-item');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Aplicar filtro inicial
            filtrarPor('por-vencer');
        });
    </script>

</body>

</html>