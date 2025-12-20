<?php

include __DIR__ . '/../layout/header.php';
?>
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">
<div class="container-fluid">

    <div class="alert alert-info mt-2 text-primary p-3 d-flex justify-content-between align-items-center">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="#" class="text-info"><i class="fas fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="#" class="text-info">Caja</a></li>
                <li class="breadcrumb-item active " aria-current="page">Contratos completados</li>
            </ol>
        </nav>


    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div id="contratos-completados">

                    </div>

                </div>
            </div>
        </div>
    </div>


</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.1/dist/js/tabulator.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {


        const table = new Tabulator("#contratos-completados", {
            ajaxURL: '/api/contratos/completados',
            ajaxConfig: 'GET',
            ajaxContentType: 'json',
            progressiveLoadScrollMargin: 300,

     
            layout: "fitColumns",
            responsiveLayout: "collapse",
            pagination: "local",
            paginationSize: 20,
            responsiveLayoutCollapseStartOpen: false,
            index: "idcontrato",
            placeholder: "No hay contratos completados.",
            movableColumns: true,

            columns: [{
                    formatter: "responsiveCollapse",
                    width: 40,
                    minWidth: 30,
                    hozAlign: "center",
                    resizable: false,
                    headerSort: false,
                    download: false
                },
                {
                    title: "#",
                    field: 'index',
                    formatter: "rownum",
                    hozAlign: "center",
                    width: 50, 
                    responsive: 10,
                    download: true
                },
                {
                    title: "Cliente",
                    field: "cliente",
                    hozAlign: "left",
                   
                    minWidth: 200,
                    widthGrow: 3, 
                    responsive: 0,
                    tooltip: true,
                },
                {
                    title: "Doc.",
                    field: "documento",
                    hozAlign: "center",
                    width: 80, // Fijo
                    responsive: 4,
                },
                {
                    title: "N° Doc",
                    field: "ndocumento",
                    hozAlign: "center",
                    width: 110, 
                    responsive: 4,
                },
                {
                    title: "Tienda",
                    field: "tienda",
                    hozAlign: "left",
                    
                    minWidth: 150,
                    widthGrow: 2, // Crecerá moderadamente
                    responsive: 2,
                },
                {
                    title: "Vehículo",
                    field: "vehiculo",
                    hozAlign: "left",
                    
                    minWidth: 200,
                    widthGrow: 3, // Crecerá bastante
                    responsive: 0,
                    tooltip: true,
                },
                {
                    title: "Meses",
                    field: "meses",
                    hozAlign: "center",
                    width: 70, // Fijo pequeño
                    responsive: 0,
                },
                {
                    title: 'Cuota',
                    field: 'cuota',
                    hozAlign: "right",
                    width: 120, // Fijo para números
                    formatter: 'money',
                    formatterParams: {
                        decimal: ",",
                        thousand: ".",
                        symbol: "$ ",
                        symbolAfter: false,
                        precision: 0,
                    },
                }
            ],

            ajaxResponse: (url, params, response) => {
                if (response.success) {
                    return response.data;
                }
                console.log("DATOS: response", response);
            },
            ajaxError: function(error) {
                console.error(" Error al cargar los contratos:", error);
            },

            langs: {
                "es-es": {
                    "pagination": {
                        "page_size": "Registros",
                        "first": "<<",
                        "last": ">>",
                        "prev": "<",
                        "next": ">",
                        "counter": {
                            "showing": "Mostrando",
                            "of": "de",
                            "rows": "registros"
                        }
                    }
                }
            },
            locale: "es-es"
        });


    });
</script>