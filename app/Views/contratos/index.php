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
    document.addEventListener("DOMContentLoaded", async () => {

        const table = new Tabulator("#tabla-contratos", {
        ajaxURL: "/api/contratos",
        ajaxConfig:'GET',
        ajaxContentType:"json",
        //  progressiveLoad:"load",
        progressiveLoadScrollMargin:300 ,
        layout: "fitColumns",
        responsiveLayout: "collapse",
        pagination: "local",
        paginationSize: 15,
        index: "idcontrato", // El identificador de cada fila
        placeholder: "No hay contratos disponibles.",
        movableColumns: true,

        columns: [
            {title: "#", formatter: "rownum", hozAlign: "center", width: 40, responsive: 10},
            {title: "Inicio", field: "fechainicio", hozAlign: "center", minWidth: 110, responsive:10, tooltip:true},
            {title: "Día Pago", field: "diapago", hozAlign: "center", width: 70, responsive:10,tooltip:true},
            {title: "Tienda", field: "tienda", headerHozAlign: "left", width:200, responsive:5,tooltip:true},
            {title: "Vehículo", field: "vehiculo", headerHozAlign: "left",width:200,  responsive:1,tooltip:true},
            {title: "Cliente", field: "cliente", headerHozAlign: "center", width:250, responsive:2,tooltip:true},
            {title: "Documento", field: "doc_cliente", hozAlign: "center", responsive:10,tooltip:true},
            {title: "Asesor", field: "asesor", headerHozAlign: "left", responsive:10,tooltip:true},
            {title: "Precio Venta", field: "precioventa", hozAlign: "right", formatter: "money", formatterParams:{symbol:"S/ ",thousand:",",precision:2}, minWidth:100,tooltip:true},
            {title: 'Acciones', hozAlign: 'left', responsive:0,
                formatter: (cell) => {
                    const id = cell.getRow().getData().idcontrato;
                    return `
                        <button class="btn btn-sm btn-verPDF fs-5"><i class="bi bi-filetype-pdf text-danger" data-id="${id}" data-action="verPDF"></i></button>
                        <button class="btn btn-sm btn-eliminar" data-id="${id}" data-action="delete">
                                <i class="bi bi-trash text-danger fs-5"></i>
                        </button>
                            
                            `;
                        
                }
            }
        ],

        ajaxResponse:(url,params,response) => {
            if(response.success) {
                 return response.data;
            }
            // console.log(" Datos cargados correctamente desde:", url);
            // console.log(" Total de registros:", response.length);
            // console.log(" Datos:", response);
            // console.info(" Carga de contratos exitosa");
            // Devolver para que tabulator lo renderice en la tabla.
       
        },
         ajaxError: function(error) {
            console.error(" Error al cargar los contratos:", error);
            console.warn("Verifica el endpoint o la respuesta del servidor");
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
                    if (await ask('¿Desactivar este contrato?', 'Contratos')) {
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