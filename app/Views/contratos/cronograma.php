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
                        <li class="breadcrumb-item active" aria-current="page">Cronograma</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <a href="/contratos/" class="btn btn-sm btn-outline-primary">Mostrar lista</a>
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
                                    <th>Fecha</th>
                                    <th>Interés</th>
                                    <th>Ahorro Capital</th>
                                    <th>Valor Cuota</th>
                                    <th>Saldo Capital</th>
                                    <th>Pagar</th>

                                </tr>
                            </thead>
                            <tbody>


                                <?php

                                $fecha_base = new DateTime('2025-06-30');

                                for ($i = 1; $i <= 24; $i++) {

                                    // Clonamos la fecha base y sumamos meses
                                    $fecha_cuota = clone $fecha_base;
                                    $fecha_cuota->modify("+$i month");
                                    $fecha_formateada = $fecha_cuota->format('d/m/Y');

                                    echo "
                                        <tr>
                                            <td>$i</td>
                                            <td>$fecha_formateada</td>
                                            <td>3</td>
                                            <td>0</td>
                                            <td>2500</td>
                                            <td>2500</td>
                                            <td>
                                                <a href='#' class='btn btn-outline-success btn-sm text-dark'>Pagar</a>
                                            </td>
                                        </tr>
                                        ";
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<?php include __DIR__ . '/../layout/footer.php'; ?>