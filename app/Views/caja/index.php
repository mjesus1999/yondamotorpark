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
                <li class="breadcrumb-item active " aria-current="page">Listar</li>
            </ol>
        </nav>

        <div class="d-flex">

            <a href="/caja/reporte/by/fecha" class="btn btn-sm text-white border border-info bg-info" style="border-radius: 0;">
                <i class="fas fa-calendar-alt"></i> Reporte por fecha
            </a>

            <button class="btn btn-danger btn-sm rounded-0" id="btn-pdf" title="Generar reporte de pagos del día" style="margin-left: -1px;">
                <i class="far fa-file-pdf"></i> Reporte diario
            </button>

            <button class="btn btn-success btn-sm rounded-0" id="btn-excel" title="Generar reporte de pagos del día en Excel" style="margin-left: -1px;">
                <i class="far fa-file-excel"></i> Reporte diario
            </button>

            <a class="btn btn-dark btn-sm rounded-0" href="/caja/pagos/denominacion" style="margin-left: 10px; border-left: 1px solid rgba(255, 255, 255, 0.1);">
                Cobros por denominación
            </a>

              <a class="btn btn-warning text-white fw-bold btn-sm rounded-0" href="/caja/contratos/completados" style="margin-left: 10px; border-left: 1px solid rgba(255, 255, 255, 0.1);">
                <i class="bi bi-clock-history"></i> Contratos completados
            </a>

        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- SOLO ESCRITORIO -->
                    <div class="table-responsive d-none d-md-block">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input

                                    type="text"
                                    id="busqueda-global"
                                    class="form-control"
                                    placeholder="Buscar ....">
                            </div>
                        </div>
                        <div id="tabla-contratos">
                            <!-- <thead>
                                <tr>
                                    <th><span class="text-body fw-bold badge">#</span></th>
                                    <th><span class="text-body fw-bold badge">Cliente</span></th>
                                    <th><span class="text-body fw-bold badge">Documento</span></th>
                                    <th><span class="text-body fw-bold badge">N° Documento</span></th>
                                    <th> <span class="text-body fw-bold badge">Tienda</span></th>
                                    <th><span class="text-body fw-bold badge">Vehículo</span></th>
                                    <th><span class="text-body fw-bold badge">Meses</span></th>
                                    <th><span class="text-body fw-bold badge">Cuota</span></th>
                                    <th><span class="text-body fw-bold badge">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($contratos)) : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay datos para mostrar.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $numeroFila = 1; ?>
                                    <?php foreach ($contratos as $contrato) : ?>
                                        <tr>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($numeroFila++) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['cliente']) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['documento']) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['ndocumento']) ?></span></td>
                                            <td><span class="badge fw-bold text-body"><?= htmlspecialchars($contrato['tienda']) ?></span></td>
                                            <td><span class="badge fw-bold text-body"><?= htmlspecialchars($contrato['vehiculo']) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['meses']) ?></span></td>
                                            <td><span class="badge text-body fw-bold"><?= htmlspecialchars($contrato['cuota']) ?></span></td>
                                            <td>
                                                <a href="/caja/cronograma/<?= htmlspecialchars($contrato['idcontrato']) ?>" title="Ver Cronograma">
                                                    <i class="bi-receipt fs-5 text-info"></i>
                                                </a>
                                                <a href="/caja/historial/pagos/<?= htmlspecialchars($contrato['idcontrato']) ?>" title="Ver historial de pagos">
                                                    <i class="bi bi-clock-history fs-5"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody> -->
                        </div>
                    </div>


                    <!-- SOLO MÓVIL (ACORDEÓN) -->
                    <div class="d-block d-md-none">
                        <?php if (!empty($contratos)) : ?>
                            <div class="accordion" id="acordeonContratos">
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($contratos as $contrato): ?>
                                    <div class="accordion-item mb-2 shadow-sm">
                                        <h2 class="accordion-header" id="heading-<?= $contrato['idcontrato'] ?>">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse<?= $contrato['idcontrato'] ?>"
                                                aria-expanded="false"
                                                aria-controls="collapse<?= $contrato['idcontrato'] ?>">
                                                <span><i class="bi bi-person-circle me-2 text-primary fw-bold"></i><?= htmlspecialchars($contrato['cliente']) ?></span>
                                            </button>
                                        </h2>
                                        <div id="collapse<?= $contrato['idcontrato'] ?>"
                                            class="accordion-collapse collapse"
                                            aria-labelledby="heading-<?= $contrato['idcontrato'] ?>"
                                            data-bs-parent="#acordeonContratos">
                                            <div class="accordion-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>#:</strong> <?= $numeroFila++ ?></li>
                                                    <li class="list-group-item"><strong>Documento:</strong> <?= htmlspecialchars($contrato['documento']) ?></li>
                                                    <li class="list-group-item"><strong>N° Documento:</strong> <?= htmlspecialchars($contrato['ndocumento']) ?></li>
                                                    <li class="list-group-item"><strong>Tienda:</strong> <span class="badge bg-primary"><?= htmlspecialchars($contrato['tienda']) ?></span></li>
                                                    <li class="list-group-item"><strong>Vehículo:</strong> <span class="badge bg-primary"><?= htmlspecialchars($contrato['vehiculo']) ?></span></li>
                                                    <li class="list-group-item"><strong>Meses:</strong> <?= htmlspecialchars($contrato['meses']) ?></li>
                                                    <li class="list-group-item"><strong>Cuota:</strong> <?= htmlspecialchars($contrato['cuota']) ?></li>
                                                    <li class="list-group-item">
                                                        <strong>Acciones:</strong>
                                                        <div class="d-flex gap-2 mt-1">
                                                            <a href="/caja/cronograma/<?= $contrato['idcontrato'] ?>" title="Ver Cronograma" class="btn btn-sm btn-outline-info">
                                                                <i class="bi-receipt"></i>
                                                            </a>
                                                            <a href="/caja/historial/pagos/<?= $contrato['idcontrato'] ?>" title="Ver historial de pagos" class="btn btn-sm btn-outline-secondary">
                                                                <i class="bi bi-clock-history"></i>
                                                            </a>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center">No hay datos para mostrar.</div>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" defer></script>
