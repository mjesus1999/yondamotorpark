<?php include __DIR__ . '/../layout/header.php'; ?>

<head>

<body>
    <!-- <h2>HOLA</h2> -->

    <div class="container-fluid">

        <!-- CABECERA -->
        <div class="alert alert-info mt-2" role="alert">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center justify-content-start">
                    <nav arial-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#" class="text-decoration-none">
                                    Área de Cobranza
                                </a>
                            <li class="breadcrumb-item active" aria-current="page">
                                Vencidos
                            </li>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/cobranza" class="btn btn-sm btn-outline-primary">Volver</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <!-- CABECERA DE LA TABLA -->
                    <!-- <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-times-circle me-2"></i>Clientes Vencidos</h6>
                        </div>
                    </div> -->

                    <!-- TABLA CON DATOS -->
                    <div class="card-body">
                        <table class="table table-sm table-hover table-hover-yonda">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Telefono</th>
                                    <th>Vehiculo</th>
                                    <th>Tienda</th>
                                    <th>Cuotas T.</th>
                                    <th>Monto Cuota</th>
                                    <!-- <th>F.Vencimiento</th> -->
                                    <th>Deuda</th>
                                    <th>Estado de pagos</th>
                                    <th>Reporte</th>
                                </tr>
                            </thead>

                            <tbody>

                                <!-- Primer dato -->
                                <tr>
                                    <td>1</td>
                                    <td>Ana Lucia Torres</td>
                                    <td>987321654</td>
                                    <td>Suzuki Swift</td>
                                    <td>Chincha Baja</td>
                                    <td>36</td>
                                    <td>S/. 650.00</td>
                                    <!-- <td>28/09/2025</td> -->
                                    <td>
                                        <span class="text-danger fw-bold">S/. 1,950.00</span>
                                        <!-- <br> -->
                                        <!-- <small class="text-muted">3 cuotas vencidas</small> -->
                                    </td>
                                    <td>
                                        3 cuotas vencidas
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF"
                                                onclick="window.open('/reportesAtrasado', '_blank')">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF Recojo"
                                                onclick="window.open('/reportesRecojo', '_blank')">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                        <!-- <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF"
                                            onclick="window.open('/reportesAtrasado', '_blank')">
                                            <i class="fas fa-file-pdf"></i>
                                        </button> -->
                                    </td>
                                    <!-- <td class="text-center">
                                        <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </td> -->
                                </tr>

                                <!-- Segundo dato -->
                                <tr>
                                    <td>2</td>
                                    <td>Juan Hernández López</td>
                                    <td>987454555</td>
                                    <td>Kia Rio Sedán</td>
                                    <td>Chincha Alta</td>
                                    <td>60</td>
                                    <td>S/. 850.00</td>
                                    <!-- <td>29/09/2025</td> -->
                                    <td>
                                        <span class="text-danger fw-bold">S/. 1,700.00</span>
                                        <!-- <br>
                                        <small class="text-muted">2 cuotas vencidas</small> -->
                                    </td>
                                    <td>2 cuotas vencidas</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF"
                                                onclick="window.open('/reportesAtrasado2', '_blank')">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                        <!-- <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button> -->
                                    </td>
                                </tr>

                                <!-- Tercer dato -->
                                <tr>
                                    <td>3</td>
                                    <td>María González Pérez</td>
                                    <td>956789123</td>
                                    <td>Hyundai Grand i10</td>
                                    <td>Chincha Alta</td>
                                    <td>48</td>
                                    <td>S/. 720.00</td>
                                    <!-- <td>30/09/2025</td> -->
                                    <td>
                                        <span class="text-danger fw-bold">S/. 3,600.00</span>
                                        <!-- <br>
                                        <small class="text-muted">5 cuotas vencidas</small> -->
                                    </td>
                                    <td>5 cuotas vencidas</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Cuarto dato -->
                                <tr>
                                    <td>4</td>
                                    <td>Carlos Antonio Ruiz</td>
                                    <td>924567890</td>
                                    <td>Bajaj Pulsar 180</td>
                                    <td>Chincha Alta</td>
                                    <td>24</td>
                                    <td>S/. 520.00</td>
                                    <!-- <td>01/10/2025</td> -->
                                    <td>
                                        <span class="text-warning fw-bold">S/. 520.00</span>
                                        <!-- <br>
                                        <small class="text-muted">1 cuota vencida</small> -->
                                    </td>
                                    <td>1 cuota vencidas</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Quinto dato -->
                                <tr>
                                    <td>5</td>
                                    <td>Roberto Silva Mendoza</td>
                                    <td>945678234</td>
                                    <td>Honda Wave 110</td>
                                    <td>Chincha Alta</td>
                                    <td>18</td>
                                    <td>S/. 280.00</td>
                                    <!-- <td>02/10/2025</td> -->
                                    <td>
                                        <span class="text-info fw-bold">S/. 125.00</span>
                                        <!-- <br>
                                        <small class="text-muted">Saldo restante</small> -->
                                    </td>
                                    <td>Saldo restante</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger mt-2" title="Generar reporte PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="text-end fw-bold">
                                        <i class="fas fa-calculator me-2"></i>Total deuda:
                                    </td>
                                    <td class="fw-bold text-danger fs-6">
                                        S/. 27,895.00
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

</body>
</head>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<!-- 
<script>
    // Auto-generar PDF cuando se carga la página
    window.onload = function () {
        // Aquí iría tu código de pdfMake que ya tienes
        const docDefinition = {
            // Tu definición del documento aquí
        };

        // Generar y abrir PDF automáticamente
        pdfMake.createPdf(docDefinition).open();
    };
</script> -->