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
                                            <button class="btn btn-sm btn-danger btn-pdf-entrega" title="Generar PDF" data-identrega="<?= htmlspecialchars($entrega['identrega']) ?>">
                                                <i class="fas fa-file-pdf "></i>
                                            </button>
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

                <div class="card p-4 mb-4 bg-white shadow-lg rounded-4 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-primary fw-bolder mb-0 fs-5">
                            <i class="fas fa-chart-bar me-2"></i> Análisis de Rendimiento de Arqueos
                        </h5>
                        
                    </div>
                    
                    <div style="height: 380px;"> <canvas id="entregaChart"></canvas>
                    </div>
                </div>
                
                <div class="row">

                    <div class="col-md-7 mb-4">
                        <div class="card p-3 h-100 shadow-sm rounded-4 border-start border-info bg-light border-5">
                            <h5 class="text-info fw-bold mb-3">
                                <i class="fas fa-cash-register me-2"></i> Detalle de Arqueos
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-light mt-2">
                                    <thead class="table-info text-white">
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
                    </div>

                    <div class="col-md-5 mb-4">
                        <div class="card p-3 h-100 shadow-sm rounded-4 border-start border-success border-5 bg-light">
                            <h5 class="text-success fw-bold mb-3">
                                <i class="fas fa-map-pin me-2"></i> Destino del Dinero
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-light mt-2">
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

                <div class="card p-3 mt-2 shadow-sm rounded-4  bg-light border-0">
                    <h5 class="mb-3 text-secondary fw-bold">
                        <i class="fas fa-comment-alt me-2"></i> Observaciones de la Entrega
                    </h5>
                    <p id="obs-entrega" class="mb-0 text-dark p-3 bg-light rounded border border-secondary border-opacity-25 fst-italic"></p>
                </div>
            </div>
            
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  

    <?php include __DIR__ . '/../layout/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const modalElement = document.getElementById('detalleEntregaModal');
            let myChart = null;


            modalElement.addEventListener('show.bs.modal', async (event) => {
                const button = event.relatedTarget;
                const identrega = button.getAttribute('data-identrega');
                const obs = button.getAttribute('data-obs');
                const montoEntrega = parseFloat(button.getAttribute('data-monto'));


                document.getElementById('modal-identrega').textContent = identrega;
                document.getElementById('obs-entrega').textContent = obs || 'Sin observaciones registradas.';

                const arqueosBody = document.getElementById('arqueos-detalle-body');
                const destinosBody = document.getElementById('destinos-detalle-body');
                arqueosBody.innerHTML = '<tr><td colspan="4" class="text-center text-primary"><i class="fas fa-spinner fa-spin me-2"></i>Cargando...</td></tr>';
                destinosBody.innerHTML = '<tr><td colspan="3" class="text-center text-primary"><i class="fas fa-spinner fa-spin me-2"></i>Cargando...</td></tr>';

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

                        //Actualizar el gráfico
                        updateChart(montoEntrega, arqueos);
                    } else {

                        arqueosBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error: ' + result.message + '</td></tr>';
                        destinosBody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error: ' + result.message + '</td></tr>';
                    }


                } catch (error) {
                    consle.log(error);
                    const errorMessage = '<tr><td colspan="4" class="text-center text-danger">No se pudieron cargar los detalles.</td></tr>';
                    arqueosBody.innerHTML = errorMessage;
                    destinosBody.innerHTML = errorMessage;
                }

            });



            function renderArqueosTable(arqueos, body) {

                if (arqueos.length === 0) {
                    body.innerHTML = '<tr><td colspan="4" class="text-center">No hay arqueos asociados.</td></tr>';
                    return;
                }
                let numeroFila = 1;
                body.innerHTML = arqueos.map(a => `
                            <tr>
                                <td>${numeroFila++}</td>
                                <td>S/ ${parseFloat(a.ingresos_efectivo).toFixed(2)}</td>
                                <td>S/ ${parseFloat(a.ingresos_digital).toFixed(2)}</td>
                                <td class="text-danger fw-bold">S/ ${parseFloat(a.egresos_dia).toFixed(2)}</td>
                                <td>S/ ${parseFloat(a.total).toFixed(2)}</td>

                                <td class="${a.estado_arqueo === 'Cuadrado' ? 'text-success' : 'text-danger'}">${a.estado_arqueo}</td>
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
                                    <td>S/ ${parseFloat(d.monto).toFixed(2)}</td>
                                </tr>
                            `).join('');
            }


      
            function updateChart(montoEntrega, arqueos) {
         
                const chartElement = document.getElementById('entregaChart');
                if (!chartElement) {
                    console.error("No se encontró el elemento 'entregaChart' en el DOM.");
                    return;
                }
                const ctx = chartElement.getContext('2d');

                
                if (myChart) {
                    myChart.destroy();
                }

                const COLOR_ENTREGA = 'rgba(75, 192, 192, 0.8)'; 
                const COLOR_APORTACION_POS = 'rgba(70, 190, 70, 0.8)'; 
                const COLOR_APORTACION_NEG = 'rgba(255, 165, 0, 0.8)'; 
                const COLOR_EGRESO = 'rgba(255, 99, 132, 0.8)';
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
                        datasets: [
                         
                            {
                                label: 'Egresos del Arqueo',
                                data: dataSetEgresos,
                                backgroundColor: COLOR_EGRESO,
                                borderColor: BORDER_COLOR,
                                borderWidth: 1,
                                order: 0, 
                            },
                            
                            {
                                label: 'Aportación Neta (Ingresos - Egresos)',
                                data: dataSetAportacionNeta,
                                
                                backgroundColor: aportacionNetaMontos.map(monto =>
                                    monto >= 0 ? COLOR_APORTACION_POS : COLOR_APORTACION_NEG
                                ),
                                borderColor: BORDER_COLOR,
                                borderWidth: 1,
                                order: 1, 
                            },
                            
                            {
                                label: 'Monto de la Entrega',
                                data: dataSetMontoEntregado,
                                backgroundColor: COLOR_ENTREGA,
                                borderColor: BORDER_COLOR,
                                borderWidth: 1,
                                order: 2, }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                   
                        layout: {
                            padding: {
                                top: 20
                            }
                        },
                        elements: {
                            bar: {
                                borderSkipped: false,
                                borderRadius: 5,
                            }
                        },

                      
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#333'
                                }
                            },
                            y: {
                        
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Monto (S/)',
                                    color: '#000',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                },
                                ticks: {
                                    color: '#333'
                                }
                            }
                        },

                
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    color: '#333'
                                }
                            },
                            title: {
                                display: true,
                                text: 'Análisis de Entrega vs. Rendimiento de Arqueos',
                                font: {
                                    size: 18,
                                    weight: 'bold'
                                },
                                color: '#000'
                            }
                        }
                    }
                });

                ctx.canvas.style.background = `linear-gradient(to bottom, rgba(240, 248, 255, 0.9), rgba(200, 220, 255, 0.7))`;
            }


    

        
        });
    </script>