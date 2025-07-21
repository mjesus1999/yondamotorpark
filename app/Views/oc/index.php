<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">

    <div class="alert alert-info mt-2" role="alert">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-start">
                <nav
                    style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
                    aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Órdenes de compra</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Listar</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 d-flex align-items-center justify-content-end">
                <a href="/oc/create" class="btn btn-outline-primary btn-sm">Registrar</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="btn-group" id="botones-filtro">
                <!-- 'emitido', 'aprobado', 'presentado', 'anulado', 'pagado' -->
                <button class="btn btn-sm btn-outline-primary"
                    title="El área de logística generó una nueva OC que aun gerencia no autoriza">Emitido</button>
                <button class="btn btn-sm btn-outline-primary"
                    title="Gerencia aprobó la OC deberá envíarsela al concesionario">Aprobado</button>
                <button class="btn btn-sm btn-outline-primary"
                    title="La OC fue enviada al concesionario, se deben realizar los pagos correspondientes">En proceso</button>
                <button class="btn btn-sm btn-outline-primary" title="OC anulada, deberá indicar los motivos">Anulado</button>
                <button class="btn btn-sm btn-outline-primary"
                    title="OC pagada completamente, verifique factura">Pagado</button>
            </div>
        </div>
        <div class="card-body" id="lista-oc">
            <div class="table-responsive">
                <table class="table table-sm table-hover" id="tabla-oc">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Número</th>
                            <th>Concesionario</th>
                            <th>Fecha</th>
                            <th>Moneda</th>
                            <!-- <th>Total</th>
              <th>Amortización</th>
              <th>Saldo</th> -->
                            <th>Operaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ordenCompras)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay ordénes de compras registradas.</td>
                            </tr>

                        <?php else : ?>
                            <?php $numeroFila = 1; ?>
                            <?php foreach ($ordenCompras as $ordenCompra) : ?>

                                <tr>
                                    <td><?= htmlspecialchars($numeroFila++) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['serie']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['razonsocial']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['emision']) ?></td>
                                    <td><?= htmlspecialchars($ordenCompra['moneda']) ?></td>
                                    <!-- <td>151788.00</td>
                                <td>50000</td>
                                <td>101788</td> -->
                                    <td>
                                        <!-- <a href="/oc/reporte/<?=htmlspecialchars($ordenCompra['idordencompra']) ?>" target="_blank" title="PDF Tradicional"><i class="fa-solid fa-file-pdf" style="color: #f73809;"></i></a> -->
                                        <a href="/oc/reporte/<?=htmlspecialchars($ordenCompra['idordencompra']) ?>" target="_blank" title="PDF HTML2PDF"><i class="fa-solid fa-file-pdf" style="color: #f73809;;"></i></a>
                                        <a href="#" class="show-details" data-idoc=<?= htmlspecialchars($ordenCompra['idordencompra']) ?>>Detalle</a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>


                        <?php endif; ?>

                    </tbody>
                </table>
            </div> <!-- ./table-responsive -->
        </div> <!-- ./card-body -->



        <div class="card-footer" id="detalle-oc" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div style="padding: 1rem;">
                        
                        <h3 id="detail-concesionario-razon-social"></h3>
                        <h5 id="detail-oc-summary"></h5>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-end" style="padding-right: 1.5rem;">
                    <button type="button" id="btn-volver" class="btn btn-outline-primary btn-sm">Volver</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm" id="tabla-detalles">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Versión</th>
                            <th>Combustible</th>
                            <th>Año</th>
                            <th>Chasis</th>
                            <th>Serie</th>
                            <th>Placa</th> 
                            <th>Placa Rotativa</th> 
                            <th>Color</th>
                            <th>Moneda</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div> <!-- ./table-responsive -->
        </div> <!-- ./card-footer -->
    </div> <!-- ./card -->

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const botonesFiltro = document.querySelectorAll("#botones-filtro .btn")
            const enlacesDetalle = document.querySelectorAll(".show-details")
            const botonVolver = document.querySelector("#btn-volver")
            const speedAnimation = 750;

            // Referencias a los contenedores principales
            const listaOc = document.getElementById('lista-oc'); 
            const ocDetailView = document.getElementById('detalle-oc');

            // Referencias a elementos dentro de la vista de detalle
            const detailConcesionarioRazonSocial = document.getElementById('detail-concesionario-razon-social');
            const detailOcSummary = document.getElementById('detail-oc-summary');
            const tablaDetallesBody = document.getElementById('tabla-detalles').querySelector('tbody');
            const btnVolver = document.getElementById('btn-volver');

            // Selecciona todos los enlaces con la clase 'show-details'
            const detailLinks = document.querySelectorAll('.show-details');

            function limpiarVistaDetalle() {
                // Limpiar el nombre del concesionario
                if (detailConcesionarioRazonSocial) {
                    detailConcesionarioRazonSocial.textContent = '';
                }

                // Limpiar el resumen de la OC (número, fecha, moneda, total)
                if (detailOcSummary) {
                    detailOcSummary.textContent = '';
                }

                // Limpiar la tabla de detalles (vehículos)
                if (tablaDetallesBody) {
                    tablaDetallesBody.innerHTML = '';
                }
            }

            // Evento para eventos clikc de detalles
            detailLinks.forEach(link => {
                link.addEventListener('click', async (event) => {
                    event.preventDefault();
                    const ocId = event.target.dataset.idoc;

                    if (!ocId) {
                        console.warn('ID de Orden de Compra no encontrado en el enlace de detalle.');
                        return;
                    }

                    // Limpiar vista antes de cargar nuevos datos
                    limpiarVistaDetalle();

                    const apiUrl = `/api/oc/${ocId}`;

                    try {
                        const response = await fetch(apiUrl);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const ocDetails = await response.json();

                        // Validar si hay datos válidos
                        if (!ocDetails || !Array.isArray(ocDetails) || ocDetails.length === 0) {
                            showToast('No hay datos para la OC', 'WARNING', 1200);
                            return; // No mostrar la vista de detalles.
                        }

                        // Mostrar la vista de detalle con animación
                        $("#lista-oc").slideUp(speedAnimation);
                        $("#detalle-oc").slideDown(speedAnimation);

                        // Llenar información del encabezado
                        const firstDetail = ocDetails[0];

                        if (detailConcesionarioRazonSocial && detailOcSummary) {
                            detailConcesionarioRazonSocial.textContent = firstDetail.concesionario_razon_social || 'N/A';

                            const numeroOc = firstDetail.numero_oc_formateado || 'N/A';
                            const fechaEmision = firstDetail.fecha_emision_oc || 'N/A';
                            const moneda = firstDetail.moneda_oc || 'N/A';
                            const total = firstDetail.total_general_orden ? parseFloat(firstDetail.total_general_orden).toFixed(2) : '0.00';

                            detailOcSummary.textContent = `${numeroOc} | ${fechaEmision} | ${moneda} ${total}`;
                        }

                        // Llenar tabla de detalles
                        if (tablaDetallesBody) {
                            tablaDetallesBody.innerHTML = '';

                            ocDetails.forEach((detail, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${detail.vehiculo_marca || 'N/A'}</td>
                            <td>${detail.vehiculo_modelo || 'N/A'}</td>
                            <td>${detail.vehiculo_version || 'N/A'}</td>
                            <td>${detail.vehiculo_combustible || 'N/A'}</td>
                            <td>${detail.vehiculo_anio_modelo || 'N/A'}</td>
                            <td>${detail.vehiculo_chasis || 'N/A'}</td>
                            <td>${detail.vehiculo_serie_motor || 'N/A'}</td>
                            <td>${detail.vehiculo_placa || 'N/A'}</td>
                            <td>${detail.vehiculo_placa_rotativa || 'N/A'}</td>
                            <td>${detail.vehiculo_color || 'N/A'}</td>
                            <td>${detail.moneda_oc || 'N/A'}</td>
                            <td>${detail.vehiculo_precio_unitario ? parseFloat(detail.vehiculo_precio_unitario).toFixed(2) : '0.00'}</td>
                        `;
                                tablaDetallesBody.appendChild(row);
                            });
                        }

                    } catch (error) {
                      showToast('No se ha podido cargar los datos','WARNING',1200);
                    }
                });
            });


            if (botonVolver) {
                botonVolver.addEventListener('click', () => {

                    limpiarVistaDetalle();


                    $("#detalle-oc").slideUp(speedAnimation);
                    $("#lista-oc").slideDown(speedAnimation);
                });
            }


            // Comportamiento para botones de filtrado
            botonesFiltro.forEach(boton => {
                boton.addEventListener("click", () => {
                    botonesFiltro.forEach(btn => btn.classList.remove("active"));
                    boton.classList.add("active");
                })
            });



        });
    </script>

    <?php include __DIR__ . '/../layout/footer.php'; ?>