<script src="/assets/js/logoBase64.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js" defer></script>
<script src="https://unpkg.com/tabulator-tables@5.5.2/dist/js/tabulator.min.js"></script>

<script>
    const btnPdf = document.getElementById('btn-pdf');
    const btnExcel = document.querySelector('#btn-excel');
    const datos = <?= json_encode($contratos) ?>




    document.addEventListener('DOMContentLoaded', async () => {

        const tabla = new Tabulator('#tabla-contratos', {
            data: datos,
            pagination: "local",
            layout: "fitColumns",
            paginationSize: 20,
            paginationSizeSelector: [5, 10, 15, 25],
            movableRows: true,
             placeholder: "No hay contratos con cronogramas para mostrar.",

            columns: [{
                    title: "#",
                    formatter: "rownum",
                    width: 50
                },
                {
                    title: "Cliente",
                    field: "cliente",
                    widthGrow: 8,
                    tooltip: true
                },
                {
                    title: "Documento",
                    field: "documento",
                    widthGrow: 3,
                    tooltip: true
                },
                {
                    title: "N° documento",
                    field: "ndocumento",
                    widthGrow: 3,
                    tooltip: true
                },
                {
                    title: "Tienda",
                    field: "tienda",
                    widthGrow: 7,
                    tooltip: true
                },
                {
                    title: "Vehículo",
                    field: "vehiculo",
                    widthGrow: 7,
                    tooltip: true
                },
                {
                    title: "Meses",
                    field: "meses",
                    widthGrow: 3,
                    tooltip: true,
                },
                {
                    title: "Cuota",
                    field: "cuota",
                    widthGrow: 3,
                    tooltip: true,

                },
                {
                    title: "Acciones",
                    field: "acciones",
                    widthGrow: 2,
                    headerSort: false,

                    formatter: function(cell, formatterParams) {
                        const data = cell.getRow().getData();
                        return `
                        
                             <a href="/caja/cronograma/${data.idcontrato}" title="Ver Cronograma">
                                                    <i class="bi-receipt fs-5 text-info"></i>
                            </a>
                            <a href="/caja/historial/pagos/${data.idcontrato}" title="Ver historial de pagos">
                                <i class="bi bi-clock-history fs-5"></i>
                            </a>
                            
                                 `;
                    }
                }
            ],
            locale: "es-es",
            langs: {
                "es-es": {
                    "pagination": {
                        "page_size": "Registros por página",
                        "first": "<<",
                        "last": ">>",
                        "prev": "<",
                        "next": ">",
                    }
                }
            },

        });

        const searchInput = document.getElementById("busqueda-global");
        if (searchInput) {
            searchInput.addEventListener("keyup", function(e) {
                const value = e.target.value;
                if (value === "") {
                    tabla.clearFilter();
                } else {
                    tabla.setFilter([
                        [{
                                field: "ndocumento",
                                type: "like",
                                value: value
                            },
                            {
                                field: "cliente",
                                type: "like",
                                value: value
                            },

                        ]
                    ]);
                }
            });
        }







        if (btnPdf) {
            btnPdf.addEventListener('click', () => {
                generarReportePDFConJsPDF();
            });
        }

        if (btnExcel) {
            btnExcel.addEventListener('click', () => {
                generarReporteExcel();
            });
        }

        // Función para generar reporte Excel:

        async function generarReporteExcel() {
            try {
                const response = await fetch('/api/reporte/hoy');

                if (!response.ok) {
                    throw new Error('Error al obtener los datos del reporte');
                }

                const data = await response.json();
                console.log('DATA: ', data);

                if (!data.success || !data.data) {
                    throw new Error('No se pudieron obtener los datos del reporte');
                }

                if (data.data.length > 0) {
                    const textoOriginal = btnExcel.innerHTML;
                    btnExcel.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando Excel...';
                    btnExcel.disabled = true;

                    const workbook = new ExcelJS.Workbook();
                    const worksheet = workbook.addWorksheet('Reporte de Pagos');

                    // Definir estilos
                    const headerStyle = {
                        font: {
                            bold: true,
                            size: 12
                        },
                        alignment: {
                            horizontal: 'center'
                        },
                        fill: {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: {
                                argb: 'FFD3D3D3'
                            }
                        },
                        border: {
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
                        }
                    };

                    const cellStyle = {
                        font: {
                            size: 11
                        },
                        border: {
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
                        }
                    };

                    // Título del reporte
                    const fechaHoy = new Date().toLocaleDateString('es-PE', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    worksheet.mergeCells('A1:G1');
                    const titleCell = worksheet.getCell('A1');
                    titleCell.value = `Reporte de Pagos - ${fechaHoy}`;
                    titleCell.font = {
                        bold: true,
                        size: 16
                    };
                    titleCell.alignment = {
                        horizontal: 'center'
                    };


                    worksheet.addRow([]);
                    const columns = ['#', 'Medio de Pago', 'Fecha', 'N° Operación', 'Entidad Bancaria', 'N° Cuenta', 'Monto (S/)'];
                    worksheet.addRow(columns).eachCell(cell => {
                        Object.assign(cell, headerStyle);
                    });


                    let totalMonto = 0;
                    let contador = 1;


                    data.data.forEach(item => {
                        item.transacciones.forEach(transaccion => {

                            const rowData = [
                                contador++,
                                transaccion.metodo_pago,
                                new Date(transaccion.fecha),
                                transaccion.numero_operacion || '',
                                transaccion.entidad_bancaria || '',
                                transaccion.numero_cuenta || '',
                                parseFloat(transaccion.monto)
                            ];

                            const newRow = worksheet.addRow(rowData);


                            newRow.eachCell(cell => {
                                Object.assign(cell, cellStyle);
                            });

                            newRow.getCell(3).numFmt = 'DD/MM/YYYY';

                            newRow.getCell(7).numFmt = '"S/"#,##0.00';

                            totalMonto += parseFloat(transaccion.monto);
                        });
                    });


                    worksheet.addRow([]);
                    const totalsRow = worksheet.addRow(['', '', '', '', '', 'Total:', totalMonto]);
                    totalsRow.getCell(6).style = {
                        font: {
                            bold: true
                        },
                        fill: {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: {
                                argb: 'FFFFFF00'
                            }
                        },
                        border: cellStyle.border
                    };
                    totalsRow.getCell(7).style = {
                        font: {
                            bold: true
                        },
                        fill: {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: {
                                argb: 'FFFFFF00'
                            }
                        },
                        border: cellStyle.border,
                        numFmt: '"S/"#,##0.00'
                    };

                    // Ajustar el ancho de las columnas con anchos predefinidos
                    worksheet.getColumn(1).width = 5; // #
                    worksheet.getColumn(2).width = 15; // Medio de Pago
                    worksheet.getColumn(3).width = 12; // Fecha
                    worksheet.getColumn(4).width = 25; // N° Operación
                    worksheet.getColumn(5).width = 17; // Entidad Bancaria
                    worksheet.getColumn(6).width = 25; // N° Cuenta
                    worksheet.getColumn(7).width = 15; // Monto (S/)


                    const buffer = await workbook.xlsx.writeBuffer();
                    const blob = new Blob([buffer], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `reporte-pagos-diario-${new Date().toLocaleDateString('es-PE').replace(/\//g, '-')}.xlsx`;
                    a.click();
                    window.URL.revokeObjectURL(url);

                    mostrarNotificacion('Reporte Excel generado y descargado correctamente', 'success');

                    setTimeout(() => {
                        btnExcel.innerHTML = textoOriginal;
                        btnExcel.disabled = false;
                    }, 2000);
                } else {
                    alert('No hay pagos aún para generar el Excel');
                }

            } catch (error) {
                console.error('Error al generar el reporte de Excel:', error);
                mostrarNotificacion('Error al generar el Excel: ' + error.message, 'error');
            }
        }




        async function generarReportePDFConJsPDF() {
            const btnPdf = document.getElementById('btn-pdf');

            try {
                const response = await fetch('/api/reporte/hoy');

                if (!response.ok) {
                    throw new Error('Error al obtener los datos del reporte');
                }

                const data = await response.json();

                if (!data.success || !data.data) {
                    alert('No se pudieron obtener los datos del reporte');
                    return;
                }

                if (data.data.length > 0) {
                    const textoOriginal = btnPdf.innerHTML;
                    btnPdf.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
                    btnPdf.disabled = true;

                    await generarPDFConDatos(data);

                    setTimeout(() => {
                        btnPdf.innerHTML = textoOriginal;
                        btnPdf.disabled = false;
                    }, 2000);
                } else {
                    alert('No hay pagos aún para generar el PDF');
                }
            } catch (error) {
                console.error('Error al generar PDF:', error);
                alert('Error al generar el PDF: ' + error.message);
            }
        }


        async function generarPDFConDatos(datosReporte) {
            const fechaHoy = new Date().toLocaleDateString('es-PE');
            const horaGeneracion = new Date().toLocaleTimeString('es-PE');
            const totalGeneral = datosReporte.total_general || 0;
            const datosTransacciones = datosReporte.data || [];

            const logoBase64 = window.logoBase64 || null;
            const ruc = '20609396866';
            const nombreEmpresa = 'YONDA & GRUPO HUARACA E.I.R.L';


            const layoutTabla = {
                hLineWidth: function(i, node) {
                    return (i === 0 || i === node.table.body.length) ? 0 : 1;
                },
                vLineWidth: function(i, node) {
                    return 0;
                },
                hLineColor: function(i, node) {
                    return '#E0E0E0';
                },
                paddingLeft: function(i, node) {
                    return 8;
                },
                paddingRight: function(i, node) {
                    return 8;
                },
                paddingTop: function(i, node) {
                    return 5;
                },
                paddingBottom: function(i, node) {
                    return 5;
                }
            };

            const styles = {
                headerPrincipal: {
                    fontSize: 20,
                    bold: true,
                    color: '#2C3E50',
                    alignment: 'center',
                    margin: [0, 0, 0, 5]
                },
                infoEmpresa: {
                    fontSize: 10,
                    bold: true,
                    alignment: 'right',
                    margin: [0, 0, 0, 2]
                },

                tableHeader: {
                    bold: true,
                    fontSize: 9,
                    color: '#2C3E50',
                    fillColor: '#F2F3F4',
                    alignment: 'center'
                },
                tableSubHeader: {
                    bold: true,
                    fontSize: 11,
                    color: '#2C3E50',
                    alignment: 'left',
                    margin: [0, 15, 0, 5]
                },
                tableSubtotal: {
                    bold: true,
                    fontSize: 9,
                    color: '#27AE60',
                    fillColor: '#F8FDF9',
                }
            };

            const documento = {
                pageSize: 'A4',
                pageOrientation: 'portrait',
                pageMargins: [40, 30, 40, 40],
                defaultStyle: {
                    fontSize: 9,
                    alignment: 'center'
                },
                styles: styles,
                content: [],
                footer: (currentPage, pageCount) => {
                    return {
                        stack: [{
                            text: `Página ${currentPage} de ${pageCount}`,
                            alignment: 'right',
                            fontSize: 8,
                            margin: [0, 10, 40, 0]
                        }, {
                            text: `© ${new Date().getFullYear()} Sistema de Gestión de Caja. Reporte generado automáticamente.`,
                            color: '#7F8C8D',
                            bold: true,
                            fontSize: 7
                        }]
                    };
                }
            };


            documento.content.push({
                columns: [
                    logoBase64 ? {
                        image: logoBase64,
                        width: 90,
                        alignment: 'left'
                    } : {},
                    {
                        stack: [{
                                text: nombreEmpresa,
                                fontSize: 12,
                                bold: true,
                                color: '#2c3e50',
                                alignment: 'right'
                            },
                            {
                                text: `RUC: ${ruc}`,
                                fontSize: 10,
                                bold: true,
                                alignment: 'right'
                            },
                            {
                                margin: [0, 5, 0, 0],
                                text: `Generado el ${fechaHoy} a las ${horaGeneracion}`,
                                alignment: 'right',
                                fontSize: 8,
                                color: '#555'
                            }
                        ],
                        alignment: 'right',
                        margin: [10, 0, 0, 0]
                    }
                ],
                margin: [0, 0, 0, 30]
            });

            documento.content.push({
                text: 'REPORTE DIARIO DE PAGOS',
                style: 'headerPrincipal',
                margin: [0, 0, 0, 20]
            });

            const totalTransacciones = datosTransacciones.reduce((sum, item) => sum + (item.transacciones?.length || 0), 0);
            documento.content.push({
                columns: [{
                        text: 'TOTAL RECAUDADO HOY: ' + `S/ ${formatearMoneda(totalGeneral)}`,
                        fontSize: 11,
                        bold: true,
                        color: '#2C3E50',
                        alignment: 'left',
                        margin: [0, 0, 0, 0]
                    },
                    {
                        text: `TOTAL DE TRANSACCIONES: ${totalTransacciones}`,
                        fontSize: 11,
                        color: '#555',
                        bold: true,
                        alignment: 'right',
                        margin: [0, 0, 0, 0]
                    }
                ],
                margin: [0, 0, 0, 30]
            });


            documento.content.push({
                text: 'RESUMEN EJECUTIVO',
                style: 'tableSubHeader',
                fontSize: 12,
                decoration: 'underline',
                margin: [0, 0, 0, 10]
            });

            const resumenBody = [
                [{
                    text: 'Método de Pago',
                    style: 'tableHeader'
                }, {
                    text: 'Cantidad',
                    style: 'tableHeader'
                }, {
                    text: 'Monto Recaudado',
                    style: 'tableHeader'
                }, {
                    text: 'Porcentaje (%)',
                    style: 'tableHeader'
                }]
            ];

            datosTransacciones.forEach(item => {
                const subtotal = item.subtotal || 0;
                const porcentaje = totalGeneral > 0 ? ((subtotal / totalGeneral) * 100).toFixed(1) : '0';
                resumenBody.push([{
                    text: item.metodo_pago,
                    alignment: 'left'
                }, {
                    text: item.transacciones?.length || 0,
                    alignment: 'center'
                }, {
                    text: `S/ ${formatearMoneda(subtotal)}`,
                    alignment: 'right'
                }, {
                    text: `${porcentaje}%`,
                    alignment: 'right'
                }]);
            });

            documento.content.push({
                table: {
                    headerRows: 1,
                    widths: ['*', 'auto', 'auto', 'auto'],
                    body: resumenBody
                },
                layout: layoutTabla,
                margin: [0, 0, 0, 30]
            });


            documento.content.push({
                text: 'DETALLES POR MÉTODO DE PAGO',
                style: 'tableSubHeader',
                fontSize: 12,
                decoration: 'underline',
                margin: [0, 0, 0, 15]
            });

            datosTransacciones.forEach(item => {
                if (item.transacciones && item.transacciones.length > 0) {

                    documento.content.push({
                        text: item.metodo_pago.toUpperCase(),
                        style: 'tableSubHeader',
                        fontSize: 10,
                        color: '#555',
                        margin: [0, 10, 0, 5]
                    });

                    const headers = ['Fecha', 'N° Operación', 'Entidad', 'N° Cuenta', 'Monto (S/)'];
                    const tableBody = [];
                    tableBody.push(headers.map(h => ({
                        text: h,
                        style: 'tableHeader'
                    })));

                    item.transacciones.forEach((t) => {
                        tableBody.push([{
                            text: new Date(t.fecha).toLocaleDateString('es-PE')
                        }, {
                            text: t.numero_operacion || '-'
                        }, {
                            text: t.entidad_bancaria || '-'
                        }, {
                            text: t.numero_cuenta || '-'
                        }, {
                            text: `S/ ${formatearMoneda(t.monto)}`,
                            alignment: 'right'
                        }]);
                    });

                    const subtotal = item.subtotal || 0;
                    tableBody.push([{
                        text: 'SUBTOTAL',
                        style: 'tableSubtotal',
                        colSpan: 4,
                        alignment: 'right',
                    }, {}, {}, {}, {
                        text: `S/ ${formatearMoneda(subtotal)}`,
                        style: 'tableSubtotal'
                    }]);

                    documento.content.push({
                        table: {
                            headerRows: 1,
                            widths: ['auto', '*', '*', '*', 'auto'],
                            body: tableBody
                        },
                        layout: layoutTabla,
                        margin: [0, 0, 0, 20]
                    });
                }
            });

            pdfMake.createPdf(documento).open();
        }

        function formatearMoneda(monto) {
            return new Intl.NumberFormat('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(parseFloat(monto) || 0);
        }



        function mostrarNotificacion(mensaje, tipo = 'info') {
            const notificacion = document.createElement('div');
            notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} position-fixed`;
            notificacion.style.cssText = `
                                top: 20px;
                                right: 20px;
                                z-index: 9999;
                                min-width: 300px;
                                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                            `;
            const icono = tipo === 'success' ? 'check-circle' : 'exclamation-triangle';
            notificacion.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-${icono} me-2"></i>
                        <span>${mensaje}</span>
                        <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
                    </div>
                `;
            document.body.appendChild(notificacion);
            setTimeout(() => {
                if (notificacion.parentNode) {
                    notificacion.remove();
                }
            }, 5000);
        }

    });
</script>