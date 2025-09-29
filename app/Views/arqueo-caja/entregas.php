<?php

include __DIR__ . '/../layout/header.php';
?>
<style>
    .card-header {
        background-color: #2196F3;
        color: white;
        text-align: center;
    }
</style>

<div class="container-fluid mt-2">

    <div class="container-fluid mt-2">
        <div class="alert alert-info" role="alert" style="border-left: 4px solid #3498db; border-radius: 0 8px 8px 0;">
            <div class="row align-items-center">
                <div class="col-md-6 d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="background-color: transparent; padding: 0;">
                            <li class="breadcrumb-item"><a href="#" class="text-primary"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-primary">Arqueo Caja</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Entregados</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header  text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Reporte de Entregas Realizadas</h5>
                <div class="btn-group" role="group">
                    <a href="/arqueoCaja/listar/N" class="btn btn-outline-light text-white">
                        <i class="fas fa-box-open"></i> Pendientes
                    </a>
                    <a href="#" class="btn btn-light active">
                        <i class="fas fa-check-circle"></i> Entregados
                    </a>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha/Hora</th>
                                <th>Colaborador Entrega</th>
                                <th>Monto Total</th>
                                <th class="text-center">Arqueos Cubiertos</th>
                                <th>Destino(s)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($entregas) && is_array($entregas) && !empty($entregas)):
                            ?>
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($entregas as $entrega): ?>
                                    <tr>
                                        <td><?= $numeroFila++ ?></td>
                                        <td><i class="far fa-calendar-alt text-muted me-1"></i> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($entrega['fechaentrega']))) ?></td>
                                        <td><?= htmlspecialchars($entrega['nombre_colaborador']) ?></td>
                                        <td class="text-success fw-bold">S/ <?= number_format($entrega['montoentregado'], 2) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($entrega['total_arqueos_cubiertos']) ?></td>
                                        <td>
                                            <?php if (str_contains($entrega['tipos_destino_resumen'], 'Gerente')): ?>
                                                <span class="badge bg-primary"><i class="bi bi-person"></i> Gerente</span>
                                            <?php endif; ?>
                                            <?php if (str_contains($entrega['tipos_destino_resumen'], 'Deposito')): ?>
                                                <span class="badge bg-success"><i class="bi bi-credit-card-2-back-fill"></i> Depósito</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-outline-primary btn-detalle-entrega"
                                                title="Ver Detalles"
                                                data-bs-toggle="modal"
                                                data-bs-target="#detalleEntregaModal"
                                                data-identrega="<?= htmlspecialchars($entrega['identrega']) ?>"
                                                data-obs="<?= htmlspecialchars($entrega['obs_entrega'] ?? '') ?>"
                                                data-monto="<?= htmlspecialchars($entrega['montoentregado']) ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <!-- <button class="btn btn-sm btn-danger btn-pdf-entrega" title="Generar PDF" data-identrega="<?= htmlspecialchars($entrega['identrega']) ?>">
                                                <i class="fas fa-file-pdf "></i>
                                            </button> -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center">No hay entregas registradas que mostrar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detalleEntregaModal" tabindex="-1" aria-labelledby="detalleEntregaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">

                <div class="modal-header bg-primary text-white p-4 border-bottom border-primary border-3">
                    <h5 class="modal-title fw-bolder fs-4" id="detalleEntregaModalLabel">
                        <i class="fas fa-box-open me-2"></i> Detalle de Entrega #<span id="modal-identrega"></span>
                    </h5>
             
                       
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  
                </div>

                <div class="modal-body p-4 bg-light">

                    <div class="card p-4 mb-4 bg-white shadow-lg rounded-4 border-1 border-primary border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary fw-bolder mb-0 fs-5">
                                <i class="fas fa-chart-bar me-2"></i> Análisis de Arqueos
                            </h5>

                             <button type="button" class="btn btn-sm btn-outline-danger me-2 rounded" id="btn-descargar-pdf" title="Descargar Reporte PDF">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </button>
                        </div>
                        <div style="height: 350px;">
                            <canvas id="entregaChart"></canvas>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center align-items-center  gap-2 mb-2">
                        <div class="col-md-3 mb-3">
                            <div class="card p-3 shadow-sm rounded-4 bg-primary text-white border-white border-1 h-100">
                                <p class=" fw-bold mb-1 text-center"><i class="fas fa-hand-holding-usd me-2"></i> TOTAL ENTREGADO</p>
                                <h4 class="fw-bolder text-center mb-0 ">S/ <span id="card-monto-entregado">0.00</span></h4>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card p-3 shadow-sm rounded-4  border-white border-1 bg-danger text-white h-100">
                                <p class="fw-bold mb-1 text-center"><i class="fas fa-receipt me-2"></i> EGRESOS REGISTRADOS</p>
                                <h4 class="fw-bolder text-center mb-0">- S/ <span id="card-total-egresos">0.00</span></h4>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-lg rounded-4 p-3" style="background-color: #f8f9fa;">

                        <ul class="nav nav-tabs nav-justified" id="detalleEntregaTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold" id="arqueos-tab" data-bs-toggle="tab" data-bs-target="#arqueos-content" type="button" role="tab" aria-controls="arqueos-content" aria-selected="true">
                                    <i class="fas fa-cash-register me-1"></i> Detalle de Arqueos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="destino-tab" data-bs-toggle="tab" data-bs-target="#destino-content" type="button" role="tab" aria-controls="destino-content" aria-selected="false">
                                    <i class="fas fa-map-pin me-1"></i> Destino del Dinero
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content pt-3" id="detalleEntregaTabsContent">

                            <div class="tab-pane fade show active" id="arqueos-content" role="tabpanel" aria-labelledby="arqueos-tab">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-light table-striped mt-2">
                                        <thead class="table-primary text-white">
                                            <tr>
                                                <th>#</th>
                                                <th class="text-end">Ingresos Efect.</th>
                                                <th class="text-end">Ingresos Dig.</th>
                                                <th class="text-end">Egresos</th>
                                                <th class="text-end">Total Neto</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="arqueos-detalle-body">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">Cargando datos...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="destino-content" role="tabpanel" aria-labelledby="destino-tab">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-light table-striped mt-2">
                                        <thead class="table-success text-white">
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Destino</th>
                                                <th class="text-end">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody id="destinos-detalle-body">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">Cargando destinos...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="mt-4 p-3 shadow-sm rounded-4 border-1" style="background-color: #eaf1f5ff;">
                        <h5 class="mb-3 text-secondary fw-bold">
                            <i class="fas fa-comment-alt me-2"></i> Observaciones de la Entrega
                        </h5>
                        <p id="obs-entrega" class="mb-0 text-dark p-3 bg-white rounded border border-secondary border-opacity-25 fst-italic shadow-sm"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <?php include __DIR__ . '/../layout/footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="/assets/js/logoBase64.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const modalElement = document.getElementById('detalleEntregaModal');
            let myChart = null;


            document.getElementById('btn-descargar-pdf').addEventListener('click', async () => {
                const identrega = document.getElementById('modal-identrega').textContent;
                const obs = document.getElementById('obs-entrega').textContent;
                const totalEntregado = document.getElementById('card-monto-entregado').textContent;
                const totalEgresos = document.getElementById('card-total-egresos').textContent;


                const canvas = document.getElementById('entregaChart');

                let chartBase64 = null;
                try {
                    const canvasImage = await html2canvas(canvas, {
                        scale: 5,
                        backgroundColor: '#ffffff'
                    });
                    chartBase64 = canvasImage.toDataURL('image/png');
                } catch (error) {
                    console.error("Error al capturar el canvas:", error);
                    chartBase64 = null;
                }


                if (identrega) {

                    generatePDF(identrega, obs, totalEntregado, totalEgresos, chartBase64);
                } else {
                    alert('Aún no se han cargado los detalles de la entrega. Inténtalo de nuevo.');
                }
            });


            modalElement.addEventListener('show.bs.modal', async (event) => {
                const button = event.relatedTarget;
                const identrega = button.getAttribute('data-identrega');
                const obs = button.getAttribute('data-obs');
                const montoEntrega = parseFloat(button.getAttribute('data-monto'));

                document.getElementById('modal-identrega').textContent = identrega;
                document.getElementById('obs-entrega').textContent = obs || 'Sin observaciones registradas.';
                document.getElementById('card-monto-entregado').textContent = montoEntrega.toFixed(2);
                document.getElementById('card-total-egresos').textContent = '0.00'; // Se actualizará después de la carga

                const arqueosBody = document.getElementById('arqueos-detalle-body');
                const destinosBody = document.getElementById('destinos-detalle-body');

                // Mostrar spinners
                const loadingArqueos = '<tr><td colspan="6" class="text-center text-primary py-3"><i class="fas fa-spinner fa-spin me-2"></i>Cargando...</td></tr>';
                const loadingDestinos = '<tr><td colspan="3" class="text-center text-primary py-3"><i class="fas fa-spinner fa-spin me-2"></i>Cargando...</td></tr>';
                arqueosBody.innerHTML = loadingArqueos;
                destinosBody.innerHTML = loadingDestinos;

                try {
                    const response = await fetch(`/api/arqueo/entregas/${identrega}`);
                    const data = await response.json();

                    if (data.success) {
                        const {
                            arqueos,
                            destinos
                        } = data.data;


                        renderArqueosTable(arqueos, arqueosBody);
                        renderDestinosTable(destinos, destinosBody);


                        const totalEgresos = arqueos.reduce((sum, a) => sum + (parseFloat(a.egresos_dia) || 0), 0);
                        document.getElementById('card-total-egresos').textContent = totalEgresos.toFixed(2);


                        updateChart(montoEntrega, arqueos);
                    } else {
                        const errorMsg = '<tr><td colspan="6" class="text-center text-danger">Error: ' + data.message + '</td></tr>';
                        arqueosBody.innerHTML = errorMsg;
                        destinosBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error: ' + data.message + '</td></tr>';

                        if (myChart) myChart.destroy();
                    }

                } catch (error) {
                    console.error("Error al cargar detalles:", error);
                    const errorMessage = '<tr><td colspan="6" class="text-center text-danger">No se pudieron cargar los detalles.</td></tr>';
                    arqueosBody.innerHTML = errorMessage;
                    destinosBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">No se pudieron cargar los destinos.</td></tr>';
                    if (myChart) myChart.destroy();
                }
            });



            function renderArqueosTable(arqueos, body) {
                if (arqueos.length === 0) {
                    body.innerHTML = '<tr><td colspan="6" class="text-center">No hay arqueos asociados.</td></tr>';
                    return;
                }
                let numeroFila = 1;
                body.innerHTML = arqueos.map(a => `
                <tr>
                    <td>${numeroFila++}</td>
                    <td class="text-end">S/ ${parseFloat(a.ingresos_efectivo).toFixed(2)}</td>
                    <td class="text-end">S/ ${parseFloat(a.ingresos_digital).toFixed(2)}</td>
                    <td class="text-end text-danger fw-bold">S/ ${parseFloat(a.egresos_dia).toFixed(2)}</td>
                    <td class="text-end fw-bolder text-primary">S/ ${parseFloat(a.total).toFixed(2)}</td>
                    <td><span class="badge ${a.estado_arqueo === 'Cuadrado' ? 'bg-success' : 'bg-warning text-dark'}">${a.estado_arqueo}</span></td>
                </tr>
            `).join('');
            }

            function renderDestinosTable(destinos, body) {
                if (destinos.length === 0) {
                    body.innerHTML = '<tr><td colspan="3" class="text-center">No se registró el destino del dinero.</td></tr>';
                    return;
                }
                body.innerHTML = destinos.map(d => `
                <tr>
                    <td>${d.tipodestino}</td>
                    <td>${d.destino}</td>
                    <td class="text-end fw-bolder">S/ ${parseFloat(d.monto).toFixed(2)}</td>
                </tr>
            `).join('');
            }


            function updateChart(montoEntrega, arqueos) {
                const chartElement = document.getElementById('entregaChart');
                if (!chartElement) return;
                const ctx = chartElement.getContext('2d');

                if (myChart) myChart.destroy();

                const COLOR_ENTREGA = 'rgba(13, 110, 253, 0.9)';
                const COLOR_APORTACION_POS = 'rgba(25, 135, 84, 0.9)';
                const COLOR_APORTACION_NEG = 'rgba(255, 193, 7, 0.9)';
                const COLOR_EGRESO = 'rgba(220, 53, 69, 0.9)';
                const BORDER_COLOR = '#444';

                const arqueoLabels = arqueos.map((a, index) => `Arqueo #${index + 1}`);
                const aportacionNetaMontos = arqueos.map(a => parseFloat(a.total) || 0);
                const egresoMontos = arqueos.map(a => {
                    const egreso = parseFloat(a.egresos_dia) || 0;
                    return egreso > 0 ? egreso * -1 : null;
                });

                const labels = ['Total Entregado', ...arqueoLabels];
                const dataSetMontoEntregado = [montoEntrega, ...new Array(aportacionNetaMontos.length).fill(null)];
                const dataSetAportacionNeta = [null, ...aportacionNetaMontos];
                const dataSetEgresos = [null, ...egresoMontos];

                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Egresos',
                                data: dataSetEgresos,
                                backgroundColor: COLOR_EGRESO,
                                borderColor: BORDER_COLOR,
                                borderWidth: 1,
                                order: 0,
                                stack: 'stack1'
                            },
                            {
                                label: 'Aportación Neta',
                                data: dataSetAportacionNeta,
                                backgroundColor: aportacionNetaMontos.map(monto =>
                                    monto >= 0 ? COLOR_APORTACION_POS : COLOR_APORTACION_NEG
                                ),
                                borderColor: BORDER_COLOR,
                                borderWidth: 1,
                                order: 1,
                                stack: 'stack1'
                            },
                            {
                                label: 'Monto de la Entrega',
                                data: dataSetMontoEntregado,
                                backgroundColor: COLOR_ENTREGA,
                                borderColor: BORDER_COLOR,
                                borderWidth: 1,
                                order: 2,
                                stack: 'stack2'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true,
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Monto (S/)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            title: {
                                display: false
                            }
                        }
                    }
                });
            }


            function buildTable(tableElement, headerColor = '#CCCCCC') {
                const columns = Array.from(tableElement.querySelectorAll('thead th')).map(th => ({
                    text: th.textContent.trim(),
                    style: 'tableHeader'
                }));

                const bodyRows = Array.from(tableElement.querySelectorAll('tbody tr')).map(row =>
                    Array.from(row.querySelectorAll('td')).map(td => ({
                        text: td.textContent.trim(),
                        alignment: td.classList.contains('text-end') ? 'right' : 'left',
                        bold: td.classList.contains('fw-bolder')
                    }))
                );


                const headerRow = columns.map(col => ({
                    text: col.text,
                    style: 'tableHeader',
                    fillColor: headerColor
                }));

                return {
                    table: {
                        headerRows: 1,
                        widths: Array(columns.length).fill('*'),
                        body: [headerRow, ...bodyRows]
                    },
                    layout: {
                        fillColor: function(rowIndex) {
                            return (rowIndex % 2 === 0) ? '#EEEEEE' : null;
                        },
                        hLineWidth: function(i, node) {
                            return (i === 0 || i === node.table.body.length) ? 1 : 1;
                        },
                        vLineWidth: function(i, node) {
                            return (i === 0 || i === node.table.widths.length) ? 1 : 1;
                        },
                        hLineColor: function(i, node) {
                            return '#AAAAAA';
                        },
                        vLineColor: function(i, node) {
                            return '#AAAAAA';
                        },
                        paddingLeft: function(i, node) {
                            return 4;
                        },
                        paddingRight: function(i, node) {
                            return 4;
                        },
                        paddingTop: function(i, node) {
                            return 4;
                        },
                        paddingBottom: function(i, node) {
                            return 4;
                        },
                    },
                    style: 'tableStyles'
                };
            }





            async function generatePDF(identrega, obs, totalEntregado, totalEgresos, chartBase64) {

                const COLOR_NEGRO = '#000000';
                const COLOR_GRIS_OSCURO = '#333333';
                const COLOR_GRIS_CLARO = '#999999';
                const COLOR_FONDO_CLARO = '#F5F5F5';

                const logo = window.logoBase64;
                const nombreEmpresa = 'YONDA & GRUPO HUARACA E.I.R.L';
                const rucEmpresa = 'RUC: 20609396866';

                const tableArqueosEl = document.querySelector('#arqueos-content table');
                const tableDestinosEl = document.querySelector('#destino-content table');

                const tableArqueosData = buildTable(tableArqueosEl, COLOR_NEGRO);
                const tableDestinosData = buildTable(tableDestinosEl, COLOR_NEGRO);

                const docDefinition = {

                    defaultStyle: {
                        fontSize: 7.4,
                        color: COLOR_GRIS_OSCURO
                    },

                    content: [

                        {
                            columns: [{
                                    image: logo,
                                    width: 80,
                                    alignment: 'left'
                                },
                                {
                                    stack: [{
                                            text: nombreEmpresa,
                                            style: 'empresaNombre'
                                        },
                                        {
                                            text: rucEmpresa,
                                            style: 'empresaRuc'
                                        }
                                    ],
                                    alignment: 'right'
                                }
                            ],
                            margin: [0, 0, 0, 20]
                        },
                        {
                            columns: [
                                // Total Entregado
                                {
                                    stack: [{
                                            text: 'MONTO TOTAL ENTREGADO',
                                            style: 'resumenEtiqueta'
                                        },
                                        {
                                            text: `S/ ${totalEntregado}`,
                                            style: 'resumenValor'
                                        }
                                    ]
                                },

                                {
                                    stack: [{
                                            text: 'TOTAL DE EGRESOS REGISTRADOS',
                                            style: 'resumenEtiqueta',
                                            alignment: 'right'
                                        },
                                        {
                                            text: `S/ ${totalEgresos}`,
                                            style: 'resumenValor',
                                            alignment: 'right'
                                        }
                                    ]
                                },
                            ],
                            margin: [0, 5, 0, 0],
                        },

                        {
                            text: 'Análisis Gráfico de Arqueos',
                            style: 'subtitle'
                        },
                        {
                            image: chartBase64,
                            width: 500,
                            alignment: 'center',
                            margin: [0, 5, 0, 20]
                        },


                        {
                            text: 'Detalle de Arqueos',
                            style: 'subtitle'
                        },
                        tableArqueosData,


                        {
                            text: 'Detalle de Destino del Dinero',
                            style: 'subtitle',

                        },
                        tableDestinosData,


                        {
                            text: 'Observaciones de la Entrega',
                            style: 'subtitle'
                        },
                        {
                            text: obs || 'No se registraron observaciones para esta entrega.',
                            fillColor: COLOR_FONDO_CLARO,
                            margin: [0, 5, 0, 10],
                            italics: true,
                            alignment: 'justify',
                            border: [true, true, true, true],
                            borderColor: [COLOR_GRIS_CLARO, COLOR_GRIS_CLARO, COLOR_GRIS_CLARO, COLOR_GRIS_CLARO]
                        }
                    ],
                    styles: {

                        empresaNombre: {
                            bold: true,
                            color: COLOR_NEGRO,
                            alignment: 'right',
                        },
                        empresaRuc: {
                            bold: true,
                            margin: [0, 2, 0, 0],
                            alignment: 'right',
                            color: COLOR_GRIS_OSCURO,
                        },

                        subtitle: {
                            fontSize: 11,
                            bold: true,
                            color: COLOR_GRIS_OSCURO,
                            margin: [0, 15, 0, 5]
                        },

                        resumenEtiqueta: {
                            color: COLOR_GRIS_CLARO,
                            bold: true,
                            margin: [0, 0, 0, 2]
                        },
                        resumenValor: {
                            fontSize: 12,
                            bold: true,
                            color: COLOR_NEGRO,
                        },

                        tableHeader: {
                            bold: true,
                            color: 'white',
                            alignment: 'center',
                            fillColor: COLOR_NEGRO
                        }
                    }
                };

                pdfMake.createPdf(docDefinition).open();
            }










        });
    </script>