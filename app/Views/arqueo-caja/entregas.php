<?php

include __DIR__ . '/../layout/header.php';
?>
<style>
    .card-header {
        background-color: #2196F3;
        color: white;
        text-align: center;
    }

    #detalleEntregaModal .nav-tabs .nav-link {
        transition: all 0.3s ease;
    }

    #detalleEntregaModal .nav-tabs .nav-link:hover {
        color: #667eea;
        background-color: #f8f9fa;
    }

    #detalleEntregaModal .nav-tabs .nav-link.active {
        border-bottom: 3px solid #667eea !important;
        color: #667eea !important;
    }



    #detalleEntregaModal .card {
        transition: all 0.3s ease;
    }

    #detalleEntregaModal .card:hover {
        transform: translateY(-2px);
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
                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Entregas</h5>
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

                <div class="table-responsive d-none d-md-block">
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
                            <?php if (!empty($entregas) && is_array($entregas)): ?>
                                <?php $numeroFila = 1; ?>
                                <?php foreach ($entregas as $entrega): ?>
                                    <tr>
                                        <td><?= $numeroFila++ ?></td>
                                        <td>
                                            <i class="far fa-calendar-alt text-muted me-1"></i>
                                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($entrega['fechaentrega']))) ?>
                                        </td>
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

                                            <!-- <button class="btn btn-sm btn-danger btn-pdf-entrega"
                                    title="Generar PDF"
                                    data-identrega="<?= htmlspecialchars($entrega['identrega']) ?>">
                                    <i class="fas fa-file-pdf"></i>
                                </button> -->

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay entregas registradas que mostrar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-block d-md-none">
                    <?php if (!empty($entregas) && is_array($entregas)): ?>
                        <div class="accordion" id="accordionEntregas">
                            <?php $i = 1;
                            foreach ($entregas as $entrega): ?>
                                <div class="accordion-item mb-2">
                                    <h2 class="accordion-header" id="headingEntrega<?= $i ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEntrega<?= $i ?>">
                                            <i class="fas fa-box-open fs-5 me-1 text-success"></i> Entrega #<?= $i ?> - <?= date('d/m/Y H:i', strtotime($entrega['fechaentrega'])) ?>
                                        </button>
                                    </h2>
                                    <div id="collapseEntrega<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#accordionEntregas">
                                        <div class="accordion-body">
                                            <p><strong>Colaborador:</strong> <?= htmlspecialchars($entrega['nombre_colaborador']) ?></p>
                                            <p><strong>Monto Total:</strong> <span class="text-success fw-bold">S/ <?= number_format($entrega['montoentregado'], 2) ?></span></p>
                                            <p><strong>Arqueos Cubiertos:</strong> <?= htmlspecialchars($entrega['total_arqueos_cubiertos']) ?></p>
                                            <p><strong>Destino(s):</strong>
                                                <?php if (str_contains($entrega['tipos_destino_resumen'], 'Gerente')): ?>
                                                    <span class="badge bg-primary"><i class="bi bi-person"></i> Gerente</span>
                                                <?php endif; ?>
                                                <?php if (str_contains($entrega['tipos_destino_resumen'], 'Deposito')): ?>
                                                    <span class="badge bg-success"><i class="bi bi-credit-card-2-back-fill"></i> Depósito</span>
                                                <?php endif; ?>
                                            </p>
                                            <div class="mt-2">
                                                <button
                                                    class="btn btn-sm btn-outline-primary btn-detalle-entrega"
                                                    title="Ver Detalles"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detalleEntregaModal"
                                                    data-identrega="<?= htmlspecialchars($entrega['identrega']) ?>"
                                                    data-obs="<?= htmlspecialchars($entrega['obs_entrega'] ?? '') ?>"
                                                    data-monto="<?= htmlspecialchars($entrega['montoentregado']) ?>">
                                                    <i class="fas fa-eye"></i> Ver Detalles
                                                </button>

                                                <!-- <button class="btn btn-sm btn-danger btn-pdf-entrega"
                                        title="Generar PDF"
                                        data-identrega="<?= htmlspecialchars($entrega['identrega']) ?>">
                                        <i class="fas fa-file-pdf"></i> Generar PDF
                                    </button> -->

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php $i++;
                            endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light text-center">No hay entregas registradas que mostrar.</div>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="detalleEntregaModal" tabindex="-1" aria-labelledby="detalleEntregaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

                <div class="modal-header text-white p-4 position-relative" style="background: linear-gradient(135deg, #3055f7ff 0%, #f2e4ffff 100%); border: none;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                            <i class="fas fa-box-open fs-4"></i>
                        </div>
                        <div>
                            <p class="modal-title fw-bold mb-1 fs-4" id="detalleEntregaModalLabel">
                                Detalle de Entrega: #<span id="modal-identrega"></span>
                            </p>
                            <p class="mb-0 opacity-75" style="font-size: 0.9rem;">

                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">


                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                                <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-white">
                                            <p class="mb-2 opacity-75" style="font-size: 0.85rem; font-weight: 500;">
                                                <i class="fas fa-hand-holding-usd me-2"></i>TOTAL ENTREGADO
                                            </p>
                                            <h3 class="mb-0 fw-bold">S/ <span id="card-monto-entregado">0.00</span></h3>
                                        </div>
                                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                                            <i class="fas fa-coins fs-2 text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                                <div class="card-body p-4" style="background: linear-gradient(135deg, #ff7e42ff 0%, #f5576c 100%);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-white">
                                            <p class="mb-2 opacity-75" style="font-size: 0.85rem; font-weight: 500;">
                                                <i class="fas fa-receipt me-2"></i>EGRESOS REGISTRADOS
                                            </p>
                                            <h3 class="mb-0 fw-bold">- S/ <span id="card-total-egresos">0.00</span></h3>
                                        </div>
                                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                                            <i class="fas fa-file-invoice-dollar fs-2 text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-body  border border-secondary p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1 fw-bold" style="color: #667eea;">
                                        <i class="fas fa-chart-bar me-2"></i>Análisis de Arqueos
                                    </h5>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Visualización comparativa de ingresos y egresos</p>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger shadow-sm" id="btn-descargar-pdf">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-4 bg-body border border-secondary">
                            <div style="height: 380px; position: relative;">
                                <div id="entregaChart"></div>

                            </div>
                        </div>
                    </div>


                    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <ul class="nav nav-tabs border-0 px-3 pt-3" id="detalleEntregaTabs" role="tablist" style="background: linear-gradient(135deg, #68cdecff 0%, #ffbc6fff 100%);">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold px-4 py-3" id="arqueos-tab" data-bs-toggle="tab" data-bs-target="#arqueos-content" type="button" role="tab"">
                                <i class=" fas fa-cash-register me-2"></i>Detalle de Arqueos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold px-4 py-3" id="destino-tab" data-bs-toggle="tab" data-bs-target="#destino-content" type="button" role="tab"">
                                <i class=" fas fa-map-pin me-2"></i>Destino del Dinero
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content p-4 bg-body border border-secondary " id="detalleEntregaTabsContent">

                            <div class="tab-pane fade show active" id="arqueos-content" role="tabpanel">

                                <div class="table-responsive">
                                    <table class="table table-hover bg-body align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="py-3" style="border-bottom: 2px solid #667eea;">#</th>
                                                <th class="text-end text-body fw-bold py-3" style="border-bottom: 2px solid #667eea;">Ingresos Efect.</th>
                                                <th class="text-end  text-body fw-bold py-3" style="border-bottom: 2px solid #667eea;">Ingresos Dig.</th>
                                                <th class="text-end text-body fw-bold py-3" style="border-bottom: 2px solid #667eea;">Egresos</th>
                                                <th class="text-end text-body fw-bold py-3" style="border-bottom: 2px solid #667eea;">Total Neto</th>
                                                <th class="py-3 text-body fw-bold" style="border-bottom: 2px solid #667eea;">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="arqueos-detalle-body">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-spinner fa-spin me-2"></i>Cargando datos...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>





                            </div>

                            <div class="tab-pane fade" id="destino-content" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead style="background-color: #f8f9fa;">
                                            <tr>
                                                <th class="py-3" style="border-bottom: 2px solid #667eea; color: #495057; font-weight: 600;">Tipo</th>
                                                <th class="py-3" style="border-bottom: 2px solid #667eea; color: #495057; font-weight: 600;">Destino</th>
                                                <th class="text-end py-3" style="border-bottom: 2px solid #667eea; color: #495057; font-weight: 600;">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody id="destinos-detalle-body">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <i class="fas fa-spinner fa-spin me-2"></i>Cargando destinos...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card border-0 shadow-sm mt-4" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-body p-4" style="background: linear-gradient(135deg, #ffc981ff 0%, #fdffe7ff 100%);">
                            <h6 class="mb-3 fw-bold text-dark">
                                <i class="fas fa-comment-alt me-2"></i>Observaciones de la Entrega
                            </h6>
                            <div class="bg-white p-3 rounded shadow-sm" style="border-left: 4px solid #667eea;">
                                <p id="obs-entrega" class="mb-0 text-dark fst-italic" style="line-height: 1.6;"></p>
                            </div>
                        </div>
                    </div>

                </div>



            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../layout/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="/assets/js/logoBase64.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let myChart = null;
        let cachedArqueos = null; // Para almacenar los datos del arqueo y usarlos en el shown.bs.modal

        document.addEventListener('DOMContentLoaded', () => {

            const modalElement = document.getElementById('detalleEntregaModal');

            function normalizeSVG(svgElement, targetWidth = 500, targetHeight = 300) {
                let svg = svgElement.cloneNode(true);

                // Forzar tamaño fijo
                svg.setAttribute("width", targetWidth);
                svg.setAttribute("height", targetHeight);

                // Asegurar viewBox
                if (!svg.getAttribute("viewBox")) {
                    const vb = `0 0 ${svgElement.clientWidth || targetWidth} ${svgElement.clientHeight || targetHeight}`;
                    svg.setAttribute("viewBox", vb);
                }

                return svg.outerHTML;
            }


            document.getElementById('btn-descargar-pdf').addEventListener('click', async () => {
                const identrega = document.getElementById('modal-identrega').textContent;
                const obs = document.getElementById('obs-entrega').textContent;
                const totalEntregado = document.getElementById('card-monto-entregado').textContent;
                const totalEgresos = document.getElementById('card-total-egresos').textContent;
                let chartSVG = null;

                try {
                    const svgElement = document.querySelector("#entregaChart svg");
                    if (svgElement) {
                        chartSVG = normalizeSVG(svgElement, 500, 300); // 👈 aquí normalizamos
                    } else {
                        console.warn("No se encontró el SVG dentro de #entregaChart");
                    }
                } catch (error) {
                    showToast("Error al exportar el gráfico como SVG del DOM", 'DANGER', 1350);
                }

                if (identrega) {
                    generatePDF(identrega, obs, totalEntregado, totalEgresos, chartSVG);
                } else {
                    alert('Aún no se han cargado los detalles de la entrega. Inténtalo de nuevo.');
                }
            });



            modalElement.addEventListener('show.bs.modal', async (event) => {
                const button = event.relatedTarget;
                const identrega = button.getAttribute('data-identrega');
                const obs = button.getAttribute('data-obs');
                const montoEntrega = parseFloat(button.getAttribute('data-monto') || 0);


                document.getElementById('modal-identrega').textContent = identrega;
                document.getElementById('obs-entrega').textContent = obs || 'Sin observaciones registradas.';
                document.getElementById('card-monto-entregado').textContent = montoEntrega.toFixed(2);
                document.getElementById('card-total-egresos').textContent = '0.00';

                const arqueosBody = document.getElementById('arqueos-detalle-body');
                const destinosBody = document.getElementById('destinos-detalle-body');

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

                        // Almacenamos en caché los datos para el handler del gráfico
                        cachedArqueos = {
                            montoEntrega,
                            arqueos
                        };
                    } else {
                        const errorMsg = '<tr><td colspan="6" class="text-center text-danger">Error: ' + data.message + '</td></tr>';
                        arqueosBody.innerHTML = errorMsg;
                        destinosBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error: ' + data.message + '</td></tr>';
                        if (myChart) myChart.destroy();
                        cachedArqueos = null; // Reiniciamos el caché en caso de error
                    }

                } catch (error) {
                    console.error("Error al cargar detalles:", error);
                    cachedArqueos = null;
                }
            });



            modalElement.addEventListener('shown.bs.modal', async () => {
                if (cachedArqueos && cachedArqueos.arqueos) {
                    await updateChart(cachedArqueos.montoEntrega, cachedArqueos.arqueos);
                }
            });


            function updateChart(montoEntrega, arqueos) {
                const chartElement = document.getElementById('entregaChart');
                if (!chartElement) return Promise.resolve(null);
                if (myChart) {
                    myChart.destroy(); // Destruye la instancia anterior
                }


                const COLOR_ENTREGA = '#667eea';
                const COLOR_APORTACION_POS = '#10b981';
                const COLOR_EGRESO = '#ef4444';
                const COLOR_PERDIDA = '#f59e0b';

                const arqueoLabels = arqueos.map((a, index) => `Arqueo #${index + 1}`);
                const labels = ['Total Entregado', ...arqueoLabels];


                const dataSetMontoEntregado = [montoEntrega, ...new Array(arqueos.length).fill(null)];
                const dataSetEgresos = [null, ...arqueos.map(a => {
                    const egreso = parseFloat(a.egresos_dia) || 0;
                    return egreso > 0 ? egreso * -1 : null;
                })];
                const dataSetAportacionNeta = [null, ...arqueos.map(a => {
                    const total = parseFloat(a.total) || 0;
                    return total > 0 ? total : null;
                })];
                const dataSetPerdidas = [null, ...arqueos.map(a => {
                    const total = parseFloat(a.total) || 0;
                    return total < 0 ? total : null;
                })];



                const options = {
                    chart: {
                        type: 'bar',
                        height: 400,
                        stacked: true,
                        toolbar: {
                            show: true
                        },
                        events: {

                            rendered: function(chartContext, config) {
                                if (chartContext.__renderResolver) {
                                    chartContext.__renderResolver();
                                }
                            }
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            horizontal: false
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(value) {
                            if (value === null) return '';
                            return "S/ " + Math.abs(value).toFixed(2);
                        },
                        style: {
                            fontSize: '11px',
                            fontWeight: 'bold',
                            colors: ['#111']
                        }
                    },

                    series: [{
                            name: 'Egresos',
                            data: dataSetEgresos,
                            color: COLOR_EGRESO
                        },
                        {
                            name: 'Aportación Neta',
                            data: dataSetAportacionNeta,
                            color: COLOR_APORTACION_POS
                        },
                        {
                            name: 'Monto de la Entrega',
                            data: dataSetMontoEntregado,
                            color: COLOR_ENTREGA
                        },
                        {
                            name: 'Pérdidas',
                            data: dataSetPerdidas,
                            color: COLOR_PERDIDA
                        }
                    ],
                    xaxis: {
                        categories: labels,
                        labels: {
                            style: {
                                fontSize: '11px',
                                bold: true,
                                colors: '#1354afff'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return 'S/ ' + value.toLocaleString('es-PE', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            },
                            style: {
                                fontSize: '11px',
                                colors: '#64748b'
                            }
                        },
                        title: {
                            text: 'Monto (S/)',
                            style: {
                                fontSize: '13px',
                                fontWeight: 'bold',
                                color: '#475569'
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px',
                        labels: {
                            colors: '#475569'
                        },
                        markers: {
                            radius: 12
                        }
                    },
                    tooltip: {
                        theme: 'dark',
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Arial, sans-serif'
                        },
                        fillSeriesColor: false,
                        marker: {
                            show: true
                        },
                        y: {
                            formatter: function(value) {
                                return "S/ " + Math.abs(value).toFixed(2);
                            },
                            title: {
                                formatter: function(seriesName) {
                                    return seriesName + ":";
                                }
                            }
                        }
                    }

                };


                myChart = new ApexCharts(chartElement, options);


                return new Promise(resolve => {
                    myChart.__renderResolver = resolve; // Almacenamos el resolvedor en el contexto del gráfico
                    myChart.render();
                });
            }


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
                    <td class="text-end fw-bold fw-bold">S/ ${parseFloat(a.egresos_dia).toFixed(2)}</td>
                    <td class="text-end fw-bolder fw-bold">S/ ${parseFloat(a.total).toFixed(2)}</td>
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


            function buildTable(tableElement, headerColor = '#000000') {
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



            async function generatePDF(identrega, obs, totalEntregado, totalEgresos, chartSVG) {
                const COLOR_NEGRO = '#000000';
                const COLOR_GRIS_OSCURO = '#333333';
                const COLOR_GRIS_CLARO = '#999999';
                const COLOR_FONDO_CLARO = '#F5F5F5';

                const logo = window.logoBase64 || '';
                const nombreEmpresa = 'YONDA & GRUPO HUARACA E.I.R.L';
                const rucEmpresa = 'RUC: 20609396866';

                const tableArqueosEl = document.querySelector('#arqueos-content table');
                const tableDestinosEl = document.querySelector('#destino-content table');

                const tableArqueosData = buildTable(tableArqueosEl, COLOR_NEGRO);
                const tableDestinosData = buildTable(tableDestinosEl, COLOR_NEGRO);

                let contentArray = [
                    // --- Encabezado ---
                    {
                        columns: [{
                            image: logo,
                            width: 80,
                            alignment: 'left'
                        }, {
                            stack: [{
                                text: nombreEmpresa,
                                style: 'empresaNombre'
                            }, {
                                text: rucEmpresa,
                                style: 'empresaRuc'
                            }],
                            alignment: 'right'
                        }],
                        margin: [0, 0, 0, 20]
                    },
                    // --- Resumen ---
                    {
                        columns: [{
                            stack: [{
                                text: 'MONTO TOTAL ENTREGADO',
                                style: 'resumenEtiqueta'
                            }, {
                                text: `S/ ${totalEntregado}`,
                                style: 'resumenValor'
                            }]
                        }, {
                            stack: [{
                                text: 'TOTAL DE EGRESOS REGISTRADOS',
                                style: 'resumenEtiqueta',
                                alignment: 'right'
                            }, {
                                text: `S/ ${totalEgresos}`,
                                style: 'resumenValor',
                                alignment: 'right'
                            }]
                        }],
                        margin: [0, 5, 0, 0],
                    },
                    // --- Título del Gráfico ---
                    {
                        text: 'Análisis Gráfico de Arqueos',
                        style: 'subtitle'
                    },
                ];

                // --- Insertar el gráfico en SVG si existe ---
                if (chartSVG) {
                    contentArray.push({
                        svg: chartSVG,
                        fit: [500, 300],
                        alignment: 'center',
                        margin: [0, 5, 0, 20]
                    });
                } else {
                    contentArray.push({
                        text: 'No se pudo generar el gráfico en formato vectorial.',
                        alignment: 'center',
                        color: '#ef4444',
                        margin: [0, 5, 0, 20],
                        fontSize: 8,
                        italics: true,
                    });
                }

                // --- Detalle de Arqueos ---
                contentArray.push({
                    text: 'Detalle de Arqueos',
                    style: 'subtitle'
                }, tableArqueosData);

                // --- Detalle de Destino ---
                contentArray.push({
                    text: 'Detalle de Destino del Dinero',
                    style: 'subtitle'
                }, tableDestinosData);

                // --- Observaciones ---
                contentArray.push({
                    text: 'Observaciones de la Entrega',
                    style: 'subtitle'
                }, {
                    text: obs || 'No se registraron observaciones para esta entrega.',
                    fillColor: COLOR_FONDO_CLARO,
                    margin: [0, 5, 0, 10],
                    italics: true,
                    alignment: 'justify',
                    border: [true, true, true, true],
                    borderColor: [COLOR_GRIS_CLARO, COLOR_GRIS_CLARO, COLOR_GRIS_CLARO, COLOR_GRIS_CLARO]
                });

                const docDefinition = {
                    pageSize: 'A4',
                    pageMargins: [30, 40, 30, 40],
                    defaultStyle: {
                        fontSize: 7.4,
                        color: COLOR_GRIS_OSCURO
                    },
                    content: contentArray,
                    styles: {
                        empresaNombre: {
                            bold: true,
                            color: COLOR_NEGRO,
                            alignment: 'right'
                        },
                        empresaRuc: {
                            bold: true,
                            margin: [0, 2, 0, 0],
                            alignment: 'right',
                            color: COLOR_GRIS_OSCURO
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
                            color: COLOR_NEGRO
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