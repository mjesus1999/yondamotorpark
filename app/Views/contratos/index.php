<?php include __DIR__ . '/../layout/header.php'; ?>
<link href="https://unpkg.com/tabulator-tables@5.5.2/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">



<div class="container-fluid">

    <div class="alert alert-info mt-2 mb-5" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Contratos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- <button id="btnExportCSV">Exportar CSV</button>
    <button id="btnExportJSON">Exportar JSON</button>
    <button id="btnExportPDF">Exportar PDF</button>
 -->

    <div class="input-group mb-2">
        <span class="input-group-text">
            <i class="bi bi-search"></i>
        </span>
        <input

            type="text"
            id="busqueda-global"
            class="form-control"
            placeholder="Buscar ....">
    </div>



    <div id="tabla-contratos">

    </div>
</div>














<?php include __DIR__ . '/../layout/footer.php'; ?>
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const table = new Tabulator("#tabla-contratos", {
            ajaxURL: "/api/contratos",
            ajaxConfig: "GET",
            ajaxContentType: "json",
            layout: "fitColumns", // Ajusta el ancho al contenedor
            responsiveLayout: "collapse", // Oculta columnas no críticas en pantallas pequeñas
            pagination: "local",
            paginationSize: 15,
            paginationSizeSelector: [5, 10, 25, 50],
            placeholder: "No hay contratos disponibles.",
            movableColumns: true,

            locale: "es-es",
            langs: {
                "es-es": {
                    "pagination": {
                        "page_size": "Filas por página",
                        "first": "<<",
                        "prev": "<",
                        "next": ">",
                        "last": ">>",
                        "counter": {
                            "showing": "Mostrando",
                            "of": "de",
                            "rows": "filas"
                        }
                    },
                    "headerFilters": {
                        "default": "Filtrar..."
                    }
                }
            },
            index: "idcontrato",

            columns: [
                //  Contrato
                {
                    title: "#",
                    formatter: "rownum",
                    hozAlign: "center",
                    width: 40,
                    responsive: 0
                },
                {
                    title: "Inicio",
                    field: "fechainicio",
                    hozAlign: "center",
                    minWidth: 110
                },
                {
                    title: "Día Pago",
                    field: "diapago",
                    hozAlign: "center",
                    width: 70
                },

                //  Ubicación
                {
                    title: "Tienda",
                    field: "tienda",
                    headerHozAlign: "left",
                    widthGrow: 4,
                    tooltip: true,
                },

                //  Vehículo
                {
                    title: "Vehículo",
                    field: "vehiculo",
                    headerHozAlign: "left",
                    widthGrow: 4,
                    tooltip: true,
                },

                //  Cliente
                {
                    title: "Cliente",
                    field: "cliente",
                    headerHozAlign: "center",
                    widthGrow: 6,
                    tooltip: true,
                },
                {
                    title: "Documento",
                    field: "doc_cliente",
                    hozAlign: "center",
                    // headerFilter: "input",
                    widthGrow: 2,
                    tooltip: true
                },

                //  Asesor
                {
                    title: "Asesor",
                    field: "asesor",
                    headerHozAlign: "center",
                    tooltip: true,
                    widthGrow: 3
                },

                //  Detalles Financieros
                {
                    title: "Moneda",
                    field: "moneda",
                    hozAlign: "left",
                    width: 80
                },
                {
                    title: "Precio Venta",
                    field: "precioventa",
                    hozAlign: "right",
                    formatter: "money",
                    formatterParams: {
                        symbol: "S/ ",
                        thousand: ",",
                        precision: 2
                    },
                    minWidth: 100
                },
                {
                    title: "Inicial",
                    field: "inicial",
                    hozAlign: "right",
                    formatter: "money",
                    formatterParams: {
                        symbol: "S/ ",
                        thousand: ",",
                        precision: 2
                    },
                    minWidth: 120
                },
                {
                    title: "Cuota",
                    field: "valorcuota",
                    editor: "input",
                    hozAlign: "right",
                    formatter: "money",
                    formatterParams: {
                        symbol: "S/ ",
                        thousand: ",",
                        precision: 2
                    },
                    minWidth: 80,
                },
                {
                    title: "N° cuota",
                    field: "numcuotas",
                    hozAlign: "center",
                    width: 90
                },

                {
                    title: 'Acciones',
                    hozAlign: 'center',
                    responsive: 0,
                    formatter: (cell) => {
                        const id = cell.getRow().getData().idcontrato

                        return `
                         <button class="btn bytn-sm btn-eliminar " data-id="${id}" data-action="delete" title="Eliminar">
                               <i class="bi bi-trash text-danger"></i>
                             </button>
                           
                        `;
                    },
                    // cellClick: async (e, cell) => {
                    //     const id = cell.getRow().getData().idcontrato;
                    //     if (e.target.closest('.btn-eliminar')) {
                    //         if (await ask('¿Eliminar contrato?', 'Confirmar')) {
                    //             deleteContrato(id);
                    //         }

                    //         console.log('Eliminar contrato con id: ', id)
                    //     }


                    // }
                }


            ],

            ajaxResponse: (url, params, response) => {
                console.log("Datos cargados desde API:", response);
                return response;
            },
            ajaxError: (xhr, textStatus, errorThrown) => {
                console.error("Error al cargar datos:", errorThrown);
            },


        });


        const searchInput = document.getElementById("busqueda-global");
        if (searchInput) {
            searchInput.addEventListener("keyup", function(e) {
                const value = e.target.value;
                if (value === "") {
                    table.clearFilter();
                } else {
                    table.setFilter([
                        [{
                                field: "cliente",
                                type: "like",
                                value: value
                            },
                            {
                                field: "vehiculo",
                                type: "like",
                                value: value
                            },
                            {
                                field: "doc_cliente",
                                type: "like",
                                value: value
                            },

                        ]
                    ]);
                }
            });
        }


        async function deleteContrato(id) {

            if (id) {
                const data = new URLSearchParams();
                data.append('idcontrato', id);

                try {
                    const req = await fetch('/contrato/delete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: data
                    });
                    const res = await req.json();

                    if (res.success) {
                        showToast(res.message, 'SUCCESS', 1200);
                        const row = table.getRow(id);

                        if (row) {
                            row.delete(); // Esto es instantáneo y mucho mejor para la UX, eliminar solo la fila
                        } else {
                            showToast('No se ha podido eliminar el contrato', 'ERROR', 1250);
                        }

                    } else {
                        showToast(res.message, 'ERROR', 1250);
                    }

                } catch (error) {
                    showToast(error, 'ERROR', 1300);
                    console.log(error);

                }
            }


        }

        // DELEGACIÓN DE EVENTOS
        document.getElementById("tabla-contratos").addEventListener("click", async (e) => {
            // Buscamos el botón más cercano al que se le hizo clic
            const button = e.target.closest("button[data-action]");

            if (!button) return;

            const id = button.dataset.id;
            const action = button.dataset.action;


            switch (action) {
                case 'delete':
                    if (await ask('¿Desactivar este contrato?', 'Confirmar')) {
                        deleteContrato(id);
                    }
                    break;
            }
        });







        // document.getElementById('btnExportCSV').addEventListener('click', () => {
        //     table.download('csv', 'contratos');
        // });

        // document.getElementById("btnExportJSON").addEventListener("click", () => {
        //     table.download("json", "contratos.json");
        // });

       








    });
</script>