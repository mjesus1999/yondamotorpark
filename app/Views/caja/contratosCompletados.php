<?php

include __DIR__ . '/../layout/header.php';
?>
<link href="https://unpkg.com/tabulator-tables@6.3.1/dist/css/tabulator_simple.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/tabulator.css">
<div class="container-fluid caja-ui">

    <div class="alert alert-info mt-2 caja-topbar" role="alert" style="border-left: 4px solid #3498db; border-radius: 0 8px 8px 0;">
        <div class="row align-items-center">
            <div class="col-md-6 d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item"><a href="#" class="text-primary">Caja</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Contratos conpletados</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex justify-content-end caja-actions">
                <a href="/caja/" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-1"></i> Lista
                </a>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-end align-items-end gap-2 mb-3">
                        <!-- <button class="btn btn-danger btn-sm" id="btn-pdf"><i class="bi bi-filetype-pdf"></i> PDF</button> -->
                        <button class="btn btn-success btn-sm" id="btn-excel"><i class="bi bi-file-earmark-excel"></i> Excel</button>
                    </div>

                    <div id="contratos-completados">

                    </div>

                </div>
            </div>
        </div>
    </div>


</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.1/dist/js/tabulator.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>
<script>

    const btnExcel = document.getElementById("btn-excel");
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
                    title: 'Ubigeo',
                    field: 'ubigeo',
                    hozAlign: "left",
                    minWidth: 200,
                    widthGrow: 3, // Crecerá bastante
                    responsive: 1,
                    tooltip: true,
                },
                {
                    title: "Dirección",
                    field: "direccion",
                    hozAlign: "left",

                    minWidth: 200,
                    widthGrow: 3, // Crecerá bastante
                    responsive: 1,
                    tooltip: true,
                },
                {
                    title: "Teléfono",
                    field: "telprimario",
                    hozAlign: "center",
                    width: 120, // Fijo
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
                        decimal: ".",
                        thousand: ".",
                        symbol: "S/ ",
                        symbolAfter: false,
                        precision: 2,
                    },
                }, {
                    title: 'Total pagado',
                    field: 'totalPagado',
                    hozAlign: "right",
                    width: 120, // Fijo para números
                    formatter: 'money',
                    formatterParams: {
                        decimal: ".",
                        thousand: ".",
                        symbol: "S/ ",
                        symbolAfter: false,
                        precision: 2,
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


    btnExcel.addEventListener("click", async () => {
        try {
            const response = await fetch('/api/contratos/completados');
            if (!response.ok) {
                throw new Error('Error al obtener los contratos');
            }

            const result = await response.json();
            if (!result.success) {
                throw new Error('La API no devolvió datos válidos');
            }

            const data = result.data;

            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Contratos Completados');


            worksheet.mergeCells('A1:L1');
            const title = worksheet.getCell('A1');
            title.value = 'Reporte de Contratos Completados';
            title.font = {
                size: 16,
                bold: true
            };
            title.alignment = {
                horizontal: 'center'
            };


            const headers = [
                '#',
                'Cliente',
                'Documento',
                'N° Documento',
                'Ubigeo',
                'Dirección',
                'Teléfono',
                'Tienda',
                'Vehículo',
                'Meses',
                'Cuota',
                'Total Pagado'
            ];

            const headerRow = worksheet.addRow(headers);
            headerRow.font = {
                bold: true
            };

            headerRow.eachCell(cell => {
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: {
                        argb: 'FFD3D3D3'
                    }
                };
                cell.border = {
                    top: {
                        style: 'thin'
                    },
                    left: {
                        style: 'thin'
                    },
                    bottom: {
                        style: 'thin'
                    },
                    right: {
                        style: 'thin'
                    }
                };
                cell.alignment = {
                    horizontal: 'center',
                    vertical: 'middle'
                };
            });


            data.forEach((item, index) => {
                const row = worksheet.addRow([
                    index + 1,
                    item.cliente,
                    item.documento,
                    item.ndocumento,
                    item.ubigeo,
                    item.direccion,
                    item.telprimario,
                    item.tienda,
                    item.vehiculo,
                    item.meses,
                    Number(item.cuota),
                    Number(item.totalPagado)
                ]);

                row.eachCell(cell => {
                    cell.border = {
                        top: {
                            style: 'thin'
                        },
                        left: {
                            style: 'thin'
                        },
                        bottom: {
                            style: 'thin'
                        },
                        right: {
                            style: 'thin'
                        }
                    };
                });
            });

            worksheet.getColumn(11).numFmt = '"S/ " #,##0.00';
            worksheet.getColumn(12).numFmt = '"S/ " #,##0.00';


            worksheet.eachRow(row => {
                row.eachCell((cell, colNumber) => {
                    if ([1, 3, 4, 7, 10].includes(colNumber)) {
                        cell.alignment = {
                            horizontal: 'center'
                        };
                    }
                });
            });


            worksheet.columns.forEach((col, index) => {
                let maxLength = 8;
                col.eachCell({
                    includeEmpty: true
                }, cell => {
                    const value = cell.value ? cell.value.toString() : '';
                    maxLength = Math.max(maxLength, value.length);
                });

                let maxWidth = 30;
                if ([1, 3, 4, 10].includes(index + 1)) maxWidth = 12;
                if ([11, 12].includes(index + 1)) maxWidth = 14;

                col.width = Math.min(maxWidth, maxLength + 2);
            });

            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });

            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Contratos_Completados.xlsx';
            a.click();
            URL.revokeObjectURL(url);

        } catch (error) {
            console.error('Error exportando Excel:', error);
            alert('Error al exportar contratos a Excel');
        }
    });

</script>