<!-- app/views/cobranza/index.php -->
<?php include __DIR__ . '/../layout/header.php'; ?>

<head>

<body>

    <div class="container-fluid">
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
    </div>

</body>
</head>



<?php include __DIR__ . '/../layout/footer.php'; ?>