<?php include __DIR__ . '/../layout/header.php'; ?>

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
            <div class="col-md-6 d-flex aling-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Cotizacion</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cotizar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="/cotizacion/create" class="">[ Registrar ]</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-22">
            <div class="card">

                <div class="card-body">
                    <table class="table table-sm table-hover table-hover-yonda" id="tabla-cotizacion">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Numero de Doc</th>
                                <th>Telefono</th>
                                <th>Marca de vehiculo</th>
                                <th>Modelo</th>
                                <th>Año</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($cotizaciones as $index => $c): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($c['nombrecliente']) ?></td>
                                    <td><?= htmlspecialchars($c['documento']) ?></td>
                                    <td><?= htmlspecialchars($c['telefono']) ?></td>
                                    <td><?= htmlspecialchars($c['marcaVehiculo']) ?></td>
                                    <td><?= htmlspecialchars($c['modeloVehiculo']) ?></td>
                                    <td><?= htmlspecialchars($c['anio']) ?></td>
                                    <td>
                                        <a href="/reports/reporte-cotizacion-dependiente.php" target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <i class="fa fa-file-pdf"></i> Hacer Reporte
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>