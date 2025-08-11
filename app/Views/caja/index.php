<?php

include __DIR__ . '/../layout/header.php';
?>


<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>


        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-hover-yonda" id="tabla-clientes-personas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>N° Documento</th>
                                    <th>Tienda</th>
                                    <th>Vehículo</th>
                                    <th>Meses</th>
                                    <th>Cuota</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (empty($contratos)) : ?>

                                    <tr>
                                        <td colspan="8" class="text-center">No hay datos para mostrar.</td>
                                    </tr>

                                <?php else: ?>

                                    <?php $numeroFila = 1; ?>
                                    <?php foreach ($contratos as $contrato) : ?>
                                        <tr>

                                            <td><?php  htmlspecialchars($numeroFila++) ?></td>
                                            <td><?php  htmlspecialchars($contrato['cliente']) ?></td>
                                            <td><?php  htmlspecialchars($contrato['documento']) ?></td>
                                            <td><?php  htmlspecialchars($contrato['ndocumento']) ?></td>
                                            <td><?php  htmlspecialchars($contrato['tienda']) ?></td>
                                            <td><?php  htmlspecialchars($contrato['vehiculo']) ?></td>
                                            <td><?php  htmlspecialchars($contrato['meses']) ?></td>
                                            <td><?php  htmlspecialchars($contrato['cuota']) ?></td>
                                            <td><a href="/caja/cronograma/<?= htmlspecialchars($contrato['idcontrato'])?>" title="Ver Cronograma"><i class="bi-receipt fs-5 text-warning"></i></a></td>
                                        </tr>

                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<?php include __DIR__ . '/../layout/footer.php'; ?>