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

                            <tr>
                                <td>1</td>
                                <td>Flores Munayco Isabel María</td>
                                <td>DNI</td>
                                <td>85858525</td>
                                <td>Chincha</td>
                                <td>Hyundai / i10 / GLP / Negro</td>
                                <td>24</td>
                                <td>2500</td>
                                <td><button class="btn btn-outline-primary btn-sm"><a href="/contrato/cronograma
                                ">Cronograma</a></button></td>

                            </tr>
                            <tr>
                                 <td>1</td>
                                <td>Fuentes Marcelo Rodolfo Enrique</td>
                                <td>DNI</td>
                                <td>36369568</td>
                                <td>Chincha</td>
                                <td>Hyundai / i10 / GLP / Negro</td>
                                <td>24</td>
                                <td>3200</td>
                                <td><button class="btn btn-outline-primary btn-sm"><a href="/contrato/cronograma">Cronograma</a></button></td>
                            </tr>

                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<?php include __DIR__ . '/../layout/footer.php'; ?>