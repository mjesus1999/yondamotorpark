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
                                <th>Vehículo</th>
                                <th class="text-end">Inicial</th>
                                <th class="text-end">Modalidad</th>
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

                                    <!-- Vehículo (MARCA/MODELO/AÑO) -->
                                    <td><?= htmlspecialchars($c['vehiculo'] ?? (($c['marcaVehiculo'] ?? '') . '/' . ($c['modeloVehiculo'] ?? '') . '/' . ($c['anio'] ?? ''))) ?>
                                    </td>

                                    <td class="text-end">
                                        <?php
                                        if (isset($c['inicial']) && $c['inicial'] !== '') {
                                            $mon = $c['moneda'] ?? 'PEN';
                                            $symbol = ($mon === 'USD') ? '$' : 'S/'; // adapta si usas otro símbolo
                                            // formatea número con separador de miles y 2 decimales
                                            $formatted = number_format((float) $c['inicial'], 2, '.', ',');
                                            echo htmlspecialchars($symbol . ' ' . $formatted);
                                        } else {
                                            echo '';
                                        }
                                        ?>
                                    </td>

                                    <td class="text-end"><?= htmlspecialchars($c['tipocotizacion'] ?? '') ?></td>

                                    <td class="text-center">
                                        <a href="/cotizacion/reporte/<?= $c['idcotizacion'] ?>" class="p-1" target="_blank"
                                            title="PDF Cotizacion">
                                            <i class="bi bi-filetype-pdf text-danger fs-5"></i>
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