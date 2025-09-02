<?php include __DIR__ . '/../layout/header.php'; ?>


<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Recepción vehiculos</a></li>
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
                        <table class="table table-sm table-hover" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Concesionario</th>
                                    <th>Dirección Conces.</th>
                                    <th>Emisión OC</th>
                                    <th>Serie OC</th>
                                    <th>Fecha compra</th>
                                    <th>Cant.Vehículos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay datos registrados.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1 ?>
                                    <?php foreach ($data as $compras): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($numeroFila++) ?></td>
                                            <td><?= htmlspecialchars($compras['nombrecomercial']) ?></td>
                                            <td><?= htmlspecialchars($compras['direccion_completa_concesionario']) ?></td>
                                            <td><?= htmlspecialchars($compras['fecha_emision_oc']) ?></td>
                                            <td><?= htmlspecialchars($compras['serie_oc']) ?></td>
                                            <td><?=  htmlspecialchars($compras['fechacompra'])  ?></td>
                                            <td><?= htmlspecialchars($compras['cantidadvehiculos']) ?></td>
                                            <td>
                                                <a href="/recepcionVehiculos/edit/<?= htmlspecialchars($compras['idcompra']) ?>">
                                                        <i class="bi bi-journal-check fs-4" class="text-primary"></i>
                                                </a>
                                            </td>
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