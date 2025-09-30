<?php include __DIR__ . '/../layout/header.php'; ?>

<head>

<body>

    <!-- <h2>HOLA</h2> -->

    <div class="container-fluid">

        <!-- CABECERA -->
        <div class="alert alert-info mt-2" role="alert">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center justify-content-start">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="#" class="text-decoration-none">Área de Cobranza</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Notificar a los Clientes
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/cobranza" class="btn btn-outline-primary btn-sm">Volver</a>
                </div>
            </div>
        </div>


        <!-- TABLA DE DATOS -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <!-- CABECERA DE LA TABLA -->
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Clientes por Notificar</h6>
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane me-1"></i>Notificar Todos
                            </button>
                        </div>
                    </div>

                    <!-- TABLA CON DATOS -->
                    <div class="card-body">
                        <?php if (!empty($cobranza)): ?>
                            <table class="table table-sm table-hover table-hover-yonda">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Telefono</th>
                                        <th>Vehiculo</th>
                                        <th>Tienda</th>
                                        <th>Cuotas</th>
                                        <th>Monto Cuota</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <!-- <?php $i = 1; ?>
                                    <?php foreach ($cobranza as $fila): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($fila['cliente']) ?></td>
                                            <td><?= htmlspecialchars($fila['telefono']) ?></td>
                                            <td><?= htmlspecialchars($fila['vehiculo']) ?></td>
                                            <td><?= htmlspecialchars($fila['direccion_local']) ?></td>
                                            <td><?= htmlspecialchars($fila['cuota_formato']) ?></td>
                                            <td>S/. <?= htmlspecialchars($fila['montocuota'], 2) ?></td>
                                            <td><?= date('d/m/Y', strtotime($fila['fechapago'])) ?></td>
                                            <td class="text-center">
                                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-check"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?> -->

                                    <!-- Primera persona -->
                                    <tr>
                                    <td>1</td>
                                    <td>Juan Carlos Pérez</td>
                                    <td>987456123</td>
                                    <td>Honda CB 125</td>
                                    <td>Chincha Alta</td>
                                    <td>3 de 12</td>
                                    <td>S/. 350.00</td>
                                    <td>25/09/2025</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>

                                    <!-- Segunda persona -->
                                    <tr>
                                    <td>2</td>
                                    <td>Maria Gonzales Lopéz</td>
                                    <td>985647162</td>
                                    <td>Yamaha YBR 125</td>
                                    <td>Chincha Alta</td>
                                    <td>8 de 24</td>
                                    <td>S/. 2000.00</td>
                                    <td>26/09/2025</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>

                                    <!-- Tercera persona -->
                                    <tr>
                                    <td>3</td>
                                    <td>Carlos Antonio Ruiz</td>
                                    <td>985455518</td>
                                    <td>Bajaj Pulsar 180</td>
                                    <td>Chincha Alta</td>
                                    <td>15 de 18</td>
                                    <td>S/. 2500.00</td>
                                    <td>27/09/2025</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-secondary">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </td>
                                </tr>

                                    <!-- <tr>
                                    <td>4</td>
                                    <td>Jorge Saldaña Ara</td>
                                    <td>953245845</td>
                                    <td>Bajaj Pulsar 180</td>
                                    <td>Chincha Alta</td>
                                    <td>1 de 18</td>
                                    <td>S/. 1200.00</td>
                                    <td>27/09/2025</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr> -->

                                </tbody>

                            </table>

                        <?php else: ?>
                            <p>No hay cuotas proximas</p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

    </div>


</body>

</head>

<?php include __DIR__ . '/../layout/footer.php'; ?>